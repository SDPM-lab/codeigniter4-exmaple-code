<?php

use App\Models\V1\MembersModel;
use Tests\Support\DatabaseTestCase;
use CodeIgniter\Test\FeatureTestTrait;

class MembersTest extends DatabaseTestCase
{
    use FeatureTestTrait;

    // For Seeds
    protected $seedOnce = false;
    protected $seed     = 'MembersConstructionSeeder';
    protected $basePath = APPPATH . DIRECTORY_SEPARATOR . 'Database' . DIRECTORY_SEPARATOR;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function tearDown(): void
    {
        parent::tearDown();

        $this->db->table('Members')->emptyTable('Members');
        $this->db->query("ALTER TABLE Members AUTO_INCREMENT = 1");
    }

    public function testDatabase()
    {
        $membersModel = new MembersModel();
        
        // Check whether database seed and migrate successfully.
        $data = $membersModel->findAll();

        $this->assertNotEmpty($data);
    }

    public function testLoginSuccessfully()
    {
        $userLoginData = [
            "account"  => "example_account",
            "password" => "example_password"
        ];

        $results = $this->withBodyFormat('json')
                        ->post("api/v1/user/login", $userLoginData);

        $results->assertStatus(200);

        $returnData = json_decode($results->getJSON());

        $excepted = [
            "account"  => "example_account",
            "name"     => "Example User",
            "key"      => "1"
        ];

        $this->assertEquals($excepted, (array)$returnData->data);

        $this->assertNotEmpty(session()->get('user'));
    }

    public function testNotPassInUserAndWillShowError()
    {
        $results = $this->withBodyFormat('json')
                        ->post("api/v1/user/login");

        $results->assertStatus(404);

        $returnData = json_decode($results->getJSON());

        $excepted = "Sign in data is not found.";

        $this->assertEquals($excepted, $returnData->messages->error);
    }

    public function testSignUpSuccessfully()
    {
        $userLoginData = [
            "account"  => "example_account_2",
            "password" => "example_password_2",
            "name"     => "Example User 2"
        ];

        $results = $this->withBodyFormat('json')
                        ->post("api/v1/user", $userLoginData);

        $results->assertStatus(200);

        $returnData = json_decode($results->getJSON());

        $excepted = [
            "account"  => "example_account_2",
            "name"     => "Example User 2"
        ];

        $this->assertEquals($excepted, (array)$returnData->data);

        $this->seeInDatabase("Members", [
            "m_account"  => "example_account_2",
            "m_name"     => "Example User 2"
        ]);
    }

    public function testLogoutSuccessfully()
    {
        $results = $this->get("api/v1/user/logout");

        $results->assertRedirect();

        $this->assertEmpty(session()->get('user'));
    }
}