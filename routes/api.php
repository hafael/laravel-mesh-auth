<?php

use Hafael\Mesh\Auth\Controllers\API\AuthMeshController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'api',
    'as' => 'api.',
    'middleware' => ['api', 'auth.mesh'],
], function(){
    Route::post('/auth/token', [AuthMeshController::class, 'issueToken'])->name('tokens.issue');
});