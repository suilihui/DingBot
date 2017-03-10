<?php

namespace Ding\DingBot;

class DingBot
{
    private $ch;
    private $baseUri;
    private static $instance;
    
    private function __construct($token)
    {
        $this->baseUri = "https://oapi.dingtalk.com/robot/send?access_token=";
        $this->_init($token);
    }
    
    /**
     * 单例模式
     */
    public static function getInstance($token)
    {
        if (!(self::$instance instanceof DingBot))
            self::$instance = new DingBot($token);
            
        return self::$instance;
    }
    
    /**
     * 发送消息
     */
    public function sendMsg($content, $at = [], $isAll = false)
    {
        $data = $this->getData($content, $at, $isAll);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $data);
        $resopnse = curl_exec($this->ch);
        
        if (($errno = curl_errno($this->ch)) != 0 )
            throw new \Exception(curl_error($this->ch), $errno);
            
        return $resopnse;
    }
    
    /**
     * 设置机器人token
     */
    public function setToken($token)
    {
        curl_setopt($this->ch, CURLOPT_URL, $this->baseUri . $token);
        
        return self::$instance;
    }
    
    /**
     * 初始化curl资源实例
     */
    private function _init($token)
    {
        $uri = $this->baseUri . $token;
        $this->ch = curl_init($uri);
        
        curl_setopt($this->ch, CURLOPT_POST, true);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
    }
    
    /**
     *  组拼json数据
     */
    private function getData($content, $at = [], $isAtAll = false)
    {
        if (!is_array($at))
            $at = [];
            
        $struct = [
            "msgtype" => "text", 
            "text" => [ "content" => $content ], 
            "at" => [ "atMobiles" => $at,  "isAtAll" => $isAtAll ],
        ];
        
        return json_encode($struct);
    }
    
    function __destruct()
    {
        curl_close($this->ch);
    }
}
