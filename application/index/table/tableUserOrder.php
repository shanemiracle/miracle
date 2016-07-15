<?php
/**
 * Created by PhpStorm.
 * User: xiaojie
 * Date: 16/7/15
 * Time: 下午4:12
 */

namespace app\index\table;


use think\Db;

class tableUserOrder
{
    protected $tableName = 'user_order';
    protected $id;
    protected $user;
    protected $order;

    /**
     * tableUserOrder constructor.
     */
    public function __construct()
    {
    }

    public function add()
    {
        $data = [
            'id'=>$this->getId(),
            'user'=>$this->getUser(),
            'order'=>$this->getOrder()
        ];

        $this->id = Db::table($this->tableName)->insertGetId($data);
        $ret = ( 0 != $this->id )?0:1;

        return $ret;
    }

    public function update($id) {
        $data = [
            'id'=>$id,
            'user'=>$this->getUser(),
            'order'=>$this->getOrder()
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
            $this->user = $data['user'];
            $this->order = $data['order'];

            return 0;
        }

        return 1;
    }

    public function findByUser($id,$user) {
        $data = Db::table($this->tableName)->field('id,order')->where('user',$user)->where('id','<',$id)->order('id','desc')->limit(10)->select();
        if($data) {
            $this->id = $id;
            $this->user = $user;
            $this->order = $data;

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
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param mixed $order
     */
    public function setOrder($order)
    {
        $this->order = $order;
    }





}