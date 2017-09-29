<?php
namespace protocol;

use rua\base\error;

abstract class protocol extends error
{

	
	//缓冲区数据
	protected $buffer = '';
	
	//读取缓冲区大小,如果采用 package_eof 边界符协议，则缓冲区最小需要大于 package_eof长度，否则永远读不到完成数据
	protected $buffer_size = 10;
	
	
	
	
	/**
	 * 构造器
	 */
	public function __construct(){

	}


    /**
     * 获取buffer size
     * @author liu.bin 2017/9/29 13:37
     */
	public function get_buffer_size(){
	    return $this->buffer_size;
    }


    /**
     * 读取buffer
     * @author liu.bin 2017/9/29 14:42
     */
    public function get_buffer(){
        $buffer = $this->decode($this->buffer);
        $this->buffer = '';
	    return $buffer;
    }


    /**
     * 是否继续读取buffer
     * @param string $buffer
     * @return mixed
     * @author liu.bin 2017/9/29 14:37
     */
    abstract public function on_read_buffer($buffer='');


    /**
     * 数据解包
     * @param $buffer string
     * @return string
     * */
    abstract public function decode($buffer);



    /**
     * 数据打包
     * @param $buffer string
     * @return string
     * */
    abstract public function encode($buffer);
	
	
}