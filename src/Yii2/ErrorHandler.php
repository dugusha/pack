<?php
/**
 * Created by PhpStorm.
 * User: gaoruishen
 * Date: 2020/3/27
 * Time: 3:40 PM
 */

namespace GRS\Yii2;

use Yii;
use yii\base\UserException;
use yii\web\Response;

class ErrorHandler extends \yii\web\ErrorHandler
{
    public function __construct(array $config = [])
    {
        parent::__construct($config);
    }

    /**
     * 格式化为apiAdapter兼容的格式
     * @param \Exception $exception
     */
    protected function renderException($exception) {

        Yii::error([
            'msg'=>'未捕获异常:'.$exception->getMessage(),
            'type'=>get_class($exception),
            'code'=>$exception->getCode(),
            'trace_stack'=>$exception->getTraceAsString()
        ]);

        if(Yii::$app->has('request')) {
            $request = Yii::$app->getRequest();
            $types = $request->getAcceptableContentTypes();
            reset($types);
            if(key($types) != 'application/json' && (isset($types['*/*']) && !$request->getIsAjax())) {
                parent::renderException($exception);
                return;
            }
        }

        if (Yii::$app->has('response')) {
            $response = Yii::$app->getResponse();
            // reset parameters of response to avoid interference with partially created response data
            // in case the error occurred while sending the response.
            $response->isSent = false;
            $response->stream = null;
            $response->data = null;
            $response->content = null;
        } else {
            $response = new Response();
        }
        $response->format = Response::FORMAT_JSON;
        if(!defined('YII_DEBUG') && !($exception instanceof UserException)) {
            $exception = new UserException('Unknown Error:未知错误','500',$exception);
        }
        $error = $this->convertExceptionToArray($exception,false);
        $response->data = ['code'=>-1, 'error'=>$error,'data'=>(object)[]];
        $response->setStatusCode(200,Response::$httpStatuses['200']);
        $response->send();
    }

    protected function convertExceptionToArray($exception,$exceptionJudge=true) {
        $result =  parent::convertExceptionToArray($exception);
        if($exceptionJudge) {
            $result['__exception__'] = 'true';
        }
        $result['msg'] = $result['message'];
        unset($result['message']);
        return $result;
    }


}