<?php

namespace App\Http\Controllers\Api\Private\OuterLetter;

use App\Http\Controllers\Controller;
use App\Http\Requests\OuterLetter\CreateOuterLetterRequest;
use App\Http\Requests\OuterLetter\UpdateOuterLetterMobileRequest;
use App\Http\Requests\OuterLetter\UpdateOuterLetterRequest;
use App\Http\Resources\OuterLetter\AllOuterLetterMobileCollection;
use App\Http\Resources\OuterLetter\OuterLetterMobileResource;
use App\Services\OuterLetter\OuterLetterMobileService;
use App\Services\Upload\UploadService;
use App\Utils\PaginateCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class OuterLetterMobileController extends Controller
{
    protected $uploadService;
    protected $outerLetterMobileService;

    public function __construct(UploadService $uploadService, OuterLetterMobileService $outerLetterMobileService)
    {
        $this->middleware('auth:api');
        $this->outerLetterMobileService = $outerLetterMobileService;
        $this->uploadService = $uploadService;
    }

    public function allOuterLetter(Request $request){
        

        $allOuterLetter = $this->outerLetterMobileService->allOuterLetter($request->all());

        return response()->json(
            new AllOuterLetterMobileCollection(PaginateCollection::paginate($allOuterLetter, $request->pageSize?$request->pageSize:10))
        );
    }

    /*public function create(CreateOuterLetterRequest $createOuterLetterRequest){

        //return $this->addressService->createAddress($addressRequest->validated());

        try {

            DB::beginTransaction();


            $path = $this->uploadService->uploadFile([...$createOuterLetterRequest->validated(), 'uploadPath' => 'outer_letters']);

            $this->outerLetterService->CreateFromExcelOuterLetter($path);

            DB::commit();

            return response()->json([
                'message' => 'File Uploaded Scuccessfully',
            ], 200);
        } catch (\Exception $e) {
            throw $e;

            DB::rollBack();
        }

    }*/

    public function edit(Request $request)
    {
        try {
            $outerLetter = $this->outerLetterMobileService->editOuterLetter($request->outerLetterId);
            
            if ($outerLetter === null) {
                return response()->json([
                    'message' => 'Outer letter not found'
                ]);
            }
            
            return response()->json(new OuterLetterMobileResource($outerLetter));
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    }

    public function qrEdit(Request $request)
    {
        
        
        try {
            $outerLetter = $this->outerLetterMobileService->qrEditOuterLetter($request->outerLetterId);
            
            if ($outerLetter === null) {
                return response()->json([
                    'message' => 'Codice a barre inesistente o errato!'
                ], 401);
            }
            
            return response()->json([
                'message' => $outerLetter->is_opened == 1? 'has been opend' : '',
                'data' =>new OuterLetterMobileResource($outerLetter)    
            ]);        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    }


    public function update(UpdateOuterLetterRequest $updateOuterLetterRequest)
    {
        $outerLetter = $this->outerLetterMobileService->updateOuterLetter($updateOuterLetterRequest->validated());
        
        if(isset($updateOuterLetterRequest->validated()['files'])){
            

            $this->uploadService->uploadFile(['file' => $updateOuterLetterRequest->validated()['files'], 'uploadPath' => 'scanner/'. $outerLetter->id]);
        }

        return response()->json([
            'message' => 'outerLetter has been updated !',
        ]);
    }

    public function updateMobile(UpdateOuterLetterMobileRequest $updateOuterLetterRequest)
    {

        $outerLetter = $this->outerLetterMobileService->updateOuterLetter($updateOuterLetterRequest->validated());
        
        if (isset($updateOuterLetterRequest->validated()['files'])) {
            $files = $updateOuterLetterRequest->validated()['files'];
            
            foreach ($files as $file) {
                $this->uploadService->uploadFile(['file' => $file, 'uploadPath' => 'scanner/' . $outerLetter->id]);
            }
        }


        return response()->json([
            'message' => 'outerLetter has been updated !',
        ]);
    }


}
