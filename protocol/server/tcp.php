<?php
namespace protocol\server;

use protocol\protocol;

class tcp extends protocol
{


	

	
	
	/**
     * 数据解包
     * @param $buffer string
     * @return string
     * */
    protected function decode($buffer){
        $buffer = str_replace(PHP_EOL, '', $buffer);
        return $buffer;
	}

    /**
     * 数据打包
     * @param $buffer string
     * @return string
     * */
    protected function encode($buffer){
        $buffer = str_replace(PHP_EOL, '', $buffer);
		return $buffer;
	}



    /**
     * 数据输出
     */
    public function output(){
        return $this->receive_data;
    }



    /**
     * 数据输入
     * 阻塞函数
     * @param $socket resource|null 客户端socket
     * @return bool|string
     */
    public function input($socket=null)
    {

        //接收客户端消息
        if(empty(socket_recv($socket,$this->buffer,$this->buffer_size,0))){
            $this->logout_flag = true;
        }

        $this->buffer = $this->decode($this->buffer);
        $this->receive_data = $this->buffer;


        if($this->logout_flag){
            $this->receive_data = '';
            $this->buffer = '';
            return false;
        }else{
            $data = $this->receive_data;
            $this->receive_data = '';
            $this->buffer = '';
            return $data;
        }
    }


}