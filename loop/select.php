<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/26
 * Time: 11:25
 */

namespace loop;




class select extends loop {




    /**
     * 所有的客户端链接
     * @var array
     */
    private $sockets = [];


    /**
     * socket select loop
     * @author liu.bin 2017/9/26 11:43
     */
    public function loop()
    {

        $this->sockets[]    = $this->get_socket();
        $protocol           = $this->get_protocol();

        while(true){


            /**
             * socket_select 此处会阻塞，不会往下执行，同时会监听客户端的链接状态
             *
             * 有客户端连接：
             * 		 socket_select函数会将change_sockets列表重置成只有一个值：master_socket, 往下执行下文,
             *		 首先会执行socket_accept，创建client_socket,
             *		 并将client_socket保存在change_sockets中，并继续回到socket_select处阻塞，此时chanege_sokets会包含所有的sockets
             *
             * 有客户端发送消息：
             * 		同时socket_select函数会将change_sockets数组重置成只有当前客户端的socket,继续执行下文，所以会执行socket_recv，执行完之后,
             *		继续回到socket_select函数处阻塞，此时change_socket会包含所有的sockets
             *
             */
            @socket_select($this->sockets,$write=NULL,$except=NULL,NULL);



            foreach($this->sockets as $socket){


                if($socket == $this->get_socket()){

                    //接受客户端链接（创建新的客户端 socket）
                    $client_socket = socket_accept($this->get_socket());

                    //保存到socket列表
                    $this->sockets[] = $client_socket;

                    //触发连接事件
                    $this->trigger(self::EVENT_CONNECT,array(\rua::server(),$socket));

                }else{

                    //接收客户端消息
                    $receive_data = $protocol->input($socket);

                    if(false === $receive_data){

                        //触发关闭事件
                        $this->trigger(self::EVENT_CLOSE,array(\rua::server(),$socket));
                    }else{
                        //触发接收消息事件
                        $this->trigger(self::EVENT_RECEIVE,array(\rua::server(),$socket,$receive_data));
                    }
                    break;
                }

            }




        }


    }



}