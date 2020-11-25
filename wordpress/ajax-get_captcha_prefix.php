<?php
/**
 * 使用ajax获取验证码的prefix，再配合验证码图片?prefix=$prefix获取验证码图片
 */

add_action('wp_ajax_get_captcha_prefix', 'lizus_get_captcha');
add_action('wp_ajax_nopriv_get_captcha_prefix', 'lizus_get_captcha');
function lizus_get_captcha_prefix(){
  $captcha = new \LizusCaptcha\Captcha();
  echo $captcha->get_prefix();
  die();
}