<?php

namespace Hafael\Mesh\Auth\Traits;

trait WithMeshAuth
{
    public function appTokens()
    {
        return $this->hasMany(config('auth-mesh.model'));
    }

    /**
     * Store a generated personal access token for the user.
     *
     * @param  string  $plainTextToken
     * @param  string  $name
     * @param  array  $abilities
     * @return \App\Models\AppAccessToken
     */
    public function savePlainTextToken($plainTextToken, string $name, array $abilities = ['*'])
    {
        $token = $this->appTokens()->create([
            'name' => $name,
            'token' => $plainTextToken,
            'abilities' => $abilities,
        ]);

        return $token;
    }
}