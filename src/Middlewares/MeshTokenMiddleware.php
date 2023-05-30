<?php

namespace Hafael\Mesh\Auth\Middlewares;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MeshTokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {

        $keyParamName = config('auth-mesh.key_names.params.key');
        $secretParamName = config('auth-mesh.key_names.params.secret');

        $keyHeaderName = config('auth-mesh.key_names.headers.key');
        $secretHeaderName = config('auth-mesh.key_names.headers.secret');

        if(!empty($request->input($keyParamName)) && 
           !empty($request->input($secretParamName))) {
            
            return $this->validateCredentials($request, $next, $request->input($keyParamName), $request->input($secretParamName));

        }else if(!empty($request->header($keyHeaderName)) &&
                !empty($request->header($secretHeaderName))) {
            
            return $this->validateCredentials($request, $next, $request->header($keyHeaderName), $request->header($secretHeaderName));

        }

        return $request->expectsJson() ? 
                   response()->json('Unauthorized', 401) :
                   abort(401, 'Unauthorized');
    }


    private function validateCredentials(Request $request, Closure $next, $apiKey, $apiSecret)
    {
        $decoded = explode("|", base64_decode($apiSecret), 2);

        if(count($decoded) == 2 && 
            $apiKey == config('auth-mesh.shared_key') &&
            $decoded[1] == config('auth-mesh.shared_secret'))
        {

            $userClass = config('auth.providers.users.model');

            $user = $userClass::where('id', $decoded[0])->first();

            if(empty($user)) {
                return $request->expectsJson() ? 
                        response()->json('Unauthorized', 401) :
                        abort(401, 'Unauthorized');
            }

            Auth::onceUsingId($user->id);

            return $next($request);
        }
    }
}
