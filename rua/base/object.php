<?php
namespace rua\base;



class object
{


    /**
     * 获取类名
     * @return string
     * @author liu.bin 2017/9/27 18:09
     */
    public function __toString()
    {
        return get_called_class();
    }

}