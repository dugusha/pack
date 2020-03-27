<?php
/**
 * Created by PhpStorm.
 * User: gaoruishen
 * Date: 2020/3/25
 * Time: 3:17 PM
 */

namespace GRS;

use yii\base\Exception;

class Util
{
    /**
     * 二维数组按key分组
     */
    public static function array2KeyGroupArray($result, $key) {
        $list = array();
        foreach ($result as $item) {
            $list[$item[$key]][] = $item;
        }
        return $list;
    }

    /**
     * check null
     * @return bool
     */
    public static function checkEmpty($params,$keys){
        if(!is_array($keys))
            $keys = explode(',', $keys);
        foreach($keys as $k){
            if (!isset($params[$k])){
                throw new Exception($k.' 为空');
            }
        }
        return true;
    }

    /**
     * 二维数组排序
     */
    public static function myArraySort(&$array, $key, $asc=true) {
        uasort($array, function($v1, $v2) use ($asc, $key) {
            $result = $v1[$key] != $v2[$key] ? $v1[$key] > $v2[$key] ? 1 : -1 : 0;
            return $asc ? $result : -$result;
        });
    }
}