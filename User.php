<?php
namespace dingtalk;

use \dingtalk\Http;
class User
{
    private $http;
    private $url;
    public function __construct()
    {
        $this->http = new Http();
        $this->url = config('oapi_host');
    }
    public  function getUserId($accessToken, $code)
    {
        $response = $this->http->get($this->url."/user/getuserinfo", 
            array("access_token" => $accessToken, "code" => $code));
            $response = json_decode($response);
            return $response->userid;
    }
    public  function getUser($accessToken,$userId){

        $response = $this->http->get($this->url."/user/get", 
            array("access_token" => $accessToken, "userid" => $userId));
            $response = json_decode($response,true);
            return $response;
    }

    public  function simplelist($accessToken,$deptId){
        $response = $this->http->get($this->url."/user/simplelist",
            array("access_token" => $accessToken,"department_id"=>$deptId));
        $response = json_decode($response);
        return $response->userlist;

    }

}