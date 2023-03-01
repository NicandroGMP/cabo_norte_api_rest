<?php

namespace App\Controllers;
use App\Controllers\BaseController;
use App\Libraries\Hash;
use App\Libraries\StringMake;
use App\Models\ProvidersModel;
use CodeIgniter\HTTP\ResponseInterface;



class Providers extends BaseController{

    protected $providers;
    public function __construct()
    {
		helper(["url", "form"]);

        $this->providers = new ProvidersModel();
    }
    public function index(){
        $user_date = $this->providers->findAll();
        return $this->getResponse([
            'message' => 'Provider retrieved successfully',
            'providers' => $user_date
        ]);
    }
 
    public function registerProvider()
    {
        $rules = [
            "name" => "required",
            "service"=> "required",
            "work_id" => "required"
        ];

        $input = $this->getRequestInput($this->request);

        if (!$this->validateRequest($input, $rules)) {
            return $this->getResponse($this->validator->getErrors(), ResponseInterface::HTTP_BAD_REQUEST);
        }else{
            $number_worker=StringMake::manager_number() ;
            $data = [
                "register_number" => $number_worker,
                "name" => $input["name"],
                "service" => $input["service"],
                "work" => $input["work_id"],
                "status" => "Habilitado",

            ];
            $query = $this->providers->insert($data);
            if (!$query){
                return $this->getResponse("", ResponseInterface::HTTP_BAD_REQUEST);
            }else{
                return $this->getResponse([
                    "message" => "El Proveedor ". $input["name"]."Se Registro Correctamente"
                ]);
            }
        }

    }
    public function updateProvider()
    {
        $rules = [
            "name" => "required",
            "service"=> "required",
        ];
        $input = $this->getRequestInput($this->request);

        if (!$this->validateRequest($input, $rules)) {
            return $this->getResponse($this->validator->getErrors(), ResponseInterface::HTTP_BAD_REQUEST);
        }else{
            $id = $input["id"];
            $data = [
                "name" => $input["name"],
                "service" => $input["service"],
		];
        $update = $this->providers->update($id, $data);
        if(!$update){
            return $this->getResponse("", ResponseInterface::HTTP_BAD_REQUEST);
        }else{
            return $this->getResponse([
                "message" => "datos actualizados"
            ]);
        }
        }
    }

    public function getProviderSearchByRegisterNumber(){

        $input = $this->getRequestInput($this->request);
        $string = $input["search"];
        $join = $this->providers->table("workers");
        $join->select('workers.id as workers_id, workers.name, works.job,workers.lastname, workers.position, workers.register_number, workers.company, CONCAT(managers.name," ",managers.lastname) as manager,CONCAT(works.job," ",works.batch) as job');
        $join->join("works", "workers.job = works.id");
        $join->join("managers", "workers.manager = managers.id")->where("workers.register_number", $string);
        $inf_user =  $join->get()->getResultArray();

        if(!$inf_user){
            return $this->getResponse("", ResponseInterface::HTTP_BAD_REQUEST);
        }else{
         
        return $this->getResponse([
            "worker" => $inf_user
        ]);   
        }
  }
  /* public function getProviderScanByRegisterNumber($number){

    $string = $number;
    $join = $this->workers->table("workers");
    $join->select('workers.id as workers_id, workers.name, works.job,workers.lastname, workers.position, workers.register_number, workers.company, CONCAT(managers.name," ",managers.lastname) as manager,CONCAT(works.job," ",works.batch) as job');
    $join->join("works", "workers.job = works.id");
    $join->join("managers", "workers.manager = managers.id")->where("workers.register_number", $string);
    $inf_user =  $join->get()->getResultArray();

    if(!$inf_user){
        return $this->getResponse("", ResponseInterface::HTTP_BAD_REQUEST);
    }else{
     
    return $this->getResponse([
        "worker" => $inf_user
    ]);   
    }
} */
//consultar datos de provedor que registrados en el subcondominio y esten habilitados
public function searchServicesByWorkId($id){
    $join = $this->providers->table("providers");
    $join->select('providers.id, providers.name, providers.service, providers.work as work_id,  CONCAT(works.job," ",works.batch) as job, providers.status as status_provider');
    $join->join("works", "providers.work = works.id")->where("providers.work", $id)->where("providers.status" , "Habilitado");
    $services = $join->get()->getResultArray();

    return $this->getResponse(["services" =>  $services]);
}

//consultar datos de provedor que solicita un cono 
public function searchServicesById($id){
    $join = $this->providers->table("providers");
    $join->select('providers.id, providers.register_number ,providers.name, providers.service, providers.work as work_id,  CONCAT(works.job," ",works.batch) as job, providers.status as status_provider');
    $join->join("works", "providers.work = works.id")->where("providers.id", $id)->where("providers.status" , "Habilitado");
    $services = $join->get()->getResultArray();

    return $this->getResponse(["provider" =>  $services]);
}
public function statusUpdate($id,$data){
    if (empty($id) || empty($data) ){
        return $this->getResponse("", ResponseInterface::HTTP_BAD_REQUEST);
    }else{
        if ($data === "Habilitado"){
            $dataUpdate = ["status" => "Deshabilitado"];
            $update = $this->providers->update($id,$dataUpdate);
            return $this->getResponse(["message" => "deshabilitado"]);   
        }elseif($data === "Deshabilitado"){
            $dataUpdate = ["status" => "Habilitado"];
            $update = $this->providers->update($id,$dataUpdate);
            return $this->getResponse(["message" => "habilitado"]);   
        }
        }
    }
    public function deleteProvider() {
    $rules = [
        "id"=> "required",
    ];
    $input = $this->getRequestInput($this->request);
    if (!$this->validateRequest($input, $rules)) {
        return $this->getResponse($this->validator->getErrors(), ResponseInterface::HTTP_BAD_REQUEST);
    }else{
        $id = $input["id"];
        $delete = $this->providers->delete($id);
        if (!$delete){
            return $this->getResponse("Error al eliminar Proveedor", ResponseInterface::HTTP_BAD_REQUEST);
        }
        return $this->getResponse(["message" => "El Proveedor se ha eliminado"]);
    }
    }


}
?>
