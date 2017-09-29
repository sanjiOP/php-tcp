<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/28
 * Time: 11:44
 */
namespace server\connect;

use rua\base\socket;
use server\server;

class connect extends socket {




    //激活
    const STATUS_ACTIVE = 1;
    //等待
    const STATUS_TASK = 2;
    //关闭
    const STATUS_CLOSE = 3;


    private $server;


    //当前连接状态
    private $status;


    //接收客户端消息
    protected $receive_data = '';


    /**
     * @param server $server
     * connect constructor.
     */
    public function __construct($server)
    {
        $this->server = $server;
        $this->create($this->server->getSocket());
    }


    /**
     * 获取服务端server对象
     * @author liu.bin 2017/9/29 13:27
     */
    public function getServer(){
        return $this->server;
    }


    /**
     * 获取连接状态
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


    /**
     * 接收客户端消息
     * @author liu.bin 2017/9/29 13:24
     */
    public function receive(){

        $protocol = $this->server->getProtocol();
        $buffer_size = $protocol->get_buffer_size();
        $receive_data = '';
        $end = false;

        while(!$end){

            //读取消息
            socket_recv($this->socket,$buffer,$buffer_size,0);
            if ($buffer === '' || $buffer === false) {
                $this->setStatus(self::STATUS_CLOSE);
                $end = true;
            }

            //是否继续接收消息
            if(!$protocol->on_read_buffer($buffer)){
                $end = true;
            }

            //读取完整消息
            $receive_data .= $protocol->get_buffer();
        }

        return $receive_data;

    }

}

