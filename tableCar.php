<?php
namespace app\index\table;
use think\Db;

/**
 * Created by PhpStorm.
 * User: xiaojie
 * Date: 16/6/30
 * Time: 下午3:33
 */
class tableCar
{
    protected $tableName = 'car';
    protected $carno;
    protected $cardesc;
    protected $seatnum;

    /**
     * @return int 返回0表示添加成功,1表示失败
     */
    public function add()
    {
        $snum = ($this->seatnum==0)?35:$this->seatnum;
        $data = ['cardesc'=>$this->cardesc,'seatnum'=>$snum];
        $this->carno = Db::table($this->tableName)->insertGetId($data);
        $ret = ( 0 != $this->carno )?0:1;

        return $ret;
    }

    /**
     * @param $carno
     * @return int
     * @throws \think\Exception
     */
    public function update($carno) {
        $data = ['carno'=>$carno,'cardesc'=>$this->cardesc,'seatnum'=>$this->seatnum];
        $result = Db::table($this->tableName)->update($data);

        $ret = $result==1?0:1;

        return $ret;
    }

    /**
     * @param $carno
     * @return int
     * @throws \think\Exception
     */
    public function del($carno) {
        $result = Db::table($this->tableName)->delete($carno);
        return $result==1?0:1;

    }

    /**
     * @param $carno
     * @return int
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function find($carno) {
        $data = Db::table($this->tableName)->where('carno',$carno)->find();
        if($data) {
            $this->cardesc = $data['cardesc'];
            $this->seatnum = $data['seatnum'];

            return 0;
        }

        return 1;
    }

    public function findByDesc($desc) {
        $data = Db::table($this->tableName)->where('cardesc',$desc)->find();
        if($data) {
            $this->carno = $data['carno'];
            $this->cardesc = $data['cardesc'];
            $this->seatnum = $data['seatnum'];

            return 0;
        }

        return 1;
    }

    /**
     * bus constructor.
     */
    public function __construct()
    {
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
    public function getCardesc()
    {
        return $this->cardesc;
    }

    /**
     * @param mixed $cardesc
     */
    public function setCardesc($cardesc)
    {
        $this->cardesc = $cardesc;
    }

    /**
     * @return mixed
     */
    public function getSeatnum()
    {
        return $this->seatnum;
    }

    /**
     * @param mixed $seatnum
     */
    public function setSeatnum($seatnum)
    {
        $this->seatnum = $seatnum;
    }




}