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
            $tableUser->setId(0);
        }
        else {
            $regs = 1;
        }

        $this->setResponseData(['mobile'=>$mobile,'regstatus'=>$regs,'userid'=>$tableUser->getId()]);

        $this->setDesc("查询成功");
        return 0;

    }

    public function add() {
        $ret = $this->subAdd();

        $retDesc = ['retCode'=>$ret,'desc'=>$this->getDesc()];

        $data = array_merge($retDesc,$this->getResponseData());

        return $this->response($data,'json',200);
    }

    private function subAdd()
    {
        switch($this->_method) {
            case 'post':
                $mobile = input('post.mobile');
                $password = input('post.password');

                $nickname = input('post.nickname');
                $logo = input('post.logo');
                $sex = input('post.sex');
                $homeaddr = input('post.homeaddr');
                $comaddr = input('post.comaddr');
                $worktime = input('post.worktime');
                $offtime = input('post.offtime');
                break;

            case 'get':
                $mobile = input('get.mobile');
                $password = input('get.password');

                $nickname = input('get.nickname');
                $logo = input('get.logo');
                $sex = input('get.sex');
                $homeaddr = input('get.homeaddr');
                $comaddr = input('get.comaddr');
                $worktime = input('get.worktime');
                $offtime = input('get.offtime');
                break;

            default:
                $this->setDesc("请求方法 $this->_method 不支持");
                return 1;
        }

        if($mobile==null || $password==null) {
            $this->setDesc("mobile,password不能为空");
            return 2;
        }

        if( strlen($password) < 6 || strlen($password) > 64 ) {
            $this->setDesc("密码长度需要满足 6<=len<=64");
            return 2;
        }

        $tableUser = new tableUser();

        if(0==$tableUser->findByMobile($mobile)) {
            $this->setDesc("手机号 $mobile 已经注册");
            return 3;
        }

        $tableUser->setMobile($mobile);
        $tableUser->setPassword($password);
        $tableUser->setNickname($nickname);
        $tableUser->setLogo($logo);
        $tableUser->setSex($sex);
        $tableUser->setHomeaddr($homeaddr);
        $tableUser->setComaddr($comaddr);
        $tableUser->setWorktime($worktime);
        $tableUser->setOfftime($offtime);

        if ( 0 != $tableUser->add() ) {
            $this->setDesc("添加数据库失败");
            return 4;
        }

        $this->setResponseData(['mobile'=>$mobile,'password'=>$password,'userid'=>$tableUser->getId()]);

        $this->setDesc("添加用户成功");
        return 0;
    }

    public function update() {
        $ret = $this->subUpdate();

        $retDesc = ['retCode'=>$ret,'desc'=>$this->getDesc()];

        $data = array_merge($retDesc,$this->getResponseData());

        return $this->response($data,'json',200);
    }

    private function subUpdate()
    {
        switch($this->_method) {
            case 'post':
                $userid = input('post.userid');
                $nickname = input('post.nickname');
                $logo = input('post.logo');
                $sex = input('post.sex');
                $homeaddr = input('post.homeaddr');
                $comaddr = input('post.comaddr');
                $worktime = input('post.worktime');
                $offtime = input('post.offtime');
                break;

            case 'get':
                $userid = input('get.userid');
                $nickname = input('get.nickname');
                $logo = input('get.logo');
                $sex = input('get.sex');
                $homeaddr = input('get.homeaddr');
                $comaddr = input('get.comaddr');
                $worktime = input('get.worktime');
                $offtime = input('get.offtime');
                break;

            default:
                $this->setDesc("请求方法 $this->_method 不支持");
                return 1;
        }



        if( $userid ==null) {
            $this->setDesc("用户ID不能为空");
            return 2;
        }

        $tableUser = new tableUser();

        $tableUser->setNickname($nickname);
        $tableUser->setLogo($logo);
        $tableUser->setSex($sex);
        $tableUser->setHomeaddr($homeaddr);
        $tableUser->setComaddr($comaddr);
        $tableUser->setWorktime($worktime);
        $tableUser->setOfftime($offtime);

        if ( 0 != $tableUser->update($userid) ) {
            $this->setDesc("修改数据库失败");
            return 4;
        }

        $this->setDesc("修改成功");
        return 0;
    }

    public function get() {
        $ret = $this->subGet();

        $retDesc = ['retCode'=>$ret,'desc'=>$this->getDesc()];

        $data = array_merge($retDesc,$this->getResponseData());

        return $this->response($data,'json',200);
    }

    private function subGet()
    {
        switch($this->_method) {
            case 'post':
                $userid = input('post.userid');
                break;

            case 'get':
                $userid = input('get.userid');
                break;

            default:
                $this->setDesc("请求方法 $this->_method 不支持");
                return 1;
        }

        if( $userid ==null) {
            $this->setDesc("用户ID不能为空");
            return 2;
        }

        $tableUser = new tableUser();
        if ( 0 != $tableUser->find($userid) ) {
            $this->setDesc("查找失败");
            return 2;
        }

        $data = ['userid'=>$userid,'mobile'=>$tableUser->getMobile(),'nickname'=>$tableUser->getNickname(),'logo'=>$tableUser->getLogo(),
        'sex'=>$tableUser->getSex(),'homeaddr'=>$tableUser->getHomeaddr(),'comaddr'=>$tableUser->getComaddr(),
        'worktime'=>$tableUser->getWorktime(),'offtime'=>$tableUser->getOfftime()];

        $this->setResponseData($data);

        $this->setDesc("获取用户ID $userid 成功");
        return 0;
    }

    public function orderget( ) {
        $ret = $this->subOrderGet();

        $retDesc = ['retCode'=>$ret,'desc'=>$this->getDesc()];

        $data = array_merge($retDesc,$this->getResponseData());

        return $this->response($data,'json',200);
    }

    private function subOrderGet()
    {
        switch($this->_method) {
            case 'post':
                $userid = input('post.userid');
                $orderseq = input('post.orderseq');
                break;

            case 'get':
                $userid = input('get.userid');
                $orderseq = input('get.orderseq');
                break;

            default:
                $this->setDesc("请求方法 $this->_method 不支持");
                return 1;
        }

        if( $userid ==null ) {
            $this->setDesc("用户ID不能为空");
            return 2;
        }

        $tableUserOrder = new tableUserOrder();
        if($orderseq == null || $orderseq==0) {
            $orderseq = 0xFFFFFFFF-1;
        }

        if ( 0 != $tableUserOrder->findByUser($orderseq,$userid) ) {
            $this->setDesc("查询数据库失败");
            return 3;
        }

        $this->setResponseData(['orderlist'=>$tableUserOrder->getOrder()]);

        $this->setDesc("查询成功");
        return 0;

    }

    public function subLogoUpload() {
        switch($this->_method) {
            case 'post':
                $userid = input('post.userid');
                break;

            default:
                $this->setDesc("请求方法 $this->_method 不支持");
                return 1;
        }

        if($userid == null) {
            $this->setDesc("用户ID不能为空");
            return 2;
        }

        $file = request()->file('image');
//        print_r( $file->getInfo());
//        echo '</br>';

//        $info = $file->move(ROOT_PATH.'public'.DS.'logo');

        $info = $file->rule('md5')->move(ROOT_PATH.'public'.DS.'logo');
        if($info) {

            $filename = $info->getFilename();
            $fatherPath = $info->getPathInfo()->getBasename();

            $logoname = 'http://www.xjmiracle.com/logo/'.$fatherPath.'/'.$filename;

            $tableUser = new tableUser();

            $tableUser->setLogo($logoname);
            if( 0 != $tableUser->update($userid) ) {
                $this->setDesc("修改数据库失败");
                return 3;
            }
        }
        else {
            $this->setDesc("文件保存失败");
            return 4;
        }

        $this->setResponseData(['userid'=>$userid,'logo'=>$logoname]);

        $this->setDesc("修改头像成功");
        return 0;
    }

    public function logoUpload() {
        $ret = $this->subLogoUpload();

        $retDesc = ['retCode'=>$ret,'desc'=>$this->getDesc()];

        $data = array_merge($retDesc,$this->getResponseData());

        return $this->response($data,'json',200);
    }
}