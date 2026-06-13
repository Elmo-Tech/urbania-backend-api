<?php

namespace App\Http\Controllers\Api\Private\OuterLetter;

use App\Http\Controllers\Controller;
use App\Http\Requests\OuterLetter\CreateOuterLetterRequest;
use App\Http\Requests\OuterLetter\UpdateOuterLetterRequest;
use App\Http\Resources\OuterLetter\AllOuterLetterCollection;
use App\Http\Resources\OuterLetter\OuterLetterResource;
use App\Services\OuterLetter\OuterLetterService;
use App\Services\Upload\UploadService;
use App\Utils\PaginateCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class OuterLetterController extends Controller
{
    protected $uploadService;
    protected $outerLetterService;

    public function __construct(UploadService $uploadService, OuterLetterService $outerLetterService)
    {
        $this->middleware('auth:api');
        $this->outerLetterService = $outerLetterService;
        $this->uploadService = $uploadService;
    }

    public function allOuterLetter(Request $request){


        $allOuterLetter = $this->outerLetterService->allOuterLetter($request->all());


        return response()->json(
            new AllOuterLetterCollection(PaginateCollection::paginate($allOuterLetter, $request->pageSize?$request->pageSize:10))
        );
    }

    public function create(CreateOuterLetterRequest $createOuterLetterRequest){

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
            DB::rollBack();

            throw $e;

        }

    }

    public function edit(Request $request)
    {
        $outerLetter = $this->outerLetterService->editOuterLetter($request->outerLetterId);

        return response()->json(new OuterLetterResource($outerLetter));
    }

    public function update(UpdateOuterLetterRequest $updateOuterLetterRequest)
    {
        $outerLetter = $this->outerLetterService->updateOuterLetter($updateOuterLetterRequest->validated());

        return response()->json([
            'message' => 'outerLetter has been updated !',
        ]);
    }


}
