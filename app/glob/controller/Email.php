<?php

namespace app\glob\controller;

use PHPMailer\PHPMailer\PHPMailer;
use think\Exception;


class Email
{

    public function sendCodeToEmail()
    {

        $getMail = input('post.email');
        $this->sendOtherMail($getMail);

    }

    protected function sendOtherMail($mail)
    {
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->CharSet = 'utf8';
        $mail->Host = 'smtp.163.com';
        $mail->SMTPAuth = true;
        $mail->Username = "aneubeeza23018@163.com";
        $mail->Password = "FUEZVYWEAMAKAAWH";
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;
        $mail->setFrom('aneubeeza23018@163.com', '验证码发送');  //定义邮件及标题（不同邮件标题显示不一致）
        $mail->addAddress($mail, 'LSR');  //要发送的地址和设置地址的昵称
        $mail->addReplyTo('siriforever.ltd@gmail.com', 'Replay'); //回复地址
        $mail->Subject = "您有新的验证码!";  //添加该邮件的主题
        $mail->Body = "您的验证码是" . 123 . "，验证码的有效期为600秒，本邮件请勿回复！"; //该邮件内容

        if (!$mail->send()) {
//            $this->return_msg(400, $mail->ErrorInfo);
            return errorMsg('error');
        } else {
            return successMsg('11');
        }

    }

}