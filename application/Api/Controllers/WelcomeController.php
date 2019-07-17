<?php
namespace App\Api\Controllers;

use App\Api\Models\WelcomeModel;
use Sf\Base\Error;
use Sf\Unit\Encrypt;
use Sf\Unit\Rsa;
use Sf\Libs\Upload;

class WelcomeController{

    public function __construct()
    {

    }

    public function onIndexAction(){
        echo 'Api Controller index';
    }

    /**
     * 导入数据
     */
    public function onImportDataAction(){
        $configs = [
            'path' => './uploads/Excel',
            'allowType' => ['xlsx','xls'],
            'maxSize' => 1000000,
        ];
        WelcomeModel::import2('import_file1',$configs);
    }

    /**
     * 对称加解密
     */
    public function onEncryptAction(){
        $input = $_GET['input'];
        $input = [
            'a' => 'a',
            'b' => 'b',
            'c' => 'c',
        ];
        if(is_array($input)){
            $str = json_encode($input);
        }else{
            $str = $input;
        }
        $key = '159';
        $res_encrypt = Encrypt::encrypt($str, $key, $iv = "1234567812345678");
        echo '加密数据：'.$res_encrypt.'<br>';
        $res_decrypt = Encrypt::decrypt($res_encrypt, $key, $iv = "1234567812345678");
        if(is_array($input)){
            $res_decrypt = json_decode($res_decrypt);
            if(is_object($res_decrypt)){
                $res_decrypt = get_object_vars($res_decrypt);
            }
        }
        echo '解密数据：'.$res_decrypt.'<br>';
    }

    public function onRsaAction(){
        $data['name'] = 'Tom';
        $data['age']  = '20';
        /*$data = [
            "user_info" =>
                [
                    "id" => 7,
//                    "nickName" => "谦逊的铅笔丶",
                    "phone" => NULL,
                    "email" => NULL,
                    "password" => NULL,
                    "gender" => 1,
                    "language" => "zh_CN",
                    "country" => "China",
                    "province" => "Shanghai",
                    "city" => "Pudong New District",
                    "type" => 0,
                    "status" => 1,
                    "love_code" => "EA15446802577",
                    "other_love_code" => "EA15446204686",
                    "last_ip" => NULL,
                    "last_login" => "2019-07-04 19:50:01",
                    "login_time" => NULL,
                    "avatarUrl" =>  "https://wx.qlogo.cn/mmopen/vi_32/DYAIOgq83eo0lx1gO185kM8v7UK0RfyUBOXD0ZTbtQCqDvO9X5RvVg5LdI0oCfHsvfjDqLJl4PTAa2eicmP92dw/132",
                    "token" => NULL,
                    "token_expire" => NULL,
                    "wx_unionid" => "o0KB158gkpnq8mk28bmKdEjvoprQ",
                    "wx_openid" => "o0KB158gkpnq8mk28bmKdEjvoprQ",
                    "love_time" => "2018-12-11 00:00:00",
                    "create_time" => "2018-12-13 13:50:57",
                    "update_time" => "2019-07-04 19:50:01",
                    "love_time_day" => 206,
                    "save_avatarUrl" =>  "1",
                    "vid" => 1
                ],
            "other_user_info" =>
                [
                    "id" => 6,
                    "nickName" =>  "Circle",
                    "phone" => NULL,
                    "email" => NULL,
                    "password" => NULL,
                    "gender" => 2,
                    "language" => "zh_CN",
                    "country" => "CA",
                    "province" => "Ontario",
                    "city" => "Toronto",
                    "type" => 0,
                    "status" => 1,
                    "love_code" => "EA15446204686",
                    "other_love_code" => "EA15446802577",
                    "last_ip" => NULL,
                    "last_login" => "2019-01-04 19:05:00",
                    "login_time" => 33,
                    "avatarUrl" => "https://wx.qlogo.cn/mmopen/vi_32/DYAIOgq83eqgasIhgVrwBzcic8LLSRhLibUlKWAaS4BiaUTWpPRI21XK3BCWyIKeBTuL3G0DLweR3h1uzW2skHEdA/132",
                    "token" => NULL,
                    "token_expire" => NULL,
                    "wx_unionid" => NULL,
                    "wx_openid" => "oPu9_4mXOFoLM1gNl2F5cm0hsBN4",
                    "love_time" => "2018-12-11 00:00:00",
                    "create_time" => "2018-12-12 21:14:28",
                    "update_time" => "2019-06-24 16:56:23",
                    "save_avatarUrl" =>  "1"
                ]
        ];*/
        $privEncrypt = Rsa::privEncrypt(json_encode($data));
        echo '私钥加密后:'.$privEncrypt.'<br>';

        $publicDecrypt = Rsa::publicDecrypt($privEncrypt);
        echo '公钥解密后:'.$publicDecrypt.'<br>';

        $publicEncrypt = Rsa::publicEncrypt(json_encode($data));
        echo '公钥加密后:'.$publicEncrypt.'<br>';

        $privDecrypt = Rsa::privDecrypt($publicEncrypt);
        echo '私钥解密后:'.$privDecrypt.'<br>';
    }

    /**
     * 验签
     * @throws \Exception
     */
    public function onVerifySignAction(){
        $data = [
            'a' => 'a',
            'b' => 'b',
            'c' => 'c',
            'timestamp' => time(),
        ];
        $secretKey = '12asdq*-/+asd/.,?><+_SEW)(*&^%$s9qw5#@!~`.';
        $sign = empty($_GET['sign']) ? Encrypt::addSign($data,$secretKey) : Encrypt::addSign($data,$secretKey);
        var_dump($sign);
        if(empty($sign)){
            Error::error('签名不能为空');
        }

        $res = Encrypt::sign($sign,$data,$secretKey);
        dd($res);
    }

    /**
     * 微信验签
     * author brian
     */
    public function onWxSignAction(){
        $data = [
            'uid'=>"7",
            'type'=>1,
            'login_count'=>1,
            'api'=>"getuserinfo",
            'timestamp' => 1562228195
        ];

        $sign = 'T3w055RnNpekpy5jMg5EZ/bmaLdi3+CbNSH31SdJQH/9ZsPytAJ4nFu/sj3f03e+Wf6ft+1h81dTWfU5xHdouPIRT5whEHpKMN5aJcNb7lop4LLdX1jnnZHBiUg5h1nnlXFZBkUxxjQxz9wPeFduga7ak9tVCGqynqheZ7jjwWs=';
        $secretKey = '*856asasQWC8d-+8366@!~`*';
        $res = Encrypt::wxSign($sign,$data,$secretKey);
        dd($res);
    }

    public function onWxEncryptAction(){

    }

    /**
     * 上传图片文件
     */
    public function onUploadAction(){
        $data = input('post');
        //上传
        $upload = new Upload();
        $configs = [
            'path' => 'uploads/images',
            'allowType' => ["png", 'jpg', 'jpeg', 'gif'],
            'maxSize' => 1000033300,
        ];
        $res = $upload->upload('upload_file',$configs);
        if($res === false){
            dd($upload->getError());
        }
//        dd($res);
        //缩略图
        foreach($res as $k=>$v){
            $a = $upload -> imageResize($v['data']['newPath'].$v['data']['newName'],50,50,'50_');
            dd($a);
            if($a === false){
                dd($upload->getError());
            }
        }
        dd(true);

    }

}

?>
