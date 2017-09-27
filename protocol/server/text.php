<?php
namespace protocol\server;

use protocol\protocol;

class text extends protocol
{
	

	//eof边界检测符
	private $package_eof = '/r/n';
	
	//边界符正则
    private $eof_pattern = '/\/r\/n/';
	

	

	
	
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
     * @阻塞函数
     * @param $socket resource|null 客户端socket
     * @return bool|string
     */
    public function input($socket=null)
    {

        //上一条消息
        $pre_buffer = '';

        //是否接收消息
        $receive_ing_mess_package = true;


        while ($receive_ing_mess_package) {

            //接收客户端消息
            if(empty(socket_recv($socket,$this->buffer,$this->buffer_size,0))){
                $this->logout_flag = true;
                $receive_ing_mess_package = false;
            }

            //数据解包
            $this->buffer = $this->decode($this->buffer);


            //上一条消息的长度
            $pre_buffer_length = strlen($pre_buffer);


            //组合上一条消息和下一条消息
            $merge_buffer = $pre_buffer . $this->buffer;


            //验证消息是否到达边界
            if( preg_match($this->eof_pattern, $merge_buffer)){

                //到达边界
                $mess_packages = explode($this->package_eof,$merge_buffer);


                if(count($mess_packages) == 2){

                    $this->receive_data = preg_replace($this->eof_pattern, '', $this->buffer);

                }else{

                }



                $receive_ing_mess_package = false;
            }else{

                //没有到达边界
                $this->receive_data .= $this->buffer;
            }

            $pre_buffer = $this->buffer;
        }

        if($this->logout_flag){
            $this->receive_data = '';
            $this->buffer = '';
            return false;
        }else{
            $data = $this->receive_data .$this->package_eof;
            $this->receive_data = '';
            $this->buffer = '';
            return $data;
        }
    }
}