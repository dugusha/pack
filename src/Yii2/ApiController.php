<?php
/**
 * Created by PhpStorm.
 * User: gaoruishen
 * Date: 2020/3/27
 * Time: 11:27 AM
 */

namespace GRS\Yii2;

use yii\rest\Controller;

class ApiController extends Controller{
    /**
     * init
     */
    public function init() {
        parent::init();
        $this->checkAccess();
    }

    /**
     * 检查来源，禁止外部请求直接访问
     */
    public function checkAccess()
    {
        $client_ip  = $this->getIP();
        if (!$this->isPrivateIP(ip2long($client_ip))) throw new UserException('禁止外部访问');
    }

    /**
     * 获取Client访问者的IP地址
     * @return string ip地址
     */
    public function getIP($long=false){
        if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
            $ip = getenv("REMOTE_ADDR");
        else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
            $ip = $_SERVER['REMOTE_ADDR'];
        else if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
            $ip = getenv("HTTP_CLIENT_IP");
        else $ip = "unknown";
        $client_ip = "";
        try {
            $arr = explode(',', $ip);
            foreach ($arr as $item) {
                $chk_item = trim($item);
                if ($chk_item != "" && strtolower($chk_item) != "unknown" &&  !self::isPrivateIP(ip2long($chk_item))) {
                    $client_ip = $chk_item;
                    break;
                }
            }
            if (empty($client_ip)) $client_ip = $arr[0] == 'unknown'? null : $arr[0];
        } catch (\Exception $e) {}
        if($long) $client_ip=sprintf("%u",ip2long($client_ip));
        return $client_ip;
    }

    /**
     * determine if ip is a private ip. rfc 1918
     * @return boolean
     */
    public function isPrivateIP($ip) {
        return (
            ($ip & 0xFF000000) == 0x00000000 || # 0.0.0.0/8
            ($ip & 0xFF000000) == 0x0A000000 || # 10.0.0.0/8
            ($ip & 0xFF000000) == 0x7F000000 || # 127.0.0.0/8
            ($ip & 0xFFF00000) == 0xAC100000 || # 172.16.0.0/12
            ($ip & 0xFFFF0000) == 0xA9FE0000 || # 169.254.0.0/16
            ($ip & 0xFFFF0000) == 0xC0A80000);  # 192.168.0.0/16
    }
}