<?php

namespace App\Modules\Auth\Middleware;

use App\Exceptions\AuthException;
use Closure;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class RecoverPasswordView
{
    private $errors;

    /**
     * @param $request
     * @param Closure $next
     *
     * @return \Illuminate\Http\JsonResponse|mixed
     * @throws Exception
     */
    public function handle($request, Closure $next)
    {
        try {
            $token = $request->token;
           $this->checkInputData(['token'=>$token]);

            return $next($request);
        } catch (AuthException $e) {
            return view('Auth::recoveryPasswordInvalid');
        }
    }

    private function checkInputData($data)
    {
        $validator = Validator::make($data, [
            'token' => 'required|string|exists:users_refresh_passwords,token',
        ],
        [
            'token.exists' => 'Token не найден.'
        ]
        );
        if ($validator->fails()) {
            foreach ($validator->errors()->toArray() as $key => $error) {
                $this->errors[] = (object)[
                    'field' => $key,
                    'type_of_validation' => key($validator->failed()[$key]),
                    'message' => implode(' ', $error)
                ];
            }

            throw new AuthException($validator->errors()->first(),Response::HTTP_BAD_REQUEST);
        }
    }

}