<?php

namespace App\Services\UserRolePremission;

use Illuminate\Contracts\Auth\Authenticatable;

/*class UserPermissionService
{
    public function getUserPermissions(Authenticatable $user)
    {

        $userPermissions = [
            ["permissionName" => 'showAll', 'access' => $user->can('showAll')],
            ["permissionName" => 'create', 'access' => $user->can('createUser')],
            ["permissionName" => 'edit', 'access' => $user->can('editUser')],
            ["permissionName" => 'update', 'access' => $user->can('updateUser')],
            ["permissionName" => 'delete', 'access' => $user->can('deleteUser')],
            ["permissionName" => 'changeStatus', 'access' => $user->can('userStatus')],
        ];

        return [
            'users' => $userPermissions
            'cients' => $clientPermissions
        ];
    }
}*/

class UserPermissionService
{

    private array $userPermissions = [];
    private array $clientPermissions = [];
    private array $contractPermission = [];
    private array $ticketPermission = [];
    private array $parameterPermission = [];
    private array $contactPermission = [];
    private array $addressPermission = [];
    private array $contractServicePermission = [];
    private array $showTicketClient = [];
    private array $dashboard = [];
    private array $email = [];
    private array $event = [];
    private array $contractEconomica = [];
    private array $lavorazione = [];
    private array $tecnica = [];
    private array $upload = [];
    private array $excelImport = [];
    private array $excelQr = [];




    public function getUserPermissions(Authenticatable $user)
    {
        /*$this->userPermissions = $this->generateResourcePermissions($user, [
            'allUser', 'createUser', 'editUser', 'updateUser', 'deleteUser'
        ]);

        $this->clientPermissions = $this->generateResourcePermissions($user, [
            'allClient', 'createClient', 'editClient', 'updateClient', 'deleteClient'
        ]);

        $this->contractPermission = $this->generateResourcePermissions($user, [
            'allContract', 'createContract', 'editContract', 'updateContract', 'deleteContract'
        ]);

        $this->ticketPermission = $this->generateResourcePermissions($user, [
            'allTicket', 'createTicket', 'editTicket', 'updateTicket', 'deleteTicket'
        ]);

        $this->parameterPermission = $this->generateResourcePermissions($user, [
            'allParameter', 'createParameter', 'editParameter', 'updateParameter', 'deleteParameter'
        ]);

        $this->contactPermission = $this->generateResourcePermissions($user, [
            'allContact', 'createContact', 'editContact', 'updateContact', 'deleteContact'
        ]);

        $this->addressPermission = $this->generateResourcePermissions($user, [
            'allAddress', 'createAddress', 'editAddress', 'updateAddress', 'deleteAddress'
        ]);

        $this->contractServicePermission = $this->generateResourcePermissions($user, [
            'createContractService', 'editContractService', 'updateContractService', 'deleteContractService'
        ]);

        $this->showTicketClient = $this->generateResourcePermissions($user, [
            'showTicketClient'
        ]);

        $this->dashboard = $this->generateResourcePermissions($user, [
            'mainstats'
        ]);

        $this->email = $this->generateResourcePermissions($user, [
            'sendEmail'
        ]);

        $this->event = $this->generateResourcePermissions($user, [
            'allEvents', 'createEvent', 'editEvent', 'updateEvent', 'deleteEvent'
        ]);

        $this->contractEconomica = $this->generateResourcePermissions($user, [
            'allContractPlusData', 'createContractPlusData', 'editContractPlusData', 'updateContractPlusData', 'deleteContractPlusData',
        ]);

        $this->lavorazione = $this->generateResourcePermissions($user, [
            'createLavorazioneMainData', 'editLavorazioneMainData', 'updateLavorazioneMainData', 'createLavorazioneSecOne', 'editLavorazioneSecOne', 'updateLavorazioneSecOne', 'deleteLavorazioneSecOne', 'createLavorazioneSecTwo', 'editLavorazioneSecTwo', 'updateLavorazioneSecTwo', 'deleteLavorazioneSecTwo','createLavorazioneSecThree', 'editLavorazioneSecThree', 'updateLavorazioneSecThree', 'deleteLavorazioneSecThree',
            'allLavorazioneSecFour', 'createLavorazioneSecFour', 'editLavorazioneSecFour', 'updateLavorazioneSecFour', 'deleteLavorazioneSecFour',
        ]);

        $this->tecnica = $this->generateResourcePermissions($user, [
            'createTecnicaMainData', 'editTecnicaMainData', 'updateTecnicaMainData', 'createTecnicaSecOne', 'editTecnicaSecOne', 'updateTecnicaSecOne', 'deleteTecnicaSecOne', 'createTecnicaSecTwo', 'editTecnicaSecTwo', 'updateTecnicaSecTwo', 'deleteTecnicaSecTwo',
        ]);*/
        
        $this->userPermissions = $this->generateResourcePermissions($user, [
            'allUser', 'createUser', 'editUser', 'updateUser', 'deleteUser'
        ]);

        $this->clientPermissions = $this->generateResourcePermissions($user, [
            'allClient', 'createClient', 'editClient', 'updateClient', 'deleteClient'
        ]);

        $this->contractPermission = $this->generateResourcePermissions($user, [
            'allContract', 'createContract', 'editContract', 'updateContract', 'deleteContract'
        ]);

        $this->ticketPermission = $this->generateResourcePermissions($user, [
            'allTicket', 'createTicket', 'editTicket', 'updateTicket', 'deleteTicket'
        ]);

        $this->parameterPermission = $this->generateResourcePermissions($user, [
            'allParameter', 'createParameter', 'editParameter', 'updateParameter', 'deleteParameter'
        ]);

        $this->contactPermission = $this->generateResourcePermissions($user, [
            'allContact', 'createContact', 'editContact', 'updateContact', 'deleteContact'
        ]);

        $this->addressPermission = $this->generateResourcePermissions($user, [
            'allAddress', 'createAddress', 'editAddress', 'updateAddress', 'deleteAddress'
        ]);

        $this->contractServicePermission = $this->generateResourcePermissions($user, [
            'createContractService', 'editContractService', 'updateContractService', 'deleteContractService'
        ]);

        $this->showTicketClient = $this->generateResourcePermissions($user, [
            'showTicketClient'
        ]);

        $this->dashboard = $this->generateResourcePermissions($user, [
            'mainstats'
        ]);

        $this->email = $this->generateResourcePermissions($user, [
            'sendEmail'
        ]);

        $this->event = $this->generateResourcePermissions($user, [
            'allEvents', 'createEvent', 'editEvent', 'updateEvent', 'deleteEvent'
        ]);

        $this->contractEconomica = $this->generateResourcePermissions($user, [
            'allContractPlusData', 'createContractPlusData', 'editContractPlusData', 'updateContractPlusData', 'deleteContractPlusData',
        ]);

        $this->lavorazione = $this->generateResourcePermissions($user, [
            'createLavorazioneMainData', 'editLavorazioneMainData', 'updateLavorazioneMainData', 'createLavorazioneSecOne', 'editLavorazioneSecOne', 'updateLavorazioneSecOne', 'deleteLavorazioneSecOne', 'createLavorazioneSecTwo', 'editLavorazioneSecTwo', 'updateLavorazioneSecTwo', 'deleteLavorazioneSecTwo','createLavorazioneSecThree', 'editLavorazioneSecThree', 'updateLavorazioneSecThree', 'deleteLavorazioneSecThree',
            'allLavorazioneSecFour', 'createLavorazioneSecFour', 'editLavorazioneSecFour', 'updateLavorazioneSecFour', 'deleteLavorazioneSecFour',
        ]);

        $this->tecnica = $this->generateResourcePermissions($user, [
            'createTecnicaMainData', 'editTecnicaMainData', 'updateTecnicaMainData', 'createTecnicaSecOne', 'editTecnicaSecOne', 'updateTecnicaSecOne', 'deleteTecnicaSecOne', 'createTecnicaSecTwo', 'editTecnicaSecTwo', 'updateTecnicaSecTwo', 'deleteTecnicaSecTwo',
        ]);

        $this->upload = $this->generateResourcePermissions($user, [
            'uploadMultipleFiles', 'readFiles', 'writeFiles', 'renameFile', 'deleteFiles',
        ]);


        $this->excelImport = $this->generateResourcePermissions($user, [
            'allImportedData',
        ]);
        
        $this->excelQr = $this->generateResourcePermissions($user, [
            'allQrData',
        ]);


        return [
            'users' => $this->userPermissions,
            'clients' => $this->clientPermissions,
            'contracts' => $this->contractPermission,
            'contractServices' => $this->contractServicePermission,
            'tickets' => $this->ticketPermission,
            'showTicketClient' => $this->showTicketClient,
            'parameters' => $this->parameterPermission,
            'contacts' => $this->contactPermission,
            'addresses' => $this->addressPermission,
            'dashboard' => $this->dashboard,
            'email' => $this->email,
            'events' => $this->event,
            'contractEconomica' => $this->contractEconomica,
            'lavorazione' => $this->lavorazione,
            'tecnica' => $this->tecnica,
            'upload' => $this->upload,
            'excelImport' => $this->excelImport,
            'excelQr' => $this->excelQr,

        ];
    }

    private function generateResourcePermissions(Authenticatable $user, array $permissions)
    {
        return array_map(function ($permission) use ($user) {
            return [
                'permissionName' => $permission,
                'access' => $user->can($permission)
            ];
        }, $permissions);
    }
}
