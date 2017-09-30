<?php
namespace protocol;

use rua\base\error;

abstract class protocol extends error
{

	
	/**
     * 缓冲区数据
     */
	protected $buffer = '';

    /**
     * 读取缓冲区大小
     * @var int
     */
	protected $buffer_size = 10;


    /**
     * 输入的数据
     * @var string
     */
	protected $in_data = '';



    /**
     * 已输入的长度
     * @var int
     */
    protected $in_size = 0;


    /**
     * 单包接收的输入长度
     * 因为tcp是数据流，如果定义的包的概念，在没有完整接收整个包的时候，会一直接收下去，造成内存泄漏
     */
	protected $max_in_length = 100;



	
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
	    return $buffer;
    }



    /**
     * 返回已接收的数据
     * @author liu.bin 2017/9/30 10:08
     */
    public function getInData(){
        $data = $this->in_data;
        $this->over();
        return $data;
    }


    /**
     * 是否继续读取buffer
     * @param string $buffer
     * @return bool false:不需要继续接收消息 ，true:继续接收消息
     * @author liu.bin 2017/9/29 14:37
     */
    abstract public function read_buffer($buffer='');


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


    /**
     * 读取结束
     * @return mixed
     * @author liu.bin 2017/9/30 9:57
     */
    abstract public function over();

}