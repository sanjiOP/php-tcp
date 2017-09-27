<?php
namespace rua\base;



class error extends object
{


    /**
     * 错误码:
     *
     * socket 1000
     *
     * server 2000
     *
     * client 3000
     *
     * protocol 4000
     *
     * poll 5000
     *
     * other 9000
     *
     * @var int
     */
    protected $error_code = 0;


    /**
     * 错误信息
     * @var string
     */
    protected $error = '';



    /**
     * 获取错误信息
     * @return string
     * @author liu.bin 2017/9/27 15:22
     */
    public function getError(){
        return $this->error;
    }


    /**
     * 获取错误码
     * @author liu.bin 2017/9/27 15:22
     */
    public function getErrorCode(){

    }


}