<?php

namespace App\Controllers\Api\V1;

use App\Controllers\BaseController;
use App\Models\V1\TodoListsModel;
use CodeIgniter\API\ResponseTrait;
use App\Entities\TodoListsEntity;

class TodoController extends BaseController
{
    use ResponseTrait;

    /**
     * TodoListsModel
     *
     * @var TodoListsModel
     */
    protected TodoListsModel $todoListsModel;

    public function __construct()
    {
        $this->todoListsModel = new TodoListsModel();
    }
    /**
     * [Get] /todo
     * Get all todo list data.
     *
     * @return void
     */
    public function index()
    {
        // Find the data from database.
        $todoList = $this->todoListsModel->where("m_key",$this->userData["key"])
                                         ->findAll();

        $returnData = [];

        // Extract only the required fields from each todo item.
        foreach ($todoList as $todo) {
            $returnData[] = [
                "title"   => $todo->t_title,
                "content" => $todo->t_content,
                'key'     => $todo->t_key,
            ];
        }
    
        return $this->respond([
            "msg"  => "success",
            "data" => $returnData
        ]);
}

    /**
     * [GET] /todo/{key}
     *
     * @param integer|null $key
     * @return void
     */
    public function show(?int $key = null)
    {
        if ($key === null) {
            return $this->failNotFound("Enter the the todo key");
        }

        // Find the data from database.
        $todo = $this-> todoListsModel->where("m_key", $this->userData["key"])
                                      ->find($key);

        if ($todo === null) {
            return $this->failNotFound("Todo is not found.");
        }

        // Define the return data structure.
        $returnData = [
            "title"   => $todo->t_title,
            "content" => $todo->t_content,
            'key'     => $todo->t_key,
        ];

        return $this->respond([
            "msg" => "success",
            "data" => $returnData
        ]);
    }

    /**
     * [POST] /todo
     * Create a new todo data into database.
     *
     * @return void
     */
    public function create()
    {
        // Get the data from request.
        $data    = $this->request->getJSON();
        $title   = $data->title   ?? null;
        $content = $data->content ?? null;

        // Check if account and password is correct.
        if (empty($title) || empty($content)) {
            return $this->fail("Pass in data is not found.", 404);
        }

        // Create a new entity instance and populate it.
        $todo = new TodoListsEntity();

        $todo->t_title   = $title;
        $todo->t_content = $content;
        $todo->m_key     = $this->userData["key"];

        // Insert data into database using ORM.
        if (!$this->todoListsModel->save($todo)) {
            return $this->fail("Create failed.");
        }
        else{
            $todoListData = [
                'title'   => $todo->t_title,
                'content' => $todo->t_content
            ];
        }

        $this->clearCache($this->userData["key"]);

        return $this->respond([
            "msg"  => "Create successfully",
            "data" => $todoListData
        ]);
    }

    /**
     * [PUT] /todo/{key}
     *
     * @param integer|null $key
     * @return void
     */
    public function update(?int $key = null)
    {
        // Get the  data from request.
        $data    = $this->request->getJSON();
        $title   = $data->title   ?? null;
        $content = $data->content ?? null;

        if ($key === null) {
            return $this->failNotFound("Key is not found.");
        }

        // Get the will update data.
        $willUpdateData = $this-> todoListsModel->where(
            "m_key",
            $this->userData["key"]
        )->find($key);

        if ($willUpdateData === null) {
            return $this->failNotFound("This data is not found.");
        }

        // Update the entity.
        if ($title !== null) {
            $willUpdateData->t_title = $title;
        }

        if ($content !== null) {
            $willUpdateData->t_content = $content;
        }

        // Save the updated entity using ORM.
        if (!$this->todoListsModel->save($willUpdateData)) {
            return $this->fail("Update failed.");
        }

        $this->clearCache($this->userData["key"]);

        return $this->respond([
            "msg" => "Update successfully"
        ]);
    }

    /**
     * [DELETE] /todo/{key}
     *
     * @param integer|null $key
     * @return void
     */
    public function delete(?int $key = null)
    {
        if ($key === null) {
            return $this->failNotFound("Key is not found.");
        }

        // Find the existing entity.
        $todo = $this->todoListsModel->where(
            'm_key', 
            $this->userData["key"]
        )->find($key);

        if ($todo === null) {
            return $this->failNotFound("This data is not found.");
        }

        // Do delete action.
        $isDeleted = $this->todoListsModel->delete($key);

        if ($isDeleted === false) {
            return $this->fail("Delete failed.");
        }

        $this->clearCache($this->userData["key"]);
        
        return $this->respond([
            "msg" => "Delete successfully"
        ]);
    }

    /**
     * 清除 Redis 快取方法
     *
     * @param integer $userKey
     * @return void
     */
    private function clearCache($userKey)
    {
        $cacheKey = 'TodoListViewController_getDatatableData_' . sha1($userKey);
        $cache = \Config\Services::cache();
        $cache->delete($cacheKey);
    }
}
