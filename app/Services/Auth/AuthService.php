<?php

namespace App\Services\Auth;

use Laravel\Passport\Passport;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Services\Post\PostService;
use App\Services\Personal\PersonalService;
use App\Services\Company\CompanyService;
use App\Services\BaseService;
use App\Services\Auth\Validations\UserValidate;
use App\Models\User;
use App\Models\Company;

class AuthService extends BaseService
{
    use UserValidate;

    protected $model;
    protected static $COMMON_RELATIONSHIP = ['roles', 'company'];

    public function __construct(User $user)
    {
        $this->model = $user;
    }

    /**
     * Login
     * 
     * @param Request $request
     * @return function getPersonalAccessToken()
     */
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

    /**
     * Get Personal AccessToken
     * 
     * @return function auth()->user()->createToken()
     */
    public function getPersonalAccessToken()
    {
        if (request()->remember_me === 'true')
            Passport::personalAccessTokensExpireIn(now()->addDays(15));

        return auth()->user()->createToken('Personal Access Token');
    }

    /**
     * Destroy Token
     * 
     * @return function auth()->user()->token()->revoke()
     */
    public function logout()
    {
        return auth()->user()->token()->revoke();
    }

    /**
     * Register
     * 
     * @param Request $request
     * @param Boolean $withRelation = true
     * 
     * @return User
     */
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

    /**
     * Get Auth User
     * 
     * @return User
     */
    public function me()
    {
        return $this->getByModel(\auth()->user());
    }

    /**
     * Register For Company
     * 
     * @param Request $request
     * 
     * @return User
     */
    public function registerCompany(Request $request)
    {
        DB::beginTransaction();

        try {
            $companyService = \resolve(CompanyService::class);
            $company = $companyService->create($request);

            $request->merge(['role' => 'admin', 'company_id' => $company->id]);
            $user = $this->register($request, \false);

            $postService = \resolve(PostService::class);

            $postService->create(new Request([
                "type" => "company_content",
                "title" => 'default',
                "body" => "default",
                "created_by" => $user->id,
                "is_published" => \true
            ]));

            DB::commit();

            return $this->getByModel($user);;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    /**
     * Register For App
     * 
     * @param Request $request
     * 
     * @return User
     */
    public function registerApp(Request $request)
    {
        $existCompany = Company::where('id', $request->company_id)->where('joinable_code', $request->joinable_code)->first();
        if (!$existCompany) \abort(400, __('fail.invalid_jonable_code'));

        DB::beginTransaction();

        try {
            $request->merge(['role' => 'employee']);
            $user = $this->register($request, \false);

            $personalService = \resolve(PersonalService::class);
            $personalService->create(new Request([
                'user_id' => $user->id,
                "name" => $user->name ?: $user->email
            ]));

            DB::commit();

            return $this->getByModel($user);;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    /**
     * Get User With Common Relationship
     * 
     * @param User $user
     * 
     * @return User
     */
    public function getByModel(User $user)
    {
        return $user->load(self::$COMMON_RELATIONSHIP);
    }
}
