<?php

// use App\Http\Controllers\Api\Private\Client\AddressController;
// use App\Http\Controllers\Api\Private\OuterLetter\OuterLetterMobileController;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Route;

// use App\Http\Controllers\Api\Public\Auth\AuthController;
// use App\Http\Controllers\Api\Private\Client\ClientController;
// use App\Http\Controllers\Api\Private\Client\ContactController;
// use App\Http\Controllers\Api\Private\Client\EmailByTicketController;

// use App\Http\Controllers\Api\Private\Contract\ContractController;
// use App\Http\Controllers\Api\Private\Contract\ContractPlusData\ContractPlusDataController;
// use App\Http\Controllers\Api\Private\Contract\Service\ServiceController;
// use App\Http\Controllers\Api\Private\Contract\Service\LavorazioneMainDataController;
// use App\Http\Controllers\Api\Private\Contract\Service\LavorazioneSecOneController;
// use App\Http\Controllers\Api\Private\Contract\Service\LavorazioneSecThreeController;
// use App\Http\Controllers\Api\Private\Contract\Service\LavorazioneSecTwoController;
// use App\Http\Controllers\Api\Private\Contract\Service\LavorazioneSecFourController;
// use App\Http\Controllers\Api\Private\Contract\Service\TecnicaMainDataController;
// use App\Http\Controllers\Api\Private\Contract\Service\TecnicaSecOneController;
// use App\Http\Controllers\Api\Private\Contract\Service\TecnicaSecTwoController;
// use App\Http\Controllers\Api\Private\DashboardController;
// use App\Http\Controllers\Api\Private\Parameter\ParameterController;
// use App\Http\Controllers\Api\Private\Select\SelectController;
// use App\Http\Controllers\Api\Private\Ticket\TicketController;
// use App\Http\Controllers\Api\Private\Ticket\SendEmailController;
// use App\Http\Controllers\Api\Private\Mail\MailerController;
// use App\Http\Controllers\Api\Private\TicketClient\TicketClientController;
// use App\Http\Controllers\Api\Private\User\UserController;
// use App\Http\Controllers\Api\Private\Event\EventCalenderController;
// use App\Http\Controllers\Api\Private\OuterLetter\OuterLetterController;
// use App\Http\Controllers\Api\Private\Upload\UploadController;

// /*
// |--------------------------------------------------------------------------
// | API Routes
// |--------------------------------------------------------------------------
// |
// | Here is where you can register API routes for your application. These
// | routes are loaded by the RouteServiceProvider and all of them will
// | be assigned to the "api" middleware group. Make something great!
// |
// */


// Route::prefix('v1/auth')->group(function(){
//     Route::post('register', [AuthController::class, 'register']);
//     Route::post('login', [AuthController::class, 'login']);
//     Route::post('logout', [AuthController::class, 'logout']);
//     Route::post('refresh', [AuthController::class, 'refresh']);
// });


// Route::prefix('v1/clients')->group(function(){
//     Route::get('', [ClientController::class, 'allclients']);
//     Route::post('create', [ClientController::class, 'create']);
//     Route::get('edit', [ClientController::class, 'edit']);
//     Route::put('update', [ClientController::class, 'update']);
//     Route::delete('delete', [ClientController::class, 'delete']);
//     Route::post('sinfo', [ClientController::class, 'sinfo']);

// });

// Route::prefix('v1/client-email')->group(function(){
    
//     Route::post('send', [SendEmailController::class, 'send']);
//     Route::get('edit', [EmailByTicketController::class, 'edit']);
// });



// Route::prefix('v1/contacts')->group(function(){
//     Route::get('', [ContactController::class, 'index']);
//     Route::post('create', [ContactController::class, 'create']);
//     Route::get('edit', [ContactController::class, 'edit']);
//     Route::put('update', [ContactController::class, 'update']);
//     Route::delete('delete', [ContactController::class, 'delete']);
// });

// Route::prefix('v1/addresses')->group(function(){
//     Route::get('', [AddressController::class, 'index']);
//     Route::post('create', [AddressController::class, 'create']);
//     Route::get('edit', [AddressController::class, 'edit']);
//     Route::put('update', [AddressController::class, 'update']);
//     Route::delete('delete', [AddressController::class, 'delete']);

// });

// Route::prefix('v1/contracts')->group(function(){
//     Route::get('', [ContractController::class, 'allcontracts']);
//     Route::post('create', [ContractController::class, 'create']);
//     Route::get('edit', [ContractController::class, 'edit']);
//     Route::put('update', [ContractController::class, 'update']);
//     Route::delete('delete', [ContractController::class, 'delete']);

// });

// Route::prefix('v1/parameters')->group(function(){
//     Route::get('', [ParameterController::class, 'allparameters']);
//     Route::post('create', [ParameterController::class, 'create']);
//     Route::get('edit', [ParameterController::class, 'edit']);
//     Route::put('update', [ParameterController::class, 'update']);
//     Route::delete('delete', [ParameterController::class, 'delete']);

// });


// Route::prefix('v1/contractservices')->group(function(){
//     Route::post('create', [ServiceController::class, 'create']);
//     Route::get('edit', [ServiceController::class, 'edit']);
//     Route::put('update', [ServiceController::class, 'update']);
//     Route::delete('delete', [ServiceController::class, 'delete']);

// });

// Route::prefix('v1/selects')->group(function(){
//     Route::get('clients', [SelectController::class, 'clients']);
//     Route::get('users', [SelectController::class, 'users']);
//     Route::get('parameters', [SelectController::class, 'parameters']);
//     Route::get('contracts', [SelectController::class, 'contracts']);
//     Route::get('roles', [SelectController::class, 'roles']);
//     Route::get('ticketclients', [SelectController::class, 'ticketclients']);
//     Route::get('ticketworker', [SelectController::class, 'ticketworker']);
//     Route::get('contractserviceyears', [SelectController::class, 'contractserviceyears']);
//     Route::get('exceltemplate', [SelectController::class, 'excelTemplate']);
//     Route::get('outerLetterCf', [SelectController::class, 'outerLetterCf']);
//         Route::get('clientCodiceFiscale', [SelectController::class, 'clientCodiceFiscale']);

    
// });


// Route::prefix('v1/tickets')->group(function(){
//     Route::get('', [TicketController::class, 'alltickets']);
//     Route::post('create', [TicketController::class, 'create']);
//     Route::get('edit', [TicketController::class, 'edit']);
//     Route::put('update', [TicketController::class, 'update']);
//     Route::delete('delete', [TicketController::class, 'delete']);
// });

// Route::prefix('v1/ticketclients')->group(function(){
//     Route::get('show', [TicketClientController::class, 'show']);
// });


// Route::prefix('v1/users')->group(function(){
//     Route::get('', [UserController::class, 'allusers'])->middleware(['role:superAdmin']);
//     Route::post('create', [UserController::class, 'create'])->middleware(['role:superAdmin']);
//     Route::get('edit', [UserController::class, 'edit'])->middleware(['role:superAdmin']);
//     Route::put('update', [UserController::class, 'update'])->middleware(['role:superAdmin']);
//     Route::delete('delete', [UserController::class, 'delete'])->middleware(['role:superAdmin']);
// });

// Route::prefix('v1/dashboard')->group(function(){
//     Route::get('mainstats', [DashboardController::class, 'mainstats']);
// });



// Route::prefix('v1/email')->group(function(){
//     Route::post('send', [MailerController::class, 'composeemail']);
// });

// Route::prefix('v1/events')->group(function(){
//     Route::get('', [EventCalenderController::class, 'allevents']);
//     Route::post('create', [EventCalenderController::class, 'create']);
//     Route::get('edit', [EventCalenderController::class, 'edit']);
//     Route::put('update', [EventCalenderController::class, 'update']);
//     Route::delete('delete', [EventCalenderController::class, 'delete']);

// });

// Route::prefix('v1/contractplusdata')->group(function(){
//     Route::get('', [ContractPlusDataController::class, 'allcontractplusdata']);
//     Route::post('create', [ContractPlusDataController::class, 'create']);
//     Route::get('edit', [ContractPlusDataController::class, 'edit']);
//     Route::put('update', [ContractPlusDataController::class, 'update']);
//     Route::delete('delete', [ContractPlusDataController::class, 'delete']);
// });

// Route::prefix('v1/lavorazionesecfour')->group(function(){
//     Route::get('', [LavorazioneSecFourController::class, 'allcontractsportello']);
//     Route::post('create', [LavorazioneSecFourController::class, 'create']);
//     Route::get('edit', [LavorazioneSecFourController::class, 'edit']);
//     Route::put('update', [LavorazioneSecFourController::class, 'update']);
//     Route::delete('delete', [LavorazioneSecFourController::class, 'delete']);
// });


// Route::prefix('v1/lavorazionemaindata')->group(function(){
//     Route::post('create', [LavorazioneMainDataController::class, 'create']);
//     Route::get('edit', [LavorazioneMainDataController::class, 'edit']);
//     Route::put('update', [LavorazioneMainDataController::class, 'update']);
// });


// Route::prefix('v1/lavorazionesecone')->group(function(){
//     Route::post('create', [LavorazioneSecOneController::class, 'create']);
//     Route::get('edit', [LavorazioneSecOneController::class, 'edit']);
//     Route::put('update', [LavorazioneSecOneController::class, 'update']);
//     Route::delete('delete', [LavorazioneSecOneController::class, 'delete']);
// });

// Route::prefix('v1/lavorazionesectwo')->group(function(){
//     Route::post('create', [LavorazioneSecTwoController::class, 'create']);
//     Route::get('edit', [LavorazioneSecTwoController::class, 'edit']);
//     Route::put('update', [LavorazioneSecTwoController::class, 'update']);
//     Route::delete('delete', [LavorazioneSecTwoController::class, 'delete']);
// });

// Route::prefix('v1/lavorazionesecthree')->group(function(){
//     Route::post('create', [LavorazioneSecThreeController::class, 'create']);
//     Route::get('edit', [LavorazioneSecThreeController::class, 'edit']);
//     Route::put('update', [LavorazioneSecThreeController::class, 'update']);
//     Route::delete('delete', [LavorazioneSecThreeController::class, 'delete']);
// });


// Route::prefix('v1/tecnicamaindata')->group(function(){
//     Route::post('create', [TecnicaMainDataController::class, 'create']);
//     Route::get('edit', [TecnicaMainDataController::class, 'edit']);
//     Route::put('update', [TecnicaMainDataController::class, 'update']);
// });

// Route::prefix('v1/tecnicasecone')->group(function(){
//     Route::post('create', [TecnicaSecOneController::class, 'create']);
//     Route::get('edit', [TecnicaSecOneController::class, 'edit']);
//     Route::put('update', [TecnicaSecOneController::class, 'update']);
//     Route::delete('delete', [TecnicaSecOneController::class, 'delete']);
// });

// Route::prefix('v1/tecnicasectwo')->group(function(){
//     Route::post('create', [TecnicaSecTwoController::class, 'create']);
//     Route::get('edit', [TecnicaSecTwoController::class, 'edit']);
//     Route::put('update', [TecnicaSecTwoController::class, 'update']);
//     Route::delete('delete', [TecnicaSecTwoController::class, 'delete']);
// });


// Route::prefix('v1/uploads')->group(function(){
//     Route::post('uploadmultiplefiles', [UploadController::class, 'uploadmultiplefiles']);
//     Route::get('getfiles', [UploadController::class, 'readfiles']);
//     Route::put('updatefiles', [UploadController::class, 'writefiles']);
//     Route::put('renamefile', [UploadController::class, 'renamefile']);
//     Route::delete('deletefiles', [UploadController::class, 'deletefiles']);
// });

// Route::prefix('v1/outerletters')->group(function(){
//     Route::get('', [OuterLetterController::class, 'allOuterLetter']);
//     Route::post('create', [OuterLetterController::class, 'create']);
//     Route::get('edit', [OuterLetterController::class, 'edit']);
//     Route::put('update', [OuterLetterController::class, 'update']);
//     Route::delete('delete', [OuterLetterController::class, 'delete']);
// });

// Route::prefix('v1/outerlettersmobile')->group(function(){
//     Route::get('', [OuterLetterMobileController::class, 'allOuterLetter']);
//     Route::post('create', [OuterLetterMobileController::class, 'create']);
//     Route::get('edit', [OuterLetterMobileController::class, 'edit']);
//     Route::get('qredit', [OuterLetterMobileController::class, 'qrEdit']);
//     Route::put('update', [OuterLetterMobileController::class, 'update']);
//     Route::put('updatemobile', [OuterLetterMobileController::class, 'updateMobile']);
//     Route::delete('delete', [OuterLetterMobileController::class, 'delete']);
// });


use App\Http\Controllers\Api\Private\Client\AddressController;
use App\Http\Controllers\Api\Private\OuterLetter\OuterLetterMobileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\Public\Auth\AuthController;
use App\Http\Controllers\Api\Private\Client\ClientController;
use App\Http\Controllers\Api\Private\Client\ContactController;
use App\Http\Controllers\Api\Private\Client\EmailByTicketController;
use App\Http\Controllers\Api\Private\ClientOuterTicket\ClientOuterTicketController;
use App\Http\Controllers\Api\Private\ClientOuterTicket\AiClientOuterTicketController;
use App\Http\Controllers\Api\Private\ClientOuterTicket\OuterTicketController;
use App\Http\Controllers\Api\Private\Contract\ContractController;
use App\Http\Controllers\Api\Private\Contract\ContractPlusData\ContractPlusDataController;
use App\Http\Controllers\Api\Private\Contract\Service\ServiceController;
use App\Http\Controllers\Api\Private\Contract\Service\LavorazioneMainDataController;
use App\Http\Controllers\Api\Private\Contract\Service\LavorazioneSecOneController;
use App\Http\Controllers\Api\Private\Contract\Service\LavorazioneSecThreeController;
use App\Http\Controllers\Api\Private\Contract\Service\LavorazioneSecTwoController;
use App\Http\Controllers\Api\Private\Contract\Service\LavorazioneSecFourController;
use App\Http\Controllers\Api\Private\Contract\Service\NoteOperativeController;
use App\Http\Controllers\Api\Private\Contract\Service\TecnicaMainDataController;
use App\Http\Controllers\Api\Private\Contract\Service\TecnicaSecOneController;
use App\Http\Controllers\Api\Private\Contract\Service\TecnicaSecTwoController;
use App\Http\Controllers\Api\Private\DashboardController;
use App\Http\Controllers\Api\Private\Parameter\ParameterController;
use App\Http\Controllers\Api\Private\Select\SelectController;
use App\Http\Controllers\Api\Private\Ticket\TicketController;
use App\Http\Controllers\Api\Private\Ticket\SendEmailController;
use App\Http\Controllers\Api\Private\Mail\MailerController;
use App\Http\Controllers\Api\Private\TicketClient\TicketClientController;
use App\Http\Controllers\Api\Private\User\UserController;
use App\Http\Controllers\Api\Private\Event\EventCalenderController;
use App\Http\Controllers\Api\Private\OuterLetter\OuterLetterController;
use App\Http\Controllers\Api\Private\Reservation\CustomerReservationController;
use App\Http\Controllers\Api\Private\Reservation\AiCustomerReservationController;
use App\Http\Controllers\Api\Private\Reservation\FreeReservationScheduleController;
use App\Http\Controllers\Api\Private\Reservation\ReservationController;
use App\Http\Controllers\Api\Private\Reservation\ReservationScheduleController;
use App\Http\Controllers\Api\Private\Upload\UploadController;
use App\Http\Controllers\MicrosoftAuthController;
use App\Http\Controllers\MicrosoftCalendarController;
//calendar

use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;

use League\OAuth2\Client\Provider\GenericProvider;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::prefix('v1/auth')->group(function(){
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
});


Route::prefix('v1/clients')->group(function(){
    Route::get('', [ClientController::class, 'allclients']);
    Route::post('create', [ClientController::class, 'create']);
    Route::get('edit', [ClientController::class, 'edit']);
    Route::put('update', [ClientController::class, 'update']);
    Route::delete('delete', [ClientController::class, 'delete']);
    Route::post('sinfo', [ClientController::class, 'sinfo']);

});

Route::prefix('v1/note-operatives')->group(function(){
    Route::get('', [NoteOperativeController::class, 'index']);
    Route::post('create', [NoteOperativeController::class, 'create']);
    Route::get('edit', [NoteOperativeController::class, 'edit']);
    Route::put('update', [NoteOperativeController::class, 'update']);
    Route::delete('delete', [NoteOperativeController::class, 'delete']);
});

Route::prefix('v1/client-email')->group(function(){

    Route::post('send', [SendEmailController::class, 'send']);
    Route::get('edit', [EmailByTicketController::class, 'edit']);
});



Route::prefix('v1/contacts')->group(function(){
    Route::get('', [ContactController::class, 'index']);
    Route::post('create', [ContactController::class, 'create']);
    Route::get('edit', [ContactController::class, 'edit']);
    Route::put('update', [ContactController::class, 'update']);
    Route::delete('delete', [ContactController::class, 'delete']);
});

Route::prefix('v1/addresses')->group(function(){
    Route::get('', [AddressController::class, 'index']);
    Route::post('create', [AddressController::class, 'create']);
    Route::get('edit', [AddressController::class, 'edit']);
    Route::put('update', [AddressController::class, 'update']);
    Route::delete('delete', [AddressController::class, 'delete']);

});

Route::prefix('v1/contracts')->group(function(){
    Route::get('', [ContractController::class, 'allcontracts']);
    Route::post('create', [ContractController::class, 'create']);
    Route::get('edit', [ContractController::class, 'edit']);
    Route::put('update', [ContractController::class, 'update']);
    Route::delete('delete', [ContractController::class, 'delete']);

});

Route::prefix('v1/parameters')->group(function(){
    Route::get('', [ParameterController::class, 'allparameters']);
    Route::post('create', [ParameterController::class, 'create']);
    Route::get('edit', [ParameterController::class, 'edit']);
    Route::put('update', [ParameterController::class, 'update']);
    Route::delete('delete', [ParameterController::class, 'delete']);

});


Route::prefix('v1/contractservices')->group(function(){
    Route::post('create', [ServiceController::class, 'create']);
    Route::get('edit', [ServiceController::class, 'edit']);
    Route::put('update', [ServiceController::class, 'update']);
    Route::delete('delete', [ServiceController::class, 'delete']);

});

Route::prefix('v1/selects')->group(function(){
    Route::get('clients', [SelectController::class, 'clients']);
    Route::get('users', [SelectController::class, 'users']);
    Route::get('parameters', [SelectController::class, 'parameters']);
    Route::get('parametersWithDescription', [SelectController::class, 'parametersWithDescription']);
    Route::get('clientServices', [SelectController::class, 'clientServices']);
    Route::get('urbaniaClients', [SelectController::class, 'urbaniaClients']);


    Route::get('contracts', [SelectController::class, 'contracts']);
    Route::get('roles', [SelectController::class, 'roles']);
    Route::get('ticketclients', [SelectController::class, 'ticketclients']);
    Route::get('ticketworker', [SelectController::class, 'ticketworker']);
    Route::get('contractserviceyears', [SelectController::class, 'contractserviceyears']);
    Route::get('exceltemplate', [SelectController::class, 'excelTemplate']);
    Route::get('outerLetterCf', [SelectController::class, 'outerLetterCf']);
        Route::get('clientCodiceFiscale', [SelectController::class, 'clientCodiceFiscale']);


});


Route::prefix('v1/tickets')->group(function(){
    Route::get('', [TicketController::class, 'alltickets']);
    Route::post('create', [TicketController::class, 'create']);
    Route::get('edit', [TicketController::class, 'edit']);
    Route::put('update', [TicketController::class, 'update']);
    Route::delete('delete', [TicketController::class, 'delete']);
});

Route::prefix('v1/ticketclients')->group(function(){
    Route::get('show', [TicketClientController::class, 'show']);
});


Route::prefix('v1/users')->group(function(){
    Route::get('', [UserController::class, 'allusers'])->middleware(['role:superAdmin']);
    Route::post('create', [UserController::class, 'create'])->middleware(['role:superAdmin']);
    Route::get('edit', [UserController::class, 'edit'])->middleware(['role:superAdmin']);
    Route::put('update', [UserController::class, 'update'])->middleware(['role:superAdmin']);
    Route::delete('delete', [UserController::class, 'delete'])->middleware(['role:superAdmin']);
});

Route::prefix('v1/dashboard')->group(function(){
    Route::get('mainstats', [DashboardController::class, 'mainstats']);
});



Route::prefix('v1/email')->group(function(){
    Route::post('send', [MailerController::class, 'composeemail']);
});

Route::prefix('v1/events')->group(function(){
    Route::get('', [EventCalenderController::class, 'allevents']);
    Route::post('create', [EventCalenderController::class, 'create']);
    Route::get('edit', [EventCalenderController::class, 'edit']);
    Route::put('update', [EventCalenderController::class, 'update']);
    Route::delete('delete', [EventCalenderController::class, 'delete']);

});

Route::prefix('v1/contractplusdata')->group(function(){
    Route::get('', [ContractPlusDataController::class, 'allcontractplusdata']);
    Route::post('create', [ContractPlusDataController::class, 'create']);
    Route::get('edit', [ContractPlusDataController::class, 'edit']);
    Route::put('update', [ContractPlusDataController::class, 'update']);
    Route::delete('delete', [ContractPlusDataController::class, 'delete']);
});

Route::prefix('v1/lavorazionesecfour')->group(function(){
    Route::get('', [LavorazioneSecFourController::class, 'allcontractsportello']);
    Route::post('create', [LavorazioneSecFourController::class, 'create']);
    Route::get('edit', [LavorazioneSecFourController::class, 'edit']);
    Route::put('update', [LavorazioneSecFourController::class, 'update']);
    Route::delete('delete', [LavorazioneSecFourController::class, 'delete']);
});


Route::prefix('v1/lavorazionemaindata')->group(function(){
    Route::post('create', [LavorazioneMainDataController::class, 'create']);
    Route::get('edit', [LavorazioneMainDataController::class, 'edit']);
    Route::put('update', [LavorazioneMainDataController::class, 'update']);
});


Route::prefix('v1/lavorazionesecone')->group(function(){
    Route::post('create', [LavorazioneSecOneController::class, 'create']);
    Route::get('edit', [LavorazioneSecOneController::class, 'edit']);
    Route::put('update', [LavorazioneSecOneController::class, 'update']);
    Route::delete('delete', [LavorazioneSecOneController::class, 'delete']);
});

Route::prefix('v1/lavorazionesectwo')->group(function(){
    Route::post('create', [LavorazioneSecTwoController::class, 'create']);
    Route::get('edit', [LavorazioneSecTwoController::class, 'edit']);
    Route::put('update', [LavorazioneSecTwoController::class, 'update']);
    Route::delete('delete', [LavorazioneSecTwoController::class, 'delete']);
});

Route::prefix('v1/lavorazionesecthree')->group(function(){
    Route::post('create', [LavorazioneSecThreeController::class, 'create']);
    Route::get('edit', [LavorazioneSecThreeController::class, 'edit']);
    Route::put('update', [LavorazioneSecThreeController::class, 'update']);
    Route::delete('delete', [LavorazioneSecThreeController::class, 'delete']);
});


Route::prefix('v1/tecnicamaindata')->group(function(){
    Route::post('create', [TecnicaMainDataController::class, 'create']);
    Route::get('edit', [TecnicaMainDataController::class, 'edit']);
    Route::put('update', [TecnicaMainDataController::class, 'update']);
});

Route::prefix('v1/tecnicasecone')->group(function(){
    Route::post('create', [TecnicaSecOneController::class, 'create']);
    Route::get('edit', [TecnicaSecOneController::class, 'edit']);
    Route::put('update', [TecnicaSecOneController::class, 'update']);
    Route::delete('delete', [TecnicaSecOneController::class, 'delete']);
});

Route::prefix('v1/tecnicasectwo')->group(function(){
    Route::post('create', [TecnicaSecTwoController::class, 'create']);
    Route::get('edit', [TecnicaSecTwoController::class, 'edit']);
    Route::put('update', [TecnicaSecTwoController::class, 'update']);
    Route::delete('delete', [TecnicaSecTwoController::class, 'delete']);
});


Route::prefix('v1/uploads')->group(function(){
    Route::post('uploadmultiplefiles', [UploadController::class, 'uploadmultiplefiles']);
    Route::get('getfiles', [UploadController::class, 'readfiles']);
    Route::put('updatefiles', [UploadController::class, 'writefiles']);
    Route::put('renamefile', [UploadController::class, 'renamefile']);
    Route::delete('deletefiles', [UploadController::class, 'deletefiles']);
});

Route::prefix('v1/outerletters')->group(function(){
    Route::get('', [OuterLetterController::class, 'allOuterLetter']);
    Route::post('create', [OuterLetterController::class, 'create']);
    Route::get('edit', [OuterLetterController::class, 'edit']);
    Route::put('update', [OuterLetterController::class, 'update']);
    Route::delete('delete', [OuterLetterController::class, 'delete']);
});

Route::prefix('v1/outerlettersmobile')->group(function(){
    Route::get('', [OuterLetterMobileController::class, 'allOuterLetter']);
    Route::post('create', [OuterLetterMobileController::class, 'create']);
    Route::get('edit', [OuterLetterMobileController::class, 'edit']);
    Route::get('qredit', [OuterLetterMobileController::class, 'qrEdit']);
    Route::put('update', [OuterLetterMobileController::class, 'update']);
    Route::put('updatemobile', [OuterLetterMobileController::class, 'updateMobile']);
    Route::delete('delete', [OuterLetterMobileController::class, 'delete']);
});

Route::prefix('v1/reservation-schedules')->group(function(){
    Route::get('', [ReservationScheduleController::class, 'index']);
    Route::post('create', [ReservationScheduleController::class, 'create']);
    Route::get('edit', [ReservationScheduleController::class, 'edit']);
    Route::put('update', [ReservationScheduleController::class, 'update']);
    Route::delete('delete', [ReservationScheduleController::class, 'delete']);
});

Route::prefix('v1/free-schedules')->group(function(){
    Route::get('', [FreeReservationScheduleController::class, 'index']);
    Route::get('check-availability', [FreeReservationScheduleController::class, 'checkAvailability']);
});

Route::prefix('v1/reservations')->group(function(){
    Route::get('', [ReservationController::class, 'index']);
    Route::post('create', [ReservationController::class, 'create']);
    Route::get('edit', [ReservationController::class, 'edit']);
    Route::put('update', [ReservationController::class, 'update']);
    Route::delete('delete', [ReservationController::class, 'destroy']);
});

Route::prefix('v1/client-reservations')->group(function(){
    Route::post('create', [CustomerReservationController::class, 'create']);
    Route::get('edit', [CustomerReservationController::class, 'edit']);
    Route::put('update', [CustomerReservationController::class, 'update']);
    Route::put('control', [CustomerReservationController::class, 'control']);

});

Route::prefix('v1/ai-client-reservations')->group(function(){
    Route::post('create', [AiCustomerReservationController::class, 'create']);
    Route::put('update', [AiCustomerReservationController::class, 'update']);

});

Route::prefix('v1/client-outer-tickets')->group(function(){
    Route::post('create', [ClientOuterTicketController::class, 'create']);
    Route::put('update', [ClientOuterTicketController::class, 'update']);
});

Route::prefix('v1/ai-client-outer-tickets')->group(function(){
    Route::post('create', [AiClientOuterTicketController::class, 'create']);
});

Route::prefix('v1/outer-tickets')->group(function(){
    Route::get('', [OuterTicketController::class, 'index']);
    Route::get('edit', [OuterTicketController::class, 'edit']);
    Route::put('update', [OuterTicketController::class, 'update']);
    Route::delete('delete', [OuterTicketController::class, 'delete']);
});


// Route::get('/auth/microsoft', [MicrosoftAuthController::class, 'redirectToMicrosoft']);
// Route::get('/auth/microsoft/callback', [MicrosoftAuthController::class, 'handleMicrosoftCallback']);

Route::get('/microsoft/calendar/events', [MicrosoftCalendarController::class, 'getEvents']);
Route::post('/microsoft/calendar/create-event', [MicrosoftCalendarController::class, 'createEvent']);
