<?php

namespace App\Controllers;
use App\Controllers\BaseController;
use App\Libraries\Hash;
use App\Libraries\StringMake;
use App\Models\WorkersModel;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\BitacoraWorkersModel;



class Workers extends BaseController{
    public function __construct()
    {
		helper(["url", "form"]);

        $this->workers = new WorkersModel();
        $this->BitacoraWorkers = new BitacoraWorkersModel();
    }
    public function index(){
        $join = $this->workers->table("workers");
        $join->select('workers.id, workers.register_number,CONCAT(workers.name ," ", workers.lastname) as fullname,workers.name ,workers.lastname,workers.job as job_id , works.job, works.batch, workers.manager, CONCAT(managers.name," ", managers.lastname )as manager_name, workers.position,  workers.company, workers.status');
        $join->join("managers", "workers.manager = managers.id");
        $join->join("works", "workers.job = works.id");
        $user_date = $join->get()->getResultArray();
        return $this->getResponse([
            'message' => 'Workers retrieved successfully',
            'workers' => $user_date
        ]);
    }
 
    public function registerWorker()
    {
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
            $number_worker=StringMake::manager_number() ;
            $data = [
                "register_number" => $number_worker,
                "name" => $input["name"],
                "lastname" => $input["lastname"],
                "company" => $input["company"],
                "position" => $input["position"],
                "job" => $input["work"],
                "manager" => $input["manager"],
                "status" => "Habilitado"

            ];
            $query = $this->workers->insert($data);
            if (!$query){
                return $this->getResponse("", ResponseInterface::HTTP_BAD_REQUEST);
            }else{
                return $this->getResponse([
                    "message" => "El Trabajador ". $input["name"]." " .$input["lastname"]." Se Registro Correctamente"
                ]);
            }
        }

    }
    public function updateWorker()
    {
        $rules = [
            "name" => "required",
            "lastname"=> "required",
            "company"=> "required",
            "position"=> "required",
            "work" => "required",
            "manager" => "required",
        ];
        $input = $this->getRequestInput($this->request);

        if (!$this->validateRequest($input, $rules)) {
            return $this->getResponse($this->validator->getErrors(), ResponseInterface::HTTP_BAD_REQUEST);
        }else{
            $id = $input["id"];
            $data = [
                "name" => $input["name"],
                "lastname" => $input["lastname"],
                "company" => $input["company"],
                "position" => $input["position"],
                "work" => $input["work"],
                "manager" => $input["manager"],
		];
        $update = $this->workers->update($id, $data);
        if(!$update){
            return $this->getResponse("", ResponseInterface::HTTP_BAD_REQUEST);
        }else{
            return $this->getResponse([
                "message" => "datos actualizados"
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
            $update = $this->workers->update($id,$dataUpdate);
            return $this->getResponse(["message" => "deshabilitado"]);   
        }elseif($data === "Deshabilitado"){
            $dataUpdate = ["status" => "Habilitado"];
            $update = $this->workers->update($id,$dataUpdate);
            return $this->getResponse(["message" => "habilitado"]);   
        }
        }
        
    }

    public function getWorkerSearchByRegisterNumber(){
        date_default_timezone_set('America/Merida');   
        $currentDate =  date("Y-m-d ");
        $input = $this->getRequestInput($this->request);
        $string = $input["search"];
        $join = $this->workers->table("workers");
        $join->select('workers.id as workers_id, workers.name, works.job,workers.lastname, workers.position, workers.register_number, workers.company, CONCAT(managers.name," ",managers.lastname) as manager,CONCAT(works.job," ",works.batch) as job');
        $join->join("works", "workers.job = works.id")->where("works.status","Habilitado");
        $join->join("managers", "workers.manager = managers.id")->where("workers.register_number", $string)->where("workers.status" , "Habilitado");
        $inf_user =  $join->get()->getResultArray();

        $stateExit = $this->BitacoraWorkers->where("register_number", $string)->like("entry_worker", $currentDate);
        $stateExit = $stateExit->get()->getResultArray();


        if(!$inf_user){
            return $this->getResponse("", ResponseInterface::HTTP_BAD_REQUEST);
        }else{
        return $this->getResponse([
            "worker" => $inf_user,
            "stateExit"=> $stateExit
        ]);   
        }
  }
  public function getWorkerScanByRegisterNumber($number){
    date_default_timezone_set('America/Merida');   
    $currentDate =  date("Y-m-d ");
    $string = $number;
    $join = $this->workers->table("workers");
    $join->select('workers.id as workers_id, workers.name, works.job,workers.lastname, workers.position, workers.register_number, workers.company, CONCAT(managers.name," ",managers.lastname) as manager,CONCAT(works.job," ",works.batch) as job');
    $join->join("works", "workers.job = works.id");
    $join->join("managers", "workers.manager = managers.id")->where("workers.register_number", $string)->where("workers.status" , "Habilitado");
    $inf_user =  $join->get()->getResultArray();

    
    $stateExit = $this->BitacoraWorkers->where("register_number", $string)->like("entry_worker", $currentDate);
    $stateExit = $stateExit->get()->getResultArray();

    if(!$inf_user){
        return $this->getResponse("", ResponseInterface::HTTP_BAD_REQUEST);
    }else{
     
    return $this->getResponse([
        "worker" => $inf_user,
        "stateExit"=> $stateExit
    ]);   
    }
}
public function deleteWorker() {
    $rules = [
        "id"=> "required",
    ];
    $input = $this->getRequestInput($this->request);
    if (!$this->validateRequest($input, $rules)) {
        return $this->getResponse($this->validator->getErrors(), ResponseInterface::HTTP_BAD_REQUEST);
    }else{
        $id = $input["id"];
        $delete = $this->workers->delete($id);
        if (!$delete){
            return $this->getResponse("Error al eliminar Trabajador", ResponseInterface::HTTP_BAD_REQUEST);
        }
        return $this->getResponse(["message" => "El Trabajador se ha eliminado"]);
    }
    }

}
?>
