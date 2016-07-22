<?php
/**
 * Created by PhpStorm.
 * User: xiaojie
 * Date: 16/6/30
 * Time: 下午6:01
 */

namespace app\index\table;


use think\Db;

class tableSchedule
{
    protected $tableName = 'schedule';
    protected $sno;
    protected $carno;
    protected $timestart;
    protected $timeend;

    /**
     * tableSchedule constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return int
     */
    public function add()
    {
        $data = ['carno'=>$this->carno,'timestart'=>$this->timestart,'timeend'=>$this->timeend];
        $this->sno = Db::table($this->tableName)->insertGetId($data);
        $ret = ( 0 != $this->sno )?0:1;

        return $ret;
    }

    /**
     * @param $sno
     * @return int
     */
    public function update($sno) {
        $data = ['sno'=>$sno,'carno'=>$this->carno,'timestart'=>$this->timestart,'timeend'=>$this->timeend];
        $result = Db::table($this->tableName)->update($data);

        $ret = $result==1?0:1;

        return $ret;
    }

    /**
     * @param $sno
     * @return int
     */
    public function del($sno) {
        $result = Db::table($this->tableName)->delete($sno);
        return $result==1?0:1;
    }

    /**
     * @param $sno
     * @return int
     */
    public function find($sno) {
        $data = Db::table($this->tableName)->where('sno',$sno)->find();
        if($data) {
            $this->carno = $data['carno'];
            $this->timestart = $data['timestart'];
            $this->timeend = $data['timeend'];
            $this->sno = $sno;

            return 0;
        }

        return 1;
    }

    /**
     * @param $carno
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function findByCar($carno) {
        $data = Db::table($this->tableName)->where('carno',$carno)->select();

        return $data;
    }

    /**
     * @param $carno
     * @param $time
     * @return int
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function findByCarTime($carno,$time) {
        echo '1111111#######</br>';
        $data = Db::table($this->tableName)->where('carno',$carno)->where('timestart','<=',$time)->where('timeend','>',$time)->find();
        echo '22222222#######</br>';
        if ($data) {
            $this->setSno($data['sno']);
            $this->setCarno($carno);
            $this->setTimestart($data['timestart']);
            $this->setTimeend($data['timeend']);
            echo '333333#######</br>';
            return 0;
        }
        echo '4444444#######</br>';
        return 1;
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
     * @return mixed
     */
    public function getTimestart()
    {
        return $this->timestart;
    }

    /**
     * @param mixed $timestart
     */
    public function setTimestart($timestart)
    {
        $this->timestart = $timestart;
    }

    /**
     * @return mixed
     */
    public function getTimeend()
    {
        return $this->timeend;
    }

    /**
     * @param mixed $timeend
     */
    public function setTimeend($timeend)
    {
        $this->timeend = $timeend;
    }




}