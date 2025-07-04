<?php

namespace App\Http\Controllers;

use App\Models\File;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Resources\FileResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreFileRequest;
use App\Http\Requests\StoreFolderRequest;

class FileController extends Controller
{
   
    public function myFiles(Request $request, ?string $folder = null)
    {
        
        if($folder){
            $folder = File::query()->where('created_by',Auth::id())
            ->where('path', $folder)->firstOrFail();
        }
       if(!$folder){
         $folder = $this->getRoot();
       }
       $files =  File::query()
              ->where('parent_id', $folder->id)
              ->where('created_by', Auth::id())
              ->orderBy('is_folder', 'desc')
              ->orderBy('created_at', 'desc')
              ->paginate(5);

              $files = FileResource::collection($files);
              if($request->wantsJson())
              {
                return $files;
              }
              $ancestors = FileResource::collection([...$folder->ancestors,$folder]);
              $folder = new FileResource($folder);
        return Inertia::render('MyFiles', compact('files','folder','ancestors'));

    }
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
    public function store(StoreFileRequest $request)
    {
         $data = $request->validated();
         $parent = $request->parent;
         $user = $request->user();
         $fileTree = $request->file_tree;
         if(!$parent)
         {
            $parent = $this->getRoot();
         }
         if(!empty($fileTree))
         {
            $this->saveFileTree($fileTree, $parent , $user);
         }else{
            foreach($data['files'] as $file)
            {
                /**
                 * @var \Illuminate\Http\UploadedFile $file
                 */
                $path = $file->store('/files/'.$user->id);
                $model = new File();
                $model->storage_path = $path;
                $model->is_folder = false;
                $model->name = $file->getClientOriginalName();
                $model->mime = $file->getMimeType();
                $model->size = $file->getSize();
                $parent->appendNode($model);
            
            }
         }
         
    }


    private function getRoot()
    {
        return File::query()->whereIsRoot()->where('created_by',Auth::id())->firstOrFail();
    }
    public function saveFileTree($fileTree , $parent , $user)
    {
        foreach($fileTree as $name => $file)
        {
            if(is_array($file))
            {
                $folder = new File();
                $folder->is_folder = 1;
                $folder->name = $name;
                $parent->appendNode($folder);
                $this->saveFileTree($file , $folder , $user);
            }else{
                 $path = $file->store('/files/'.$user->id);
                $model = new File();
                $model->storage_path = $path;
                $model->is_folder = false;
                $model->name = $file->getClientOriginalName();
                $model->mime = $file->getMimeType();
                $model->size = $file->getSize();
                $parent->appendNode($model);
            }
        }
    }
    public function destroy(Request $request )
    {
        $data = $request->validated();
        $parent = $request->parent;
        if($data['all'])
        {
            $children = $parent->children;
            foreach($children as $child)
            {
                $child->delete();
            }
        }else{
            foreach($data['ids'] ?? [] as $id)
            {
                $file = File::find($id);
                $file->delete();
            }
        }

        return to_route('myFiles' , ['folder' => $parent->path]);
    }
}
