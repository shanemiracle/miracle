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
        $data=['index'=>$index];
        $ret = Db::table($this->tableName)->insert($data);
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

//        print 'min'.$minIndex.'max'.$maxIndex;
//        echo '<br>';where 'index' >= '$minIndex' and 'index' < '$maxIndex'

        $data =  Db::query("select count(*) as num from bus.$this->tableName where 'index' >= '$minIndex' and 'index' < '$maxIndex' ");
        print_r($data);
        if ( $data ) {
            return $data[0]['num'];
        }

        return -1;

//        return Db::table($this->tableName)->where('index','>=',$minIndex)->where('index','<',$maxIndex)->count();
    }
}