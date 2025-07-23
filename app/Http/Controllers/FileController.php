<?php

namespace App\Http\Controllers;

use App\Models\File;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Resources\FileResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreFileRequest;
use App\Http\Requests\TrashFileRequest;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\FilesActionRequest;
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
    public function trash(Request $request)
    {
        $files = File::onlyTrashed()
        ->where('created_by', Auth::id())
        ->orderBy('is_folder', 'desc')
        ->orderBy('deleted_at','desc')
        ->paginate(5);
    
        $files = FileResource::collection($files);

        if ($request->wantsJson()) {
            return $files;
        }

        return Inertia::render('Trash', compact('files'));
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
    public function destroy(FilesActionRequest $request )
    {
        $data = $request->validated();
        $parent = $request->parent;
        if($data['all'])
        {
            $children = $parent->children;
            foreach($children as $child)
            {
                $child->moveToTrach();
            }
        }else{
            foreach($data['ids'] ?? [] as $id)
            {
                $file = File::find($id);
                if($file)
                {
                    $file->moveToTrach();
                }
               
            }
        }

        return to_route('myFiles' , ['folder' => $parent->path]);
    }
    public function download(FilesActionRequest $request)
    {
        $data = $request->validated();
        $parent = $request->parent;

        $all = $data['all'] ?? false;
        $ids = $data['ids'] ?? [];
        if(!$all && empty($ids))
        {
            return [
                'message' => 'No files selected'
            ];
        }
        if($all)
        {
            $url = $this->createZip($parent->children);
            $fileName = $parent->name .'.zip';
        }else{
            if(count($ids) == 1)
            {
                $file = File::find($ids[0]);
                if($file->is_folder)
                {
                    if($file->children->count() == 0)
                    {
                        return [
                            'message' => 'Folder is empty'
                        ];
                    }
                    $url = $this->createZip($file->children);
                    $fileName = $file->name .'.zip';
                }else{
                    $publicFilename = pathinfo($file->storage_path, PATHINFO_BASENAME);

                    // Copy from 'local' (private) disk to 'public' disk
                    $fileContents = Storage::disk('local')->get($file->storage_path);
                    Storage::disk('public')->put($publicFilename, $fileContents);

                    $url = asset('storage/' . $publicFilename);
                    $fileName = $file->name;
                }

            }else{
                $files = File::query()->whereIn('id', $ids)->get();
                $url = $this->createZip($files);
                $fileName = $parent->name .'.zip';
            }
        
        }
        return [
            'url' => $url,
            'fileName' => $fileName,
        ];
    }
    public function createZip($files)
    {
        $zipName = Str::random().'.zip';
        $zipPath = 'zip/'.$zipName;

        $zipFile = Storage::disk('public')->path($zipPath);

        if (!is_dir(dirname($zipFile))) {
            Storage::disk('public')->makeDirectory('zip');
        }

        $zip = new \ZipArchive();

        if ($zip->open($zipFile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
            $this->addFileToZip($zip, $files);
        }

        $zip->close();

        return asset('storage/' . $zipPath);
    }
    private function addFileToZip($zip , $files, $ancestors = '')
    {
        foreach($files as $file)
        {
            if($file->is_folder)
            {
                $this->addFileToZip($zip , $file->children, $ancestors . $file->name . '/');
            }else{
                $zip->addFile(Storage::disk('local')->path($file->storage_path), $ancestors . $file->name);
            }
        }
    } 
    
    public function restore(TrashFileRequest $request) 
    {
       $data = $request->validated();
       if($data['all'])
       {
        $children = File::onlyTrashed()->where('created_by', Auth::id())->get();
        foreach($children as $child)
        {
            $child->restore();
        }
       }else{
          $ids = $data['ids'] ?? [];
          $children = File::onlyTrashed()->whereIn('id' , $ids)->get();
          foreach($children as $child)
          {
            $child->restore();
          }
       }
       return to_route('trash');
    }
    public function deleteForEver()
    {

    }
}
