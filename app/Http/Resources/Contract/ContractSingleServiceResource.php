<?php

namespace App\Http\Resources\Contract;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContractSingleServiceResource extends JsonResource
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
            "contractServiceId" => $this->id,
            'contractId' => $this->contract_id,
            'serviceId' => $this->service_id,
            'paymentIds' => $this->payment_id?$paymentIds:[],
            'accountNumber' => $this->account_number?$this->account_number:"",
            'startDate' => $this->start_date?$this->start_date:"",
            'endDate' => $this->end_date?$this->end_date:"",
            'note' => $this->note?$this->note:"",
            'caricoId' => $this->carico_id?$this->carico_id:"",
            'lavorazioneMainDataId' => $this->lavorazioneMainData?$this->lavorazioneMainData->id:"",
            'tecnicaMainDataId' => $this->tecnicaMainData?$this->tecnicaMainData->id:"",
        ];
    }
}
