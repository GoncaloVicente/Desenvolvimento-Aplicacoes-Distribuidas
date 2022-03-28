<?php

namespace App\Http\Resources;

use App\Category;
use App\Wallet;
use Illuminate\Http\Resources\Json\Resource;

class Movement extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $movement = [
            'id' => $this->id,
            'wallet_id' => $this->wallet_id,
            'type' => $this->type,
            'transfer' => $this->transfer,
            'transfer_email' => $this->transfer_wallet_id,
            'type_payment' => $this->type_payment,
            'category' => $this->category_id,
            'date' => $this->date,
            'start_balance' => $this->start_balance,
            'end_balance' => $this->end_balance,
            'value' => $this->value,
            'email' => $this->wallet->email,
            'description' => $this->description,
            'source_description' => $this->source_description,
            'iban' => $this->iban,
            'mb_entity_code' => $this->mb_entity_code,
            'mb_payment_reference' => $this->mb_payment_reference,

        ];

        $category = Category::find($movement['category']);
        $movement['category'] = $category['name'];

        $wallet = Wallet::find($movement['transfer_email']);
        $movement['transfer_email'] = $wallet['email'];

        return $movement;
    }
}
