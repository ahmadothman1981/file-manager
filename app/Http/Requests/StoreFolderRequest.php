<?php

namespace App\Http\Requests;

use App\Models\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreFolderRequest extends ParentIdBaseRequest
{
     /**
     * Determines the effective parent ID for the uniqueness check.
     * This will be the ID of the parent folder from the request,
     * or the ID of the user's root folder if no parent_id is specified.
     */
    protected function getEffectiveParentIdForUniqueCheck(): ?int
    {
        // $this->parent is populated by ParentIdBaseRequest::authorize()
        // It's the File model if a valid parent_id was provided in the request.
        if ($this->parent) {
            return $this->parent->id;
        }

        // If $this->parent is null and a parent_id was in the input,
        // it means the parent_id was invalid (e.g., not found).
        // The `parent::rules()` will catch this. For the unique rule, using null is fine
        // as the request will fail due to the invalid parent_id anyway.
        if ($this->input('parent_id')) {
            return null;
        }

        // No parent_id provided in the request; new folder goes into the user's root.
        $rootFolder = File::query()->whereNull('parent_id')->where('created_by', Auth::id())->first();
        return $rootFolder?->id;
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $effectiveParentId = $this->getEffectiveParentIdForUniqueCheck();
        return array_merge(
            parent::rules(),
            [
                'name' => [
                    'required',
                    'string',
                    'max:255',
                Rule::unique(File::class,'name')
                ->where('created_by' , Auth::id())
                ->where('parent_id', $effectiveParentId)
                ->whereNull('deleted_at')
                ]
            ]
        );
    }


    public function messages(): array
    {
        return [
            'name.unique' => 'Name already exists'
        ];
    }
}
