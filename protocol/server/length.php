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
     * @param $mess string
     * @return string
     * */
    public function decode($mess){
        $mess = str_replace(PHP_EOL, '', $mess);
        return $mess;
	}




    /**
     * 数据打包
     * @param $mess string
     * @return string
     * */
    public function encode($mess){
        $mess = str_replace(PHP_EOL, '', $mess);
        $head = pack('N',strlen($mess));
		return $head . $mess;
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
        if('' === $buffer || is_null($buffer)){
            $this->over();
            console('??');
            return false;
        }


        //如果输入的字节 >= 最大长度的话，数据错误，数据重置
        if($this->in_size >= $this->max_in_length){
            $this->over();
            console('---');
            return false;
        }


        //没有消息body,数据错误
        if(!$this->first_read &&  (0 === $this->body_size)){
            $this->over();
            return false;
        }


        //第一次读取消息
        if($this->first_read){

            $this->buffer = $this->decode($buffer);//11

            //获取头部
            $head = substr($buffer,0,$this->head_size);

            if(empty($head)){
                return false;
            }else{
                //从头部获取body长度
                $this->body_size = unpack('N',$head)[1];
            }


            //获取body
            $body = substr($buffer,$this->head_size);
            $this->first_read = false;

        }else{

            //获取body
            $body = $buffer;
        }


        $this->in_data .= $body;
        $this->in_size += strlen($body);

        //是否还有剩余数据没有接收：$left_length 还剩多少长度没有接收（第一次：$this->in_size==0）
        $left_length = $this->body_size - $this->in_size;

        if($left_length > $this->buffer_size){
            return true;
        }elseif ($left_length <= 0 ){
            return false;
        }elseif($left_length < $this->buffer_size){
            $this->buffer_size = $left_length;
            return true;
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
        $this->buffer_size = 10;
        console('重置');
        parent::over();
    }




}