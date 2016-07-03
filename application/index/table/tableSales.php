<?php
/**
 * Created by PhpStorm.
 * User: xiaojie
 * Date: 16/6/30
 * Time: 下午8:47
 */

namespace app\index\table;


use think\Db;

class tableSales
{
    protected $tableName = 'sales';

    public function addByIndex($carno,$ontime,$seatseq) {
        $index = sprintf("%07d%010d%03d",$carno,strtotime($ontime),$seatseq);
        $ret = Db::table($this->tableName)->insert($index);
        return $ret==1?0:1;
    }

    public function delByIndex($carno,$ontime,$seatseq) {
        $index = sprintf("%07d%010d%03d",$carno,strtotime($ontime),$seatseq);

        $ret = Db::table($this->tableName)->delete($index);

        return $ret==1?0:1;
    }

    public function countByCarDate($carno,$date) {



        $minIndex = sprintf("%07d%010d%03d",$carno,strtotime($date),0);
        $maxIndex = sprintf("%07d%010d%03d",$carno,strtotime($date."+1 day"),999);

        return Db::table($this->tableName)->where('index','>=',$minIndex)->where('index','<',$maxIndex)->count();
    }
}