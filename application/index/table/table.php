<?php
/**
 * Created by PhpStorm.
 * User: xiaojie
 * Date: 16/6/30
 * Time: 下午6:58
 */

namespace app\index\table;


use think\Db;

class table
{
    public static function startTrans() {
        Db::startTrans();
    }

    public static function commit() {
        Db::commit();
    }

    public static function rollback() {
        Db::rollback();
    }


}