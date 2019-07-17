<?php
namespace Sf\Message;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Sf\Base\Error;
use Sf\Logs\Log;

/**
 * Class RabbitMQ Command
 */
class RabbitMQ{

    public $configs = array();
    //交换机名称
    public $exchange_name = '';
    //队列名称
    public $queue_name = '';
    //路由名称
    public $route_key = '';
    /*
     * 持久化，默认True
     */
    public $durable = True;
    /*
     * 自动删除
     * exchange is deleted when all queues have finished using it
     * queue is deleted when last consumer unsubscribes
     *
     */
    public $autodelete = False;
    /*
     * 镜像
     * 镜像队列，打开后消息会在节点之间复制，有master和slave的概念
     */
    public $mirror = False;

    private $_conn = Null;
    private $_exchange = Null;
    private $_channel = Null;
    private $_queue = Null;

    public function __construct($configs = array(), $exchange_name = '', $queue_name = '', $route_key = '') {
        $this->setConfigs($configs);
        $this->exchange_name = $exchange_name;
        $this->queue_name = $queue_name;
        $this->route_key = $route_key;

        //创建连接和channel
        $this->connection();
    }

    /**
     * 创建连接和channel信道
     */
    private function connection(){
        $this->_conn = new AMQPStreamConnection($this->configs['host'], $this->configs['port'], $this->configs['login'], $this->configs['password']);
        $this->_channel = $this->_conn->channel();
    }

    /**
     * 设置配置
     * @param $configs
     * @throws \Exception
     */
    private function setConfigs($configs) {
        if (!is_array($configs)) {
            Error::error('configs is not array');
        }
        if (!($configs['host'] && $configs['port'] && $configs['username'] && $configs['password'])) {
            Error::error('configs is empty');
        }
        if (empty($configs['vhost'])) {
            $configs['vhost'] = '/';
        }
        $configs['login'] = $configs['username'];
        unset($configs['username']);
        $this->configs = $configs;
    }

    /**
     * 关闭连接
     */
    private function close(){
        $this->_channel->close();
        $this->_conn->close();
    }

    /**
     * 生产者
     * @param string $message
     * @throws \Exception
     */
    public function send(string $message){
//        $message = implode(' ', array_slice($argv, 1));
        if (empty($message)) {
//            $message = "Hello World!";
            Error::error('rabbitmq message is empty');
        }
        //创建队列
        $this->_channel->queue_declare($this->queue_name, false, true, false, false);
        $msg = new AMQPMessage(
            $message,
            array('delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT)
        );

        //发送信息
        $this->_channel->basic_publish($msg, '', $this->queue_name);

//        echo ' [x] Sent ', $message, "\n";
        Log::INFO('Rabbitmq Send Message: '. $message. "\n");

        //关闭连接
        $this->close();
    }

    /**
     * 消费者
     */
    public function run(){
        //创建队列
        $this->_channel->queue_declare($this->queue_name, false, true, false, false);

//        echo " [*] Waiting for messages. To exit press CTRL+C\n";
        Log::INFO("Rabbitmq Waiting for messages. queue：".$this->queue_name."\n");
        $callback = function ($msg) {
//            echo ' [x] Received ', $msg->body, "\n";
            Log::INFO('Rabbitmq queue：'.$this->queue_name.' -> Received Message: '. $msg->body. "\n");
            sleep(substr_count($msg->body, '.'));
//            echo " [x] Done\n";
            Log::INFO('Rabbitmq queue：'.$this->queue_name." -> Done \n");
            //确定此消息已经处理完成，否则不确认的话，queue会将此消息交给其他consumer处理哦
            $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
        };

        //同时最多处理1条信息
        $this->_channel->basic_qos(null, 1, null);
        //回调
        $this->_channel->basic_consume($this->queue_name, '', false, false, false, false, $callback);

        //注意，此处会一直循环下去
        while (count($this->_channel->callbacks)) {
            $this->_channel->wait();
        }

        //关闭连接
        $this->close();
    }

}