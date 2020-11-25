<?php
// 根据传入的prefix生成验证码图片，prefix可以从$_GET,$_POST中设定。

use LizusCaptcha\Captcha;

require dirname(__DIR__).'/vendor/autoload.php';


$img = new Captcha();

$img->create_captcha_image();