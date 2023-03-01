<?php 

namespace App\Models;
	use CodeIgniter\Model;

class BitacoraProvidersModel extends Model {
		protected $table = 'bitacora_providers';
		protected $primaryKey = 'id';
		protected $allowedFields = ['name','work','num_provider','service','num_cone','entry_provider', 'exit_provider', 'identification'];
		protected $useSoftDeletes = false;


}