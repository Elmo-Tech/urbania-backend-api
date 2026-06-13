<?php

namespace App\Services\Contract\Service;

use App\Models\Contract\Service\TecnicaSecOne;
use App\Services\Upload\UploadService;
use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class TecnicaSecOneService{

    protected $uploadService;

    public function __construct(UploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }


    public function createMultipleTecnicaSecOne(array $tecnicaSecOneData, array $options)
    {

        foreach ($tecnicaSecOneData as $key => $data) {
            $newTecnicaSecOneItem = TecnicaSecOne::create([
                'description' => $data['description'],
                'tecnica_sec_one_parameter_id'=> $data['tecnicaSecOneParameterId']?$data['tecnicaSecOneParameterId']:0,
                'tecnica_main_data_id'=> $options['tecnicaId'],
            ]);

            /*$files = $data['files'];
            
            $filesDirectory = "contracts/".$options['contractId']."/services/".$options['contractServiceId']."/tecnica/" . $newTecnicaSecOneItem->id;
            foreach ($files as $key => $file) {
                
                $dataToUpload = [
                    'file' => $file['path'],
                    'uploadPath' => $filesDirectory,
                ];

                $newFile = $this->uploadService->uploadFile($dataToUpload, 'uploads');

            }*/
    
        }

    }

    public function createTecnicaSecOne(array $tecnicaSecOneData)
    {

       $newTecnicaSecOneItem = TecnicaSecOne::create([
            'description' => $tecnicaSecOneData['description'],
            'tecnica_sec_one_parameter_id'=> $tecnicaSecOneData['tecnicaSecOneParameterId']?$tecnicaSecOneData['tecnicaSecOneParameterId']:0,
            'tecnica_main_data_id'=> $tecnicaSecOneData['tecnicaMainDataId'],
        ]);

        
        /*$files = $tecnicaSecOneData['files'];
            
        $filesDirectory = "contracts/".$tecnicaSecOneData['contractId']."/services/".$tecnicaSecOneData['contractServiceId']."/tecnica/" . 
        
        $newTecnicaSecOneItem->id;

        foreach ($files as $key => $file) {
            
            $dataToUpload = [
                'file' => $file,
                'uploadPath' => $filesDirectory,
            ];

            $newFile = $this->uploadService->uploadFile($dataToUpload, 'uploads');
        }*/

        return $newTecnicaSecOneItem;

    }

    public function editTecnicaSecOne(int $tecnicaSecOneId)
    {
        
        $newTecnicaSecOneItem = TecnicaSecOne::find($tecnicaSecOneId); 
        
        return $newTecnicaSecOneItem;
 
    }


    public function updateTecnicaSecOne(array $tecnicaSecOneData)
    {

        $updateTecnicaSecOneItem = TecnicaSecOne::find($tecnicaSecOneData['tecnicaSecOneId']);
        $updateTecnicaSecOneItem->fill([
            'description' => $tecnicaSecOneData['description'],
            'tecnica_sec_one_parameter_id'=> $tecnicaSecOneData['tecnicaSecOneParameterId']?$tecnicaSecOneData['tecnicaSecOneParameterId']:0,
            //'tecnica_main_data_id'=> $tecnicaSecOneData['tecnicaMainDataId'],
         ]);

        $updateTecnicaSecOneItem->save();

        return $updateTecnicaSecOneItem;
 
    }
 

    public function deleteTecnicaSecOne(int $tecnicaSecOneId)
    {

        
        try {
            
            $tecnicaSecOneItem = TecnicaSecOne::find($tecnicaSecOneId);
    
            $tecnicaSecOneItem->delete();
            
        } catch (\Throwable $th) {
            
            throw new Exception('error.');

        }

    }


    
}