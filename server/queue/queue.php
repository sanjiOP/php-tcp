<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/28
 * Time: 11:21
 */

namespace server\queue;

use rua\base\interfaceSocket;
use server\connect\connect;
use server\server;

class queue {


    /**
     * socket list
     * @var array
     */
    protected static $sockets = [];


    /**
     * 当前连接的数量
     * @var integer
     */
    protected static $active_count = 0;


    /**
     * 当前活动的连接
     * @var array
     */
    protected static $active_list = [];


    /**
     * 当前等待的数量
     * @var int
     */
    protected static $task_count = 0;

    /**
     * 当前等待的连接
     * @var array
     */
    protected static $task_list = [];


    /**
     * 已关闭的数量
     * @var int
     */
    protected static $close_count = 0;




    /**
     * 活动队列最大长度
     * @var int
     */
    private static $active_limit = 5;


    /**
     * 等待队列最大长度
     * @var int
     */
    private static $task_limit = 10;


    /**
     * 在活动队列末尾增加一个元素，如果超出则添加到等待队列
     * 保证队列元素唯一性
     * @param $conn connect 元素
     * @return bool
     * @author liu.bin 2017/9/28 11:36
     */
    public static function add($conn){

        if(!($conn instanceof interfaceSocket)){
            return false;
        }


        $key = $conn->getId();
        if(empty($key)){
            return false;
        }

        //添加到活动队列
        if(self::$active_count < self::$active_limit){
            if(!isset(self::$active_list[$key]) || empty(self::$active_list[$key]) ){
                self::$active_list[$key] = $conn;
                self::$active_count++;
                self::$sockets[$key] = $conn->getSocket();
                $conn->setStatus(connect::STATUS_ACTIVE);
            }
            return true;
        }

        //添加到等待队列
        if((self::$active_count >= self::$active_limit) && (self::$task_count < self::$task_limit)){
            if(!isset(self::$task_list[$key]) || empty(self::$task_list[$key])){
                self::$task_list[$key] = $conn;
                self::$task_count++;
                $conn->setStatus(connect::STATUS_TASK);
            }
            return true;
        }

        return false;

    }


    /**
     * 服务端启动的时候，添加到队列中
     * @param $server server
     * @return bool
     * @author liu.bin 2017/9/28 15:58
     */
    public static function addServer($server){

        if(!($server instanceof interfaceSocket)){
            return false;
        }

        $key = $server->getId();
        if(empty($key)){
            return false;
        }


        self::$active_list[$key] = $server;
        self::$sockets[$key] = $server->getSocket();

    }



    /**
     * 移除一个元素
     * @param $conn interfaceSocket 元素
     * @return bool
     * @author liu.bin 2017/9/28 13:34
     */
    public static function remove($conn){

        if(!($conn instanceof interfaceSocket)){
            return false;
        }

        $key = $conn->getId();
        if(empty($key)){
            return false;
        }
        //从活动列表中删除
        if( isset(self::$active_list[$key]) && self::$active_list[$key] ){
            //$conn->setStatus(connect::STATUS_CLOSE);
            unset($conn);
            unset(self::$active_list[$key]);
            unset(self::$sockets[$key]);
            self::$active_count--;
            return true;
        }
        //从等待列表中删除
        if( isset(self::$task_list[$key]) && self::$task_list[$key] ){
            //$conn->setStatus(connect::STATUS_CLOSE);
            unset($conn);
            unset(self::$task_list[$key]);
            self::$task_count--;
            return true;
        }

    }



    /**
     * 查找一个connect
     * @param $socket
     * @return interfaceSocket|bool
     * @author liu.bin 2017/9/28 14:55
     */
    public static function findConnBySocket($socket){

        $key = socket_id($socket);
        if(empty($key)){
            return false;
        }

        //从活动列表中查找
        if( isset(self::$active_list[$key]) && self::$active_list[$key] ){
            return self::$active_list[$key];
        }

        //从等待列表中删除
        if( isset(self::$task_list[$key]) && self::$task_list[$key] ){
            return self::$task_list[$key];
        }

        return false;
    }




    /**
     * 重置所有的活动 socket
     * @author liu.bin 2017/9/28 15:22
     */
    public static function init_sockets(){
        self::$sockets = array();
        foreach (self::$active_list as $conn){
            self::$sockets[] = $conn->getSocket();
        }
    }


    /**
     * 获取所有的 socket
     * @return array
     * @author liu.bin 2017/9/28 15:22
     */
    public static function sockets(){
        return self::$sockets;
    }


    /**
     * 激活一个等待的连接
     * @author liu.bin 2017/9/28 14:03
     */
    public static function active(){



    }



    /**
     * 获取活动的元素
     * @return array
     * @author liu.bin 2017/9/28 13:58
     */
    public static function get_actives(){
        return self::$active_list;
    }


    /**
     * 获取活动的数量
     * @author liu.bin 2017/9/28 13:59
     */
    public static function get_active_count(){
        return self::$active_count;
    }


    /**
     * 获取等待的列表
     * @author liu.bin 2017/9/28 14:00
     */
    public static function get_tasks(){
        return self::$task_list;
    }


    /**
     * 获取等待的数量
     * @author liu.bin 2017/9/28 14:01
     */
    public static function get_tasking_count(){
        return self::$task_count;
    }



    /**
     * 获取关闭的数量
     * @author liu.bin 2017/9/28 14:01
     */
    public static function get_close_count(){
        return self::$close_count;
    }

}