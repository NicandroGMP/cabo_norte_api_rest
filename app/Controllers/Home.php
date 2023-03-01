<?php
namespace App\Controllers;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Home extends BaseController
{
    public function index(){
        return $this->getResponse([
            "status" => "ok"
        ], ResponseInterface::HTTP_CREATED);
    }
}
