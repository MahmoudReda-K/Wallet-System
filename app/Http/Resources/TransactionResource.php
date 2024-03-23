<?php

namespace App\Http\Resources;

use App\Constants\TransactionStatus;
use App\Constants\TransactionTypes;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'amount' => $this->amount,
            'transaction_fee' => $this->transaction_fee,
            'type' => TransactionTypes::getLabel($this->type),
            'description' => $this->description,
            'status' => TransactionStatus::getLabel($this->status),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
