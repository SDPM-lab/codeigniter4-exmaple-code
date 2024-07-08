<?php

namespace App\Models\V1;

use CodeIgniter\Model;
use App\Entities\TodoListsEntity;

class TodoListsModel extends Model
{
    protected $DBGroup          = USE_DB_GROUP;
    protected $table            = 'TodoLists';
    protected $primaryKey       = 't_key';
    protected $useAutoIncrement = true;
    protected $returnType       = TodoListsEntity::class;
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        't_title', 't_content', 'm_key'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];
    
    /**
     * Get all todos for a specific user as entities.
     *
     * @param integer $m_key
     * @return array|false
     */
    public function getAllTodosByUser(int $m_key): array|false
    { 
        $results = $this->where('m_key', $m_key)
                        ->where('deleted_at', null)
                        ->findAll();
        return $results ?: false;
    }
}
