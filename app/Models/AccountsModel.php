<?php 

namespace App\Models;
use CodeIgniter\Model;
use Exception;

class AccountsModel extends Model{
    protected $table = "accounts";
    protected $primaryKey = "id";
    protected $allowedFields = ["user_inf","type_user","username" , "password", "status"];
    protected $updatedField = "updated_at";
    protected $useSoftDeletes = false;



    public function findUserByUsername($username){
        $user = $this->asArray()->where(["username" => $username])->first();

        if (!$user){
            throw new Exception("user does not exist for especified Username");
        }
        return $user;
    }
}

?>