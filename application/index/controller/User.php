<?php
namespace app\index\controller;
use app\index\table\table;
use app\index\table\tableCar;
use app\index\table\tableSchedule;

/**
 * Created by PhpStorm.
 * User: xiaojie
 * Date: 16/6/16
 * Time: 下午6:48
 */
class User extends \think\controller\Rest
{
    public function get()
    {

        $data = ['name'=>'xiaoj','age'=>'27','sex'=>'male'];
        return $this->response($data,'json',200);
    }

    public function add() {
        $schedule = new tableSchedule();

        $schedule->setCarno(3);
        $schedule->setTimestart('09:00:00');
        $schedule->setTimeend('10:00:00');

        $ret = $schedule->add();

        if ( 0 == $ret ) {
            print 'add success '.$schedule->getSno();
        }
        else {
            print 'add faild';
        }
    }

    public function update($carno, $desc, $num) {
        $car = new tableCar();
        $car->setCardesc($desc);
        $car->setSeatnum($num);

        $ret = $car->update($carno);
        print ($ret==0)?"update success":'update failed';
    }
    
    public function del($sno) {
        $car = new tableSchedule();
        $ret = $car->del($sno);

        print ($ret==0)?"del success":'del failed';
    }

    public function find($carno,$time) {
        $car = new tableSchedule();

        $ret = $car->findByCarTime($carno,$time);

//        print_r($ret);

        if($ret == 0) {
            print 'carno '.$car->getCarno();
            print ' sno'.$car->getSno();
            print ' start '.$car->getTimestart();
            print ' end '.$car->getTimeend();
        }
        else {
            print 'get failed';
        }
    }

}