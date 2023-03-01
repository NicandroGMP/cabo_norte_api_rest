<?php

use Config\Services;
use App\Models\AccountsModel;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function getJWTFromRequest($authenticationHeader): string
{
    if (is_null($authenticationHeader)) {
        throw new Exception('Missing or invalid JWT in request');
    }

    return explode(' ', $authenticationHeader)[1];
}

function validateJWTFromRequest(string $encodedToken)
{
    $key = Services::getSecretKey();
    $decodedToken = JWT::decode($encodedToken, new Key($key, 'HS256'));
    $AccountsModel = new AccountsModel();
    $AccountsModel->findUserByUsername($decodedToken->username);
}

function getSignedJWTForUser(string $username): string
{
    $issuedAtTime = time();
    $tokenTimeToLive = getenv('JWT_TIME_TO_LIVE');
    $tokenExpiration = $issuedAtTime + $tokenTimeToLive;
    $payload = [
        'username' => $username,
        'iat' => $issuedAtTime,
        'exp' => $tokenExpiration
    ];

    $jwt = JWT::encode($payload, Services::getSecretKey(), 'HS256');

    return $jwt;
}