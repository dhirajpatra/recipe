<?php
use User_Controller as User;
use Recipe_Controller as Recipe;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;
/**
 * Class RecipeTest
 */
class RecipeTest extends TestCase
{
    private $user;
    private $recipe;

    /**
     * this is example test
     */
    public function testPushAndPop()
    {
        $stack = [];
        $this->assertEquals(0, count($stack));

        array_push($stack, 'foo');
        $this->assertEquals('foo', $stack[count($stack)-1]);
        $this->assertEquals(1, count($stack));

        $this->assertEquals('foo', array_pop($stack));
        $this->assertEquals(0, count($stack));
    }

    /**
     * This is set up method for setting the primary need to tests
     */
    protected function setUp()
    {
        // ifconfig result provide this IP address
        $this->user = new User();
        $this->recipe = new Recipe();

        $this->client = new GuzzleHttp\Client([
            'base_uri' => 'http://172.18.0.1',
            'headers' => [
                'Accept' => 'application/json; charset=utf-8',
                'X-Auth-Token' => '12345678',
                'X-Auth-Secret' => '12345678'
            ]
        ]);

    }

    /**
     * type of desctructor
     */
    protected function tearDown()
    {
        $this->user = NULL;
        $this->recipe = NULL;
    }

    /**
     * test user login which is require to call any protected api
     */
    public function testLoginPost()
    {
        $response = $this->client->request('POST', '/api/user/login',
            [
                'json' => [
                    'username' => 'recipe',
                    'password' => 'recipe'
                ]
            ]
        );
        $this->assertEquals(200, $response->getStatusCode());
        $body = json_decode($response->getBody(), true);
        $this->assertEquals('Login successful', $body['message']);
    }

    /**
     * test recipes api
     */
    public function testRecipes()
    {
        $response = $this->client->request('GET', '/api/recipes');
        $this->assertEquals(200, $response->getStatusCode());
        $body = json_decode($response->getBody(), true);
        $this->assertEquals('Recipes fetched successfully', $body['message']);
    }

    /**
     * this will test to fetch one recipe
     */
    public function testRecipe()
    {
        $response = $this->client->request('GET', '/api/recipes/1');
        $this->assertEquals(200, $response->getStatusCode());
        $body = json_decode($response->getBody(), true);
        $this->assertEquals('Recipe fetched successfully', $body['message']);
    }

    /**
     * this will test create new recipe
     */
    public function testRecipePost()
    {
        $response = $this->client->request('POST', '/api/recipes',
            [
                'json' => [
                    "recipe_name" => $this->randw(8),
                    "preparation_time" => 2,
                    "difficulty_level" => "2",
                    "veg" => true,
                    "status" => true
                ]
            ]
        );
        $this->assertEquals(200, $response->getStatusCode());
        $body = json_decode($response->getBody(), true);
        $this->assertEquals('Recipe created successfully', $body['message']);
    }

    /**
     * this will test recipe update
     */
    public function testRecipeUpdate()
    {
        $response = $this->client->request('PATCH', '/api/recipes/1',
            [
                'json' => [
                    "recipe_name" => $this->randw(8),
                    "preparation_time" => 3,
                    "difficulty_level" => "3",
                    "veg" => false,
                    "status" => true
                ]
            ]
        );
        $this->assertEquals(200, $response->getStatusCode());
        $body = json_decode($response->getBody(), true);
        $this->assertEquals('Recipe updated successfully', $body['message']);
    }

    /**
     * this will test rating for a specific recipe
     */
    public function testRatingPost()
    {
        $response = $this->client->request('POST', '/api/recipes/1/rating');
        $this->assertEquals(200, $response->getStatusCode());
        $body = json_decode($response->getBody(), true);
        $this->assertEquals('Recipe rated successfully', $body['message']);
    }

    /**
     * this will test search by keyword
     */
    public function testSearchPost()
    {
        $response = $this->client->request('POST', '/api/recipes/search/biri');
        $this->assertEquals(200, $response->getStatusCode());
        $body = json_decode($response->getBody(), true);
        $this->assertEquals('Recipe found successfully', $body['message']);
    }

    /**
     * this will test delete of a recipe
     */
    public function testRecipeDelete()
    {
        $response = $this->client->request('DELETE', '/api/recipes/1');
        $this->assertEquals(200, $response->getStatusCode());
        $body = json_decode($response->getBody(), true);
        $this->assertEquals('Recipe deleted successfully', $body['message']);
    }

    /**
     * generate random word/string
     * @param int $length
     * @return string
     */
    private function randw($length=4){
        return substr(str_shuffle("qwertyuiopasdfghjklzxcvbnm"),0,$length);
    }
}