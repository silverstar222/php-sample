<?php

namespace App\Modules\Auth\Controllers;

use App\Http\Controllers\Controller;;
use App\Http\Resources\UsersResource;
use App\Models\Mailer\Mailer;
use App\Models\Mailer\SimpleMail;
use App\Models\Users\Users;
use App\Models\JWT\JWT;
use App\Models\Users\UsersRefreshPasswords;
use App\Models\Users\UsersRefreshTokens;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    /**
     * @param Request $request
     * @param Users $users
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function signUp(Request $request, Users $users)
    {
        /*
         * Creating New User
         */
        $response = $users->createNew($request->only(['user_name', 'email', 'password']));

        /*
         * Create/Update Refresh Token
         */
        $refreshTokenArray = $this->createOrUpdateRefreshToken($response->users_id, $request);

        /*
         * Jwt Refresh Token Generate
         */
        $jwtToken = JWT::generateToken($response->users_id, $refreshTokenArray['users_refresh_tokens_id']);

        return response()->json([
            'success' => true,
            'data' => new UsersResource($response),
            'jwt_token' => $jwtToken,
            'refresh_token' => $refreshTokenArray['token'],
            'message' => trans('Auth.registeredNewUser')
        ],Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @param Users $users
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function signIn(Request $request, Users $users)
    {
        /*
         * Get User By Email
         */
        $response = $users->getUserByEmail($request->post('email'));

        /*
         * Create/Update Refresh Token
         */
        $refreshTokenArray = $this->createOrUpdateRefreshToken($response->users_id, $request);

        /*
         * Jwt Refresh Token Generate
         */
        $jwtToken = JWT::generateToken($response->users_id, $refreshTokenArray['users_refresh_tokens_id']);

        return response()->json([
            'success' => true,
            'data' => new UsersResource($response),
            'jwt_token' => $jwtToken,
            'refresh_token' => $refreshTokenArray['token'],
            'message' => trans('Auth.loginUser')
        ],Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @param UsersRefreshTokens $usersRefreshTokens
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refreshTokens(Request $request, UsersRefreshTokens $usersRefreshTokens)
    {
        /*
         * Generate Refresh Token
         */
        $refreshToken = $usersRefreshTokens->generateRefreshToken();

        /*
         * Get Refresh Object
         */
        $refreshObj = $usersRefreshTokens->getByRefreshToken($request->post('refresh_token'));

        /*
         * Delete Last Refresh Token
         */
        $usersRefreshTokens->deleteByRefreshToken($request->post('refresh_token'));

        /*
         * Create New Refresh Token
         */
        $usersRefreshTokens->users_id = $refreshObj->users_id;
        $usersRefreshTokens->ip = $request->ip();
        $usersRefreshTokens->token = $refreshToken;
        $usersRefreshTokens->device_token = $request->post('device_token');
        $usersRefreshTokens->user_agent = $request->userAgent();
        $usersRefreshTokens->save();

        /*
         * Jwt Refresh Token Generate
         */
        $jwtToken = JWT::generateToken($refreshObj->users_id, $usersRefreshTokens->users_refresh_tokens_id);

        return response()->json([
            'success' => true,
            'data' => new UsersResource(Users::find($refreshObj->users_id)),
            'jwt_token' => $jwtToken,
            'refresh_token' => $refreshToken,
            'message' => trans('Auth.refreshTokens')
        ],Response::HTTP_OK);
    }

    public function recoveryPassword(Request $request, Users $users, UsersRefreshPasswords $usersRefreshPasswords)
    {
        /*
         * Get User By Email
         */
        $usersObj = $users->getUserByEmail($request->post('email'));

        /*
         * Delete Last Tokens
         */
        $usersRefreshPasswords->deleteAllUsersTokens($usersObj->users_id);

        /*
         * Temporary Token Generate And Save
         */
        $passwordRecoveryToken = $usersRefreshPasswords->generateRefreshToken();
        $usersRefreshPasswords->token = $passwordRecoveryToken;
        $usersRefreshPasswords->users_id = $usersObj->users_id;
        $usersRefreshPasswords->save();

        /*
         * Asset Full Link
         */
        $recoverUrl = asset('/password/recovery/'.$passwordRecoveryToken);

        /*
         * Send Simple Email
         */
        $text = "Перейдите <a href='" .$recoverUrl. "'>по ссылке</a> для восстановления пароля ";
        $mailer = new Mailer(new SimpleMail($request->post('email'),'easy-meals@yobibyte.in.ua','Easy Meals. Recovery Password', $text));
        $mailer->sendEmail();

        return response()->json([
            'success' => true,
            'message' => trans('auth.recoveryPasswordSentToEmail')
        ],Response::HTTP_OK);
    }

    public function recoverPasswordView(Request $request, $token)
    {
        return view('Auth::recoveryPassword', [
            'token' => $token
        ]);
    }

    public function recoverPasswordViewSuccess()
    {
        return view('Auth::recoveryPasswordSuccess');
    }

    /**
     * @param Request $request
     * @param Users $users
     * @param UsersRefreshPasswords $usersRefreshPasswords
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function setRecoveryPassword(Request $request, Users $users ,UsersRefreshPasswords $usersRefreshPasswords)
    {
        /*
         * Get UsersRefreshPasswords Object
         */
        $usersRefreshPasswordsObj = $usersRefreshPasswords->getByToken($request->post('token'));

        /*
         * Delete Last Tokens
         */
        $usersRefreshPasswords->deleteAllUsersTokens($usersRefreshPasswordsObj->users_id);

        /*
         * Update Password
         */
        $usersObj = $users->find($usersRefreshPasswordsObj->users_id);
        $usersObj->password = $request->post('new_password');
        $usersObj->update();

        return response()->json([
            'success' => true,
            'message' => trans('auth.recoveryPasswordSet')
        ],Response::HTTP_OK);
    }

    /**
     * @param int $usersId
     * @param object $request
     *
     * @return array
     */
    private function createOrUpdateRefreshToken($usersId, $request)
    {
        $users_refresh_tokens_id = null;
        $usersRefreshTokens = new UsersRefreshTokens();

        /*
         * Generate Refresh Token
         */
        $refreshToken = $usersRefreshTokens->generateRefreshToken();

        /*
         * Save/update Refresh Token
         */
        if (!$findItem = $usersRefreshTokens->checkExistsDeviceToken($request->post('device_token'))) {
            $usersRefreshTokens->users_id = $usersId;
            $usersRefreshTokens->ip = $request->ip();
            $usersRefreshTokens->token = $refreshToken;
            $usersRefreshTokens->device_token = $request->post('device_token');
            $usersRefreshTokens->user_agent = $request->userAgent();
            $usersRefreshTokens->save();
            $users_refresh_tokens_id = $usersRefreshTokens->users_refresh_tokens_id;
        } else {
            $findItem->token = $refreshToken;
            $findItem->update();
            $users_refresh_tokens_id = $findItem->users_refresh_tokens_id;
        }

        return [
            'token' => $refreshToken,
            'users_refresh_tokens_id' => $users_refresh_tokens_id
        ];
    }
}