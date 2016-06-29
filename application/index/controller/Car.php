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
    public $timeDay = '2016-';

    public function index() {

    }

    public function status() {
        $carNo = input('get.carSeqNo');
        $seatStatus = '';
        if($carNo != null) {
            $carInfo = Db::table('car')->where('carNo',$carNo)->select();


            if( $carInfo ) {

//                $timeNow = date();
//                $timeEnd = $carInfo[0]['timeend'];
//                $timeNow = date('H:i:s');
//                print $timeNow;

//                if($timeNow>$timeEnd) {
//                   if(1== Db::table('car')->where('carNo',$carNo)->update(['seatstatus'=>'00000000000000000000000000000000000000000000000000000000000000000000000000000000']) ) {
//                       $carInfo = Db::table('car')->where('carNo',$carNo)->select();
//                   }
//                }

                for ($i=0;$i<34;$i++) {
                    $seatStatus = $seatStatus.($i+1)."|".$statusSeat = $carInfo[0]['seatstatus'][$i].",";
                }
                $statusSeat = $carInfo[0]['seatstatus'][34];
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

        return $this->response(['carSeqNo'=>$carNo,'retCode'=>$ret,'seatStatus'=>$seatStatus]);
    }

}