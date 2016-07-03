<?php
/**
 * Created by PhpStorm.
 * User: xiaojie
 * Date: 16/7/1
 * Time: 上午9:49
 */

namespace app\index\controller;


use app\index\table\tableCar;
use app\index\table\tableSchedule;
use app\index\table\tableSeatOrderStatus;
use think\controller\Rest;
use think\Validate;

class Car extends Rest
{
    protected $desc;
    protected $carno;

    protected $sno;

    protected $responseData = [];

    /**
     * @return mixed
     */
    public function getResponseData()
    {
        return $this->responseData;
    }

    /**
     * @param mixed $responseData
     */
    public function setResponseData($responseData)
    {
        $this->responseData = $responseData;
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
    public function getDesc()
    {
        return $this->desc;
    }

    /**
     * @param mixed $desc
     */
    public function setDesc($desc)
    {
        $this->desc = $desc;
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
     * @param $cardesc
     * @param $seatnum
     * @return int 1-请求方法错误,2-请求参数错误
     */
    private function addSub() {
        switch($this->_method) {
            case 'post':
                $cardesc = input('post.cardesc');
                $seatnum = input('post.seatnum');
                break;
            case 'get':
                $cardesc = input('get.cardesc');
                $seatnum = input('get.seatnum');
                break;

            default:
                $this->setDesc("请求方法必须是post");
                return 1;
        }

        if($cardesc==null||$seatnum==null) {
            $this->setDesc("有空参数");
            return 2;
        }

        if(strlen($cardesc)>50) {
            $this->setDesc("车牌号超过50");
            return 2;
        }

        if($seatnum > 300) {
            $this->setDesc("座位数太大,不符合实际");
            return 2;
        }

        $car = new tableCar();
        $car->setCardesc($cardesc);
        $car->setSeatnum($seatnum);

        $ret = $car->add();
        if( $ret != 0 ) {
            $this->setDesc("写入数据库失败");
            return 3;
        }

        $this->setCarno($car->getCarno());
        $carno = $car->getCarno();
        $this->setDesc("创建车辆成功");
        return 0;
    }

    public function add() {

        $ret = $this->addSub();

        return $this->response(['retCode'=>$ret,'desc'=>$this->getDesc(),'carno'=>$this->getCarno()],'json',200);
    }

    public function scheduleAdd( ) {
        $ret = $this->saddSub();

        $retDesc = ['retCode'=>$ret,'desc'=>$this->getDesc()];

        $data = array_merge($retDesc,$this->getResponseData());

        return $this->response($data,'json',200);

    }

    private function saddSub()
    {
        switch($this->_method) {
            case 'post':
                $carno = input('post.carno');
                $start = input('post.starttime');
                $end = input('post.endtime');

            break;

            case 'get':
                $carno = input('get.carno');
                $start = input('get.starttime');
                $end = input('get.endtime');
                break;


            default:
                $this->setDesc("请求方法 $this->_method 不支持");
                return 1;
        }

        if($carno==null||$start==null||$end==null) {
            $this->setDesc("有空参数");
            return 2;
        }
        $rule=[
            'start'=>'require|dateFormat:H:i:s',
            'end'=>'require|dateFormat:H:i:s'
        ];

        $checkdata = ['start'=>$start,'end'=>$end];
        $valid = new Validate($rule);
        if ( !$valid->check($checkdata,$rule) ) {
            $this->setDesc("时间格式错误");
            return 2;
        }

        $tableSche = new tableSchedule();
        $tableSche->setCarno($carno);
        $tableSche->setTimestart($start);
        $tableSche->setTimeend($end);

        $ret = $tableSche->add();

        if($ret != 0) {
            $this->setDesc("添加记录失败");
            return 3;
        }

        $this->setSno($tableSche->getSno());
        $this->setDesc("添加记录成功");

        $this->setResponseData(['carno'=>$carno,'starttime'=>$start,'endtime'=>$end,'sno'=>$this->getSno()]);

        return 0;
    }

    private function subOrderStatus() {
        switch($this->_method) {
            case 'post':
                $carno = input('post.carno');
                $time = input('post.time');

                break;

            case 'get':
                $carno = input('get.carno');
                $time = input('get.time');
                break;

            default:
                $this->setDesc("请求方法 $this->_method 不支持");
                return 1;
        }

        if($carno==null) {
            $this->setDesc("carno 不能为空");
            return 2;
        }

        $valid = new Validate();

        if($time) {
            if(true != $valid->check(['time'=>$time],['time'=>'dateFormat:Y-m-d H:i:s'])) {
                $this->setDesc("time 格式需要满足YYYY-MM-DD HH:mm:ss");
                return 2;
            }

            $timeMult = explode(" ", $time);

            $onDate = $timeMult[0];
            $onTime = $timeMult[1];
        }
        else {
            $onDate = date('Y-m-d');
            $onTime = date('H:i:s');
        }


        $car = new tableCar();
        $car->find($carno);

        $tableSchedule = new tableSchedule();
        if( 0 != $tableSchedule->findByCarTime($carno,$onTime) ) {
            $this->setDesc("carno $carno 在 $onTime 时间点没有车次");
            return 3;
        }

        $sno = $tableSchedule->getSno();

        $seatStatus = new tableSeatOrderStatus();

        $allSeatStatus = '';

        for ( $i = 1; $i <= $car->getSeatnum(); $i++ ) {
            if( 0 != $seatStatus->queryStatusByIndex($sno,$onDate,$i) ) {
                $this->setDesc("carno $carno 车次 $sno 对应座位 $i 状态查询异常");
                return 3;
            }

            if( $i != $car->getSeatnum() ) {
                $allSeatStatus = $allSeatStatus.$i.'|'.$seatStatus->getStatus().',';
            }
            else {
                $allSeatStatus = $allSeatStatus.$i.'|'.$seatStatus->getStatus();
            }
        }

        $this->setResponseData(['carno'=>$carno,'sno'=>$sno,'onTime'=>$onTime,'seatStatus'=>$allSeatStatus]);

        $this->setDesc("获取状态成功");

        return 0;

    }

    public function orderStatus() {

        $ret = $this->subOrderStatus();

        $retDesc = ['retCode'=>$ret,'desc'=>$this->getDesc()];

        $data = array_merge($retDesc,$this->getResponseData());

        return $this->response($data,'json',200);
    }



    public function realStatus() {

    }




}