<?php

namespace Florence\Test;

use Florence\Emoji;
use Florence\Config;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class EmojiRestApiTest extends \PHPUnit_Framework_TestCase
{
    protected $emoji;
    protected $client;
    protected $url;
    protected $token;

    public function setUp ()
    {
        Config::loadenv();

        $this->token = getenv('TEST_TOKEN');
        $this->emoji = new Emoji();
        $this->client = new Client();
        $this->url = "http://emojis4devs.herokuapp.com";
    }

    /**
    * @expectedException GuzzleHttp\Exception\ClientException
    */
    public function testInvalidEndpoint()
    {
        $request = $this->client->request('GET', $this->url.'/auth/emojis');

        $this->assertInternalType('object' , $body);
        $this->assertEquals('200', $body->getStatusCode());
    }

    /**
    * protected route
    * tests registration endpoint
    */
    public function testRegister()
    {
        $register = $this->client->request('POST', $this->url.'/register');
        $content = $register->getHeader('content-type')[0];

        $this->assertEquals('200', $register->getStatusCode());
        $this->assertInternalType('object', $register->getBody());
        $this->assertEquals('application/json', $content);
    }

    /**
    * test login
    */
    public function testLogin()
    {
        $req = $this->client->request('POST', $this->url.'/auth/login',
            [ 'headers' => ['Authorization'=> ''],'form_params' => [
                            'username' => 'craig',
                            'password' => 'pass123'
        ]]);

        $content = $req->getHeader('content-type')[0];

        $this->assertInternalType('object' , $req);
        $this->assertEquals('200', $req->getStatusCode());
        $this->assertEquals('application/json', $content);
    }

    /**
    * test logout
    */
    public function testLogout()
    {
        $req = $this->client->request('POST', $this->url.'/auth/logout',
            [ 'headers' => ['Authorization'=> $this->token]]);

        $content = $req->getHeader('content-type')[0];

        $this->assertInternalType('object' , $req);
        $this->assertEquals('200', $req->getStatusCode());
        $this->assertEquals('application/json', $content);
    }

    /**
    * tests for all unprotected GET routes
    */
    public function testAllGETRoutes()
    {
        $index = $this->client->request('GET', $this->url);
        $emojis = $this->client->request('GET', $this->url.'/emojis');

        $contentTypeForIndex = $index->getHeader('content-type')[0];
        $contentTypeForEmojis = $emojis->getHeader('content-type')[0];

        $this->assertEquals('200', $index->getStatusCode());
        $this->assertInternalType('object', $index->getBody());

        $this->assertEquals('200', $emojis->getStatusCode());
        $this->assertInternalType('object', $emojis->getBody());

        $this->assertEquals('text/html;charset=UTF-8', $contentTypeForIndex);
        $this->assertEquals('application/json', $contentTypeForEmojis);

    }

    /**
    * @expectedException GuzzleHttp\Exception\ClientException
    */
    public function testCreate()
    {
        $body = $this->client->request('POST', $this->url.'/emojis',
            [ 'headers' => ['Authorization'=> $this->token],'form_params' => [
                            'name'      => 'test',
                            'emojichar'      => '💯',
                            'keywords'  => 'tia, andela',
                            'category'  => 'andela'
        ]]);

        $this->assertInternalType('object' , $body);
        $this->assertEquals('200', $body->getStatusCode());
    }


    public function testPut()
    {
        $body = $this->client->request('PUT', $this->url.'/emojis/4',[ 'headers' => ['Authorization'=> $this->token],'form_params' => [
                            'name'      => 'Sunny',
                            'char'      => '😎',
                            'keywords'  => 'Holiday, fun',
                            'category'  => 'Vacation'
        ]]);

        $this->assertInternalType('object' , $body);
        $this->assertEquals('200', $body->getStatusCode());
        $this->assertEquals('application/json', $content);
    }


    public function testPatch()
    {
        $body = $this->client->request('PUT', $this->url.'/emojis/4',
            [ 'headers' => ['Authorization'=> $this->token],'form_params' => [
                            'name'      => 'Sunny',
                            'char'      => '😎',
                            'keywords'  => 'Holiday, fun',
                            'category'  => 'Vacation'
        ]]);

        $this->assertInternalType('object' , $body);
        $this->assertEquals('200', $body->getStatusCode());
        $this->assertEquals('application/json', $content);
    }


    public function testDelete()
    {
        $body = $this->client->request('DELETE', $this->url.'/emojis/4',
            [ 'headers' => ['Authorization'=> $this->token],'form_params' => [
                            'name'      => 'Sunny',
                            'char'      => '😎',
                            'keywords'  => 'Holiday, fun',
                            'category'  => 'Vacation'
        ]]);

        $this->assertInternalType('object' , $body);
        $this->assertEquals('200', $body->getStatusCode());
        $this->assertEquals('application/json', $content);
    }
}
