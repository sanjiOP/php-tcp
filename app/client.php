<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/26
 * Time: 10:51
 */


$dir = dirname(__DIR__);

require $dir . '/rua/rua.php';

rua::worker(rua::TCP_CLIENT)->connect('127.0.0.1',4000)->run();