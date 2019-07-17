<?php
namespace Sf\Unit\Sms;

use Sf\Unit\Sms as Sms;
use Sf\Unit\SmsInterface as SmsInterface;

use Sf\Unit\HttpCurl as HttpCurl;

class Unicom extends Sms implements SmsInterface{

    protected $api = "http://api.ums86.com:8888/sms/Api/";

    const SEND_METHOD = "Send.do";

    /**
     * 统一发送方法
     * @param $msg
     * @param $send_to
     * @return bool
     */
    public function send($msg, $send_to){

        $params = array();
        $params['SpCode'] = '240808';
        $params['LoginName'] = 'hr_sswyy';
        $params['Password'] = 'HES2057@';
        $params['MessageContent'] = iconv('UTF-8','GB2312',$msg);
        $params['UserNumber'] = $send_to;
        $params['SerialNumber'] = '';
        $params['ScheduleTime'] = '';
        $params['ExtendAccessNum'] = '';
        $params['f'] = 1;

        $ret = HttpCurl::post($this->api.self::SEND_METHOD, $params,'text');
        if(false === $ret){
            $this->set_error('接口调用失败');
            return false;
        }
        $ret = iconv('GB2312','UTF-8',$ret);
        parse_str($ret, $arr_ret);
        if(empty($arr_ret) || !is_array($arr_ret)){
            $this->set_error('接口返回数据解析失败');
            return false;
        }

        if($arr_ret['result'] == "0"){
            return true;
        }

        $this->set_error($ret);
        return false;
    }

    public function sendVerifyCode($params){

        if(empty($params['mobile'])){
            $this->set_error('手机号码不能为空');
            return false;
        }
        if(strlen($params['mobile']) !=11 || !is_numeric($params['mobile'])){
            $this->set_error('手机号码格式错误');
            return false;
        }
        if(empty($params['verify_code'])){
            $this->set_error('验证码不能为空');
            return false;
        }

        $msg = "尊敬的客户".$params['mobile']."欢迎登陆HBS客户订货系统。您本次的验证码为".$params['verify_code']."，验证码10分钟内有效。如非本人操作，请忽略本条短信";
        return $this->send($msg, $params['mobile']);
    }

    /*
    $params['mobile'] 			收货人手机号
    $params['order_no'] 		订单号
    $params['delivery_state'] 	发货状态：全部|部分
    $params['delivery_no'] 		发货单号
    $params['contactor'] 		发货人
    $params['contact_phone'] 	发货人联系电话
    */
    public function sendDeliveryNotify($params){

        if(empty($params['mobile'])){
            $this->set_error('手机号码不能为空');
            return false;
        }
        if(strlen($params['mobile']) !=11 || !is_numeric($params['mobile'])){
            $this->set_error('手机号码格式错误');
            return false;
        }
        if(empty($params['order_no'])){
            $this->set_error('订单号码不能为空');
            return false;
        }
        if(empty($params['delivery_state'])){
            $this->set_error('发货状态不能为空');
            return false;
        }
        if(!in_array($params['delivery_state'], array("全部","部分"))){
            $this->set_error('发货状态只能是全部或者部分');
            return false;
        }
        if(empty($params['delivery_no'])){
            $this->set_error('发货单号不能为空');
            return false;
        }
        if(empty($params['contactor'])){
            $this->set_error('发货人不能为空');
            return false;
        }
        if(empty($params['contact_phone'])){
            $this->set_error('发货人联系电话不能为空');
            return false;
        }

        $msg = "尊敬的客户：".$params['mobile']."，您的订单".$params['order_no'];
        $msg.= "已".$params['delivery_state']."发货，配送单号为".$params['delivery_no'];
        $msg.= "请及时查收。务必在7个工作日内在系统上完成收货入库，逾期系统将自动执行。销售内勤".$params['contactor'];
        $msg.= "为您服务。如有疑问请联系".$params['contact_phone']."如非本人操作，请您及时修改登录密码。";
        //var_dump($params['mobile']);exit;
        return $this->send($msg, $params['mobile']);
    }

    public function sendOrderNotice($params){

        if(empty($params['connect'])){
            $this->set_error('联系人不能为空');
            return false;
        }

        if(empty($params['order_num'])){
            $this->set_error('订单号码不能为空');
            return false;
        }

        $msg = "销售内勤：".$params['connect']."，您所负责的客户“".$params['hname'];
        $msg.= "”已在订货系统上下单，采购单号为：".$params['order_num'].'，请尽快处理！';
        return $this->send($msg, $params['tel']);

    }

}