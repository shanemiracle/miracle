<?php
/**
 * Created by PhpStorm.
 * User: xiaojie
 * Date: 16/6/30
 * Time: 下午8:21
 */

namespace app\index\table;


use think\Db;

class seatRealStatus
{
    protected $tableName = 'seat_real_status';
    protected $index;//carno+seatseq
    protected $status;

    public function addByCarSeat($carno,$seatSeq) {
        $index = sprintf("%08d%04d",$carno,$seatSeq);

        $data = ['index'=>$index,'status'=>0];
        $ret = Db::table($this->tableName)->insert($data);

        return $ret==1?0:1;
    }

    public function delByCarSeat($carno,$seatSeq) {
        $index = sprintf("%08d%04d",$carno,$seatSeq);

        $ret = Db::table($this->tableName)->delete($index);

        return $ret==1?0:1;
    }

    public function updateByCarSeat($carno,$seatSeq,$status) {
        $index = sprintf("%08d%04d",$carno,$seatSeq);

        $data = ['index'=>$index,'status'=>$status];

        $ret = Db::table($this->tableName)->update($data);

        return $ret==1?0:1;
    }

    public function findByCarSeat($carno,$seatSeq) {
        $index = sprintf("%08d%04d",$carno,$seatSeq);

        $data = Db::table($this->tableName)->where('index',$index)->find();
        if($data) {
            $this->status = $data['status'];

            return 0;
        }

        return 1;
    }
}