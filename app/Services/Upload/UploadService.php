<?php

namespace App\Services\Upload;

use Illuminate\Support\Facades\Storage;

class UploadService
{
    private $defaultUploadPath = 'uploads';
    private $storageDisks = [
        "uploads" => "uploads",
        "images"  => "images",
        "files"  => "files"
    ];

    public function uploadFile(array $fileData, string $storageDisk = 'uploads'): string
    {
        $file = $fileData['file'];
        $uplodedPath = "";
        $uploadPath = $fileData['uploadPath']? $fileData['uploadPath']:$this->defaultUploadPath;

        /*if($storageDisk == 'uploads'){
            $uploadPath = "";
        }*/

        $fileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $fileOnServerName = time() . '___' . $fileName;
        $fileExtension = $file->guessExtension();
        $filePath = Storage::disk($this->storageDisks[$storageDisk])->putFileAs($uploadPath, $file, $fileOnServerName . '.' . $fileExtension);

        //$filePath = Storage::disk($this->storageDisks[$storageDisk])->put($uploadPath . "/" .$fileOnServerName . '.' . $fileExtension, $file, 'public');

        $uploadedPath = $storageDisk."/".$filePath;

        return $uploadedPath;
    }

    public function uploadMultipleFile(array $filesData): array
    {
        $uploadedPaths = [];
        $uploadPath = $filesData['uploadPath'] ?? $this->defaultUploadPath;
        $files = $filesData['files'];
        foreach ($files as $key => $file) {

            $fileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

            $fileOnServerName = time() . '___' . $fileName;
            $fileExtension = $file->guessExtension();
            $filePath = Storage::disk('public')->putFileAs($uploadPath, $file, $fileOnServerName . '.' . $fileExtension);
            $uploadedPaths[] = $filePath;
        }

        return $uploadedPaths;
    }


    public function readFiles(string $path)
    {
        $fileDirectory = str_replace("-", "/", $path);

        $paths = Storage::disk('uploads')->files($fileDirectory);

        $files = [];

        foreach ($paths as $key => $path) {
            $files[] = [
                "path" => "uploads/" .$path,
                "actionStatus" => ""
            ];
        }
        return $files;

    }

    public function copyFiles(string $sourceDirectory, string $destinationDirectory, string $storageDisk = 'uploads'): int
    {
        $diskName = $this->storageDisks[$storageDisk] ?? $storageDisk;
        $disk = Storage::disk($diskName);
        $sourceDirectory = trim($sourceDirectory, '/');
        $destinationDirectory = trim($destinationDirectory, '/');
        $copiedFiles = 0;

        if (!$disk->exists($sourceDirectory)) {
            return 0;
        }

        foreach ($disk->allFiles($sourceDirectory) as $filePath) {
            $relativePath = ltrim(substr($filePath, strlen($sourceDirectory)), '/\\');
            $destinationPath = $destinationDirectory . '/' . $relativePath;
            $disk->copy($filePath, $destinationPath);
            $copiedFiles++;
        }

        return $copiedFiles;
    }

    /*public function renameFile(array $data)
    {

        $location = explode("-", $data['location']);

        $fileDircetoryPart1 = $location[0];
        $fileDircetoryPart2 = $location[1];

        if(isset($location[2])){
            $fileDircetoryPart2 = $location[1]. "/" .$location[2];
        }

        $newLocation = $fileDircetoryPart1.'/'.$fileDircetoryPart2.'/';

        $oldFileName = $data['oldFileName'];
        $fileName = $data['fileName'];



        print_r($newLocation.$oldFileName);
        print_r("\r");
        print_r($newLocation.$fileName);
        $path = Storage::disk('uploads')->move($newLocation.$oldFileName, $newLocation.$fileName);


        return [
                "path" => "uploads/" .$path,
                "actionStatus" => ""
            ];

    }*/

public function renameFile(array $data)
{
    $location = explode("-", $data['location']);

    $fileDircetoryPart1 = $location[0];
    $fileDircetoryPart2 = $location[1];

    if(isset($location[2])){
        $fileDircetoryPart2 = $location[1]. "/" .$location[2];
    }

    $newLocation = $fileDircetoryPart1.'/'.$fileDircetoryPart2.'/';

    $oldFileName = $data['oldFileName'];
    $fileName = $data['fileName'];

    print_r($newLocation . $oldFileName);
    print_r("=======");
    print_r($newLocation . $fileName);

    $path = Storage::disk('uploads')->move($newLocation . $oldFileName, $newLocation . $fileName);

    return [
        "path" => "uploads/" . $path,
        "actionStatus" => ""
    ];
}





    public function writeFiles(array $filesData)
    {

        $files = $filesData['files'];
        $directory = str_replace("-", "/", $filesData['directory']);

        foreach ($files as $key => $file) {

            if($file['actionStatus'] == "create"){
                $dataToUpload = [
                    'file' => $file['path'],
                    'uploadPath' => $directory,
                ];

                $newFile = $this->uploadFile($dataToUpload, 'uploads');
            }

            /*if($file['actionStatus'] == "delete"){
                if(File::exists($file['path'])) {
                    File::delete($file['path']);
                }

            }*/

        }

        return true;

    }


    public function deleteFile(string $path)
    {


            Storage::disk('uploads')->delete($path);
            //Storage::delete("app/public/" . $path);
            //File::delete(storage_path() . '/app/public/' . $path);
            /*if(Storage::disk('uploads')->delete(storage_path() . '/app/public/' . $path));
            {
                //dd(storage_path() . '/app/public/' . $path);
                return true;
            }*/


        return false;

    }


}
