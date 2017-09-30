<?php


namespace client;

use rua\base\socket;

abstract class client extends socket
{


    /**
     * 接连服务器
     * @var string
     */
    protected $host = '';


    /**
     * 连接端口
     * @var int
     */
    protected $port = 0;


    /**
     * 客户端启动时间
     * @var int
     */
    private $start_time = 0;



    private $isConnected = false;


    /**
     * server constructor.
     */
    public function __construct()
    {
        //创建 socket
        $this->create(null);

        //触发启动事件
        $this->trigger(self::EVENT_START,array($this));
    }



    /**
     * 设置服务器运行参数参数
     * @param array $config 配置项
     * @author liu.bin 2017/9/27 14:38
     */
    public function set($config=array()){

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
     * 连接服务器
     * @param $host
     * @param $port
     * @return bool
     * @author liu.bin 2017/9/29 11:22
     */
    public function connect($host,$port){

        $this->host = $host;
        $this->port = $port;

        //检测socket
        if(is_null($this->socket) || empty($this->socket)){
            return false;
        }


        //初始化服务器信息
        $this->init();

        //链接服务器
        $connect = socket_connect($this->socket, $this->host, $this->port);
        if (!$connect) {
            $this->error = "socket_connect() failed, reason: " . socket_strerror($connect);
            return false;
        }

        $this->isConnected = true;

        //展示ui
        $this->displayUI();


        //触发链接事件
        $this->trigger(self::EVENT_CONNECT,array($this));

        return true;
    }




    /**
     * 重启客户端
     * @author liu.bin 2017/9/27 14:57
     */
    public function reload(){

    }


    /**
     * 关闭客户端
     * @author liu.bin 2017/9/27 14:57
     */
    public function stop(){

    }


    /**
     * 关闭客户端
     * @author liu.bin 2017/9/27 14:58
     */
    public function shutdown(){

    }




    /**
     * 发送消息到服务端
     * @param string $data
     * @author liu.bin 2017/9/27 15:02
     */
    public function send($data){

        $protocol = $this->getProtocol();
        $data = $protocol->encode($data);
        socket_write($this->socket,$data,strlen($data));
    }


    /**
     * 发送文件到服务端
     * @param int $fd
     * @param string $filename
     * @param int $offset
     * @param int $length
     * @author liu.bin 2017/9/27 15:03
     */
    public function sendFile($fd, $filename, $offset =0, $length = 0){

    }





    /**
     * 客户端接收服务端消息: socket_read
     * /////服务端接收客户端消息: socket_recv
     * @return string
     * @author liu.bin 2017/9/29 16:59
     */
    public function receive(){

        $protocol = $this->getProtocol();
        while(true){

            //获取buffer_size ,固定包头+包体的协议中，buffer_size会变化；边界检测的协议，buffer_size固定
            $buffer_size = $protocol->get_buffer_size();

            //读取消息
            $mess = socket_read($this->socket,$buffer_size);

            if (empty($mess) || $mess === '' || $mess === false) {
                break;
            }

            //是否继续接收消息:true 继续读取，false:读取结束
            if(!$protocol->read_buffer($mess)){
                break;
            }

        }
        $receive_data = $protocol->getInData();
        console('完整数据：'. $receive_data);
        $this->trigger(self::EVENT_RECEIVE,array($this,$receive_data));
        return true;
    }



    /**
     *
     * 客户端信息
    start_time      客户端启动的时间
    connection_num  当前连接的状态
     * @author liu.bin 2017/9/27 15:10
     */
    public function stats(){
        return [
            'start_time'=>date('Y-m-d H:i:s',$this->start_time),
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
     * 是否连接到服务器
     * @author liu.bin 2017/9/29 11:24
     */
    public function isConnected(){
        return $this->isConnected;
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

