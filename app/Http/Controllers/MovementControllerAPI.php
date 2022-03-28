<?php

namespace App\Http\Controllers;

use App\Category;
use App\Movement;
use App\Wallet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use mysql_xdevapi\Exception;
use App\Http\Resources\Movement as MovementResource;


class MovementControllerAPI extends Controller
{
    public function storeExpense(Request $request)
    {
        $request->validate([
            'type_movement' => 'required',
            'value' => 'required|between:0.01,5000.00',
            'category_id' => 'required|exists:categories,id'
        ]);

        $movement = $request->all();
        if (!empty($movement['email']) && $movement['type_movement']=='t') {
            $request->validate([
                'email' => 'required|email|exists:wallets,email',
            ]);
        }

        if (!empty($movement['type_payment'])) {
            $request->validate([
                'type_payment' => 'required',
            ]);
        }

        if($movement['type_movement']!='t'){
            if (!empty($movement['iban']) && $movement['type_payment']=='bt') {
                $request->validate([
                    'iban' => 'required|regex:/^[A-Z]{2}[0-9]{23}$/',
                ]);
            }

            if (!empty($movement['mb_entity_code']) && $movement['type_payment']=='mb') {
                $request->validate([
                    'mb_entity_code' => 'required|digits:5',
                ]);
            }

            if (!empty($movement['mb_payment_reference']) && $movement['type_payment']=='mb') {
                $request->validate([
                    'mb_payment_reference' => 'required|digits:9',
                ]);
            }
        }

        $movement['wallet_id']=auth()->guard('api')->user()->id;

        if($movement['type_movement']=='t'){

            $movement['transfer']=1;
            $movement['transfer_wallet_id'] = WalletControllerAPI::getWallet($movement['email'])[0]->id;
            $movement['type_payment'] = null;
            $movement['iban'] = null;
            $movement['mb_entity_code'] = null;
            $movement['mb_payment_reference'] = null;

            $mirrorMovement = array();
            $mirrorMovement['wallet_id'] = $movement['transfer_wallet_id'];
            $mirrorMovement['type'] = 'i';
            $mirrorMovement['transfer'] = 1;
            $mirrorMovement['transfer_wallet_id'] = $movement['wallet_id'];
            $mirrorMovement['value'] = $movement['value'];

        }else{
            $movement['transfer']=0;
            $movement['transfer_wallet_id'] = null;
            $movement['transfer_movement_id'] = null;
            $movement['source_description'] = null;

            if($movement['type_payment']=='bt'){

                $movement['mb_entity_code'] = null;
                $movement['mb_payment_reference'] = null;

            }else{

                $movement['iban'] = null;

            }
        }

        $movement['type']='e';
        $movement['date']=Carbon::now()->toDateTimeString();
        DB::beginTransaction(); //Se algum falhar, não executa
        try{
            $wallet = Wallet::find($movement['wallet_id']);
            $wallet->updated_at=Carbon::now()->toDateTimeString();
            $movement['end_balance']=intval(-$movement['value'])+$wallet->balance;
            $wallet->balance=$movement['end_balance'];

            $newMovement = Movement::create($movement);

            if($movement['type_movement']=='t'){

                $mirrorMovement['date'] = $movement['date'];
                $mirrorMovement['transfer_movement_id'] = $newMovement->id;

                $mirrorWallet = Wallet::find($mirrorMovement['wallet_id']);
                $mirrorMovement['start_balance'] = $mirrorWallet->balance;
                $mirrorWallet->updated_at=Carbon::now()->toDateTimeString();
                $mirrorMovement['end_balance']=intval($mirrorMovement['value'])+$mirrorWallet->balance;
                $mirrorWallet->balance = $mirrorMovement['end_balance'];

                $newMirrorMovement = Movement::create($mirrorMovement);
                $newMovement->transfer_movement_id = $newMirrorMovement->id;
                $newMovement->save();

                $mirrorWallet->save();

            }

            $wallet->save();
            DB::commit();
        }catch (Exception $e){
            Log::error($e);
            DB::rollBack();
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:wallets,email',
            'value' => 'required|between:0.01,5000.00',
            'type_payment' => 'required'
        ]);

        $movement = $request->all();

        if ($movement['type_payment'] == 'c') {
            $movement['iban'] = null;
        }

        if (!empty($movement['iban'])) {
            $request->validate([
                'iban' => 'required|regex:/^[A-Z]{2}[0-9]{23}$/',
            ]);
        }

        $movement['type']='i';

        $movement['transfer']=0;
        $movement['date']=Carbon::now()->toDateTimeString();
        DB::beginTransaction(); //Se algum falhar, não executa
        try{
            $wallet = Wallet::find($movement['wallet_id']);
            $wallet->updated_at=Carbon::now()->toDateTimeString();
            $movement['end_balance']=intval($movement['value'])+$wallet->balance;
            $wallet->balance=$movement['end_balance'];

            Movement::create($movement);
            $wallet->save();
            DB::commit();
        }catch (Exception $e){
            Log::error($e);
            DB::rollBack();
        }
    }

    public function getCategories()
    {
        $categories = Category::all();

        return $categories;
    }

    public function getMovementsCategory($search)
    {
        $categories = Category::where('name', 'like', '%' . $search . '%')->take(5)->select(array('id', 'name'))->get()->map(function ($item) {
            return [$item->id,$item->name];
        });
        return $categories;
    }

    public function getMyMovements()
    {
        $id=auth()->guard('api')->user()->id;

        $id_movement = request('id');
        $type = request('type');
        $transfer_email = request('transfer_email');
        $type_payment = request('type_payment');
        $category = request('category');
        $inicial_date = request('dateInicial');
        $final_date = request('dateFinal');

        $query = Movement::query();
        $query->where('wallet_id',$id);


        if (isset($id_movement)) {
            $query->where('id', 'like', '%' . $id_movement . '%');
        }

        if (!isset($inicial_date) && isset($final_date)) {
            $query->where('date',  '<=' ,$final_date );
        }

        if (isset($inicial_date) && !isset($final_date)) {
            $query->where('date',  '>=' ,$inicial_date );
        }

        if (isset($inicial_date) && isset($final_date)) {
            $query->whereBetween('date',[$inicial_date, $final_date ]);
        }

        if (isset($type)){
            $query->where('type', '=', $type);
        }

        if (isset($transfer_email)) {
            $query->leftjoin('wallets','wallets.id','transfer_wallet_id')->where('wallets.email', 'like', '%' . $transfer_email . '%');
        }

        if (isset($type_payment)){
            $query->where('type_payment', '=', $type_payment);
        }

        if (isset($category)) {
            $query->leftjoin('categories','categories.id','category_id')->where('categories.name', 'like', '%' . $category . '%');
        }

        $movements = $query->count();

        $movementsPerPage = $query->orderBy('date', 'desc')->paginate(15);

        $returnData = new \stdClass();
        $returnData->data = MovementResource::collection($movementsPerPage);
        $returnData->total = $movements;
        return response()->json($returnData);
    }

    public function getStaticsByCategory()
    {
        $id=auth()->guard('api')->user()->id;

        $movements = DB::table('movements')->leftJoin('categories', 'movements.category_id','=','categories.id')->
            where('wallet_id',$id)->selectRaw("ifnull(categories.name,'others') as name, sum(movements.value) as Total")
            ->groupBy('categories.name')->get();

        return $movements;
    }

    public function getAllStaticsByCategory()
    {
        $movements = DB::table('movements')->leftJoin('categories', 'movements.category_id','=','categories.id')->
        selectRaw("ifnull(categories.name,'others') as name, sum(movements.value) as Total")
            ->groupBy('categories.name')->get();

        return $movements;
    }

    public function getStaticByType()
    {
        $id=auth()->guard('api')->user()->id;

        $type = DB::table('movements')->selectRaw("type, count(type) as Total")->groupBy('type')->where('wallet_id',$id)->get();
        return $type;
    }

    public function getAllStaticByType()
    {
        $type = DB::table('movements')->selectRaw("type, count(type) as Total")->groupBy('type')->get();
        return $type;
    }

    public function getMoneyForMonth()
    {
        $moneyForMonth = DB::table('movements')->selectRaw("month(date), sum(value) as Total")->groupBy('month(date)')->get();
        return $moneyForMonth;
    }

    public function getMoneyForYear()
    {
        $moneyForYear = DB::table('movements')->selectRaw("year(date), sum(value) as Total")->groupBy('year(date)')->get();
        return $moneyForYear;
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'category' => 'required',
            'description' => 'nullable'
        ]);

        $movement = Movement::findOrFail($id);

        $category = Category::where('name', 'like', $request->category)->select('id')->get();

        $movement->category = $category[0]->id;
        $movement->description = $request->description;

        $movement->save();

        return new MovementResource($movement);
    }

    public function sum()
    {
        $sum = DB::table('movements')->selectRaw("sum(value) as sum")->get();
        return $sum;
    }

}
