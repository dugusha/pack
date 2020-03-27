<?php
/**
 * Created by PhpStorm.
 * User: gaoruishen
 * Date: 2020/3/27
 * Time: 11:18 AM
 */

namespace GRS\Yii2;

use yii\db\ActiveQuery;
use yii\db\Connection;

class Page extends ActiveQuery{
    /**
     * @param int|PageCtl $page|$pageCtl
     * @param int|\yii\db\Connection $pageSize|$db
     * @param \yii\db\Connection $db
     * @return array
     */
    public function paginate($page=1,$pageSize=20,$db = null) {
        //初始化参数
        $pageCtl = new PageCtl();
        $db = null;

        //获取参数
        $args = func_get_args();
        $args_int_pos = 0;
        foreach($args as $arg) {
            if(empty($arg))  continue;
            //用pageCtl初始化分页信息
            if($arg instanceof PageCtl) $pageCtl = $arg;
            //用数字参数初始化分页信息
            else if(is_numeric($arg)) {
                $args_int_pos || $pageCtl->setPage($arg);
                $args_int_pos && $pageCtl->setPageSize($arg);
                $args_int_pos ++;
            }
            //初始化db信息
            else if($arg instanceof Connection) $db = $arg;
        }
        $page = $pageCtl->getPage() - 1;
        $pageSize = $pageCtl->getPageSize();
        $count = $this->count(1,$db);
        $offset = $page * (int) $pageSize;
        $data = $this->limit($pageSize)->offset($offset)->all($db);
        return $pageCtl->formatResult($count,$data);
    }
}