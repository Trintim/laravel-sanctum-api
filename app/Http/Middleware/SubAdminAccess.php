<?php

namespace App\Http\Middleware;

use App\Http\Library\ApiHelpers;
use Closure;
use Illuminate\Http\Request;

class SubAdminAccess
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
        if($this->isSubAdmin($user)){
            //dd($user);
            return $next($request);
        }
        dd('Aceso negado Não é subAdmin!!');
    }
}
