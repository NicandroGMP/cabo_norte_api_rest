<?php

namespace App\Controllers;

use Exception;
use App\Models\ManagersModel;
use App\Models\AccountsModel;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Libraries\Hash;
use App\Libraries\StringMake;

class Guards extends BaseController
{
    
    public function __construct()
    {
        $this->managers = new ManagersModel();
        $this->accounts = new AccountsModel();
    }
    public function index(){
        
        $join = $this->managers->table("managers");
        $join->select('accounts.id,accounts.status, managers.id as guards_id,accounts.username, CONCAT(managers.name," ",managers.lastname)as fullname ,managers.name,managers.lastname, managers.position,managers.company');
        $join->join("accounts", "accounts.user_inf = managers.id")->where("accounts.type_user", "guardia");
        $user_date = $join->get()->getResultArray();

        return $this->getResponse([
            'message' => 'guards retrieved successfully',
            'guards' => $user_date
        ]);
    }
    public function RegisterGuards()
    {
        $rules = [
            "name" => "required",
            "lastname"=> "required",
            "company"=> "required",
            "position"=> "required",
            "username" => "required",
            "typeUser" => "required",
            "password"=> "required|min_length[5]|max_length[12]|",
        ];

        $input = $this->getRequestInput($this->request);

        if (!$this->validateRequest($input, $rules)) {
            return $this->getResponse($this->validator->getErrors(), ResponseInterface::HTTP_BAD_REQUEST);
        }else{
            $number_guard=StringMake::manager_number() ;
            $dataForm1 = [
                "name" => $input["name"],
                "lastname" => $input["lastname"],
                "manager_number" => $number_guard,
                "company" => $input["company"],
                "position" => $input["position"],
                "work" => null,

            ];
            $query = $this->managers->insert($dataForm1);
            $data_user = $this->managers->where("manager_number", $number_guard)->first();
            $newAccount = $data_user["id"];

            $dataForm2 = [
                "user_inf" => $newAccount,
                "username" => $input["username"],
                "type_user" => $input["typeUser"],
                "password" => Hash::make($input["password"]),
                "status" => "Habilitado"
            ];
            $query2 = $this->accounts->insert($dataForm2);
            if (!$query && !$query2){
                return $this->getResponse("", ResponseInterface::HTTP_BAD_REQUEST);
            }else{
                return $this->getResponse([
                    "message" => "El Encargado ". $input["typeUser"]." " .$input["lastname"]." Se Registro Correctamente"
                ]);
            }
        }

    }
    public function updateGuards()
    {
        $rules = [
            "name" => "required",
            "lastname"=> "required",
            "company"=> "required",
            "position"=> "required",
            "username" => "required"
        ];
        $input = $this->getRequestInput($this->request);

        if (!$this->validateRequest($input, $rules)) {
            return $this->getResponse($this->validator->getErrors(), ResponseInterface::HTTP_BAD_REQUEST);
        }else{
            $id_guard = $input["id_guard"];
            $id_account = $input["id"];
            $valuesGuards = [
                "name" => $input["name"],
                "lastname" => $input["lastname"],
                "company" => $input["company"],
                "position" => $input["position"],
		];
        $valuesAccount = ["username"=> $input["username"]];
        $update = $this->managers->update($id_guard, $valuesGuards);
        $update2 = $this->accounts->update($id_account, $valuesAccount);
        if(!$update && !$update2){
            return $this->getResponse("", ResponseInterface::HTTP_BAD_REQUEST);
        }else{
            return $this->getResponse([
                "message" => "datos actualizados"
            ]);
        }
        }
    }
    public function updateGuardPass()
    {
        $rules = [
            "newPassword"=> "required|min_length[5]|max_length[12]|",
        ];
        $input = $this->getRequestInput($this->request);

        if (!$this->validateRequest($input, $rules)) {
            return $this->getResponse($this->validator->getErrors(), ResponseInterface::HTTP_BAD_REQUEST);
        }else{
            $id = $input["id"];
            $valuesManagers = [
                "password" => Hash::make($input["newPassword"]),
		];
        $update = $this->accounts->update($id, $valuesManagers);
        if(!$update){
            return $this->getResponse("", ResponseInterface::HTTP_BAD_REQUEST);
        }else{
            return $this->getResponse([
                "message" => "Contraseña Actualizada"
            ]);
        }
        }
    }
    public function statusUpdate($id,$data){
        if (empty($id) || empty($data) ){
            return $this->getResponse("", ResponseInterface::HTTP_BAD_REQUEST);
        }else{
            if ($data === "Habilitado"){
                $dataUpdate = ["status" => "Deshabilitado"];
                $update = $this->accounts->update($id,$dataUpdate);
                return $this->getResponse(["message" => "deshabilitado"]);   
            }elseif($data === "Deshabilitado"){
                $dataUpdate = ["status" => "Habilitado"];
                $update = $this->accounts->update($id,$dataUpdate);
                return $this->getResponse(["message" => "habilitado"]);   
            }
            }
        }


    public function deleteGuard(){

    $rules = [
        "id_guards"=> "required",
    ];
    $input = $this->getRequestInput($this->request);
    if (!$this->validateRequest($input, $rules)) {
        return $this->getResponse($this->validator->getErrors(), ResponseInterface::HTTP_BAD_REQUEST);
    }else{
        $id = $input["id_guards"];
        $delete = $this->managers->delete($id);
        if (!$delete){
            return $this->getResponse("Error al eliminar Guardia", ResponseInterface::HTTP_BAD_REQUEST);
        }
        return $this->getResponse(["message" => "El Guardia se ha eliminado"]);
    }
    }
}
