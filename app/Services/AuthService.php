<?php

namespace App\Services;

use Laravel\Passport\Passport;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\User;

class AuthService extends BaseService
{
    protected $model;

    public function __construct(User $user)
    {
        $this->model = $user;
    }

    public function login(Request $request)
    {
        // Set login method to either phone, email or name.
        if (filter_var($request->credential, FILTER_VALIDATE_EMAIL)) $loginMethod = 'email';
        elseif (filter_var(is_numeric($request->credential))) $loginMethod = 'phone';
        else $loginMethod = 'name';

        $loginCredential = [
            $loginMethod => $request->credential,
            "password" => $request->password
        ];

        if (!auth()->attempt($loginCredential)) abort(401, __('fail.invalid_credential'));

        return $this->getPersonalAccessToken();
    }

    public function getPersonalAccessToken()
    {
        if (request()->remember_me === 'true')
            Passport::personalAccessTokensExpireIn(now()->addDays(15));

        return auth()->user()->createToken('Personal Access Token');
    }

    public function logout()
    {
        return auth()->user()->token()->revoke();
    }

    public function register(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = $this->model->newInstance();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = \bcrypt($request->password);
            $user->save();

            if ($request->image_profile)
                $this->addFileToModel($request->image_profile, $user, $user->image_profile_collection_name);

            DB::commit();

            return $user;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function update(Request $request, User $user)
    {
        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        return $user;
    }

    public function me()
    {
        return \auth()->user();
    }
}
