<?php
namespace protocol;

use rua\base\error;

abstract class protocol extends error
{

	
	//接收客户端消息
	protected $receive_data = '';
	
	//缓冲区数据
	protected $buffer = '';
	
	//读取缓冲区大小,如果采用 package_eof 边界符协议，则缓冲区最小需要大于 package_eof长度，否则永远读不到完成数据
	protected $buffer_size = 10;
	
	//客户端是否退出
	protected $logout_flag = false;
	
	
	
	
	/**
	 * 构造器
	 */
	public function __construct(){

	}


    /**
     * 数据输入
     * @param $socket resource 客户端socket
     * @return bool|string
     */
	abstract public function input($socket=null);



	/**
	 * 数据输出
	 */
	abstract public function output();


    /**
     * 数据解包
     * @param $buffer string
     * @return string
     * */
    abstract protected function decode($buffer);



    /**
     * 数据打包
     * @param $buffer string
     * @return string
     * */
    abstract protected function encode($buffer);
	
	
}