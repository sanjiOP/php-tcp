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
        $protocol           = $this->get_protocol();
        $server             = $this->get_server();
        queue::addServer($server);


        while(true){

            console('=================== loop start ========================');


            $change_socket_queue = queue::sockets();

            //选择前
            $socket_name = '';
            foreach ($change_socket_queue as $socket){
                $socket_name .= '【' . socket_id($socket) . '】';
            }
            console('socket_select 前 : ' . $socket_name . ' and 数量 :' . count($change_socket_queue));


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
            @socket_select($change_socket_queue,$write=NULL,$except=NULL,NULL);


            //
            $socket_name = '';
            foreach ($change_socket_queue as $socket){
                $socket_name .= '【'.socket_id($socket) . '】';
            }
            console('socket_select 后 : ' . $socket_name . ' and 数量 :' . count($change_socket_queue));





            foreach($change_socket_queue as $socket){

                console('选中 : 【'.socket_id($socket) .'】 and master_socket :【'. socket_id($this->master_socket).'】');


                if($socket == $this->master_socket){


                    //接受客户端链接（创建新的客户端 socket）
                    //$client_socket = socket_accept($this->master_socket);

                    $connect = new connect();
                    $connect->create($this->master_socket);
                    queue::add($connect);


                    //触发连接事件
                    $this->trigger(self::EVENT_CONNECT,array($server,$connect));

                }else{



                    //接收客户端消息
                    $receive_data = $protocol->input($socket);
                    $connect = queue::findConnBySocket($socket);


                    if(false === $receive_data){

                        //触发关闭事件
                        console('socket close...');
                        $this->trigger(self::EVENT_CLOSE,array($server,$connect));
                        $this->get_server()->close($connect);
                        break;


                    }else{

                        //触发接收消息事件
                        $this->trigger(self::EVENT_RECEIVE,array($server,$connect,$receive_data));

                    }

                }


            }

            //console('=================== loop end ========================');


        }


    }








}