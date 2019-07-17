<?php
namespace App\Web\Controllers;

use App\Web\Models\WelcomeModel;
use Sf\Web\Controller;//继承web控制器基类
use Sf;
use Sf\View\Smarty;
use Sf\Libs\Mail;
use Sf\Libs\Upload;
use Sf\Logs\Log;
use Sf\Base\Csrf;
use Sf\Cache\RedisCache;
use Sf\Cache\Redis;
use Sf\Message\RabbitMQ;

class WelcomeController extends Controller{

    public function __construct(){}

    /**
     * redis实现秒杀
     */
    public function onSecKillAction(){
        $key = 'miaosha';
        if($_GET['type'] == '1'){
            WelcomeModel::secKill($key);
        }elseif($_GET['type'] == '2'){
            WelcomeModel::secKill2($key);
        }
        dd(RedisCache::lrange($key,0,10));
    }

    /**
     * 消息中间件 rabbitmq
     */
    public function onRabbitMQAction(){
        $configs = [
            'host'=>rabbitmq_config('host'),
            'port'=>rabbitmq_config('port'),
            'username'=>rabbitmq_config('username'),
            'password'=>rabbitmq_config('password'),
            'vhost'=>rabbitmq_config('vhost')
        ];
        $exchange_name = 'class-e-1';
        $queue_name = 'class-q-1';
        $route_key = 'class-r-1';
        if($_GET['type'] == '1'){
            for($i=0;$i<=100;$i++){
                $ra = new RabbitMQ($configs,$exchange_name,$queue_name,$route_key);
//                sleep(1);//休眠1秒
                $ra->send($i.' '.date('Y-m-d H:i:s',time()));
            }
        }else{
            $ra = new RabbitMQ($configs,$exchange_name,$queue_name,$route_key);
            $ra->run();
        }
        echo 'rabbitmq Done';
        exit();
    }

    /**
     * 暂时先不处理，待有具体业务场景时再实现
     * 场景一：第三方调用但不登录时，可访问，但是返回的数据会被加密，须第三方按照约定解密才可获取到具体数据，也就不知道提交的参数。故不存在csrf，因为token会过期，麻烦
     * 场景二：第三方调用并登录时，可访问但是须验证对应的登录令牌，令牌会过期
     */
    public function onApiCSRFAction(){
//        dd($_SERVER);
//        $getallheaders = Csrf::getHeaders();
//        $getApiToken = Csrf::getApiCsrfToken();
        $checkApiToken = Csrf::checkApiCsrfToken('csrf-token');
//        echo $getApiToken;
        var_dump($checkApiToken);
//        dd($getallheaders);
    }

    /**
     * 导入数据
     */
    public function onImportDataAction(){
        $this->smartyDisplay('welcome/ImportData.html');
    }

    /**
     * 日志调用
     */
    public function onLogAction(){

//        Log::DEBUG( 'debug' );
//        Log::WARN( 'warn' );
//        Log::ERROR( 'error' );
        Log::INFO( 'info' );

    }

    /**
     * 上传图片文件
     */
    public function onUploadAction(){
        $this->smartyDisplay('welcome/upload.html');
        //上传
//        $upload = new Upload();
//        $configs = [
//            'path' => './uploads',
//            'allowType' => ["png", 'jpg', 'jpeg', 'gif'],
//            'maxSize' => 1000000,
//        ];
//        $res = $upload->upload('file',$configs);
////        dd($res);
//        //缩略图
//        $a = $upload -> imageResize($res['data']['newPath'].$res['data']['newName'],50,50,'50_');
//        dd($a);
    }

    /**
     * 模板实现的原理操作
     */
    public function onIndexAction(){
        $body = 'Web Controller index';
        require ROOT_PATH.'/application/Web/Views/Welcome/Welcome.php';
    }

    /**
     * 数据交互
     */
    public function onModelAction()
    {
//        dd(memory_get_usage(true));
//        $user = WelcomeModel::findOne(['age' => 20, 'name' => 'harry']);
//        $data = [
//            'first' => 'awesome-php-zh_CN',
//            'second' => 'simple-framework',
//            'user' => $user
//        ];
//        echo $this->toJson($data);
        WelcomeModel::select();
//        WelcomeModel::insertSql();
//        WelcomeModel::updateSql();
//        WelcomeModel::deleteSql();
//        WelcomeModel::transaction();
    }

    /**
     * 缓存
     */
    public function onCacheAction()
    {
//        $cache = Sf::createObject('fileCache');
//        $cache->set('test', '我就是测试一下file缓存组件');
//        $result = $cache->get('test');
//        $cache->flush();
//        echo $result;

        /*使用原生redis*/
//        RedisCache::set('test', '我就是测试一下redis缓存组件');
//        RedisCache::set('test2', '我就是测试一下redis缓存组件2');
//        $result = RedisCache::get('test');
//        $result2 = RedisCache::get('test2');
//        RedisCache::flush();

        /*使用composer redis*/
        Redis::set('test', '我就是测试一下composer redis缓存组件');
        Redis::set('test2', '我就是测试一下composer redis缓存组件2');
        $result = Redis::get('test');
        $result2 = Redis::get('test2');

        echo $result;
        echo $result2;
    }

    /**
     * 自己写的模板实现
     */
    public function onViewAction(){
        $this->render('welcome/index', ['body' => 'Test body information']);
        //        $data = ['first' => 'awesome-php-zh_CN', 'second' => 'simple-framework'];
//        echo $this->toJson($data);

        $this->display('welcome/welcome', ['body' => 'Test body information']);
    }

    /**
     * 利用smarty实现模板
     */
    public function onSmartyViewAction(){
        $this->smartyAssign('title', '标题');
        $this->smartyAssign('content', '我使用了smarty进行模板编译');
        $this->smartyDisplay('welcome/smarty.html');
    }

    /**
     * 发送邮件
     */
    public function onSendMailAction(){
        $res = Mail::sendMail('494490727@qq.com','发送邮件','这是一封测试邮件');
        dd($res);
    }
}

?>
