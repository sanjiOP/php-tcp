<?php


namespace client;

use protocol\client\tcp;

class tcp_client extends client
{


    const VERSION = '0.0.1';


    protected $protocol;


    /**
     * 采用的协议
     * @author liu.bin 2017/9/27 16:44
     */
    public function getProtocol(){
        if(!$this->protocol){
            $this->protocol = new tcp();
        }
        return $this->protocol;
    }



    /**
     * 展示启动界面
     * @return void
     */
    protected function displayUI()
    {
        global $argv;
        if(in_array('-q', $argv))
        {
            return;
        }
        echo "----------------------- RUA SOCKET CLIENT-----------------------------".PHP_EOL;
        echo "Rua client version:" . self::VERSION .PHP_EOL;
        echo "PHP version:" . PHP_VERSION .PHP_EOL;
        echo "socket connect ".$this->host. ":" .$this->port ." status [ok]".PHP_EOL;
        echo "----------------------------------------------------------------".PHP_EOL;
        echo "Press Ctrl-C to quit. Start success.".PHP_EOL;
    }





}

