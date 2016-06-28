<?php
/**
 * Created by PhpStorm.
 * User: xiaojie
 * Date: 16/6/27
 * Time: 下午6:00
 */

namespace app\index\controller;


use think\controller\Rest;

class Code extends Rest
{
    public function index() {
        $startPos = input('get.startPos');
        $endPos = input('get.endPos');
        $time = input('get.time');
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

        $OrderNo = md5(time());
        $Price = count($num)*3;
        $codeUrl="www.xjmiracle.com";

        $more = ['retCode'=>$retCode,'orderNo'=>$OrderNo,'price'=>$Price,'codeUrl'=>$codeUrl];

        $data = array_merge($ret,$more);

        return $this->response($data,"json",200);

    }

}