<?php


defined('IS_WIN')           or define('IS_WIN',         strtoupper(substr(PHP_OS,0,3))==='WIN' ? true : false);
defined('IS_CLI')           or define('IS_CLI',		    PHP_SAPI=='cli'? 1   :   0);    //运行环境检测

defined('WS_ROOT')          or define('WS_ROOT',		dirname(__DIR__) . '/');//定义根目录
defined('WS_RUA')           or define('WS_RUA',			WS_ROOT . 'rua/'  );    //框架目录
defined('WS_RUNTIME')       or define('WS_RUNTIME',     WS_ROOT . 'runtime/');  //运行目录
defined('WS_APP')           or define('WS_APP',		    WS_ROOT . 'app/');      //应用目录
defined('WS_SOCKET')        or define('WS_SOCKET',		WS_ROOT . 'socket/');   //socket目录


defined('RUA_SOCK_TCP')     or define('RUA_SOCK_TCP','rua_tcp_socket');
defined('RUA_SOCK_UDP')     or define('RUA_SOCK_UDP','rua_udp_socket');



class rua{
	

    static $server;

    static $client;

    static $is_init = false;


	//程序初始化
	static public function init(){



        if(!IS_CLI) {
            die('must cli run');
        }



        // 注册AUTOLOAD方法
        spl_autoload_register('\rua::autoload');

		error_reporting(E_ALL);//报告所有错误
		ob_implicit_flush(true);//打开绝对刷送


		// 读取加载文件
		$load = include WS_RUA .'autoload.php';




		// 加载函数和类文件
		foreach ($load['common'] as $file){
			if(is_file($file)) {
				include $file;
			}
		}



		// 加载配置文件 保存配置信息到静态变量，可用C函数调用
		foreach ($load['config'] as $key=>$file){
			if(is_file($file)) {
				is_numeric($key)?C(include $file):C($key,include $file);
			}
		}

	}
	
	
	/**
     * 类库自动加载
     * @param string $class 对象类名
     * @return bool
     */
    public static function autoload($class) {

		if(empty(WS_ROOT)){
			return false;
		}

        //加载文件
		$file = WS_ROOT . str_replace('\\','/',$class) . '.php';
		if(is_file($file)){
			include $file;
			return true;
		}else{
			return false;
		}

    }


    /**
     * 获取server对象
     * @param $server
     * @param $host
     * @param $port
     * @return mixed
     * @author liu.bin 2017/9/27 16:22
     */
    static public function server($server='',$host='',$port=0){

        if(!self::$is_init){
            rua::init();
            self::$is_init = true;
        }
        if(self::$server){
            return self::$server;
        }else{
            $class = '\server\\'.$server;
            self::$server = new $class($host,$port);
            return self::$server;
        }
    }


    /**
     * 客户端 对象
     * @param $client
     * @param $host
     * @param $port
     * @return mixed
     * @author liu.bin 2017/9/27 14:51
     */
    static public function client($client='',$host='',$port=0){

        if(!self::$is_init){
            rua::init();
            self::$is_init = true;
        }

        if(self::$client){
            return self::$client;
        }else{
            $class = '\server\\'.$client;
            self::$client = new $class($host,$port);
            return self::$client;
        }

    }






}


