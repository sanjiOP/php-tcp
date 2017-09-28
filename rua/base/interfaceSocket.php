<?php
namespace rua\base;



interface interfaceSocket
{



    /**
     * 打印编号
     * @return string
     * @author liu.bin 2017/9/28 14:17
     */
    public function __toString();


    /**
     * 创建socket套接字
     * @param resource $socket
     * @param int $socket_type
     * @return bool
     * @author liu.bin 2017/9/27 15:25
     */
    public function create($socket,$socket_type=SOL_TCP);


    /**
     * 初始化 socket
     * @param $socket resource
     * @return bool
     * @author liu.bin 2017/9/28 14:23
     */
    public function init_socket($socket);


    /**
     * 获取socket
     * @return mixed
     * @author liu.bin 2017/9/28 15:06
     */
    public function getSocket();




    /**
     * 获取连接编号
     * @author liu.bin 2017/9/28 14:18
     */
    public function getId();




}