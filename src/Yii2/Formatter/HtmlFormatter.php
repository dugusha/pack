<?php
/**
 * Created by PhpStorm.
 * User: gaoruishen
 * Date: 2020/3/27
 * Time: 1:37 PM
 */

namespace GRS\Yii2\Formatter;

use yii\web\HtmlResponseFormatter;
use yii\helpers\Json;

class HtmlFormatter extends HtmlResponseFormatter {

    public function format($response) {
        parent::format($response);
        if(is_array($response->content)) {
            if(isset($response->content['ret'])) {
                $response->content = Json::encode($response->content);
            } else {
                $response->content = Json::encode(['code'=>0,'msg'=>'','data'=>$response->data]);
            }

        } else if (is_object($response->content)) {
            if (method_exists($response->content, '__toString')) {
                $this->content = $response->content->__toString();
            } else {
                $response->content = Json::encode(['code'=>0,'msg'=>'','data'=>$response->data]);
            }
        }
    }

}