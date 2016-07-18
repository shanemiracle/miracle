<?php
/**
 * Created by PhpStorm.
 * User: xiaojie
 * Date: 16/7/15
 * Time: 下午2:10
 */

namespace app\index\table;


use think\Db;

class tableUser
{
    protected $tableName = 'user';

    protected $id;
    protected $mobile;
    protected $nickname;
    protected $logo;
    protected $password;
    protected $sex;
    protected $homeaddr;
    protected $comaddr;
    protected $worktime;
    protected $offtime;

    /**
     * tableUser constructor.
     */
    public function __construct()
    {
    }

    public function add()
    {
        $data = [
            'mobile'=>$this->getMobile(),
            'password'=>$this->getPassword()
        ];

        if( $this->getNickname() ) { $data['nickname']= $this->getNickname(); }

        if($this->getLogo()) { $data['logo']= $this->getLogo(); }

        if($this->sex) { $data['sex']= $this->sex; }

        if($this->homeaddr) { $data['homeaddr']= $this->homeaddr; }

        if($this->comaddr) { $data['comaddr']= $this->comaddr; }

        if($this->worktime) { $data['worktime']= $this->worktime; }

        if($this->offtime) { $data['offtime']= $this->offtime; }
        
        $this->id = Db::table($this->tableName)->insertGetId($data);
        $ret = ( 0 != $this->id )?0:1;

        return $ret;
    }

    public function update($id) {
        $data = [
            'id'=>$id,
            'mobile'=>$this->getMobile(),
            'nickname'=>$this->getNickname(),
            'logo'=>$this->getLogo(),
            'password'=>$this->getPassword(),
            'sex'=>$this->getSex(),
            'homeaddr'=>$this->getHomeaddr(),
            'comaddr'=>$this->getComaddr(),
            'worktime'=>$this->getWorktime(),
            'offtime'=>$this->getOfftime()
        ];
        $result = Db::table($this->tableName)->update($data);

        $ret = $result==1?0:1;

        return $ret;
    }

    public function del($id) {
        $result = Db::table($this->tableName)->delete($id);
        return $result==1?0:1;
    }

    public function find($id) {
        $data = Db::table($this->tableName)->where('id',$id)->find();
        if($data) {
            $this->id = $id;
            $this->mobile = $data['mobile'];
            $this->nickname = $data['nickname'];
            $this->logo = $data['logo'];
            $this->password = $data['password'];
            $this->sex = $data['sex'];
            $this->homeaddr = $data['homeaddr'];
            $this->comaddr = $data['comaddr'];
            $this->worktime = $data['worktime'];
            $this->offtime = $data['offtime'];

            return 0;
        }

        return 1;
    }

    public function findByMobile($mobile) {
        $data = Db::table($this->tableName)->where('mobile',$mobile)->find();
        if($data) {
            $this->id = $data['id'];
            $this->mobile = $mobile;
            $this->nickname = $data['nickname'];
            $this->logo = $data['logo'];
            $this->password = $data['password'];
            $this->sex = $data['sex'];
            $this->homeaddr = $data['homeaddr'];
            $this->comaddr = $data['comaddr'];
            $this->worktime = $data['worktime'];
            $this->offtime = $data['offtime'];

            return 0;
        }

        return 1;
    }




    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getMobile()
    {
        return $this->mobile;
    }

    /**
     * @param mixed $mobile
     */
    public function setMobile($mobile)
    {
        $this->mobile = $mobile;
    }

    /**
     * @return mixed
     */
    public function getNickname()
    {
        return $this->nickname;
    }

    /**
     * @param mixed $bickname
     */
    public function setNickname($nickname)
    {
        $this->nickname = $nickname;
    }

    /**
     * @return mixed
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * @param mixed $logo
     */
    public function setLogo($logo)
    {
        $this->logo = $logo;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getSex()
    {
        return $this->sex;
    }

    /**
     * @param mixed $sex
     */
    public function setSex($sex)
    {
        $this->sex = $sex;
    }

    /**
     * @return mixed
     */
    public function getHomeaddr()
    {
        return $this->homeaddr;
    }

    /**
     * @param mixed $homeaddr
     */
    public function setHomeaddr($homeaddr)
    {
        $this->homeaddr = $homeaddr;
    }

    /**
     * @return mixed
     */
    public function getComaddr()
    {
        return $this->comaddr;
    }

    /**
     * @param mixed $comaddr
     */
    public function setComaddr($comaddr)
    {
        $this->comaddr = $comaddr;
    }

    /**
     * @return mixed
     */
    public function getWorktime()
    {
        return $this->worktime;
    }

    /**
     * @param mixed $worktime
     */
    public function setWorktime($worktime)
    {
        $this->worktime = $worktime;
    }

    /**
     * @return mixed
     */
    public function getOfftime()
    {
        return $this->offtime;
    }

    /**
     * @param mixed $offtime
     */
    public function setOfftime($offtime)
    {
        $this->offtime = $offtime;
    }






}