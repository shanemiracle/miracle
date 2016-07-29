<?php
/**
 * Created by PhpStorm.
 * User: xiaojie
 * Date: 16/6/30
 * Time: 下午7:42
 */

namespace app\index\table;


use think\Db;

class tableSeatOrderStatus
{
    protected $tableName = 'seat_order_status';

    protected $index;//sno+ondate+seatseq;
    protected $status;//1-未购买,2-购买锁定,3-已支付
    protected $time;//当status为2时,当前time表示锁定时间

    public function add() {
        $data = ['index'=>$this->index,'status'=>$this->status,'time'=>$this->time];
        $result = Db::table($this->tableName)->insert($data);

        $ret = $result==1?0:1;

        return $ret;
    }

    public function update() {
        $data = ['index'=>$this->index,'status'=>$this->status,'time'=>$this->time];
        $result = Db::table($this->tableName)->update($data);

        $ret = $result==1?0:1;

        return $ret;
        
    }

    /**
     * @param $sno
     * @param $ondate
     * @param $seatseq
     * @return int 0-成功,1-失败
     */
    public function addByIndex($sno,$ondate,$seatseq) {

        $index = sprintf('%07d%010d%03d',$sno,strtotime($ondate),$seatseq);
        $this->setStatus(1);
        $this->setTime(date('Y-m-d H:i:s'));
        $data = ['index'=>$index,'status'=>$this->getStatus(),'time'=>$this->getTime()];
        $ret = Db::table($this->tableName)->insert($data);

        return $ret==1?0:1;
    }

    /**
     * @param $sno
     * @param $ondate
     * @param $seatseq
     * @param $status
     * @return int
     * @throws \think\Exception
     */
    public function updateByStatus($sno,$ondate,$seatseq, $status) {
        $index = sprintf('%07d%010d%03d',$sno,strtotime($ondate),$seatseq);
        $this->setStatus($status);
        $this->setTime(date('Y-m-d H:i:s'));
        $data = ['index'=>$index,'status'=>$this->getStatus(),'time'=>$this->getTime()];
        $result = Db::table($this->tableName)->update($data);

        $ret = $result==1?0:1;

        return $ret;
    }

    /**
     * @param $sno
     * @param $ondate
     * @param $seatseq
     * @return int
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function findByIndex($sno,$ondate,$seatseq) {
        $index = sprintf('%07d%010d%03d',$sno,strtotime($ondate),$seatseq);

        $data = Db::table($this->tableName)->where('index',$index)->find();

        if($data) {
            $this->index = $index;
            $this->status = $data['status'];
            $this->time = $data['time'];

            return 0;
        }

        return 1;
    }

    /**查询座位状态,如果记录不存在,添加记录;如果状态是2,但是超期,则改为1;是3表示购买成功
     * @param $sno
     * @param $ondate
     * @param $seatseq
     * @return int 0-获取成功,1-添加记录失败,2-修复状态失败
     */
    public function queryStatusByIndex($sno,$ondate,$seatseq) {
        $ret = $this->findByIndex($sno,$ondate,$seatseq);
        if($ret == 1) {
            $result = $this->addByIndex($sno,$ondate,$seatseq);
            return $result==0?0:1;
        }

        $status = $this->getStatus();
        if($status==1) {
            return 0;
        }
        else if($status==2) {

            $timeNow = date('Y-m-d H:i:s');
//            print '#'.$status.'#'.$timeNow.'#'.$this->getTime().' ';
            if ( strtotime($timeNow)-strtotime($this->getTime()) > 60*30 ) {//超过30分钟超时
                if ( 0 == $this->updateByStatus($sno,$ondate,$seatseq,1) ) {
                    return 0;
                }
                else {
                    return 2;
                }
            }
        }

        return 0;
    }

    /**
     * @param $sno
     * @param $ondate
     * @param $seatseq
     * @return int
     * @throws \think\Exception
     */
    public function delByIndex($sno,$ondate,$seatseq) {//ondate表示日期
        $index = sprintf('%07d%010d%03d',$sno,strtotime($ondate),$seatseq);

        $result = Db::table($this->tableName)->delete($index);

        return $result==1?0:1;
    }

    /**
     * @return mixed
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * @param mixed $index
     */
    public function setIndex($index)
    {
        $this->index = $index;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @param mixed $time
     */
    public function setTime($time)
    {
        $this->time = $time;
    }
    
    

}