<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/26
 * Time: 11:32
 */

namespace loop;
use rua\base\event;
use server\server;

abstract class loop extends event {


    private $server;



    public function __construct(server $server)
    {
        $this->server = $server;
    }


    /**
     * 获取socket
     * @author liu.bin 2017/9/26 17:57
     */
    protected function get_socket(){
        return $this->server->getSocket();
    }


    /**
     * 获取协议
     * @return null
     * @author liu.bin 2017/9/27 16:40
     */
    protected function get_protocol(){
        return $this->server->getProtocol();
    }


    /**
     * 事件触发，通知监听者
     * @param $event string 触发事件
     * @param $param array 传递参数
     * @author liu.bin 2017/9/27 16:29
     */
    protected function trigger($event,$param=array()){
        $this->server->trigger($event,$param);
    }


    /**
     * loop
     * @return mixed
     * @author liu.bin 2017/9/26 18:14
     */
    abstract public function loop();
}