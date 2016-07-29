<?php
/**
 * Created by PhpStorm.
 * User: xiaojie
 * Date: 16/6/30
 * Time: 下午8:21
 */

namespace app\index\table;


use think\Db;

class tableSeatRealStatus
{
    protected $tableName = 'seat_real_status';
    protected $index;//carno+seatseq
    protected $status;//0没被占用,1占用

    public function addByCarSeat($carno,$seatSeq) {
        $index = sprintf("%08d%04d",$carno,$seatSeq);

        $this->setIndex($index);
        $this->setStatus(0);

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

//        print $seatSeq.'|'.$status;
        $data = ['index'=>$index,'status'=>$status];

        $this->setStatus($status);
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

    public function queryByCarSeat($carno,$seatSeq) {
        $index = sprintf("%08d%04d",$carno,$seatSeq);

        $data = Db::table($this->tableName)->where('index',$index)->find();
        if($data) {
            $this->status = $data['status'];

            return 0;
        }
        else {
            return $this->addByCarSeat($carno,$seatSeq);
        }

        return 1;
    }

    /**
     * @return mixed
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * @param mixed $index
     */
    public function setIndex($index)
    {
        $this->index = $index;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }


}