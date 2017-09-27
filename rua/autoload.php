<?php

return array(
    // 配置文件
    'config' =>  array(
        WS_RUA      . 'config/config.php',      // 系统配置
        WS_SOCKET   . 'config/config.php',      // socket配置
        WS_APP      . 'config/config.php',      // 应用配置
    ),

    // 通用类和函数
    'common' =>  array(
        WS_RUA      . 'common/function.php',      // 系统公共函数
        WS_SOCKET   . 'common/function.php',      // socket函数
        WS_APP      . 'common/function.php',      // 应用函数
    ),

);

