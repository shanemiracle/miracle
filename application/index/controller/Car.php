<?php
/**
 * Created by PhpStorm.
 * User: xiaojie
 * Date: 16/7/1
 * Time: 上午9:49
 */

namespace app\index\controller;


use app\index\table\table;
use app\index\table\tableCar;
use app\index\table\tableSales;
use app\index\table\tableSchedule;
use app\index\table\tableSeatOrderStatus;
use app\index\table\tableSeatRealStatus;
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
        $ret = $car->find($carno);
        if ( 0 != $ret ) {
            $this->setDesc("carno $carno 不存在");
            return 3;
        }


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



    private function subRealStatus() {
        switch($this->_method) {
            case 'post':
                $carno = input('post.carno');
                break;

            case 'get':
                $carno = input('get.carno');
                break;

            default:
                $this->setDesc("请求方法 $this->_method 不支持");
                return 1;
        }

        if($carno==null) {
            $this->setDesc("carno 不能为空");
            return 2;
        }

        $car = new tableCar();
        if ( 0 != $car->find($carno) ) {
            $this->setDesc("carNo $carno 不存在");
            return 3;
        }
        
        $tableReal = new tableSeatRealStatus();

        $allRealStatus = '';
        for ( $i = 1; $i <= $car->getSeatnum(); $i++) {
            if ( 0 != $tableReal->queryByCarSeat($carno, $i) ) {
                $this->setDesc("carNo $carno 座位 $i 状态查询异常");
                return 3;
            }
            else {
                if( $i != $car->getSeatnum() ) {
                    $allRealStatus = $allRealStatus.$i.'|'.$tableReal->getStatus().',';
                }
                else {
                    $allRealStatus = $allRealStatus.$i.'|'.$tableReal->getStatus();
                }
            }
        }
        $this->setResponseData(['carno'=>$carno,'realStatus'=>$allRealStatus]);

        $this->setDesc("获取成功");
        return 0;

    }

    
    public function realStatus() {

        $ret = $this->subRealStatus();

        $retDesc = ['retCode'=>$ret,'desc'=>$this->getDesc()];

        $data = array_merge($retDesc,$this->getResponseData());

        return $this->response($data,'json',200);

    }


    private function subRealStatusUpdate() {
        switch($this->_method) {
            case 'post':
                $carno = input('post.carno');
                $status = input('post.status');
                break;

            case 'get':
                $carno = input('get.carno');
                $status = input('get.status');
                break;

            default:
                $this->setDesc("请求方法 $this->_method 不支持");
                return 1;
        }

        if($carno==null||$status==null) {
            $this->setDesc("carno status 都不能为空,参考格式:http://www.xjmiracle.com/car/realStatusUpdate?carno=13&status=1|1,2|0");
            return 2;
        }

        $statusMul = explode(",",$status);
        if( $statusMul == null || count($statusMul) == 0) {
            $this->setDesc("要修改的座位数不能为0 格式如:1|0,2|0,3|1,...");
            return 2;
        }

        $car = new tableCar();
        if ( 0 != $car->find($carno) ) {
            $this->setDesc("carNo $carno 不存在");
            return 3;
        }

        $tableReal = new tableSeatRealStatus();

        table::startTrans();

        for ( $i = 0; $i < count($statusMul); $i++) {
            $snoAndseat = explode('|',$statusMul[$i]);
//            print '#'.count($snoAndseat).'|'.$snoAndseat[1].'|'.$snoAndseat[0];
            if($snoAndseat ==null || count($snoAndseat) != 2 ) {
                $this->setDesc("座位格式不对 格式如:1|0,2|0,3|1,...10,0");
                table::rollback();
                return 2;
            }

            $num = $car->getSeatnum();

            if($snoAndseat[0] > $num ) {
                $this->setDesc("座位 $snoAndseat[0] 超过总的座位数 $num ");
                table::rollback();
                return 2;
            }

            if($snoAndseat[1] == null || ($snoAndseat[1] != 0 && $snoAndseat[1] != 1)) {
                $this->setDesc("座位 $snoAndseat[0] 要修改的状态 $snoAndseat[1] 不支持。 状态0-没占用,1-占用 ");
                table::rollback();
                return 2;
            }

            if ( 0 != $tableReal->queryByCarSeat($carno,$snoAndseat[0]) ) {
                $this->setDesc("座位 $snoAndseat[0] 获取失败");
                table::rollback();
                return 2;
            }

            if ( $tableReal->getStatus() != $snoAndseat[1]) {
                if ( 0 != $tableReal->updateByCarSeat($carno,$snoAndseat[0],$snoAndseat[1]) ) {
                    $this->setDesc("座位 $snoAndseat[0] 状态$snoAndseat[1] 修改失败");
                    table::rollback();
                    return 2;
                }
            }
        }

        table::commit();
        $this->setResponseData(['carno'=>$carno,'status'=>$status]);

        $this->setDesc("修改状态成功");
        return 0;

    }


    public function realStatusUpdate() {

        $ret = $this->subRealStatusUpdate();

        $retDesc = ['retCode'=>$ret,'desc'=>$this->getDesc()];

        $data = array_merge($retDesc,$this->getResponseData());

        return $this->response($data,'json',200);

    }


    private function subSaleCount() {
        switch($this->_method) {
            case 'post':
                $carno = input('post.carno');
                $date = input('post.date');
                break;

            case 'get':
                $carno = input('get.carno');
                $date = input('get.date');
                break;

            default:
                $this->setDesc("请求方法 $this->_method 不支持");
                return 1;
        }
        if($carno == null) {
            $this->setDesc("carno 不能为空");
            return 2;
        }

        if($date) {
            $valid = new Validate();

            if( true != $valid->check (['date'=>$date],['date'=>'dateFormat:Y-m-d']) ) {
                $this->setDesc("日期 $date 格式不对: YYYY-MM-DD");
                return 2;
            }
        }
        else{
            $date = date('Y-m-d');
        }

        $tableSale = new tableSales();
        $saleNum = $tableSale->countByCarDate($carno,$date);
        print_r($saleNum);
//        if( $saleNum == -1 ) {
            $this->setDesc("查询失败");
            return 2;
//        }
//
//        $saleCount = $saleNum*3;
//        $this->setResponseData(['carno'=>$carno,'date'=>$date,'saleNum'=>$saleNum,'saleCount'=>$saleCount.'.00']);
//
//        $this->setDesc("查询成功");
//        return 0;


    }

    public function saleCount() {
        $ret = $this->subSaleCount();

        $retDesc = ['retCode'=>$ret,'desc'=>$this->getDesc()];

        $data = array_merge($retDesc,$this->getResponseData());

        return $this->response($data,'json',200);
    }




}