<?php

namespace App\Http\Middleware;

use App\Http\Library\ApiHelpers;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminAccess
{
    use ApiHelpers;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        //dd($user);
        if($this->isAdmin($user)){
            //dd($user);
            return $next($request);
        }
        dd('Aceso negado você não e um Admin!!');
    }
}
