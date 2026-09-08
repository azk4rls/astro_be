<?php

use Illuminate\Support\Facades\Route;


Route::get('/test', function () {
    return response()->json([
        'success'=>true,
        'message'=>'Astronova API Ready',
        'data'=>null
    ]);
});