<?php

namespace App\Http\Resources\Contract;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SingleServiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
                $strPaymentIds = explode('#', $this->payment_id);
        $paymentIds = [];
        foreach ($strPaymentIds as $each_number) {
            $paymentIds[] = (int)$each_number;
        }

        return [
            'contractId' => $this->contract_id,
            'serviceId' => $this->service_id,
            'paymentIds' => $this->payment_id?$paymentIds:[],
            'accountNumber' => $this->account_number?$this->account_number:"",
            'startDate' => $this->start_date?$this->start_date:"",
            'endDate' => $this->end_date?$this->end_date:"",
            'caricoId' => $this->carico_id,
            'note' => $this->note?$this->note:""
        ];

    }
}
