<?php
namespace rua\base;



class socket extends event implements interfaceSocket
{


    /**
     * php 套接字
     * @var
     */
    protected $socket;

    /**
     * @var int 连接编号
     */
    protected $id=0;



    /**
     * 打印编号
     * @return string
     * @author liu.bin 2017/9/28 14:17
     */
    public function __toString()
    {
        return (string)$this->getId();
    }


    /**
     * 创建socket套接字
     * @param resource|null $socket
     * @param int $socket_type
     * @return bool
     * @author liu.bin 2017/9/27 15:25
     */
    public function create($socket,$socket_type=SOL_TCP){


        if(!is_null($socket)){
            $this->socket = socket_accept($socket);
        }else{
            $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
            if($this->socket < 0){
                $this->error = 'socket_create() failed';
                $this->error_code = 1001;
                return false;
            }
        }
        return $this->init_socket($this->socket);
    }





    /**
     * 初始化
     * @param $socket resource
     * @return bool
     * @author liu.bin 2017/9/28 14:23
     */
    public function init_socket($socket){


        if(empty($this->socket) || ('resource' !== gettype($socket))){
            return false;
        }
        $this->socket = $socket;
        $this->id = socket_id($this->socket);
        if(!is_numeric($this->id) || $this->id <= 0){
            return false;
        }

        return true;
    }


    /**
     * 获取socket
     * @return mixed
     * @author liu.bin 2017/9/28 15:06
     */
    public function getSocket(){
        return $this->socket;
    }

    /**
     * 获取连接编号
     * @author liu.bin 2017/9/28 14:18
     */
    public function getId(){
        return $this->id;
    }

}