<?php


namespace App;

use Illuminate\Database\Eloquent\Model;

class Movement extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'mb_payment_reference','mb_entity_code','transfer_movement_id','transfer_wallet_id','description','type_payment', 'type', 'source_description', 'iban','start_balance','end_balance', 'value','wallet_id','transfer','date','start_balance','end_balance','category_id'
    ];

    public function transferWallet()
    {
        return $this->hasOne(Wallet::class,'id','transfer_wallet_id');

    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class,'id','wallet_id');

    }

    public function category()
    {
        return $this->hasOne(Category::class,'id','category_id');
    }

}
