<?php
namespace protocol\client;


class tcp extends clientProtocol
{


	

	
	
	/**
     * 数据解包
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
		return $buffer;
	}


    /**
     * 只读一次输入：
     * 如果内容长度超过缓冲区长度，则截取舍弃
     * @param string $buffer
     * @return bool false:不需要继续接收消息 ，true:继续接收消息
     * @author liu.bin 2017/9/29 14:37
     */
    public function read_buffer($buffer = '')
    {


        //消息格式不正确
        if(empty($buffer)){
            $this->over();
            return false;
        }


        //如果输入的字节 >= 最大长度的话，就不用输入数据,数据重置
        if($this->in_size >= $this->max_in_length){
            $this->over();
            return false;
        }

        $this->buffer = $this->decode($buffer);
        $length = strlen($this->buffer);


        //如果输入的数据 > 缓冲区的长度 则截取
        if( $length > $this->buffer_size ){
            $this->in_data = substr($this->buffer,0,$this->buffer_size);
            $this->in_size = strlen($this->in_data);
        }else{
            $this->in_data = $this->buffer;
            $this->in_size = strlen($this->buffer);
        }
        return false;
    }
}