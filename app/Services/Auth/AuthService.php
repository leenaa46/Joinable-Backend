<?php

namespace App\Services\Auth;

use Laravel\Passport\Passport;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Services\Company\CompanyService;
use App\Services\BaseService;
use App\Services\Auth\Validations\UserValidate;
use App\Models\User;

class AuthService extends BaseService
{
    use UserValidate;

    protected $model;
    protected static $COMMON_RELATIONSHIP = ['roles', 'company'];

    public function __construct(User $user)
    {
        $this->model = $user;
    }

    public function login(Request $request)
    {
        $this->validateLogin($request);

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

    public function register(Request $request, $withRelation = true)
    {
        $this->validateRegister($request);

        DB::beginTransaction();
        try {
            $user = $this->model->newInstance();
            $user->company_id = $request->company_id;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = \bcrypt($request->password);
            $user->save();

            if ($request->role) $user->assignRole($request->role);

            if ($request->image_profile)
                $this->addFileToModel($request->image_profile, $user, $user->image_profile_collection_name);

            DB::commit();

            return $withRelation
                ? $this->getByModel($user)
                : $user;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function update(Request $request, User $user, $withRelation = true)
    {
        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        return $withRelation
            ? $this->getByModel($user)
            : $user;
    }

    public function me()
    {
        return $this->getByModel(\auth()->user());
    }

    public function registerCompany(Request $request)
    {
        DB::beginTransaction();

        try {
            $companyService = \resolve(CompanyService::class);
            $company = $companyService->create($request);

            $request->merge(['role' => 'admin', 'company_id' => $company->id]);
            $user = $this->register($request, \false);

            DB::commit();

            return $this->getByModel($user);;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function getByModel(User $user)
    {
        return $user->load(self::$COMMON_RELATIONSHIP);
    }
}
