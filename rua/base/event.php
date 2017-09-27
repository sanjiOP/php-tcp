<?php
namespace rua\base;



class event extends error
{

    /**
     * 服务器启动 事件
     */
    const EVENT_START = 'start';

    /**
     * 客户端连接 事件
     */
    const EVENT_CONNECT = 'connect';


    /**
     * 接收客户端消息 事件
     */
    const EVENT_RECEIVE = 'receive';


    /**
     * 客户端断开 事件
     */
    const EVENT_CLOSE = 'close';




    /**
     * 注册的事件列表
     * @var array
     */
    protected $events = [];

    /**
     * 注册事件
     * @param $event
     * @param $callback
     * @author liu.bin 2017/9/27 14:43
     */
    public function on($event,$callback){
        $this->events[$event] = $callback;
    }


    /**
     * 事件触发，通知监听者
     * @param $event string 触发事件
     * @param $param array 传递参数
     * @author liu.bin 2017/9/27 16:29
     */
    protected function trigger($event,$param=array()){


        if(isset($this->events[$event]) && 'object' == gettype($this->events[$event])){
            call_user_func_array($this->events[$event],$param);
        }

    }


}