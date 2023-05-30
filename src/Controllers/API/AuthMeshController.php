<?php

namespace Hafael\Mesh\Auth\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthMeshController extends Controller
{

    /**
     * In this method, the client side generates a key on the server side.
     * 
     * Protected by APITokenMiddlerare.
     */
    public function issueToken(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string',
            'abilities' => 'required|array',
            'abilities.*' => 'required|string',
        ]);

        $user = $request->user();

        $token = $user->createToken($request->input('name'), $request->input('abilities'));

        $parts = explode("|", $token->plainTextToken);
        
        return response()->json([
            'id' => $token->accessToken->id,
            'name' => $token->accessToken->name,
            'abilities' => $token->accessToken->abilities,
            'tokenable_id' => $token->accessToken->tokenable_id,
            'tokenable_type' => class_basename($token->accessToken->tokenable_type),
            'access_token' => $parts[1],
            'plain_text_token' => $token->plainTextToken,
            'last_used_at' => $token->accessToken->last_used_at,
        ]);
    }

}
