<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/26
 * Time: 10:52
 */

namespace server;

use protocol\server\tcp;

class tcp_server extends server {


    const VERSION = '0.0.1';

    /**
     * 采用的协议
     * @author liu.bin 2017/9/27 16:44
     */
    public function getProtocol(){
        return new tcp();
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
        echo "----------------------- RUA SOCKET -----------------------------".PHP_EOL;
        echo "Rua version:" . self::VERSION .PHP_EOL;
        echo "PHP version:" . PHP_VERSION .PHP_EOL;
        echo "socket listen ".$this->host. ":" .$this->port ." status [ok]".PHP_EOL;
        echo "----------------------------------------------------------------".PHP_EOL;
        echo "Press Ctrl-C to quit. Start success.".PHP_EOL;
    }

}