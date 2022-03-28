<?php


namespace App\Http\Controllers;

use App\Http\Resources\Wallet;
use App\User;
use App\Http\Controllers\Controller;
use App\UserList;
use Illuminate\Http\Request;
use App\Http\Resources\User as UserResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class UserControllerAPI extends Controller
{
    public function index()
    {

        $name = request('name');
        $email = request('email');
        $balance = request('balance');
        $type = request('type');
        $status = request('status');

        $query = User::query();

        if (isset($name)) $query->where('name', 'like', '%' . $name . '%');
        if (isset($email)) $query->where('email', 'like', '%' . $email . '%');
        if (isset($type))
            $query->where('type', '=', $type);
        if (isset($status))
            $query->where('active', '=', $status);

        if (isset($balance)) {
            $query = $query->whereHas('balance', function (Builder $q) use ($balance) {
                if ($balance == 'Empty')
                    $q->where('balance', '=', '0');
                if ($balance == 'Has Money')
                    $q->where('balance', '>', '0');
            });
        }

        $totalUsers = $query->get()->count();

        $users = $query->paginate(15);

        $returnData = new \stdClass();
        $returnData->data = UserResource::collection($users);
        $returnData->total = $totalUsers;
        return response()->json($returnData);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|regex:/^[A-Za-záàâãéèêíóôõúçÁÀÂÃÉÈÍÓÔÕÚÇ ]+$/',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:3',
            'nif' => 'required|integer|digits:9|unique:users,nif'
        ]);

        $user = $request->all();
        dd($user);
        $user['password'] = password_hash($user['password'], PASSWORD_DEFAULT);

        if (!empty($user['photo'])) {
            $request->validate([
                'photo' => 'mimetypes:image/jpeg,image/png,image/jpg'
            ]);
        }

        $newUser = User::create($user);

        if (!empty($user['photo'])) {
            $fileName = $newUser->id . '_' . time() . '.' . $user['photo']->getClientOriginalExtension();
            $path = $user['photo']->storeAs('/public/fotos', $fileName);
            //Pasta public ou database
            $user = User::find($newUser->id);
            $user->photo = $fileName;
            $user->save();
        }

        return $newUser;
    }

    public function createUser(Request $request)
    {
        $request->validate([
            'name' => 'required|regex:/^[A-Za-záàâãéèêíóôõúçÁÀÂÃÉÈÍÓÔÕÚÇ ]+$/',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:3',
            'type' => 'required'
        ]);

        $user = $request->all();
        $user['password'] = password_hash($user['password'], PASSWORD_DEFAULT);

        if (!empty($user['photo'])) {
            $request->validate([
                'photo' => 'mimetypes:image/jpeg,image/png,image/jpg'
            ]);
        }

        $newUser = User::create($user);

        if (!empty($user['photo'])) {
            $fileName = $newUser->id . '_' . time() . '.' . $user['photo']->getClientOriginalExtension();
            $path = $user['photo']->storeAs('/public/fotos', $fileName);
            //Pasta public ou database
            $user = User::find($newUser->id);
            $user->photo = $fileName;
            $user->save();
        }

        return $newUser;


//        User::create($user);
    }

    public function myProfile(Request $request)
    {
        return new UserResource($request->user());
    }

    public function activeUser($id)
    {
        $user = User::find($id);
        if ($user->active == 0) {
            $user->active = 1;
        }
        $user->save();
        return $user;
    }

    public function disableUser($id)
    {
        $user = User::find($id);
        $balance = \App\Wallet::where('id','=',$id)->select('balance')->get();

        if($balance[0]->balance != 0){
            return response()->json(null, 203);
        }

        if ($user->active == 1) {
            $user->active = 0;
        }
        $user->save();
        return $user;
    }

    public function destroy($id)
    {
        $id=auth()->guard('api')->user()->id;

        $user = User::findOrFail($id);

        if($user->id != $id){
            $user->delete();
            return response()->json(null, 204);
        }

        return response()->json(null, 203);
    }

    public function getUsersPerType()
    {
        $usersType = DB::table('users')->selectRaw("count(type) as Total")->groupBy('type')->get();
        return $usersType;
    }

    public function update(Request $request, $id) {
        $v = Validator::make($request->all(), [
            'name' => 'required',
            'nif' => 'nullable|min:9|max:9',
            // 'photo' => 'nullable|mimetypes:image/jpg',
            'newPassword' => 'nullable|min:3',
            'oldPassword' => 'nullable'
        ]);

        if($v->fails()) {
            return response($v->errors(), 422);
        }

        $user = User::findOrFail($id);
        $user->name = $request->name;


        if($request->newPassword != null) {
            if(Hash::check($request->oldPassword, $user->password)) {
                $user->password = Hash::make($request->newPassword);
            }
            return response()->json(['errors'=>array("oldPassword" => "Old Password does not match with the Current Password")], 422);
        }

        if($user->type === 'u') {
            $user->nif = $request->nif;
        }

        /*
        if (!empty($request->photo)) {
            $fileName = $id . '_' . time() . '.' . $request->photo->getClientOriginalExtension();
            $path = $request->photo->storeAs('/public/fotos', $fileName);
            //Pasta public ou database
            $user->photo = $fileName;
            $user->save();
        }
        */

        $user->save();
        return (new UserResource($user))->response()->setStatusCode(200);
    }
}
