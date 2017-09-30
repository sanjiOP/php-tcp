<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/26
 * Time: 11:25
 */

namespace loop;




use server\connect\connect;
use server\queue\queue;

class select extends loop {



    /**
     * 所有的客户端链接
     * @var array
     */
    public $sockets = [];

    public $master_socket;





    public function __construct($server)
    {
        parent::__construct($server);
        $this->master_socket    = $this->get_socket();
    }





    /**
     * socket select loop
     * @author liu.bin 2017/9/26 11:43
     */
    public function loop()
    {


        $server = $this->get_server();
        queue::addServer($server);



        while(true){


            //重置所有链接队列
            $change_socket_queue = queue::sockets();

            // socket 选择
            @socket_select($change_socket_queue,$write=NULL,$except=NULL,NULL);


            foreach($change_socket_queue as $socket){

                if($socket == $this->master_socket){

                    //接受客户端链接（创建新的客户端 socket）
                    $connect = new connect($this->server);

                    //加入链接队列
                    if(queue::add($connect)){
                        //触发连接事件
                        $this->trigger(self::EVENT_CONNECT,array($server,$connect->getId()));
                    }else{
                        unset($connect);
                    }

                }else{


                    //接收客户端消息
                    $connect = queue::findConnBySocket($socket);
                    $receive_data = $connect->receive();

                    if(empty($receive_data) || connect::STATUS_CLOSE == $connect->getStatus()){
                        //触发关闭事件
                        $this->trigger(self::EVENT_CLOSE,array($server,$connect->getId()));
                        $this->get_server()->close($connect->getId());
                        break;
                    }

                    //触发接收消息事件
                    $this->trigger(self::EVENT_RECEIVE,array($server,$connect->getId(),$receive_data));

                }
            }


        }


    }




}