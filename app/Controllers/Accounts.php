<?php

namespace App\Controllers;
use App\Controllers\BaseController;
use App\Models\AccountsModel;
use CodeIgniter\HTTP\ResponseInterface;

class Accounts extends BaseController{
    public function __construct()
    {
		helper(["url", "form"]);

        $this->accounts = new AccountsModel();
    }
    public function index(){
        return $this->getResponse([
            'message' => 'Managers retrieved successfully',
            'Accounts' => $this->accounts->findAll()
        ]);
    }
    public function getUser($id){
        $inf_user = $this->accounts->where("id", $id)->first();

        return $this->getResponse([
            "user" => $inf_user
        ]);
    }


}
?>