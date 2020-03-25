<?php
/**
 * Created by PhpStorm.
 * User: gaoruishen
 * Date: 2020/3/25
 * Time: 2:49 PM
 */
require_once __DIR__."/vendor/autoload.php";
use ZDZY\Util;
use ZDZY\Yii2\Rose;
$a = [
    ["a"=>2],
    ["a"=>3],
    ["a"=>1]
];

print_r($a);
Util::myArraySort($a,"a");
print_r($a);
Rose::desc();