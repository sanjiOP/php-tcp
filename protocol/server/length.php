<?php
namespace protocol\server;



class length extends serverProtocol
{


    /**
     * header 长度，采用固定4个字节的方式
     * @var int
     */
    protected $head_size = 4;




    /**
     * body 长度，从header中解码获取，变长
     * @var int
     */
    protected $body_size = 0;




    /**
     * 第一次读取
     * @var bool
     */
    protected $first_read = true;




	/**
     * 数据解包
     * @param $buffer string
     * @return string
     * */
    public function decode($buffer){

        $buffer = str_replace(PHP_EOL, '', $buffer);
        return $buffer;
	}

    /**
     * 数据打包
     * @param $buffer string
     * @return string
     * */
    public function encode($buffer){
        $buffer = str_replace(PHP_EOL, '', $buffer);
		return $buffer;
	}




    /**
     * 是否继续读取buffer
     * 获取的body后，继续读取buffer,知道buffer读取完整，读取完整后，重置 buffer_size
     * @param string $buffer
     * @return bool false:不需要继续接收消息 ，true:继续接收消息
     * @author liu.bin 2017/9/29 14:37
     */
	public function read_buffer($buffer=''){

	    //消息格式不正确
        $buffer_length = strlen($buffer);
	    if(empty($buffer) || $buffer_length <= $this->head_size){
	        $this->over();
	        return false;
        }


        //如果输入的字节 >= 最大长度的话，就不用输入数据,数据重置
        if($this->in_size >= $this->max_in_length){
            $this->over();
            return false;
        }




        //第一次读取消息
	    if($this->first_read && (0 === $this->body_size)){

            $this->buffer = $this->decode($buffer);

            //获取头部
	        $head = substr($buffer,0,$this->head_size);

            //从头部获取body长度
	        $this->body_size = unpack('N',$head);

            //获取body
            $body = substr($buffer,$this->head_size);
	        $this->first_read = false;

        }else{

            //获取body
            $body = $buffer;
        }


	    //如果接收的字节 >= 最大长度的话，就不用接收消息,数据重置
	    if($this->in_size >= $this->max_in_length){
	        $this->over();
	        return false;
        }


        //是否还有剩余数据没有接收：$left_length：还剩多少长度没有接收
        $left_length = $this->body_size - $this->in_size;
        if($left_length <= 0){
	        return false;
        }


        //设置下次接收的buffer长度
        if($left_length < $this->buffer_size){
            $this->buffer_size = $left_length;
        }

        //当前接收到body长度
        $body_length = strlen($body);

        if($body_length <= $left_length){

            //验证当前body长度 <= 需要接收的长度，继续接收
            $this->in_data .= $body;
            $this->in_size += $body_length;
            return true;
        }else{

            //验证当前body长度 >= 需要接收的长度，不需要接收
            $this->in_data = substr($body,0,$left_length);
            $this->in_size += $left_length;
            return false;
        }


    }





    /**
     * 读取结束
     * @return mixed
     * @author liu.bin 2017/9/30 9:57
     */
    public function over()
    {
        $this->first_read = true;
        $this->body_size = 0;
        parent::over();
    }




}