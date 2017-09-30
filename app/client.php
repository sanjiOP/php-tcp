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
$client = rua::client('length_client');


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
    console('client connect : 【' . $client.'】','connect');
});



/**
 * 客户端接收消息
 */
$client->on('receive',function ($client, $data){
    console('【' . $client . '】 say : ' . $data,'receive');
});


/**
 * 客户端断开
 */
$client->on('close',function ($client){
    console('client ' .$client. ' close','client');
});


$connect = $client->connect('127.0.0.1',5000);

$protocol = $client->getProtocol();

$receive = true;


while ($connect){


    //连接成功后，接收消息
    if($receive){
        $client->receive();
    }

    console('==============手动输入===============');
    $buffer_size = $protocol->get_buffer_size();

    while(true){


        $buffer_size = $protocol->get_buffer_size();

        //此处阻塞，用户输入的时候，最后需要输入/r/n结束，在read_buffer中，会删除/r/n，在最后send的时候，会添加上/r/n
        $buffer = fgets(STDIN);
        if ($buffer === '' || $buffer === false) {
            break;
        }

        //固定包头包体专用
        $buffer = $protocol->encode($buffer);


        //是否继续接收消息:true 继续读取，false:读取结束
        if(!$protocol->read_buffer($buffer)){
            break;
        }
    }

    $send_data = $protocol->getInData();
    if($send_data !== ''){
        $client->send($send_data);
    }else{
        console('没有消息 ：' .$send_data);
        $receive = false;
    }

}