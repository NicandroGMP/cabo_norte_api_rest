<?php

namespace App\Controllers;
use App\Controllers\BaseController;

use App\Models\ConesModel;
use App\Models\BitacoraProvidersModel;
use CodeIgniter\HTTP\ResponseInterface;



class Cones extends BaseController{
    public function __construct()
    {
		helper(["url", "form"]);

        $this->cones = new ConesModel();
        $this->bitacoraProviders = new BitacoraProvidersModel();
    }

    public function index(){
        $cones = $this->cones->findAll();
        return $this->getResponse([
            'cones' => $cones
        ]);
    }
    public function ocuppiedCones(){
        $rules = [
            "currentCone" => "required",
            "provider" => "required",
            "register_num" => "required"
        ];

        $input = $this->getRequestInput($this->request);
        if (!$this->validateRequest($input, $rules)) {
            return $this->getResponse($this->validator->getErrors(), ResponseInterface::HTTP_BAD_REQUEST);
        }else{
            $data = [
                "status" =>  0,
                "provider" => $input["provider"],
                "register_number" => $input["register_num"]

            ];
            $query = $this->cones->update($input["currentCone"],$data);
            if (!$query){
                return $this->getResponse("", ResponseInterface::HTTP_BAD_REQUEST);
            }else{
                return $this->getResponse([
                    "message" => "se asigno el cono al proveedor exitosamente"
                ]);
            }
        }
    }
    public function SearchConeById(){
        $input = $this->getRequestInput($this->request);
/*         date_default_timezone_set('America/Merida');   
        $currentDate =  date("Y-m-d ");
    $join = $this->cones->table("cones");
    $join->select('cones.id as cone_id, cones.status, cones.register_number as setPovider');
    $join->join("providers", "cones.provider = providers.id")->where("register_number", $input["register_num"]);
    $inf_user =  $join->get()->getResultArray();

 */
    $data_cone = $this->bitacoraProviders->select("identification, id, name, service")->where("num_provider", $input["register_num"])->where("exit_provider is null");
    $data_cone = $data_cone->get()->getResultArray();


    if(!$data_cone){
        return $this->getResponse("", ResponseInterface::HTTP_BAD_REQUEST);
    }else{
     
    return $this->getResponse([
        "conesData" => $data_cone,
    ]);
    }
    }
}