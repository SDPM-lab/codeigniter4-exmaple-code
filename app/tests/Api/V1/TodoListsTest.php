<?php

use App\Models\V1\TodoListsModel;
use Tests\Support\DatabaseTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use App\Entities\TodoListsEntity;

class TodoListsTest extends DatabaseTestCase
{
    use FeatureTestTrait;

    /**
     * Session Data.
     *
     * @var array
     */
    protected array $sessionData;

    public function setUp(): void
    {
        parent::setUp();

        //seed some user fake data.
        $now  = date("Y-m-d H:i:s");

        $data = [
            [
                'm_name'     => 'Example User',
                'm_account'  => 'example_account',
                'm_password' => password_hash("example_password", PASSWORD_DEFAULT),
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        $this->db->table('Members')->insertBatch($data);

        $this->sessionData = [
            "user" => [
                "account"  => "example_account",
                'name'     => 'Example User',
                "key"      => 1
            ]
        ];
    }

    public function tearDown(): void
    {
        parent::tearDown();

        $this->db->table('Members')->emptyTable('Members');
        $this->db->table('TodoLists')->emptyTable('TodoLists');
        $this->db->query("ALTER TABLE Members AUTO_INCREMENT = 1");
        $this->db->query("ALTER TABLE TodoLists AUTO_INCREMENT = 1");

        session()->destroy("user");
    }

    public function testCreateTodoSuccessfully()
    {
        $createData = [
            "title"     => "Example Title",
            "content"   => "Example Content",
        ];

        $results = $this->withSession($this->sessionData)
                        ->withBodyFormat('json')
                        ->post("api/v1/todo", $createData);

        $results->assertStatus(200);

        $returnData = json_decode($results->getJSON());

        $excepted = [
            "title"     => "Example Title",
            "content"   => "Example Content",
        ];

        $this->assertEquals($excepted, (array)$returnData->data);

        $this->seeInDatabase('TodoLists', [
            "t_title"   => $createData["title"],
            "t_content" => $createData["content"],
        ]);
    }

    public function testShowAllDataSuccessfully()
    {
        $todoListsModel = new TodoListsModel();

        $createData = [
            "t_title"    => "Example Title",
            "t_content"  => "Example Content",
            "m_key"      => 1,
        ];

        $todo = new TodoListsEntity($createData);

        $createdKey = $todoListsModel->save($todo);
        $this->assertEquals(1, $createdKey);

        $createSecondData = [
            "t_title"     => "Example Title 2",
            "t_content"   => "Example Content 2",
            "m_key"      => 1,
        ];

        $todoSecond = new TodoListsEntity($createSecondData);

        $createdSecondKey = $todoListsModel->save($todoSecond);

        $this->assertEquals(2, $createdSecondKey);

        $results = $this->withSession($this->sessionData)
                        ->get("api/v1/todo");

        $returnData = json_decode($results->getJSON());

        $excepted = [
            [
                "title"    => "Example Title",
                "content"  => "Example Content",
                "key"      => "1",
            ],
            [
                "title"    => "Example Title 2",
                "content"  => "Example Content 2",
                "key"      => "2",
            ]
        ];

        $this->assertEquals($excepted[0], (array)$returnData->data[0]);
        $this->assertEquals($excepted[1], (array)$returnData->data[1]);
    }

    public function testShowSingleDataSuccessfully()
    {
        $todoListsModel = new TodoListsModel();

        $createData = [
            "t_title"    => "Example Title",
            "t_content"  => "Example Content",
            "m_key"      => 1,
        ];

        $todo = new TodoListsEntity($createData);

        $createdKey = $todoListsModel->save($todo);
        $this->assertEquals(1, $createdKey);

        $createSecondData = [
            "t_title"    => "Example Title 2",
            "t_content"  => "Example Content 2",
            "m_key"      => 1,
        ];

        $todoSecond = new TodoListsEntity($createSecondData);

        $createdSecondKey = $todoListsModel->save($todoSecond);
        $this->assertEquals(2, $createdSecondKey);

        $results = $this->withSession($this->sessionData)
                        ->get("api/v1/todo/2");

        $returnData = json_decode($results->getJSON());

        $excepted = [
            "title"    => "Example Title 2",
            "content"  => "Example Content 2",
            "key"      => "2",
        ];

        $this->assertEquals($excepted, (array)$returnData->data);
    }

    public function testUpdateTodoSuccessfully()
    {
        $todoListsModel = new TodoListsModel();

        $createData = [
            "t_title"    => "Example Title",
            "t_content"  => "Example Content",
            "m_key"      => 1,
        ];

        $todo = new TodoListsEntity($createData);

        $createdKey = $todoListsModel->save($todo);
        $this->assertEquals(1, $createdKey);

        $this->seeInDatabase('TodoLists', [
            "t_title"   => $createData["t_title"],
            "t_content" => $createData["t_content"],
            "m_key"     => $createData["m_key"],
        ]);

        $updatedData = [
            "title"     => "Example Title 2",
            "content"   => "Example Content 2",
        ];

        $results = $this->withSession($this->sessionData)
                        ->withBodyFormat('json')
                        ->put("api/v1/todo/1", $updatedData);

        $results->assertStatus(200);

        $this->seeInDatabase('TodoLists', [
            "t_title"   => $updatedData["title"],
            "t_content" => $updatedData["content"],
        ]);

        $returnData = json_decode($results->getJSON());

        $excepted = [
            "msg" => "Update successfully"
        ];

        $this->assertEquals($excepted, (array)$returnData);
    }

    public function testDeleteTodoSuccessfully()
    {
        $now = date("Y-m-d H:i:s");

        $todoListsModel = new TodoListsModel();

        $createData = [
            "t_title"    => "Example Title",
            "t_content"  => "Example Content",
            "m_key"      => 1,
            "created_at" => $now,
            "updated_at" => $now,
        ];

        $todo = new TodoListsEntity($createData);

        $createdKey = $todoListsModel->save($todo);
        $this->assertEquals(1, $createdKey);

        $createSecondData = [
            "t_title"    => "Example Title 2",
            "t_content"  => "Example Content 2",
            "m_key"      => 1,
            "created_at" => $now,
            "updated_at" => $now,
        ];

        $todoSecond = new TodoListsEntity($createSecondData);

        $createdSecondKey = $todoListsModel->save($todoSecond);
        $this->assertEquals(2, $createdSecondKey);

        $results = $this->withSession($this->sessionData)
                        ->delete("api/v1/todo/2");

        $returnData = json_decode($results->getJSON());

        $excepted = [
            "msg" => "Delete successfully"
        ];

        $this->assertEquals($excepted, (array)$returnData);
    }
}