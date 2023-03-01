<?php

namespace App\Controllers;

use Exception;
use App\Models\ManagersModel;
use App\Models\AccountsModel;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Libraries\Hash;
use App\Libraries\StringMake;


class Managers extends BaseController
{

    public function __construct()
    {
        $this->managers = new ManagersModel();
        $this->accounts = new AccountsModel();
    }
    public function index()
    {
        $join = $this->managers->table("managers");
        $join->select('accounts.id,accounts.status, managers.id as manager_id,accounts.username, CONCAT(managers.name," ",managers.lastname)as fullname ,managers.name,managers.lastname, managers.position,managers.company, managers.work as work_id, CONCAT(works.job," ", works.batch)as job');
        $join->join("accounts", "accounts.user_inf = managers.id");
        $join->join("works", "managers.work = works.id", "left")->where("accounts.type_user", "encargado");
        $user_date = $join->get()->getResultArray();

        return $this->getResponse([
            'message' => 'Managers retrieved successfully',
            'managers' => $user_date
        ]);
    }
    public function getManagerById($id){
          $join = $this->managers->table("managers");
        $join->select(' managers.id as manager_id, CONCAT(managers.name," ",managers.lastname)as fullname, managers.work as work_id, works.job, works.batch');
        $join->join("accounts", "accounts.user_inf = managers.id");
        $join->join("works", "managers.work = works.id")
        ->where("managers.id", $id);
        $inf_user =  $join->get()->getResultArray();
        if (!$inf_user) {
            return $this->getResponse("La session ha Expirado", ResponseInterface::HTTP_BAD_REQUEST);
        }else{
            return $this->getResponse([
                "manager" => $inf_user
            ]);
        }
       
    }

    public function RegisterManager()
    {
        $rules = [
            "name" => "required",
            "lastname"=> "required",
            "company"=> "required",
            "position"=> "required",
            "work" => "required",
            "username" => "required",
            "typeUser" => "required",
            "password"=> "required|min_length[5]|max_length[12]|",
        ];

        $input = $this->getRequestInput($this->request);

        if (!$this->validateRequest($input, $rules)) {
            return $this->getResponse($this->validator->getErrors(), ResponseInterface::HTTP_BAD_REQUEST);
        }else{
            $number_manger=StringMake::manager_number() ;
            $dataForm1 = [
                "name" => $input["name"],
                "lastname" => $input["lastname"],
                "manager_number" => $number_manger,
                "company" => $input["company"],
                "position" => $input["position"],
                "work" => $input["work"],
                
            ];
            $query = $this->managers->insert($dataForm1);
            $data_user = $this->managers->where("manager_number", $number_manger)->first();
            $newAccount = $data_user["id"];

            $dataForm2 = [
                "user_inf" => $newAccount,
                "username" => $input["username"],
                "type_user" => $input["typeUser"],
                "password" => Hash::make($input["password"]),
                "status" => "Habilitado",

            ];
            $query2 = $this->accounts->insert($dataForm2);
            if (!$query && !$query2){
                return $this->getResponse("", ResponseInterface::HTTP_BAD_REQUEST);
            }else{
                return $this->getResponse([
                    "message" => "El Encargado ". $input["name"]." " .$input["lastname"]." Se Registro Correctamente"
                ]);
            }
        }

    }

    public function updateManager()
    {
        $rules = [
            "name" => "required",
            "lastname"=> "required",
            "company"=> "required",
            "position"=> "required",
            "work" => "required",
        ];
        $input = $this->getRequestInput($this->request);

        if (!$this->validateRequest($input, $rules)) {
            return $this->getResponse($this->validator->getErrors(), ResponseInterface::HTTP_BAD_REQUEST);
        }else{
            $id_manager = $input["id_manager"];
            $id_account = $input["id"];
            $valuesManagers = [
                "name" => $input["name"],
                "lastname" => $input["lastname"],
                "company" => $input["company"],
                "position" => $input["position"],
                "work" => $input["work"],
            ];
            $valuesAccount = [
                "username" => $input["username"],
            ];
        $update = $this->managers->update($id_manager, $valuesManagers);
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
    public function updateManagerPass()
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
    public function deleteManager()
   {
    $rules = [
        "id_manager"=> "required",
    ];
    $input = $this->getRequestInput($this->request);
    if (!$this->validateRequest($input, $rules)) {
        return $this->getResponse($this->validator->getErrors(), ResponseInterface::HTTP_BAD_REQUEST);
    }else{
        $id = $input["id_manager"];
        $delete = $this->managers->delete($id);
        if (!$delete){
            return $this->getResponse("Error al eliminar encargado", ResponseInterface::HTTP_BAD_REQUEST);
        }
        return $this->getResponse(["message" => "El encargado se ha eliminado"]);
    }
    }
    
}