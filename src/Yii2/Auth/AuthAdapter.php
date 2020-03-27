<?php
/**
 * Created by PhpStorm.
 * User: gaoruishen
 * Date: 2020/3/27
 * Time: 10:18 AM
 */

namespace GRS\Yii2\Auth;


use GRS\Yii2\ApiAdapter;

class AuthAdapter extends ApiAdapter{
    protected  function getApiServer(){
        // TODO: Implement getApiServer() method.
        return AUTH_SERVER;
    }

    public function checkAccess($utoken, $uri, $params) {
//        $form = array(
//            'utoken' => $utoken,
//            'uri' => $uri,
//            'system' => ME,
//            'params' =>$params
//        );
//        $result = $this->get('/api/auth/checkAccess', $form );
        $result = [
            "user" => [
                "id"    => "0001",
                "name"  => "小明"
            ],
            "hasPermission" => true
        ];
        return $result;
    }

}