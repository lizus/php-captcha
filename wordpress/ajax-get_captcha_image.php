<?php
/**
 * 根据传入的prefix生成验证码图片，prefix可以从$_GET,$_POST中设定。
 */
add_action('wp_ajax_get_captcha_image', 'vitara_ajax_get_captcha_image');
add_action('wp_ajax_nopriv_get_captcha_image', 'vitara_ajax_get_captcha_image');
function vitara_ajax_get_captcha_image(){
    $captcha = new \LizusCaptcha\Captcha();
    echo $captcha->create_captcha_image();
    die();
}