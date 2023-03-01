<?php

namespace App\Models;

use CodeIgniter\Model;

class ManagersModel extends Model
{
    protected $table = "managers";
    protected $primaryKey = "id";
    protected $allowedFields = [
        "manager_number","name","lastname", "company", "position", "work"
    ];
    protected $updatedField = "updated_at";
    protected $useSoftDeletes = false;

    
    public function findManagerById($id)
    
    {
        $manager = $this->asArray()->where(['id' => $id])->first();

        if (!$manager) {
            throw new \Exception('Could not find manager for specified ID');
        }

        return $manager;
    }
}