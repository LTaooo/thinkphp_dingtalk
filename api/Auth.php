<?php
namespace app\index\api;

use \app\index\api\Http;
// require_once "ding.php";
class Auth
{
    private $http;
    private $url;
    public function __construct()
    {
        $this->http = new Http();
        $this->url = config('oapi_host');
    }
    public function getAccessToken()
    {
        /**
         * 缓存accessToken。accessToken有效期为两小时，需要在失效前请求新的accessToken（注意：以下代码没有在失效前刷新缓存的accessToken）。
         */
        // $accessToken = Cache::get('corp_access_token');
        // $accessToken = null;如果过期了
//        dump("in");
//        dump(config('appkey'));
//        dump(config('appsecret'));
        $response = $this->http->get(
            $this->url.'/gettoken',
            array('corpid' => config('appkey'), 'corpsecret' =>config('appsecret'))
        );
        $accessToken = json_decode($response)->access_token;
//        dump($accessToken);
        // Cache::set('corp_access_token', $accessToken);
        return $accessToken;
    }
    
    /**
     * 缓存jsTicket。jsTicket有效期为两小时，需要在失效前请求新的jsTicket（注意：以下代码没有在失效前刷新缓存的jsTicket）。
     */
    public function getTicket($accessToken)
    {
        $response = $this->http->get($this->url.'/get_jsapi_ticket', array('type' => 'jsapi', 'access_token' => $accessToken));
        $response = json_decode($response);
        $this->check($response);
        $jsticket = $response->ticket;
        return $jsticket;
    }


    public function curPageURL()
    {
        $pageURL = 'http';

        if (array_key_exists('HTTPS', $_SERVER)&&$_SERVER["HTTPS"] == "on") {
            $pageURL .= "s";
        }
        $pageURL .= "://";

        if ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
        } else {
            $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
        }
        return $pageURL;
    }

    public function getConfig()
    {
        $corpId = session('corpid');
        $agentId = config('agentid');
        $nonceStr = 'abcdefg';
        $timeStamp = time();
        $url = $this->curPageURL();
        $corpAccessToken = $this->getAccessToken();
        if (!$corpAccessToken) {
            dump("[getConfig] ERR: no corp access token");
        }
        $ticket = $this->getTicket($corpAccessToken);
        $signature = $this->sign($ticket, $nonceStr, $timeStamp, $url);
        
        $config = array(
            'url' => $url,
            'nonceStr' => $nonceStr,
            'agentId' => $agentId,
            'timeStamp' => $timeStamp,
            'corpId' => $corpId,
            'signature' => $signature);
        return json_encode($config, JSON_UNESCAPED_SLASHES);
    }
    
    
    public function sign($ticket, $nonceStr, $timeStamp, $url)
    {
        $plain = 'jsapi_ticket=' . $ticket .
            '&noncestr=' . $nonceStr .
            '&timestamp=' . $timeStamp .
            '&url=' . $url;
        return sha1($plain);
    }
    
    public function check($res)
    {
        if ($res->errcode != 0) {
            dump("FAIL: " . json_encode($res));
            exit("Failed: " . json_encode($res));
        }
    }
}
