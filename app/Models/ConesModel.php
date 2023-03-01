<?php 

namespace App\Models;
	use CodeIgniter\Model;

class ConesModel extends Model {
		protected $table = 'cones';
		protected $primaryKey = 'id';
		protected $allowedFields = ['num_cone','status','provider', 'register_number'];
		protected $useSoftDeletes = false;


}