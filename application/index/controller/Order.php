<?php
/**
 * Created by PhpStorm.
 * User: xiaojie
 * Date: 16/6/30
 * Time: 下午9:09
 */

namespace app\index\controller;


use app\index\table\table;
use app\index\table\tableOrder;
use app\index\table\tableSales;
use app\index\table\tableSchedule;
use app\index\table\tableSeatOrderStatus;
use app\index\table\tableUserOrder;
use think\controller\Rest;
use think\Validate;
use think\View;

class Order extends Rest
{
    protected $desc;
    protected $responseData = [];
    protected $payData = [];
//    public $url = 'http://localhost:8888';
    public $url = 'http://www.xjmiracle.com';

    /**
     * @return array
     */
    public function getPayData()
    {
        return $this->payData;
    }

    /**
     * @param array $payData
     */
    public function setPayData($payData)
    {
        $this->payData = $payData;
    }

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


    public function index(){}

    /**
     * @param $startPos
     * @param $endPos
     * @param $time
     * @param $seatNo
     * @return int 0-成功,1-请求方法,2-参数错误,3-没有对应车次,
     * 4-座位状态获取失败/不可购票,5-座位状态修改失败,6-订单添加失败
     */
    private function addSub() {
        switch($this->_method) {
            case 'post':
                $carno = input('post.carno');
                $startPos = input('post.startPos');
                $endPos = input('post.endPos');
                $time = input('post.onTime');
                $seatNo = input('post.seatNo');
                $user = input('post.userid');
                break;
            case 'get':
                $carno = input('get.carno');
                $startPos = input('get.startPos');
                $endPos = input('get.endPos');
                $time = input('get.onTime');
                $seatNo = input('get.seatNo');
                $user = input('get.userid');
                break;

            default:
                $this->setDesc("请求方法 $this->_method 不支持");
                return 1;

        }

        //-参数检查
        if($carno==null||$startPos==null||$endPos==null||$seatNo==null) {
            $this->setDesc("有空参数");
            return 2;//
        }

        //参数格式
        $rule = [
            'start'=>'require|max:50',
            'end'=>'require|max:50',
//            'time'=>'require|dateFormat:Y-m-d H:i:s',
            'seat'=>'require|max:50',
        ];

        $indata = [
            'start'=>$startPos,
            'end'=>$endPos,
//            'time'=>$time,
            'seat'=>$seatNo,
        ];

        $validate = new Validate($rule);
        if( !$validate->check($indata) ) {
            $this->setDesc("参数格式不对:".$validate->getError());
            return 2;
        }

        if($time == null) {
            $time = date('Y-m-d H:i:s');
        }

        $timenow = date('Y-m-d H:i:s');

        $timeMult = explode(" ",$time);
        $ondate = $timeMult[0];
        $ontime = $timeMult[1];

        if( strtotime($timenow) > strtotime($time) ) {
            $this->setDesc("当前时间是$timenow,您预订的车票时间是$time,已经过时");
            return 2;
        }


        //判断座位是否符合规定
        $seatMult = explode(",",$seatNo);
        $num = count($seatMult);
        if($num ==0 ||$num >35 ){
            $this->setDesc("座位参数错误:".$seatNo);
            return 2;
        }

        foreach ( $seatMult as $item) {
            if($item>35){
                $this->setDesc("座位序号 $item 超过上限");
                return 2;
            }
        }
        //sno选择
        $carSchedule = new tableSchedule();

        $ret = $carSchedule->findByCarTime($carno,$ontime);
        if($ret!=0) {
            $this->setDesc("车辆 $carno 在时间 $ontime 对应的车次没查询到");
            return 2;
        }

        $timenow = date('Y-m-d H:i:s');

        $sno = $carSchedule->getSno();

        //2.查询座位状态 1-才可以订购
        foreach ($seatMult as $item) {
            $tableStatus = new tableSeatOrderStatus();

            if(0== $tableStatus->queryStatusByIndex($sno,$ondate,$item) ) {
                if ($tableStatus->getStatus()==1) {
                    continue;
                }
                else if ($tableStatus->getStatus()==2){
                    $this->setDesc("座位序号 $item 已被锁定");
                    return 4;
                }
                else {
                    $this->setDesc("座位序号 $item 已订购 status:".$tableStatus->getStatus());
                    return 4;
                }
            }
            else {
                $this->setDesc("座位序号 $item 状态获取失败");
                return 4;
            }
        }

        //订票流程操作
        table::startTrans();//事务开始
        //1.座位状态改为2-表示锁定;
        foreach ($seatMult as $item) {
            $tableStatus = new tableSeatOrderStatus();
            if(0!=$tableStatus->updateByStatus($sno,$ondate,$item,2) ) {
                $this->setDesc("座位状态修改失败");
                table::rollback();
                return 5;
            }
        }


        //2.订单添加
        $tableOrder = new tableOrder();
        $tableOrder->setStatus(1);
        $tableOrder->setSno($sno);
        $tableOrder->setEndpos($endPos);
        $tableOrder->setOndate($ondate);
        $tableOrder->setOrderno(md5(time().mt_rand()));
        $tableOrder->setStartpos($startPos);
        $tableOrder->setSeatno($seatNo);
        $tableOrder->setCarno($carno);

        if(0!=$tableOrder->add()) {
            $this->setDesc("订单添加失败");
            table::rollback();
            return 6;
        }

        if($user) {
            $tableUserOrder = new tableUserOrder();
            $tableUserOrder->setUser($user);
            $tableUserOrder->setOrder($tableOrder->getOrderno());

            if( 0 != $tableUserOrder->add() ) {
                $this->setDesc("用户与订单绑定失败");
                table::rollback();
                return 6;
            }
        }

        table::commit();
        $this->setDesc("创建订单成功");

        $price = count($seatMult)*3;
        $codeUrl = urlencode($this->url.'/order/payGet?startPos='.$startPos.'&endPos='.$endPos.'&onTime='.$ontime.'&onDate='.$ondate.
            '&seatNo='.$seatNo.'&orderNo='.$tableOrder->getOrderno().'&price='.$price);

        $this->setResponseData(['carno'=>$carno,'startPos'=>$startPos, 'endPos'=>$endPos,'onTime'=>$time,'seatNo'=>$seatNo,'orderNo'=>$tableOrder->getOrderno(),'price'=>$price,'codeUrl'=>$codeUrl]);
//        print_r($this->getResponseData());

        return 0;
    }

    public function add() {

        $ret = $this->addSub();

        $retdesc = ['retCode'=>$ret,'desc'=>$this->desc];

        $data = array_merge($retdesc,$this->getResponseData());

        return $this->response($data,'json',200);

    }


    private function subPayGet() {

        switch($this->_method) {
            case 'post':
                $ontime = input('post.onTime');
                $ondate = input('post.onDate');
                $startpos = input('post.startPos');
                $endpos = input('post.endPos');
//                $seatnum = input('post.seatNum');
                $seatno = input('post.seatNo');
                $price = input('post.price');
                $orderno = input('post.orderNo');
                break;
            case 'get':
                $ontime = input('get.onTime');
                $ondate = input('get.onDate');
                $startpos = input('get.startPos');
                $endpos = input('get.endPos');
                $seatno = input('get.seatNo');
                $price = input('get.price');
                $orderno = input('get.orderNo');
                break;

            default:
                $this->setDesc("请求方法 $this->_method 不支持");
                return 1;

        }

        $seatMult = explode(",",$seatno);

        $this->setPayData(['ontime'=>$ontime,'startpos'=>$startpos,'endpos'=>$endpos,
            'ondate'=>$ondate,'seatno'=>$seatno,'seatnum'=>count($seatMult),'price'=>$price,'orderno'=>$orderno, 'url'=>$this->url]);

        return 0;

    }

    public function payGet() {
        $view = new View();
        if(0 == $this->subPayGet()) {
            return $view->fetch('pay',$this->getPayData());
        }

        print 'GET PAYINFO ERROR';

    }

    private function subPay() {

        switch($this->_method) {
            case 'post':
                $orderno = input('post.orderNo');
                break;
            case 'get':
                $orderno = input('get.orderNo');
                break;

            default:
                $this->setDesc("请求方法 $this->_method 不支持");
                return 1;

        }

        $tableOrder = new tableOrder();
        if( 0 != $tableOrder->find($orderno) ) {
            $this->setDesc("支付失败:订单不存在");
            return 2;
        }

        if( $tableOrder->getStatus() == 2 ) {//已经支付
            $this->setDesc("已经支付");
            return 0;
        }

        //查看订单是否超时
        $timeNow = date('Y-m-d H:i:s');

        if( strtotime($timeNow) - strtotime($tableOrder->getCreatetime()) > 30*60 ) {
            $this->setDesc("支付失败:超过30分钟没付款,订单无效");
            return 3;
        }

        table::startTrans();
        $tableSeatOrderStatus = new tableSeatOrderStatus();

        $seatMult = explode(",",$tableOrder->getSeatno());

        $tableSale = new tableSales();

        for ( $i = 0; $i < count($seatMult); $i++ ) {
            if ( 0 != $tableSeatOrderStatus->updateByStatus($tableOrder->getSno(),$tableOrder->getOndate(),$seatMult[$i],3) ) {
                $this->setDesc("支付失败:座位 $seatMult[$i] 状态更改失败");
                table::rollback();
                return 4;
            }
            if( 0 != $tableSale->addByIndex($tableOrder->getCarno(),$tableOrder->getOndate(),$seatMult[$i])) {
                $this->setDesc("支付失败:座位 $seatMult[$i] 统计状态更改失败");
                table::rollback();
                return 4;
            }
        }

        if ( 0 != $tableOrder->updateByOrderStatus($orderno,2) ) {
            $this->setDesc("支付失败:订单状态更改失败");
            table::rollback();
            return 5;
        }


        table::commit();

        $this->setDesc("支付成功");
        return 0;
    }

    public function pay() {

        $result = $this->subPay();

        $view = new View();

        return $view->fetch('payResult',['result'=>$result,'desc'=>$this->getDesc()]);

    }

    
//    public function payResult($result) {
//        $view = new View();
//
//        return $view->fetch('payResult',['result'=>$result,'desc'=>'支付失败']);
//    }

    private function subPayStatus() {
        switch($this->_method) {
            case 'post':
                $orderno = input('post.orderNo');
                break;
            case 'get':
                $orderno = input('get.orderNo');
                break;

            default:
                $this->setDesc("请求方法 $this->_method 不支持");
                return 1;

        }

        if($orderno == null) {
            $this->setDesc("orderNo 不能为空");
            return 2;
        }

        $tableOrder = new tableOrder();
        if ( 0 !=  $tableOrder->find($orderno) ) {
            $this->setDesc("订单 $orderno 查询失败");
            return 2;
        }

        $status = $tableOrder->getStatus();

        $this->setResponseData(['orderNo'=>$orderno,'status'=>$status]);
        $this->setDesc("订单状态查询成功");

        return 0;
    }

    public function payStatus() {

        $ret = $this->subPayStatus();

        $retdesc = ['retCode'=>$ret,'desc'=>$this->desc];

        $data = array_merge($retdesc,$this->getResponseData());

        return $this->response($data,'json',200);


    }



    public function get() {
        $ret = $this->subGet();

        $retdesc = ['retCode'=>$ret,'desc'=>$this->desc];

        $data = array_merge($retdesc,$this->getResponseData());

        return $this->response($data,'json',200);
    }

    private function subGet()
    {
        switch($this->_method) {
            case 'post':
                $orderno = input('post.orderNo');
                break;
            case 'get':
                $orderno = input('get.orderNo');
                break;

            default:
                $this->setDesc("请求方法 $this->_method 不支持");
                return 1;

        }

        if($orderno == null) {
            $this->setDesc("orderNo不允许为空");
            return 2;
        }

        $tableOrder = new tableOrder();
        if ( 0 !=  $tableOrder->find($orderno) ) {
            $this->setDesc("订单 $orderno 查询失败");
            return 3;
        }

        $this->setResponseData(['orderNo'=>$orderno,'carno'=>$tableOrder->getCarno(),'startPos'=>$tableOrder->getStartpos(),
        'endPos'=>$tableOrder->getEndpos(),'onTime'=>$tableOrder->getOndate(),'seatNo'=>$tableOrder->getSeatno(),
            'sno'=>$tableOrder->getSno(), 'status'=>$tableOrder->getStatus() ]);
        $this->setDesc("订单查询成功");

        return 0;

    }


}