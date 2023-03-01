<?php

namespace App\Validation;
use App\Models\AccountsModel;
use Exception;
class AccountRules
{
    public function validateAccount(string $str, string $fields, array $data): bool
    {
        try {
            $model = new AccountsModel();
            $user = $model->findUserByUsername($data['username']);
            return password_verify($data['password'], $user['password']);
        } catch (Exception $e) {
            return false;
        }
    }
}
