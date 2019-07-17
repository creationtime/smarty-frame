<?php
namespace Sf\Libs;
/**
 * 邮件类
 */
use PHPMailer\PHPMailer\PHPMailer;

class Mail extends PHPMailer{

    public function __construct($exceptions)
    {
        parent::__construct($exceptions);
    }

    /**
     * 发送邮件的方法,youwenti
     * //使用前请取配置文件中设置 MAIL_LOGIN等用户账号信息//
     * @param string $to		发送对象
     * @param string $subject	邮箱主题		在提醒时候的标题文件
     * @param string $body	正文主题    内容
     */
    public static function sendMail($to, $subject = "", $body = "") {
        //$to 表示收件人地址 $subject 表示邮件标题 $body表示邮件正文
        date_default_timezone_set("Asia/Shanghai"); //设定时区东八区
        $mail = new PHPMailer(); //new一个PHPMailer对象出来
        /**
         * smtp 服务设置
         */
        $mail->CharSet = "UTF-8"; //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
        $mail->IsSMTP(); // 设定使用SMTP服务
        $mail->SMTPDebug = 1;   // 启用SMTP调试功能
        // 1 = errors and messages
        // 2 = messages only
        $mail->SMTPAuth = true;   // 启用 SMTP 验证功能
        $mail->SMTPSecure = Config('MAIL_ENCRYPTION');  // 安全协议
        $mail->Host = Config('MAIL_HOST');   // SMTP 服务器

        $mail->Port = Config('MAIL_PORT');   // SMTP服务器的端口号
        $mail->Username = Config('MAIL_USERNAME');  // SMTP服务器用户名
        $mail->Password = Config('MAIL_PASSWORD');   // SMTP服务器密码
        $mail->SetFrom(Config('MAIL_USERNAME'), Config("MAIL_NICKNAME"));   //发送方账号
        $mail->AddReplyTo(Config('MAIL_USERNAME'), Config('MAIL_PASSWORD'));//回复接收方
        $mail->Subject = $subject;
//	$mail->AltBody = "To view the message, please use an HTML compatible email viewer! - From www.jiucool.com"; // optional, comment out and test
//	$mail->MsgHTML($body);
        $mail->isHTML(TRUE);
        $mail->Body = $body;
        $mail->WordWrap = 100;
        $address = $to;
        $mail->AddAddress($address, "收件人名称");
        //$mail->AddAttachment("images/phpmailer.gif");      // attachment
        //$mail->AddAttachment("images/phpmailer_mini.gif"); // attachment
        ob_start();
        if (!$mail->Send()) {
            $result = array("status" => FALSE, "info" => $mail->ErrorInfo);
        } else {
            $result = array("status" => TRUE, "info" =>  "恭喜，邮件发送成功！");
        }
        ob_get_contents();
        ob_end_clean();
        return $result;
    }
}