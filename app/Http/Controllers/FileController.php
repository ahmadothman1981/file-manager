<?php

namespace App\Http\Controllers;

use App\Models\File;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreFolderRequest;
use App\Http\Resources\FileResource;

class FileController extends Controller
{
    private function getOrCreateUserRootFolder(): File
    {
        $user = Auth::user();
        /** @var File $root */
        $root = File::query()
            ->where('is_folder', 1)
            ->where('created_by', $user->id)
            ->whereNull('parent_id')
            ->first();

        if (!$root) {
            // If root folder doesn't exist, create it.
            // This is crucial after a database wipe or for new users.
            $root = File::create([
                'name' => 'My Files', // Or a dynamic name like $user->name . "'s Files"
                'is_folder' => 1,
                'created_by' => $user->id,
                'parent_id' => null,
                // Add any other default fields required by your File model for a folder
                // e.g., 'path' => '/', 'size' => 0, 'mime' => null, 'updated_by' => $user->id
            ]);
        }
        return $root;
    }

    /**
     * Display a listing of the resource.
     * This method handles the /my-files route.
     */
    public function myFiles(Request $request, string $folder = null)
{
    $user = Auth::user();

    // Determine current folder
    $currentFolder = $folder
        ? File::where('path', $folder)
            ->where('created_by', $user->id)
            ->where('is_folder', true)
            ->firstOrFail()
        : $this->getOrCreateUserRootFolder();

    // Fetch children files/folders
    $files = File::query()
        ->where('parent_id', $currentFolder->id)
        ->where('created_by', $user->id)
        ->orderBy('is_folder', 'desc')
        ->orderBy('name', 'asc')
        ->paginate(config('app.files_per_page', 15))
        ->withQueryString();

    return Inertia::render('MyFiles', [
        'files' => FileResource::collection($files),
        'currentFolder' => new FileResource($currentFolder),
        'breadcrumbs' => FileResource::collection($currentFolder->ancestors()->orderBy('path')->get()),
    ]);
}
    // Other methods like storeFolder, download, delete, etc.


    public function createFolder(StoreFolderRequest $request)
    {
        $data = $request->validated();
        $parent = $request->parent;
        if(!$parent)
        {
           $parent = $this->getRoot();
        }
        $file = new File();
        $file->is_folder = 1;
        $file->name = $data['name'];
        $parent->appendNode($file);
       
    }

    private function getRoot()
    {
        return File::query()->whereIsRoot()->where('created_by',Auth::id())->firstOrFail();
    }
}
