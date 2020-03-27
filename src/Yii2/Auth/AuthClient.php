<?php
/**
 * Created by PhpStorm.
 * User: gaoruishen
 * Date: 2020/3/27
 * Time: 9:59 AM
 */
namespace GRS\Yii2\Auth;

class AuthClient{
    /**
     * 构建统一登录平台，确认登录用户，身份
     */

    //定义token名称
    const TOKEN = 'utoken';

    //截取鉴权uri
    public static function getPureUri() {
        $uri = explode('?', Yii::$app->request->getUrl())[0];
        $prefix = '/'.Yii::$app->controller->id;

        empty(Yii::$app->controller->action) || $actionId ='/'. Yii::$app->controller->action->id;
        if(empty($actionId)) {
            $actionId = '/index';
        }
        if ( $uri == '/' || $uri==$prefix || $uri==$prefix.'/') {
            return $prefix.$actionId;
        } else if ( substr( $uri, 0, strlen($prefix) ) == $prefix ) {
            $subfix = substr($uri, strlen($prefix), strlen($uri));
            $subfix = ( empty($subfix) || $subfix == '/') ? $actionId : $subfix;
            $uri = $prefix.$subfix;
        }

        return $uri;
    }

    //进行鉴权
    public static function checkAccess($params = array()) {
        $token = array_key_exists(self::TOKEN, $_COOKIE) ? $_COOKIE[self::TOKEN] : null;
        if(empty($token)) return false;
        return AuthAdapter::getInstance()->checkAccess($token, self::getPureUri(), $params);
    }


    public static function redirectToLogin($message = '', $redirectUrl='') {
        empty($redirectUrl) && $redirectUrl = $_SERVER['SERVER_NAME'].self::getPureUri();
        header('Location:' .  AUTH_SERVER . '/site/login?' . 'redirect_url=' . urlencode($redirectUrl) . '&message=' . $message);
        exit;
    }

    public static function redirectToNotFind($redirectUrl='') {
        if(!empty($redirectUrl)) $redirect_url = $redirectUrl;
        else if(!empty($_SERVER['HTTP_REFERER'])) $redirect_url = $_SERVER['HTTP_REFERER'];
        else $redirect_url = $_SERVER['SERVER_NAME'].self::getPureUri();
        header('Location:' . AUTH_SERVER . '/site/notfind?' . 'redirect_url=' . urlencode($redirect_url));
        exit;
    }

}