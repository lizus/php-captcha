<?php
require dirname(__DIR__).'/vendor/autoload.php';

$captcha = new \LizusCaptcha\Captcha();
echo $captcha->get_prefix();