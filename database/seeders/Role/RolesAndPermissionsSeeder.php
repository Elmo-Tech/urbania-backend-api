<?php

namespace Database\Seeders\Role;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // users page
        $allUser = "allUser";
        $createUser = "createUser";
        $editUser = "editUser";
        $updateUser = "updateUser";
        $deleteUser = "deleteUser";

        // clients page
        $allClient = "allClient";
        $createClient = "createClient";
        $editClient = "editClient";
        $updateClient = "updateClient";
        $deleteClient = "deleteClient";
        $sInfoClint = "sInfoClint";

        // contract page
        $allContract = "allContract";
        $createContract = "createContract";
        $editContract = "editContract";
        $updateContract = "updateContract";
        $deleteContract = "deleteContract";

        // ticket page
        $allTicket = "allTicket";
        $createTicket = "createTicket";
        $editTicket = "editTicket";
        $updateTicket = "updateTicket";
        $deleteTicket = "deleteTicket";

        // parameter page
        $allParameter = "allParameter";
        $createParameter = "createParameter";
        $editParameter = "editParameter";
        $updateParameter = "updateParameter";
        $deleteParameter = "deleteParameter";

        //contacts
        $allContact = "allContact";
        $createContact = "createContact";
        $editContact = "editContact";
        $updateContact = "updateContact";
        $deleteContact = "deleteContact";

        //addresses
        $allAddress = "allAddress";
        $createAddress = "createAddress";
        $editAddress = "editAddress";
        $updateAddress = "updateAddress";
        $deleteAddress = "deleteAddress";

        //contractservices
        $createContractService = "createContractService";
        $editContractService = "editContractService";
        $updateContractService = "updateContractService";
        $deleteContractService = "deleteContractService";

        //ticketclient
        $showTicketClient = "showTicketClient";

        //dashboard
        $dashboardMainStats = "mainstats";

        //email
        $sendEmail = "sendEmail";

        //events
        $allEvents = "allEvents";
        $createEvent = "createEvent";
        $editEvent = "editEvent";
        $updateEvent = "updateEvent";
        $deleteEvent = "deleteEvent";

        //contractplusdata
        $allContractPlusData = "allContractPlusData";
        $createContractPlusData = "createContractPlusData";
        $editContractPlusData = "editContractPlusData";
        $updateContractPlusData = "updateContractPlusData";
        $deleteContractPlusData = "deleteContractPlusData";

        //lavorazionesecfour
        $allLavorazioneSecFour = "allLavorazioneSecFour";
        $createLavorazioneSecFour = "createLavorazioneSecFour";
        $editLavorazioneSecFour = "editLavorazioneSecFour";
        $updateLavorazioneSecFour = "updateLavorazioneSecFour";
        $deleteLavorazioneSecFour = "deleteLavorazioneSecFour";

        //lavorazionemaindata
        $createLavorazioneMainData = "createLavorazioneMainData";
        $editLavorazioneMainData = "editLavorazioneMainData";
        $updateLavorazioneMainData = "updateLavorazioneMainData";

        //lavorazionesecone
        $createLavorazioneSecOne = "createLavorazioneSecOne";
        $editLavorazioneSecOne = "editLavorazioneSecOne";
        $updateLavorazioneSecOne = "updateLavorazioneSecOne";
        $deleteLavorazioneSecOne = "deleteLavorazioneSecOne";

        //lavorazionesectwo
        $createLavorazioneSecTwo = "createLavorazioneSecTwo";
        $editLavorazioneSecTwo = "editLavorazioneSecTwo";
        $updateLavorazioneSecTwo = "updateLavorazioneSecTwo";
        $deleteLavorazioneSecTwo = "deleteLavorazioneSecTwo";

        //lavorazionesecthree
        $createLavorazioneSecThree = "createLavorazioneSecThree";
        $editLavorazioneSecThree = "editLavorazioneSecThree";
        $updateLavorazioneSecThree = "updateLavorazioneSecThree";
        $deleteLavorazioneSecThree = "deleteLavorazioneSecThree";

        //tecnicamaindata
        $createTecnicaMainData = "createTecnicaMainData";
        $editTecnicaMainData = "editTecnicaMainData";
        $updateTecnicaMainData = "updateTecnicaMainData";

        //tecnicasecone
        $createTecnicaSecOne = "createTecnicaSecOne";
        $editTecnicaSecOne = "editTecnicaSecOne";
        $updateTecnicaSecOne = "updateTecnicaSecOne";
        $deleteTecnicaSecOne = "deleteTecnicaSecOne";
        
        // tecnicasectwo
        $createTecnicaSecTwo = "createTecnicaSecTwo";
        $editTecnicaSecTwo = "editTecnicaSecTwo";
        $updateTecnicaSecTwo = "updateTecnicaSecTwo";
        $deleteTecnicaSecTwo = "deleteTecnicaSecTwo";
        
        //uploads
        $uploadMultipleFiles = "uploadMultipleFiles";
        $readFiles = "readFiles";
        $writeFiles = "writeFiles";
        $renameFile = "renameFile";
        $deleteFiles = "deleteFiles";

        // user Permissions
        Permission::create(['name' => $allUser]);
        Permission::create(['name' => $createUser]);
        Permission::create(['name' => $editUser]);
        Permission::create(['name' => $updateUser]);
        Permission::create(['name' => $deleteUser]);

        // client Permissions
        Permission::create(['name' => $allClient]);
        Permission::create(['name' => $createClient]);
        Permission::create(['name' => $editClient]);
        Permission::create(['name' => $updateClient]);
        Permission::create(['name' => $deleteClient]);
        Permission::create(['name' => $sInfoClint]);

        // contract Permissions
        Permission::create(['name' => $allContract]);
        Permission::create(['name' => $createContract]);
        Permission::create(['name' => $editContract]);
        Permission::create(['name' => $updateContract]);
        Permission::create(['name' => $deleteContract]);

        // ticket Permissions
        Permission::create(['name' => $allTicket]);
        Permission::create(['name' => $createTicket]);
        Permission::create(['name' => $editTicket]);
        Permission::create(['name' => $updateTicket]);
        Permission::create(['name' => $deleteTicket]);

        
        // parameter Permissions
        Permission::create(['name' => $allParameter]);
        Permission::create(['name' => $createParameter]);
        Permission::create(['name' => $editParameter]);
        Permission::create(['name' => $updateParameter]);
        Permission::create(['name' => $deleteParameter]);


        // contact Permissions
        Permission::create(['name' => $allContact]);
        Permission::create(['name' => $createContact]);
        Permission::create(['name' => $editContact]);
        Permission::create(['name' => $updateContact]);
        Permission::create(['name' => $deleteContact]);


        // address Permissions
        Permission::create(['name' => $allAddress]);
        Permission::create(['name' => $createAddress]);
        Permission::create(['name' => $editAddress]);
        Permission::create(['name' => $updateAddress]);
        Permission::create(['name' => $deleteAddress]);


        // contractservice Permissions
        Permission::create(['name' => $createContractService]);
        Permission::create(['name' => $editContractService]);
        Permission::create(['name' => $updateContractService]);
        Permission::create(['name' => $deleteContractService]);

        // ticketclient Permissions
        Permission::create(['name' => $showTicketClient]);


        // dashboard Permissions
        Permission::create(['name' => $dashboardMainStats]);


        // email Permissions
        Permission::create(['name' => $sendEmail]);


        // events Permissions
        Permission::create(['name' => $allEvents]);
        Permission::create(['name' => $createEvent]);
        Permission::create(['name' => $editEvent]);
        Permission::create(['name' => $updateEvent]);
        Permission::create(['name' => $deleteEvent]);


        // contractplusdata Permissions
        Permission::create(['name' => $allContractPlusData]);
        Permission::create(['name' => $createContractPlusData]);
        Permission::create(['name' => $editContractPlusData]);
        Permission::create(['name' => $updateContractPlusData]);
        Permission::create(['name' => $deleteContractPlusData]);


        // lavorazionesecfour Permissions
        Permission::create(['name' => $allLavorazioneSecFour]);
        Permission::create(['name' => $createLavorazioneSecFour]);
        Permission::create(['name' => $editLavorazioneSecFour]);
        Permission::create(['name' => $updateLavorazioneSecFour]);
        Permission::create(['name' => $deleteLavorazioneSecFour]);


        // lavorazionemaindata Permissions
        Permission::create(['name' => $createLavorazioneMainData]);
        Permission::create(['name' => $editLavorazioneMainData]);
        Permission::create(['name' => $updateLavorazioneMainData]);


        // lavorazionesecone Permissions
        Permission::create(['name' => $createLavorazioneSecOne]);
        Permission::create(['name' => $editLavorazioneSecOne]);
        Permission::create(['name' => $updateLavorazioneSecOne]);
        Permission::create(['name' => $deleteLavorazioneSecOne]);


        // lavorazionesectwo Permissions
        Permission::create(['name' => $createLavorazioneSecTwo]);
        Permission::create(['name' => $editLavorazioneSecTwo]);
        Permission::create(['name' => $updateLavorazioneSecTwo]);
        Permission::create(['name' => $deleteLavorazioneSecTwo]);


        // lavorazionesecthree Permissions
        Permission::create(['name' => $createLavorazioneSecThree]);
        Permission::create(['name' => $editLavorazioneSecThree]);
        Permission::create(['name' => $updateLavorazioneSecThree]);
        Permission::create(['name' => $deleteLavorazioneSecThree]);

        //tecnicamaindata Permissions
        Permission::create(['name' => $createTecnicaMainData]);
        Permission::create(['name' => $editTecnicaMainData]);
        Permission::create(['name' => $updateTecnicaMainData]);


        //tecnicasecone Permissions
        Permission::create(['name' => $createTecnicaSecOne]);
        Permission::create(['name' => $editTecnicaSecOne]);
        Permission::create(['name' => $updateTecnicaSecOne]);
        Permission::create(['name' => $deleteTecnicaSecOne]);


        //tecnicasectwo Permissions
        Permission::create(['name' => $createTecnicaSecTwo]);
        Permission::create(['name' => $editTecnicaSecTwo]);
        Permission::create(['name' => $updateTecnicaSecTwo]);
        Permission::create(['name' => $deleteTecnicaSecTwo]);

        //uploads Permissions
        Permission::create(['name' => $uploadMultipleFiles]);
        Permission::create(['name' => $readFiles]);
        Permission::create(['name' => $writeFiles]);
        Permission::create(['name' => $renameFile]);
        Permission::create(['name' => $deleteFiles]);

        $superAdmin = Role::create(['name' => 'superAdmin']);
        $superAdmin->givePermissionTo(Permission::all());

        $admin = Role::create(['name' => 'admin'])
        ->givePermissionTo([
                        //dashboard
                        $dashboardMainStats,

            // clients
            $allClient,
            $createClient,
            $editClient,
            $updateClient,
            $deleteClient,
            $sInfoClint,
            // contract
            $allContract,
            $createContract,
            $editContract,
            $updateContract,
            $deleteContract,
            // contractplusdata
            $allContractPlusData,
            $createContractPlusData,
            $editContractPlusData,
            $updateContractPlusData,
            $deleteContractPlusData,
            // lavorazionesecfour
            $allLavorazioneSecFour,
            $createLavorazioneSecFour,
            $editLavorazioneSecFour,
            $updateLavorazioneSecFour,
            $deleteLavorazioneSecFour,
            // lavorazionemaindata
            $createLavorazioneMainData,
            $editLavorazioneMainData,
            $updateLavorazioneMainData,
            // lavorazionesecone
            $createLavorazioneSecOne,
            $editLavorazioneSecOne,
            $updateLavorazioneSecOne,
            $deleteLavorazioneSecOne,
            // lavorazionesectwo
            $createLavorazioneSecTwo,
            $editLavorazioneSecTwo,
            $updateLavorazioneSecTwo,
            $deleteLavorazioneSecTwo,
            // lavorazionesecthree
            $createLavorazioneSecThree,
            $editLavorazioneSecThree,
            $updateLavorazioneSecThree,
            $deleteLavorazioneSecThree,
            // tecnicamaindata
            $createTecnicaMainData,
            $editTecnicaMainData,
            $updateTecnicaMainData,
            // tecnicasecone
            $createTecnicaSecOne,
            $editTecnicaSecOne,
            $updateTecnicaSecOne,
            $deleteTecnicaSecOne,
            // tecnicasectwo
            $createTecnicaSecTwo,
            $editTecnicaSecTwo,
            $updateTecnicaSecTwo,
            $deleteTecnicaSecTwo,
            // uploads
            $uploadMultipleFiles,
            $readFiles,
            $writeFiles,
            $renameFile,
            $deleteFiles,
            // tickets
            $allTicket,
            $createTicket,
            $editTicket,
            $updateTicket,
            $deleteTicket,
            // parameters
            $allParameter,
            $createParameter,
            $editParameter,
            $updateParameter,
            $deleteParameter,
            // contacts
            $allContact,
            $createContact,
            $editContact,
            $updateContact,
            $deleteContact,
            // addresses
            $allAddress,
            $createAddress,
            $editAddress,
            $updateAddress,
            $deleteAddress,
            // contractservices
            $createContractService,
            $editContractService,
            $updateContractService,
            $deleteContractService,
            // ticketclient
            $showTicketClient,
            // dashboard
            $dashboardMainStats,
            // email
            $sendEmail,
            // events
            $allEvents,
            $createEvent,
            $editEvent,
            $updateEvent,
            $deleteEvent
        ]);


        $standard = Role::create(['name' => 'standard'])
        ->givePermissionTo([
            // clients
            $allClient,
            $createClient,
            $editClient,
            $deleteClient,
            $sInfoClint,
            // contract
            $allContract,
            $createContract,
            $editContract,
            $deleteContract,
            // lavorazionesecfour
            $allLavorazioneSecFour,
            $createLavorazioneSecFour,
            $editLavorazioneSecFour,
            $updateLavorazioneSecFour,
            // lavorazionemaindata
            $createLavorazioneMainData,
            $editLavorazioneMainData,
            $updateLavorazioneMainData,
            // lavorazionesecone
            $createLavorazioneSecOne,
            $editLavorazioneSecOne,
            $updateLavorazioneSecOne,
            // lavorazionesectwo
            $createLavorazioneSecTwo,
            $editLavorazioneSecTwo,
            $updateLavorazioneSecTwo,
            // lavorazionesecthree
            $createLavorazioneSecThree,
            $editLavorazioneSecThree,
            $updateLavorazioneSecThree,
            // tecnicamaindata
            $createTecnicaMainData,
            $editTecnicaMainData,
            $updateTecnicaMainData,
            // tecnicasecone
            $createTecnicaSecOne,
            $editTecnicaSecOne,
            $updateTecnicaSecOne,
            // tecnicasectwo
            $createTecnicaSecTwo,
            $editTecnicaSecTwo,
            $updateTecnicaSecTwo,
            // uploads
            $uploadMultipleFiles,
            $readFiles,
            $writeFiles,
            $renameFile,
            $deleteFiles,
            // tickets
            $allTicket,
            $createTicket,
            $editTicket,
            $updateTicket,
            $deleteTicket,
            // parameters
            $allParameter,
            $createParameter,
            $editParameter,
            $updateParameter,
            $deleteParameter,
            // contacts
            $allContact,
            $createContact,
            $editContact,
            $updateContact,
            $deleteContact,
            // addresses
            $allAddress,
            $createAddress,
            $editAddress,
            $updateAddress,
            $deleteAddress,
            // contractservices
            $createContractService,
            $editContractService,
            $updateContractService,
            $deleteContractService,
            // ticketclient
            $showTicketClient,
            // dashboard
            $dashboardMainStats,
            // email
            $sendEmail,
            // events
            $allEvents,
            $createEvent,
            $editEvent,
            $updateEvent,
            $deleteEvent
        ]);


        $limitata = Role::create(['name' => 'limitata'])
        ->givePermissionTo([
            //dashboard
            $dashboardMainStats,
            // tickets
            $allTicket,
            $createTicket,
            $editTicket,
            $updateTicket,
            // ticketclient
            $showTicketClient,
            // events
            $allEvents,
            $createEvent,
            $editEvent,
            $updateEvent,
        ]);

        $superLimitata = Role::create(['name' => 'superLimitata'])
        ->givePermissionTo([
            //dashboard
            $dashboardMainStats,
            // tickets
            $allTicket,
            $createTicket,
            $editTicket,
            $updateTicket,
            // ticketclient
            $showTicketClient,
            // events
            $allEvents,
            $createEvent,
            $editEvent,
            $updateEvent,
        ]);



        
    }
}
