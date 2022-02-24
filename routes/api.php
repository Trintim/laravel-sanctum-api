<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProductController;
use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
/* Route::resource('products', ProductController::class); */
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register'])->middleware('restrictothers');
Route::get('products', [ProductController::class,'index']);
Route::get('products/{id}', [ProductController::class,'show']);
Route::get('products/search/{name}', [ProductController::class,'search']);
/* Route::get('products/search/{name}', [ProductController::class,'search']); */


/* Route::post('/products', function(){
    return Products::create([
        'name' => 'Produto 1',
        'slug' => 'product-one',
        'description' => 'This is product one',
        'price' => '99.99'
    ]);
}); */

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('products', [ProductController::class,'store']);
    Route::put('products/{id}', [ProductController::class,'update']);
    Route::delete('products/{id}', [ProductController::class,'destroy']);
    Route::post('/logout', [AuthController::class, 'logout']);

    //exemple 
    // list all posts
    Route::get('posts', [PostController::class, 'post']);
    // get a post
    Route::get('posts/{id}', [PostController::class, 'singlePost']);
    // add a new post
    Route::post('posts', [PostController::class, 'createPost']);
    // updating a post
    Route::put('posts/{id}', [PostController::class, 'updatePost']);
    // delete a post
    Route::delete('posts/{id}', [PostController::class, 'deletePost']);
    // add a new user with writer scope
    Route::post('users/sub-admin', [AuthController::class, 'createSubAdmin']);
    // add a new user with subscriber scope
    Route::post('users/associates', [AuthController::class, 'createAssociates']);
    // delete a user
    Route::delete('users/{id}', [AuthController::class, 'deleteUser']);

});
