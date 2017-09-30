<?php
namespace protocol\client;



/**
 * EOF检测协议
 * 在数据发送结尾加入特殊字符，表示一个请求传输完毕
 * 该协议只解决数据包合并，不解决拆分。
 * Class text
 * @package protocol\server
 */
class text extends clientProtocol
{
	

	//eof边界检测符
	private $package_eof = '/r/n';
	
	//边界符正则
    private $eof_pattern = '/\/r\/n/';
    /**
     * 上一次接收的buffer的最后x(x由$package_eof长度决定)个字符，主要解决$package_eof被分开发送的情况
     * @var string
     */
    private $pre_last_buffer = '';

    /**
     * 是否到达边界
     * @var bool
     */
    private $eof_end = false;

	/**
     * 数据解包
     *
     *
     * @param $buffer string
     * @return string
     * */
    public function decode($buffer){

        $buffer = str_replace(PHP_EOL, '', $buffer);
        return $buffer;
	}



    /**
     * 数据打包
     * @param $buffer string
     * @return string
     * */
    public function encode($buffer){
        $buffer = str_replace(PHP_EOL, '', $buffer);
		return $buffer.$this->package_eof;
	}



    /**
     * 是否继续读取buffer
     * @param string $buffer
     * @return bool false:不需要继续接收消息 ，true:继续接收消息
     * @author liu.bin 2017/9/29 14:37
     */
    public function read_buffer($buffer = '')
    {

        console('解码之前 ：' . $buffer);
        //消息格式不正确
        if(empty($buffer)){
            $this->over();
            return false;
        }


        //如果接收的字节 >= 最大长度的话，就不用接收消息,数据重置
        if($this->in_size >= $this->max_in_length){
            $this->over();
            return false;
        }

        //解码
        $buffer = $this->decode($buffer);

        $this->buffer = $buffer;
        return $this->eof($this->buffer) ? false : true;

    }




    /**
     * 检测 数据到达边界
     * @param string $buffer
     * @return bool true:到达边界；false没有到达边界
     * @author liu.bin 2017/9/30 13:25
     */
    private function eof($buffer){

        $this->in_data .= $buffer;
        //检测是否有 package_eof
        if(preg_match($this->eof_pattern, $this->in_data)){
            list($this->in_data) = explode($this->package_eof,$this->in_data,2);
            $this->in_size = strlen($this->in_data);
            return true;
        }else{
            $this->in_size = strlen($this->in_data);
            return false;
        }
    }


    /**
     * 重置
     * @author liu.bin 2017/9/30 13:34
     */
    public function over()
    {
        $this->eof_end = false;
        parent::over();
    }

}