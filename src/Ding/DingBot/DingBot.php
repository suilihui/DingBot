<?php

namespace Ding\DingBot;

class DingBot
{
    private $ch;
    private $baseUri;
    private static $instance;
    private static $token;

    private function __construct()
    {
        $this->baseUri = "https://oapi.dingtalk.com/robot/send?access_token=";
        $this->_init();
    }

    /**
     * 单例模式
     */
    public static function getInstance($token)
    {
        //每次获取实例时更新token信息
        self::$token = $token;

        if (!(self::$instance instanceof DingBot))
            self::$instance = new DingBot();

        return self::$instance;
    }

    /**
     * 发送消息
     */
    public function sendMsg($content, $at = [], $isAll = false)
    {
        $data = $this->getData($content, $at, $isAll);
        //设置机器人url
        $uri = $this->baseUri . self::$token;
        curl_setopt($this->ch, CURLOPT_URL, $uri);
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
        self::$token = $token;

        return self::$instance;
    }

    /**
     * 初始化curl资源实例
     */
    private function _init()
    {
        $this->ch = curl_init();

        curl_setopt_array($this->ch, array(
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTPHEADER => ["Content-Type: application/json"],
        ));
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
