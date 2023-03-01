<?php 

namespace App\Models;
	use CodeIgniter\Model;

class WorksModel extends Model {
		protected $table = 'works';
		protected $primaryKey = 'id';
		protected $allowedFields = ['job','batch','status', 'color'];
		protected $updatedField = "updated_at";
		protected $useSoftDeletes = false;


}