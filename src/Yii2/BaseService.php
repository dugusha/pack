<?php
/**
 * Created by PhpStorm.
 * User: gaoruishen
 * Date: 2020/3/25
 * Time: 4:54 PM
 */

namespace GRS\Yii2;

use Yii;
use yii\base\Component;

class BaseService extends Component{
    /**
     * 单例
     * @return static
     */
    public static function  getInstance() {
        $obj = Yii::createObject(static::className());
        if(!Yii::$container->hasSingleton(static::className())) {
            Yii::$container->setSingleton(static::className(),$obj);
        }
        return $obj;
    }
}