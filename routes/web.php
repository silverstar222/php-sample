<?php

    //Migrate
    Route::get('/migrate',function (){
        \Illuminate\Support\Facades\Artisan::call('migrate');
        return response()->json([
            'success'=>true,
            'message'=>'success migrate'
        ],200);
    });

    use App\Models\JWT\JWT;
    Route::get('/', function () {
        return view('welcome');
    });

    Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');
