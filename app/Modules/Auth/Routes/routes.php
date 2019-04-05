<?php
    Route::group( [ 'namespace' => 'App\Modules\Auth\Controllers',
                    'as' => 'auth.',
                    'https' => true,
    ], function() {
        Route::group(['prefix' => '/api/v1.0/auth'], function (){
            //Auth Sign Up
            Route::post('/sign-up', 'AuthController@signUp')
                ->middleware(\App\Modules\Auth\Middleware\SignUp::class);

            //Auth Sign In
            Route::post('/sign-in', 'AuthController@signIn')
                ->middleware(\App\Modules\Auth\Middleware\SignIn::class);

            //Refresh JWT/Refresh Tokens
            Route::post('/tokens/refresh', 'AuthController@refreshTokens')
                ->middleware(\App\Modules\Auth\Middleware\RefreshTokens::class);

            //Recovery Password
            Route::post('/password/recovery', 'AuthController@recoveryPassword')
                ->middleware(\App\Modules\Auth\Middleware\RecoveryPassword::class);

            //Recovery Password Set
            Route::post('/password/recovery/set', 'AuthController@setRecoveryPassword')
                ->middleware(\App\Modules\Auth\Middleware\RecoveryPasswordSet::class);
        });

        //Recovery Password View
        Route::get('/password/recovery/{token}', 'AuthController@recoverPasswordView')
            ->middleware(\App\Modules\Auth\Middleware\RecoverPasswordView::class);

        //Recovery Successfully
        Route::get('/recovery/success', 'AuthController@recoverPasswordViewSuccess');
    });
