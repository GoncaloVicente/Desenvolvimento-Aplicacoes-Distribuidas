<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class Wallet extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'balance' => $this->balance,
        ];
    }
}
