<?php 

namespace App\Models;
	use CodeIgniter\Model;

class BitacoraWorkersModel extends Model {
		protected $table = 'bitacora_workers';
		protected $primaryKey = 'id';
		protected $allowedFields = ['register_number','fullname','company','work','manager', 'position', 'entry_worker', 'exit_worker'];
		protected $useSoftDeletes = false;


}