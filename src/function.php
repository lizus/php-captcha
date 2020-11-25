<?php

/**
 * load_captcha
 * 在wordpress中载入验证码程序
 * 通过ajax.php?action-get_captcha_prefix.php来生成prefix
 * 再通过ajax.php?action=get_captcha_image?prefix={prefix}来调用对应{prefix}的验证码图片
 * @return void
 */
function load_captcha(){
    require dirname(__DIR__).'/wordpress/ajax-get_captcha_image.php';
    require dirname(__DIR__).'/wordpress/ajax-get_captcha_prefix.php';
}