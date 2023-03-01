<?php

namespace App\Controllers;

use Exception;
use App\Models\ManagersModel;
use App\Models\AccountsModel;
use App\Models\BitacoraWorkersModel;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use DateTime;

class BitacoraWorkers extends BaseController
{
    
    public function __construct()
    {
        $this->managers = new ManagersModel();
        $this->accounts = new AccountsModel();
        $this->BitacoraWorkers = new BitacoraWorkersModel();
    }
    public function index($date){

        //Fuction Bitacora function to filter by date
        $bitacora = $this->BitacoraWorkers->table("bitacora_workers")->like("entry_worker", $date);
        $bitacora = $bitacora->get()->getResultArray();
        //end fuction Bitacora function to filter by date

        //Fuction statics dont exit worker
        $count=$this->BitacoraWorkers->select("COUNT(bitacora_workers.id) as dontExit");
        $count->where("bitacora_workers.exit_worker is null")->like('bitacora_workers.entry_worker', $date,'both');
        $isnull = $count->get()->getResultArray();
        $isnull =$isnull[0];
        //end Fuction statics dont exit worker

        //Fuction statics entry worker
        $total_workers = count( $bitacora);
        $success_entry = $total_workers - intval($isnull["dontExit"]);
        //end Fuction statics entry worker

        return $this->getResponse([
            'message' => 'Bitacora retrieved successfully',
            'bitacora' => $bitacora,
            'dontExit' => $isnull,
            'total_worker' => $total_workers,
            'success_entry' =>  $success_entry 
        ]);
    }
    public function registerWorker(){
        $rules = [
            "name" => "required",
            "lastname"=> "required",
            "company"=> "required",
            "position"=> "required",
            "work" => "required",
            "manager" => "required"
        ];

        $input = $this->getRequestInput($this->request);

        if (!$this->validateRequest($input, $rules)) {
            return $this->getResponse($this->validator->getErrors(), ResponseInterface::HTTP_BAD_REQUEST);
        }else{
            date_default_timezone_set('America/Merida');   
            $entry_worker =  date("Y-m-d H:i:s");
            $data = [
                "register_number" => $input["register_number"],
                "fullname" => $input["name"]." ".$input["lastname"],
                "company" => $input["company"],
                "position" => $input["position"],
                "work" => $input["work"],
                "manager" => $input["manager"],
                "entry_worker" => $entry_worker,
                "exit_worker" => null,

            ];
            $query = $this->BitacoraWorkers->insert($data);
            if (!$query){
                return $this->getResponse("", ResponseInterface::HTTP_BAD_REQUEST);
            }else{
                return $this->getResponse([
                    "message" => "El Trabajador ". $input["name"]." " .$input["lastname"]." Se Registro Correctamente"
                ]);
            }
        }
    }
    public function registerExitWorker(){
        $rules = [
            "id" => "required",
        ];

        $input = $this->getRequestInput($this->request);

        if (!$this->validateRequest($input, $rules)) {
            return $this->getResponse($this->validator->getErrors(), ResponseInterface::HTTP_BAD_REQUEST);
        }else{
            date_default_timezone_set('America/Merida');   
            $exit_worker =  date("Y-m-d H:i:s");
            $data = [
                "exit_worker" =>  $exit_worker ,

            ];
            $query = $this->BitacoraWorkers->update($input["id"],$data);
            if (!$query){
                return $this->getResponse("", ResponseInterface::HTTP_BAD_REQUEST);
            }else{
                return $this->getResponse([
                    "message" => "se registro la Salida Correctamente"
                ]);
            }
        }
    }
    
    public function bitacoraWorkerSearch(){
        date_default_timezone_set('America/Merida');   
        $entry_worker =  date("Y-m-d");
        $input = $this->getRequestInput($this->request);
        $search = $input["search"];
        $result = $this->BitacoraWorkers->where("register_number",$search)->where("exit_worker is null")->like('entry_worker', $entry_worker,'both');
        $result = $result->get()->getResultArray();
        return $this->getResponse([
            "worker" => $result
        ]);
    }
    public function bitacoraWorkerScan($search){
        date_default_timezone_set('America/Merida');   
        $entry_worker =  date("Y-m-d");
        $result = $this->BitacoraWorkers->where("register_number",$search)->like('entry_worker', $entry_worker,'both');
        $result = $result->get()->getResultArray();
        return $this->getResponse([
            "worker" => $result
        ]);
    }
}
