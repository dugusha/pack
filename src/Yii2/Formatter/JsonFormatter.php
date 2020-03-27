<?php
/**
 * Created by PhpStorm.
 * User: gaoruishen
 * Date: 2020/3/27
 * Time: 1:36 PM
 */
namespace GRS\Yii2\Formatter;

use yii\web\JsonResponseFormatter;

class JsonFormatter extends JsonResponseFormatter {
    /**
     * 格式化为兼容apiadapter的格式
     * @param \yii\web\Response $response
     */
    protected function formatJson($response) {
        if(!isset($response->data['ret'])) {
            if(isset($response->data['__exception__'])) {
                unset($response->data['__exception__']);
                $code = empty($response->data["code"])?-1:$response->data["code"];
                $response->data = ['code'=>$code,'msg'=>$response->data["msg"],'data'=>$response->data];
            } else {
                $response->data = ['code'=>0,'msg'=>'','data'=>$response->data];
            }
        }
        parent::formatJson($response);
    }

}