<?php

namespace App\Http\Controllers\Api\Private\Upload;

use App\Http\Controllers\Controller;
use App\Http\Requests\Upload\UpdateFileRequest;
use App\Http\Requests\Upload\UploadFileRequest;
use App\Services\Upload\UploadService;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    protected $uploadService;

    public function __construct(UploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    public function uploadmultiplefiles(UploadFileRequest $uploadFileRequest)
    {
        
        $paths = $this->uploadService->uploadMultipleFile($uploadFileRequest->validated());
        
        return response()->json([
            'paths' => $paths
        ], 200);
    
    }

    public function readfiles(Request $req){

        $paths = $this->uploadService->readFiles($req->directory);

        return response()->json(
            $paths
        , 200);

    }

    public function writefiles(UpdateFileRequest $updateFileRequest){

        $paths = $this->uploadService->writeFiles($updateFileRequest->validated());

        return response()->json([
            'message' => "files has been updated"
        ], 200);

    }
    
    public function deletefiles(Request $req){


        $paths = $this->uploadService->deleteFile($req->directory);

        return response()->json([
            'message' => "file has been deleted"
        ], 200);

    }
    
     public function renamefile(Request $req){


        $paths = $this->uploadService->renameFile($req->all());

        return response()->json([
            'message' => "file has been renamed"
        ], 200);

    }

}
