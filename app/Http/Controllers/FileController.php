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
    public function myFiles(Request $request) // Or your specific FormRequest if applicable
    {
        $user = Auth::user();
        $currentFolder = null;

        $requestedParentId = $request->input('parent_id');

        if ($requestedParentId) {
            // If a specific folder is requested, try to load it.
            // Ensure it belongs to the user and is a folder.
            $currentFolder = File::query()
                ->where('id', $requestedParentId)
                ->where('created_by', $user->id)
                ->where('is_folder', 1)
                ->firstOrFail(); // This will throw a 404 if the folder is not found or not accessible.
        } else {
            // No specific folder requested, so display the root folder.
            // This will get the existing root or create one if it's missing.
            $currentFolder = $this->getOrCreateUserRootFolder();
        }

        // Fetch files and subfolders within the currentFolder
        $files = File::query()
            ->where('parent_id', $currentFolder->id)
            ->where('created_by', $user->id)
            ->orderBy('is_folder', 'desc') // Show folders first
            ->orderBy('name', 'asc')
            ->paginate(config('app.files_per_page', 15)); // Use a config value for items per page
            $files = FileResource::collection($files);
        return Inertia::render('MyFiles', [
            'files' => $files,
            // Optionally, you can pass the current folder and its ancestors for breadcrumbs
            // 'currentFolder' => new FileResource($currentFolder),
            // 'ancestors' => FileResource::collection($currentFolder->ancestors()->orderBy('path', 'asc')->get()),
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
