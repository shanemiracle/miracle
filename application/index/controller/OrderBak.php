<?php
/**
 * Created by PhpStorm.
 * User: xiaojie
 * Date: 16/6/27
 * Time: 下午6:00
 */

namespace app\index\controller;


use think\controller\Rest;
use think\Db;

class OrderBak extends Rest
{
    public function index() {

    }

    public function status() {
        $order = input('get.orderNo');
        if($order) {
            $result = Db::table("order")->where("orderno",$order)->select();
            if( $result != null) {
                $ret = ($result[0]['status']==1)?1:2;
                $desc = ($ret==1)?"已支付":"未支付";
            }
            else {
                $ret = 2;
                $desc = "无此订单";
            }
        }
        else {
                $ret = 2;
                $desc = "请输入订单号";
        }

//       print_r($result);


        return $this->response(['orderNo'=>$order,'retCode'=>$ret,'desc'=>$desc],'json',200);

    }

    public function pay() {
        $order = input('get.orderNo');
        $desc = '';
        $ret = 0;
        if( $order) {
            $orderInfo = Db::table("order")->where('orderno',$order)->select();

            if($orderInfo) {
                $time = $orderInfo[0]['ontime'];

                $timeArray = explode(" ",$time);
                if( $timeArray != null ) {
                    $timeBig = $timeArray[0];
                    $timeSmall = $timeArray[1];
                }

                $seat = $orderInfo[0]['seatno'];
                $allSeat = explode(",",$seat);

                $start = "'".$timeSmall."'";

                $result = Db::query("select carno from bus.car where (timestart<= $start and $start <= timeend )");
                if($result) {
                    $carNo = $result[0]['carno'];

                    if($carNo) {
//                        print "carNo".$carNo;
                        $seatStatus = '';
                        $carInfo = Db::table('car')->where('carNo',$carNo)->select();
                        if ( $carInfo ) {
                            $status = $carInfo[0]['seatstatus'];
                            for($i = 0; $i < count($allSeat);$i++) {
                                if ( $allSeat[$i] > 30 ) {
                                    $ret = 2;

//                                    print "车票锁定";
                                    break;
                                }

                                if($status[$allSeat[$i]-1] == 0) {
                                    $status[$allSeat[$i]-1] = '1';
                                }
                                else {
                                    $ret = 2;

//                                    print "车票锁定";
                                    break;
                                }
                            }

                            if($ret != 2) {
                                print $status;
                                if( 1 != Db::table('car')->where('carNo',$carNo)->update(['seatstatus'=>$status]) ) {
                                    $ret = 2;
//                                    print "座位状态更新失败";
                                }
                            }
                        }
                        else {
                            $ret = 2;
//                            print $carInfo." 不存在 ";
                        }
                    }
                    else{
                        $ret = 2;
//                        print "car not ";
                    }

                }
                else
                {
                    $ret = 2;
//                    print "time not ";
                }


            }
            else {
                $ret = 2;
//                print "order not ";
            }


               if( $ret != 2) {
                   if( 1 == Db::table("order")->update(['orderno'=>$order,'status'=>1]) )
                   {
                       $ret = 1;
                       $desc = "支付成功";
                   }
//        $queryData = Db::table("order")->where("orderno",$order)->select();

                   else
                   {
                       $ret = 2;
                       $desc = "支付失败";
                   }
               }

        }
        else {
            $ret = 2;
            $desc = "请输入订单号";
        }

//        $data = ["orderNo"=>$order,"retCode"=>$ret,"desc"=>$desc];
//        print_r($data);
        $icon = "\"".($ret==1)?"weui_icon_success":"weui_icon_warn";
//        return $this->response($data,"json",200);

        $ticks = ($ret==1)?"感谢使用,祝您出行愉快":"很抱歉,您需要重新扫描二维码支付";

        if( $ret == 1) {
            return   '<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1" charset="UTF-8" />

<title>支付结果</title>

</head>
<body>
<div class="weui_msg">
			<div class="weui_icon_area"><i class="weui_icon_success weui_icon_msg"></i></div>
		    <div class="weui_text_area">
		        <h2 class="weui_msg_title">支付成功</h2>
		        <p class="weui_msg_desc">感谢使用,祝您出行愉快</p>
		    </div>
		<div class="weui_opr_area">
	        <p class="weui_btn_area">
	            <a href="javascript:;" class="weui_btn weui_btn_primary">关闭</a>
	        </p>
	    </div>
</div>
</body>
</html>';
        }
        else{
            return   '<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1" charset="UTF-8" />

<title>支付结果</title>

</head>
<body>

<div class="weui_msg">
			<div class="weui_icon_area"><i class="weui_icon_warn weui_icon_msg"></i></div>
		    <div class="weui_text_area">
		        <h2 class="weui_msg_title">支付失败</h2>
		        <p class="weui_msg_desc">很抱歉,您需要重新扫描二维码支付</p>
		    </div>
		<div class="weui_opr_area">
	        <p class="weui_btn_area">
	            <a href="javascript:;" class="weui_btn weui_btn_primary">关闭</a>
	        </p>
	    </div>
</div>
</body>
</html>';
        }


    }

    public function add() {
        $startPos = input('get.startPos');
        $endPos = input('get.endPos');
        $time = input('get.onTime');
        $seatNo = input('get.seatNo');

        $num = 0;

        if ($seatNo != null) {
            $num  = explode(",",$seatNo);
        }

        $ret = ['startPos'=>$startPos,'endPos'=>$endPos,'onTime'=>$time,'seatNo'=>$seatNo];

        if($startPos==null||$endPos==null||$time==null||$seatNo==null) {
            $retCode = 2;
        }
        else {
            $retCode = 1;
        }

        $orderNo = md5(time());
        $seatNum = count($num);
        $price = $seatNum*3;
        $codeUrl="http://www.xjmiracle.com/pay?startPos=$startPos&endPos=$endPos&onTime=$time&seatNum=$seatNum&orderNo=$orderNo&price=$price";
//        $codeUrl="http://192.9.60.133:8080/soyea_busdemo/view/pay.html?startPos=$startPos&endPos=$endPos&onTime=$time&seatNum=$seatNum&orderNo=$orderNo&price=$price";
        $codeUrl = urlencode($codeUrl);


        if ($retCode==1) {

            $tableData = ['orderno'=>$orderNo,'status'=>0,'startpos'=>$startPos,'endpos'=>$endPos,'ontime'=>$time,'seatno'=>$seatNo,'seatnum'=>count($num),'price'=>$price];
            if( 1 != Db::table('order')->insert($tableData) ) {
                $retCode = 2;
            }
//            $tableData = Db::table("order")->where("orderNo","123")->select();
//            $retCode = $tableData[0];

        }

        $more = ['retCode'=>$retCode,'orderNo'=>$orderNo,'price'=>$price,'codeUrl'=>$codeUrl];

        $data = array_merge($ret,$more);

        return $this->response($data,"json",200);

    }

    public function count() {
        $queryTime = input('get.bizDate');
        $start = "'".$queryTime." 00:00:00'";
        $end = "'".$queryTime." 23:59:59'";

        $result = Db::query("select status,seatnum,price from bus.order where (createtime>=$start and createtime <= $end)");

        $seatNum = 0;
        $cost = 0;

        if($result != null) {
            $retCode = 1;
            for($i=0;$i<count($result);$i++) {
                $d = $result[$i];
                if($d['status'] == 1) {
                    $seatNum += $d['seatnum'];
                    $cost += $d['price'];
                }
            }
        }
        else {
            $retCode = 2;
        }

        $data = ['bizDate'=>$queryTime,'retCode'=>$retCode,'members'=>$seatNum,'sales'=>$cost];
        return $this->response($data,"json",200);


    }

}