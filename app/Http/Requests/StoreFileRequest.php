<?php

namespace App\Http\Requests;

use App\Models\File;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ParentIdBaseRequest;
use Illuminate\Foundation\Http\FormRequest;

class StoreFileRequest extends ParentIdBaseRequest
{
   protected function prepareForValidation()
   {
      $path =  array_filter( $this->relative_paths ?? [] , fn($f) =>$f != null );
      $this->merge([
        'file_paths' => $path,
        'folder_name' => $this->detectFolderName($path),
      ]);
   }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return array_merge(
            parent::rules(),
             [
            'files.*' => [
                'required',
                'file',
               function ($attribute, $value, $fail) {
                if(!$this->folder_name){
                         /** @var $value \Illuminate\Http\UploadedFile */
                 $file = File::query()->where('name', $value->getClientOriginalName())
                    ->where('created_by' , Auth::id())
                    ->where('parent_id' , $this->parent_id)
                    ->whereNull('deleted_at')
                    ->exists();

                    if($file){
                        $fail('File "' . $value->getClientOriginalName() . ' " already exists');
                    }
                }
                   
                }
            ],
            'folder_name' => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) {
                    if($value){
                    /** @var $value \Illuminate\Http\UploadedFile */
                 $file = File::query()->where('name', $value)
                    ->where('created_by' , Auth::id())
                    ->where('parent_id' , $this->parent_id)
                    ->whereNull('deleted_at')
                    ->exists();

                    if($file){
                        $fail('Folder"' . $value. ' " already exists');
                    }
                 }
                }

            ]
        ]);
    }

    public function detectFolderName($path){
        if(!$path){
            return null;
        }
       $part=  explode('/', $path[0]);
        return $part[0];
    }
}
