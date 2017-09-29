<?php
namespace protocol\server;

use protocol\protocol;


/**
 * EOF检测协议
 * 在数据发送结尾加入特殊字符，表示一个请求传输完毕
 * 该协议只解决数据包合并，不解决拆分。
 * Class text
 * @package protocol\server
 */
class text extends protocol
{
	

	//eof边界检测符
	private $package_eof = '/r/n';
	
	//边界符正则
    private $eof_pattern = '/\/r\/n/';

    //是否继续读取
    private $on_read_buffer = false;
	
	
	/**
     * 数据解包
     *
     *
     * @param $buffer string
     * @return string
     * */
    public function decode($buffer){

        $buffer = str_replace(PHP_EOL, '', $buffer);

        //验证消息是否到达边界
        if( preg_match($this->eof_pattern, $buffer)){

            //到达边界，connect 不需要继续读取
            $this->on_read_buffer = false;
            $mess_packages = explode($this->package_eof,$buffer);
            return $mess_packages[0];
        }else{

            //没有到达边界
            $this->on_read_buffer = true;
        }

        return $buffer;
	}



    /**
     * 数据打包
     * @param $buffer string
     * @return string
     * */
    public function encode($buffer){
        $buffer = str_replace(PHP_EOL, '', $buffer);
		return $buffer . $this->package_eof;
	}







    /**
     * 是否继续读取buffer
     * @param string $buffer
     * @return mixed
     * @author liu.bin 2017/9/29 14:37
     */
    public function on_read_buffer($buffer = '')
    {
        $this->buffer = $buffer;
        $this->decode($buffer);

        return $this->on_read_buffer;
    }
}