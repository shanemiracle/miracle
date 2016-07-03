<?php
/**
 * Created by PhpStorm.
 * User: xiaojie
 * Date: 16/6/28
 * Time: 下午2:58
 */

namespace app\index\controller;


class PayBak
{
    public function index() {
        $startPos = input('get.startPos');
        $endPos = input('get.endPos');
        $time = input('get.onTime');
        $seatNo = input('get.seatNo');
        $price = input('get.price').".00";
        $seatNum = input('get.seatNum');
        $orderNo = input('get.orderNo');


        $timeArray = explode(" ",$time);
        if( $timeArray != null ) {
            $date = $timeArray[0];
            $timeMin = $timeArray[1];
        }


        return "";
    }
}