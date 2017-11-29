<?php

/**
 * 功能：邮件发送函数
 * @param string $to 目标邮箱
 * @param string $subject 邮件主题（标题）
 * @param string $to 邮件内容
 * @return bool true
 */
function sendMail($to, $subject, $content) {
    vendor('PHPMailer.class#smtp');
    vendor('PHPMailer.class#phpmailer');
//    vendor('PHPMailer.class#smtp');
//    vendor('PHPMailer.class#phpmailer');
    $mail = new PHPMailer();
    // 装配邮件服务器
    if (C('MAIL_SMTP')) {
        $mail->IsSMTP();
    }
    $mail->Host = C('MAIL_HOST');
    $mail->IsSMTP();
    $mail->Port = 25;
    $mail->SMTPAuth = C('MAIL_SMTPAUTH');
    $mail->Username = C('MAIL_USERNAME');
    $mail->Password = C('MAIL_PASSWORD');
    $mail->SMTPSecure = C('MAIL_SECURE');
    $mail->CharSet = C('MAIL_CHARSET');
    // 装配邮件头信息
    $mail->From = C('MAIL_USERNAME');
    $mail->AddAddress($to);
    $mail->FromName = C('MAIL_FROMNAME');
    $mail->IsHTML(C('MAIL_ISHTML'));
    // 装配邮件正文信息
    $mail->Subject = $subject;
    $mail->Body = $content;
    // 发送邮件
//    return $mail;
    if (!$mail->Send()) {
        return FALSE;
    } else {
        return TRUE;
    }
}

