<?php
namespace protocol\server;



class tcp extends serverProtocol
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
     * 缓冲区只读一次：
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


        //如果接收的字节 >= 最大长度的话，就不用接收消息,数据重置
        if($this->in_size >= $this->max_in_length){
            $this->over();
            return false;
        }

        $this->buffer = $this->decode($buffer);

        //如果内容长度超过缓冲区长度，则截取舍弃
        $length = strlen($buffer);
        if( $length > $this->buffer_size ){
            $this->in_data = substr($buffer,0,$this->buffer_size);
            $this->in_size = $this->buffer_size;
        }else{
            $this->in_data = $buffer;
            $this->in_size = $length;
        }

        return false;
    }

}