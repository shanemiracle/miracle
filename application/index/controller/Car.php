<?php
/**
 * Created by PhpStorm.
 * User: xiaojie
 * Date: 16/6/28
 * Time: 下午2:25
 */

namespace app\index\controller;


use think\controller\Rest;

class Car extends Rest
{
    public function index() {

    }

    public function status() {
        $carNo = input('get.carSeqNo');
        $seatStatus = '';

        if($carNo != null) {
            $ret = 1;

            for ($i=0;$i<19;$i++) {
                if( $i%3==0&&$i%2==0) {
                    $seatStatus = $seatStatus.($i+1)."|1,";
                }
                else {
                    $seatStatus = $seatStatus.($i+1)."|0,";
                }

            }
            $seatStatus = $seatStatus."20|0";
        }
        else {
            $ret = 2;
        }

        return $this->response(['carSeqNo'=>$carNo,'retCode'=>$ret,'seatStatus'=>$seatStatus]);
    }

}