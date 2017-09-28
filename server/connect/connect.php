<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/28
 * Time: 11:44
 */
namespace server\connect;

use rua\base\socket;

class connect extends socket {




    //激活
    const STATUS_ACTIVE = 1;
    //等待
    const STATUS_TASK = 2;
    //关闭
    const STATUS_CLOSE = 3;



    //当前连接状态
    private $status;


    /**
     * 获取
     * @return integer
     * @author liu.bin 2017/9/28 14:21
     */
    public function getStatus(){
        return $this->status;
    }


    /**
     * 设置连接状态
     * @param $status integer
     * @author liu.bin 2017/9/28 14:57
     */
    public function setStatus($status){
        $this->status = $status;
    }



}

