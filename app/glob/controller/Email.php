<?php

namespace app\glob\controller;

use PHPMailer\PHPMailer\PHPMailer;
use Psr\SimpleCache\InvalidArgumentException;
use think\facade\Cache;


class Email
{

    public function sendCodeToEmail()
    {

        $getMail = input('post.email');
        return $this->sendOtherMail($getMail);

    }

    protected function sendOtherMail($mails)
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
        $mailCode = $this->getCode();
        try {
            $mail->setFrom('aneubeeza23018@163.com', '验证码发送');
            $mail->addAddress($mails, 'LSR');  //要发送的地址和设置地址的昵称
            $mail->addReplyTo('siriforever.ltd@gmail.com', 'Replay'); //回复地址
        } catch (\PHPMailer\PHPMailer\Exception $e) {
            return $e->getMessage();
        }

        $mail->Subject = "您有新的验证码!";  //添加该邮件的主题
        $mail->Body = "您的验证码是" . $mailCode . "，验证码的有效期为5分钟，本邮件请勿回复！"; //该邮件内容

        if (!$mail->send()) {
            return errorMsg('发送失败', $mail->ErrorInfo);
        } else {
            try {
                Cache::store('redis')->set($mails, $mailCode, 300);
                return successMsg('发送成功');

            } catch (InvalidArgumentException $e) {
                // 删除缓存
                Cache::store('redis')->delete($mails);
                return errorMsg('发送失败', $e->getMessage());
            }
        }

    }

    // 获取 6 位随机字母验证码
    protected function getCode(): string
    {
        $code = '';
        for ($i = 0; $i < 6; $i++) {
            $code .= chr(rand(65, 90));
        }
        return $code;
    }


}