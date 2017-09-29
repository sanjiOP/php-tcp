<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/26
 * Time: 10:51
 */


$dir = dirname(__DIR__);
require $dir . '/rua/rua.php';

// tcp客户端
$client = rua::client('text_client');


/**
 * 客户端开启
 */
$client->on('start',function ($client){
    console('server : 【'. $client .'】 start','client');
});


/**
 * 客户端连接
 */
$client->on('connect',function ($client){
    console('client connect : 【' . $client.'】','client');
});



/**
 * 客户端接收消息
 */
$client->on('receive',function ($client, $data){
    console('【' . $client . '】 say : ' . $data,'client');
});


/**
 * 客户端断开
 */
$client->on('close',function ($client){
    console('client ' .$client. ' close','client');
});


$connect = $client->connect('127.0.0.1',5000);

$protocol = $client->getProtocol();


while ($connect){


    //连接成功后，接收消息
    $client->receive();

    $buffer_size = $protocol->get_buffer_size();
    $send_data = '';

    $end = false;//是否继续输入

    while(!$end){

        //此处阻塞
        $buffer = fgets(STDIN);

        if ($buffer === '' || $buffer === false) {
            break;
        }

        //是否继续输入消息
        if(!$protocol->on_read_buffer($buffer)){
            $end = true;
        }

        //读取完整消息
        $send_data .= $protocol->get_buffer();
    }


    if($send_data){
        $send_data = $protocol->encode($send_data);
        $client->send($send_data);
    }

}