<?php
namespace app\index\api;

use \app\index\api\Http;


class Department
{
    private $http;
    private $url;
    public function __construct()
    {
        $this->http = new Http();
        $this->url = config('oapi_host');
    }
    public function createDept($accessToken, $dept)
    {
        $response = $this->http->post(
            $this->url."/department/create",
            array("access_token" => $accessToken),
            json_encode($dept)
        );
        $response = json_decode($response);
        return $response->id;
    }
    
    
    public function listDept($accessToken)
    {
        $response = $this->http->get(
            $this->url."/department/list",
            array("access_token" => $accessToken)
        );
        $response = json_decode($response);
        return $response->department;
    }
    
    
    public function deleteDept($accessToken, $id)
    {
        $response = $this->http->get(
            $this->url."/department/delete",
            array("access_token" => $accessToken, "id" => $id)
        );
        $response = json_decode($response);
        return $response->errcode == 0;
    }
}
