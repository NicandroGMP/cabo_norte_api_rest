<?php namespace App\Models;
	use CodeIgniter\Model;

class RecovePassModel extends Model {
		protected $table = "recove_pass";
		protected $primaryKey = "id";
		protected $allowedFields = ["encript_string","email","expire_link",];
		protected $useSoftDeletes = false;

}