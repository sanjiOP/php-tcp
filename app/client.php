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
$client = rua::client('tcp_client');


/**
 * 客户端开启
 */
$client->on('start',function ($client){
    console('server : 【'. $client .'】 start');
});


/**
 * 客户端连接
 */
$client->on('connect',function ($client){
    console('client connect : 【' . $client.'】');
});



/**
 * 客户端接收消息
 */
$client->on('receive',function ($client, $data){
    console('【' . $client . '】 say : ' . $data);
});


/**
 * 客户端断开
 */
$client->on('close',function ($client){
    console('client ' .$client. ' close');
});


$connect = $client->connect('127.0.0.1',5000);


while ($connect){
    //此处阻塞
    $data = $protocol->in();
    $client->send($this->clientSocket,$data);
    $mess = socket_read($this->clientSocket,1024);
    console('recv data from server : ' . $mess);
}