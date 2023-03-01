<?php namespace App\Models;
	use CodeIgniter\Model;

class ProvidersModel extends Model {
		protected $table = "providers";
		protected $primaryKey = "id";
		protected $allowedFields = ["register_number","name","service", "work","status" ];
		protected $useSoftDeletes = false;
		protected $updatedField = 'updated_at';

}