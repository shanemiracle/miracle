<?php
/**
 * Created by PhpStorm.
 * User: xiaojie
 * Date: 16/6/30
 * Time: 下午7:16
 */

namespace app\index\table;


use think\Db;

class tableOrder
{
    protected $tablename = 'order';

    protected $orderno;
    protected $status;//1.未支付,2已支付
    protected $ondate;
    protected $sno;
    protected $seatno;
    protected $createtime;
    protected $startpos;
    protected $endpos;
    protected $carno;

    /**
     * @return mixed
     */
    public function getCarno()
    {
        return $this->carno;
    }

    /**
     * @param mixed $carno
     */
    public function setCarno($carno)
    {
        $this->carno = $carno;
    }


    /**
     * order constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return int
     */
    public function add()
    {
        $data = ['orderno'=>$this->orderno,'status'=>$this->status,
            'ondate'=>$this->ondate,'sno'=>$this->sno,'seatno'=>$this->seatno,
            'startpos'=>$this->startpos,'endpos'=>$this->endpos, 'carno'=>$this->carno];

        $result = Db::table($this->tablename)->insert($data);

        return $result==1?0:1;
    }


    public function update() {
        $data = ['orderno'=>$this->orderno,'status'=>$this->status,
            'ondate'=>$this->ondate,'sno'=>$this->sno,'seatno'=>$this->seatno,
            'startpos'=>$this->startpos,'endpos'=>$this->endpos];
        $result = Db::table($this->tablename)->update($data);

        $ret = $result==1?0:1;

        return $ret;
    }

    public function updateByOrderStatus($orderno,$status) {
        $data = ['orderno'=>$orderno,'status'=>$status];
        $result = Db::table($this->tablename)->update($data);

        $ret = $result==1?0:1;

        return $ret;
    }

    public function del($orderno) {
        $result = Db::table($this->tablename)->delete($orderno);
        return $result==1?0:1;
    }


    public function find($orderno) {
        echo $orderno;
        $data = Db::table($this->tablename)->where('orderno',$orderno)->find();
        if($data) {
            $this->status = $data['status'];
            $this->ondate = $data['ondate'];
            $this->sno = $data['sno'];
            $this->seatno = $data['seatno'];
            $this->createtime = $data['createtime'];
            $this->orderno = $data['orderno'];
            $this->startpos = $data['startpos'];
            $this->endpos = $data['endpos'];
            $this->carno = $data['carno'];

            return 0;
        }

        return 1;
    }



    /**
     * @return mixed
     */
    public function getOrderno()
    {
        return $this->orderno;
    }

    /**
     * @param mixed $orderno
     */
    public function setOrderno($orderno)
    {
        $this->orderno = $orderno;
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

    /**
     * @return mixed
     */
    public function getOndate()
    {
        return $this->ondate;
    }

    /**
     * @param mixed $ondate
     */
    public function setOndate($ondate)
    {
        $this->ondate = $ondate;
    }

    /**
     * @return mixed
     */
    public function getSno()
    {
        return $this->sno;
    }

    /**
     * @param mixed $sno
     */
    public function setSno($sno)
    {
        $this->sno = $sno;
    }

    /**
     * @return mixed
     */
    public function getSeatno()
    {
        return $this->seatno;
    }

    /**
     * @param mixed $seatno
     */
    public function setSeatno($seatno)
    {
        $this->seatno = $seatno;
    }

    /**
     * @return mixed
     */
    public function getCreatetime()
    {
        return $this->createtime;
    }

    /**
     * @param mixed $createtime
     */
    public function setCreatetime($createtime)
    {
        $this->createtime = $createtime;
    }

    /**
     * @return mixed
     */
    public function getStartpos()
    {
        return $this->startpos;
    }

    /**
     * @param mixed $startpos
     */
    public function setStartpos($startpos)
    {
        $this->startpos = $startpos;
    }

    /**
     * @return mixed
     */
    public function getEndpos()
    {
        return $this->endpos;
    }

    /**
     * @param mixed $endpos
     */
    public function setEndpos($endpos)
    {
        $this->endpos = $endpos;
    }




}