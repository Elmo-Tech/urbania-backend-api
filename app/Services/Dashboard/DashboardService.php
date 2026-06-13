<?php

namespace App\Services\Dashboard;

use App\Models\Client;
use App\Models\User;
use App\Models\Contract;
use App\Models\Ticket;
use App\Services\Client\ClientService;
use App\Services\User\UserService;
use App\Services\Ticket\TicketService;
use App\Services\Contract\ContractService;

class DashboardService{


    private function countUsers(){

        $users = User::where()->get();

        return $users->count;
    
    }

    private function countClients(){

        $clients = Client::count();

        return $clients;
    
    }


    private function countContracts(){


        $contracts = Contract::count();

        return $contracts;
    
    }

    private function countTickets(){


        $tickets = Ticket::count();

        return $tickets;
    
    }
    
    private function countOpenedTickets(){


        $tickets = Ticket::where('status', '0', )
                            ->orWhere('status', '1')
                            ->count();

        return $tickets;
    
    }
    
    private function countSuspendedTickets(){


        $tickets = Ticket::where('status', 3)->count();

        return $tickets;
    
    }



    public function getMainStats(Object $request){
        
        $allStatsBoxes = [
            'clientsCount' => $this->countClients(),
            'contractsCount' => $this->countContracts(),
            'ticketsCount' => $this->countTickets(),
            'openedTicketCount' => $this->countOpenedTickets(),
            'suspendedTicketCount' => $this->countSuspendedTickets()
        ];

        return response()->json($allStatsBoxes, 200); 
    }        

}