<?php
namespace app\index\controller;
use app\index\table\table;
use app\index\table\tableCar;
use app\index\table\tableSchedule;
use app\index\table\tableUser;
use app\index\table\tableUserOrder;

/**
 * Created by PhpStorm.
 * User: xiaojie
 * Date: 16/6/16
 * Time: 下午6:48
 */
class User extends \think\controller\Rest
{
    protected $desc;
    protected $responseData = [];

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

    /**
     * @return array
     */
    public function getResponseData()
    {
        return $this->responseData;
    }

    /**
     * @param array $responseData
     */
    public function setResponseData($responseData)
    {
        $this->responseData = $responseData;
    }


    public function mobilereg() {
        $ret = $this->subMobilereg();

        $retDesc = ['retCode'=>$ret,'desc'=>$this->getDesc()];

        $data = array_merge($retDesc,$this->getResponseData());

        return $this->response($data,'json',200);
    }

    private function subMobilereg()
    {
        switch($this->_method) {
            case 'post':
                $mobile = input('post.mobile');
                break;

            case 'get':
                $mobile = input('get.mobile');
                break;

            default:
                $this->setDesc("请求方法 $this->_method 不支持");
                return 1;
        }

        if($mobile==null) {
            $this->setDesc("mobile 不能为空");
            return 2;
        }

        $tableUser = new tableUser();

        if ( 0 != $tableUser->findByMobile($mobile) ) {
            $regs = 0;
        }
        else {
            $regs = 1;
        }

        $this->setResponseData(['mobile'=>$mobile,'regstatus'=>$regs,'userid'=>$tableUser->getId()]);

        $this->setDesc("查询成功");
        return 0;

    }

}