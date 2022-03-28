<?php


namespace App\Http\Controllers;

use App\Wallet;
use App\Http\Resources\User as WalletResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WalletControllerAPI extends Controller
{

    public function index(Request $request)
    {
        return WalletResource::collection(Wallet::all());
    }

    public function numWallets()
    {
        $wallets = Wallet::all()->count();
        return $wallets;
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'balance' => 'required|min:0'
        ]);

        $wallet = $request->all();

        Wallet::create($wallet);
    }

    public function getWalletsEmail($search)
    {
        $email = Wallet::where('email', 'like', '%' . $search . '%')->take(5)->get('email')->map(function ($item, $key) {
            return $item->email;
        });

        return $email;
    }


    public static function getWallet($email)
    {
        $wallet = DB::table('wallets')->where('email', '=', $email)->get();
        return $wallet;
    }
}
