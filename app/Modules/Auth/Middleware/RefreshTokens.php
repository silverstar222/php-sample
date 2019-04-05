<?php

namespace App\Modules\Auth\Middleware;

use App\Exceptions\AuthException;
use Closure;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class RefreshTokens
{
    private $errors;
    private $requestAllData;

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
            $this->checkInputData($request->only('refresh_token','device_token'));

            return $next($request);
        } catch (AuthException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => $this->errors
            ], $e->getCode());
        }
    }

    /**
     * @param $request
     * @param $response
     */
    public function terminate($request, $response)
    {
        if (!$response->original['success'] && env('LOG_WRITE') == true) {
            Log::debug($response->original['message']."\n\r".
                "URL : ".$request->url()."\n\r".
                "IP : ".\Request::ip()."\n\r".
                "User Agent : ".\Request::header('User-Agent')."\n\r".
                "Request : ".json_encode($request->all(),JSON_UNESCAPED_UNICODE)."\n\r".
                "Response : ".json_encode($response->original,JSON_UNESCAPED_UNICODE))."\n\r";
        }
    }

    private function checkInputData($data)
    {
        $statuses = [
            'refresh_token:Required' => Response::HTTP_BAD_REQUEST,
            'refresh_token:String' => Response::HTTP_BAD_REQUEST,
            'refresh_token:Exists' => Response::HTTP_UNAUTHORIZED,
            'device_token:Required' => Response::HTTP_BAD_REQUEST,
            'device_token:String' => Response::HTTP_BAD_REQUEST,
        ];
        $validator = Validator::make($data, [
            'refresh_token' => 'required|string|exists:users_refresh_tokens,token',
            'device_token' => 'required|string',
        ],[
            'refresh_token.exists' => 'Невалидный токен. Переавторизуйтесь.'
        ]);
        if ($validator->fails()) {
            foreach ($validator->errors()->toArray() as $key => $error) {
                $this->errors[] = (object)[
                    'field' => $key,
                    'type_of_validation' => key($validator->failed()[$key]),
                    'message' => implode(' ', $error)
                ];
            }

            throw new AuthException($validator->errors()->first(),$statuses[$this->errors[0]->field.':'.$this->errors[0]->type_of_validation]);
        }
    }

}