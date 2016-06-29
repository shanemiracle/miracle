<?php
/**
 * Created by PhpStorm.
 * User: xiaojie
 * Date: 16/6/28
 * Time: 下午2:25
 */

namespace app\index\controller;


use think\controller\Rest;
use think\Db;

class Car extends Rest
{
    public function index() {

    }

    public function status() {
        $carNo = input('get.carSeqNo');
        $seatStatus = '';
        if($carNo != null) {
            $carInfo = Db::table('car')->where('carNo',$carNo)->select();


            if( $carInfo ) {
                $timeEnd = $carInfo[0]['timeend'];

                $timeNow = date('h:i:s');

                if($timeNow>$timeEnd) {
                   if(1== Db::table('car')->where('carNo',$carNo)->update(['seatstatus'=>0]) ) {
                       $carInfo = Db::table('car')->where('carNo',$carNo)->select();
                   }
                }
                print_r( $carInfo[0]);

                for ($i=0;$i<34;$i++) {
                    $statusSeat = array($carInfo[0]['seatstatus']);
                    $statusSeat = intval($statusSeat[0][$i]);
                    print "#".$statusSeat;
                    $seatStatus = $seatStatus.($i+1)."|".$statusSeat.",";
                }
                $statusSeat = $carInfo[0]['seatstatus'][34];
                $statusSeat = intval($statusSeat);
                $seatStatus = $seatStatus."35|".$statusSeat;
                $ret = 1;
            }
            else {
                $ret = 2;
            }
        }
        else {
            $ret = 2;
        }


//        if($carNo != null) {
//            $ret = 1;
//
//            for ($i=0;$i<34;$i++) {
//                if( $i%3==0&&$i%2==0) {
//                    $seatStatus = $seatStatus.($i+1)."|1,";
//                }
//                else {
//                    $seatStatus = $seatStatus.($i+1)."|0,";
//                }
//
//            }
//            $seatStatus = $seatStatus."35|0";
//        }
//        else {
//            $ret = 2;
//        }

        return $this->response(['carSeqNo'=>$carNo,'retCode'=>$ret,'seatStatus'=>$seatStatus]);
    }

}