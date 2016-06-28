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

class Order extends Rest
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
        if( $order) {
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
        else {
            $ret = 2;
            $desc = "请输入订单号";
        }

        $data = ["orderNo"=>$order,"retCode"=>$ret,"desc"=>$desc];
        $icon = ($ret==1)?"weui_icon_success":"weui_icon_warn";
//        return $this->response($data,"json",200);

        $ticks = ($ret==1)?"感谢使用,祝您出行愉快":"很抱歉,您需要重新扫描二维码支付";
        return   '<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1" charset="UTF-8" />
<title>支付结果</title>

</head>
<body>
<div class="weui_msg">
			<div class="weui_icon_area"><i class=$icon." weui_icon_msg"></i></div>
		    <div class="weui_text_area">
		        <h2 class="weui_msg_title">$desc</h2>
		        <p class="weui_msg_desc">$ticks</p>
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
        $price = $seatNum;
        $codeUrl="www.xjmiracle.com/pay?startPos=$startPos&endPos=$endPos&onTime=$time&seatNum=$seatNum&orderNo=$orderNo&price=$price";
//        $codeUrl="http://192.9.60.133:8080/soyea_busdemo/view/pay.html?startPos=$startPos&endPos=$endPos&onTime=$time&seatNum=$seatNum&orderNo=$orderNo&price=$price";


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