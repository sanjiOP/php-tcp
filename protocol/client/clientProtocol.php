<?php
namespace protocol\client;

use protocol\protocol;



abstract class clientProtocol extends protocol
{




    /**
     * 重置数据
     * @author liu.bin 2017/9/30 10:51
     */
    public function over()
    {
        $this->buffer = '';
        $this->in_data = '';
        $this->in_size = 0;
    }


}