<?php
namespace protocol\client;

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
     * @param $socket resource 客户端socket
     * @return bool|string
     */
    public function input($socket=null)
    {

        while(!preg_match($this->eof_pattern, $this->buffer)){
            $this->buffer = $this->encode($this->buffer);
            $this->receive_data .= $this->buffer;
            $this->buffer = fgets(STDIN);
        }
        $data = $this->receive_data . $this->buffer;
        $this->receive_data = '';
        $this->buffer = '';
        return $data;

    }
}