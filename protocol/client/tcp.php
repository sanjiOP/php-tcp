<?php
namespace protocol\client;

use protocol\protocol;

class tcp extends protocol
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
     * 是否继续读取buffer
     * @param string $buffer
     * @return mixed
     * @author liu.bin 2017/9/29 14:37
     */
    public function on_read_buffer($buffer = '')
    {
        $length = strlen($buffer);
        if( $length > $this->buffer_size ){
            $this->buffer = substr($buffer,0,$this->buffer_size);
        }else{
            $this->buffer = $buffer;
        }
        return false;
    }


}