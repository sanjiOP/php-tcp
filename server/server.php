<?php
namespace server;


use loop\select;
use rua\base\socket;
use server\queue\queue;
use rua\base\interfaceSocket;


/**
 * Class server
 * @package server
 *
 *
 * 服务器消息由轮训或事件触发自动接收，采用自定义协议处理数据包，通过回调通知应用程序
 *
 */
abstract class server extends socket {



    /**
     * 服务器启动时间
     * @var int
     */
    protected $start_time = 0;


    /**
     * 当前服务器连接数量
     * @var int
     */
    protected $connection_num = 0;


    /**
     * 主机
     * @var string
     */
    protected $host = '';

    /**
     * 端口
     * @var string
     */
    protected $port = '';


    /**
     * 消息协议
     * @var
     */
    protected $protocol;


    /**
     * 轮询事件
     * @var
     */
    protected $loop;

    /**
     * server constructor.
     * @param $host
     * @param $port
     */
    public function __construct($host,$port)
    {
        $this->host = $host;
        $this->port = $port;
    }







    /**
     * 设置服务器运行参数参数
     * @param array $config 配置项
     * @author liu.bin 2017/9/27 14:38
     */
    public function set($config=array()){

    }



    /**
     * 增加 监听主机和端口
     * @param string $host
     * @param int $port
     * @param string $type
     * @author liu.bin 2017/9/27 14:54
     */
    public function addListener($host, $port, $type = RUA_SOCK_TCP){

    }


    /**
     * 增加 监听主机和端口
     * addListener 别名
     * @param string $host
     * @param int $port
     * @param string $type
     * @author liu.bin 2017/9/27 14:55
     */
    public function listen($host, $port, $type = RUA_SOCK_TCP){
        $this->addListener($host, $port, $type);
    }


    /**
     * 初始化服务器信息
     * @author liu.bin 2017/9/27 15:50
     */
    private function init(){

        //服务器启动时间
        $this->start_time = time();

    }



    /**
     * 启动服务器
     * @author liu.bin 2017/9/27 14:56
     */
    public function start(){


        $result = $this->start_socket();

        if(false === $result){
            return false;
        }


        //初始化服务器信息
        $this->init();

        //展示ui
        $this->displayUI();


        //触发事件
        $this->trigger(self::EVENT_START,array($this));

        //默认采用socket_select方式接收客户端连接
        $this->loop = new select($this);
        $this->loop->loop();

    }


    /**
     * 重启服务器
     * @author liu.bin 2017/9/27 14:57
     */
    public function reload(){

    }


    /**
     * 关闭服务器
     * @author liu.bin 2017/9/27 14:57
     */
    public function stop(){

    }


    /**
     * 关闭服务器
     * @author liu.bin 2017/9/27 14:58
     */
    public function shutdown(){

    }



    /**
     * 关闭客户端连接
     * @param int $fd
     * @return bool
     * @author liu.bin 2017/9/28 14:35
     */
    public function close($fd){

        $connect = queue::findConnByFd($fd);
        if(!($connect instanceof interfaceSocket)){
            return false;
        }
        socket_close($connect->getSocket());
        queue::remove($connect);
        unset($connect);
    }


    /**
     * 定时器
     * @author liu.bin 2017/9/27 14:59
     */
    public function tick(){

    }


    /**
     * 指定时间之后执行
     * @author liu.bin 2017/9/27 15:00
     */
    public function after(){

    }


    /**
     * 清除定时器 tick after
     * @author liu.bin 2017/9/27 15:01
     */
    public function clearTimer(){

    }


    /**
     * 发送消息到客户端
     * @param string $data
     * @param int $fd
     * @return bool
     * @author liu.bin 2017/9/27 15:02
     */
    public function send($data,$fd){

        $protocol = $this->getProtocol();
        $data = $protocol->encode($data);
        $connect = queue::findConnByFd($fd);
        if($connect){

            console('send data protocol:' . $data);

            socket_write($connect->getSocket(),$data,strlen($data));
            return true;
        }
        return false;
    }


    /**
     * 发送文件到客户端
     * @param int $fd
     * @param string $filename
     * @param int $offset
     * @param int $length
     * @author liu.bin 2017/9/27 15:03
     */
    public function sendFile($fd, $filename, $offset =0, $length = 0){

    }




    /**
     * 检测客户端是否存在
     * @param int $fd
     * @author liu.bin 2017/9/27 15:05
     */
    public function exist($fd){

    }


    /**
     * 停止接收客户端消息
     * @param int $fd
     * @author liu.bin 2017/9/27 15:06
     */
    public function pause($fd){

    }


    /**
     * 恢复接收客户端消息
     * @param int $fd
     * @author liu.bin 2017/9/27 15:06
     */
    public function resume($fd){

    }


    /**
     * 客户端连接信息
     *
     * array(5) {
        ["reactor_id"]  => int(3)
        ["server_fd"]   => int(14)
        ["server_port"] => int(9501)
        ["remote_port"] => int(19889)
        ["remote_ip"]   => string(9) "127.0.0.1"
        ["connect_time"]=> int(1390212495)
        ["last_time"]   => int(1390212760)
     }
     * @param int $fd
     * @author liu.bin 2017/9/27 15:07
     */
    public function connection_info($fd){

    }


    /**
     * 客户端连接列表
     * @param int $start_fd
     * @param int $page_size
     * @author liu.bin 2017/9/27 15:09
     */
    public function connection_list($start_fd = 0, $page_size = 10){
        
    }


    /**
     *
     * 服务器信息
        start_time      服务器启动的时间
        connection_num  当前连接的数量
        accept_count    接受了多少个连接
        close_count     关闭的连接数量
        tasking_num     当前正在排队的任务数
     * @author liu.bin 2017/9/27 15:10
     */
    public function stats(){
        return [
            'start_time'=>date('Y-m-d H:i:s',$this->start_time),
            'connection_num'=>$this->connection_num,
        ];
    }


    /**
     * 获取socket套接字
     * @author liu.bin 2017/9/27 15:13
     */
    public function getSocket(){
        return $this->socket;
    }






    /**
     * socket开启
     * @author liu.bin 2017/9/27 15:35
     */
    private function start_socket(){

        $this->create(null);
        socket_set_option($this->socket, SOL_SOCKET, SO_REUSEADDR, 1);

        $bind = socket_bind($this->socket, $this->host, $this->port);
        if($bind < 0){
            $this->error = "socket_bind() failed, reason: " . socket_strerror($bind);
            $this->error_code = 1005;
            return false;
        }

        $listen = socket_listen($this->socket, 5);
        if($listen < 0){
            $this->error = "socket_listen() failed, reason: " . socket_strerror($listen);
            $this->error_code = 1010;
            return false;
        }

        return true;
    }

    /**
     * 展示启动界面
     * @return void
     */
    abstract protected function displayUI();


    /**
     * 采用协议
     * @return mixed
     * @author liu.bin 2017/9/27 16:44
     */
    abstract public function getProtocol();

}