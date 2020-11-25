<?php
/* ---=*--*=*-=*-=-*-=* 🌹 *---=*--*=*-=*-=-*-=*
验证码图片生成及验证类
使用redis存储验证随机数对应的值
---=*--*=*-=*-=-*-=* 🌹 *---=*--*=*-=*-=-*-=* */
namespace LizusCaptcha;

date_default_timezone_set('UTC');

class Captcha
{
  private $captcha_image;
  private $captcha_prefix;
  private $captcha_code;
  private $code_length=4;
  private $expire=600;
  private $redis;
  private $charset='abcdefghjkmnpqrstuvwxyABCDEFGHJKMNPQRSTUVWXY3456789';
  function __construct()
  {
  }
  function create_captcha_image() {
    $prefix=$this->get_prefix();
    $code=$this->get_code();
    $this->set_data();
    $code=chunk_split($code,1,'.');
    $code=explode('.',$code);
    $code=array_filter($code);

    header ( 'Content-type: image/png' );
    //创建图片
    $im = \imagecreate($x=150,$y=60 );
    $bg = \imagecolorallocate($im,rand(190,250),rand(150,255),rand(190,155)); //第一次对 imagecoorallocate() 的调用会给基于调色板的图像填充背景色
    $fontstyle = dirname(dirname(__DIR__)).'/font/font'.rand(5,9).'.ttf';
    //干扰线
    for ($i=0;$i<15;$i++){
      $lineColor=\imagecolorallocate($im,rand(150,235),rand(170,250),rand(180,250));
      $this->imagelinethick ($im,rand(0,$x),rand(0,10),rand(0,$x),rand($y-10,$y),$lineColor,rand(4,10));
    }
    //产生随机字符
    for($i = 0; $i < 4; $i ++) {
      $str=$code[$i];
      $fontColor = \imagecolorallocate ( $im, rand(0,60), rand(0,120), rand(0,180) );  //字体颜色
      \imagettftext($im,rand(30,40),rand(0,15)-rand(0,15),5+$i*35,rand(45,50),$fontColor,$fontstyle,$str);
    }
    //干扰线
    $lineColor=\imagecolorallocate($im,rand(0,50),rand(0,120),rand(0,150));
    $this->imagelinethick ($im,rand(10,$x/3),rand($y/4,$y/2),rand(2*$x/3,$x-10),rand($y/3,4*$y/5),$lineColor,3);

    //干扰点
    /*
    for ($i=0;$i<1000;$i++){
      imagesetpixel($im,rand(0,$x),rand(0,$y),imagecolorallocate ($im, rand(130,255), rand(150,255), rand(190,255)));
    }
    */

    //产生曲线
    /*
    for($i=0;$i<380;$i+=0.1){
      $sx = $i/20;
      $sy = sin($sx);
      $sy = 30 + 10*$sy;
      imagesetpixel($im,$i+5,$sy,$lineColor);
    }
    */
    \imagepng($im);
    \imagedestroy($im);
  }
  function set_prefix() {
    $prefix=isset($_REQUEST['prefix']) ? trim(strip_tags(strval($_REQUEST['prefix']))) : '';
    $is_prefix=$this->validate_prefix($prefix);
    if (empty($is_prefix)) {
      $total_count=11;
      $prefix='_';
      $str=base_convert(time(),10,36);
      $str=chunk_split($str,1,'.');
      $str=explode('.',$str);
      $charset=$this->charset;
      $charset=chunk_split($charset,1,'.');
      $charset=explode('.',$charset);
      $str=array_intersect($str,$charset);
      $str=implode('',$str);
      $str=trim($str);
      $len=$total_count-strlen($str);
      for ($i=0; $i < $len; $i++) {
        $str.=$charset[rand(0,count($charset)-2)];
      }
      $prefix=$prefix.str_shuffle($str);
    }
    $this->captcha_prefix=$prefix;
    $this->redis=new Redis($prefix);
  }
  function get_prefix() {
    if (empty($this->captcha_prefix)) {
      $this->set_prefix();
    }
    return $this->captcha_prefix;
  }
  function create_code() {
    $code = '';
    $clen = strlen($this->charset);
    for($i = 1; $i <= $this->code_length; ++$i) {
      $code .= substr($this->charset, rand(0, $clen - 1), 1);
    }
    $this->captcha_code=$code;
    return $this->captcha_code;
  }
  function get_code(){
    if (empty($this->captcha_code)) {
      $this->create_code();
    }
    return $this->captcha_code;
  }
  function get_data_code(){
    $prefix=$this->get_prefix();
    if (!$this->validate_prefix($prefix)) return false;
    return $this->redis->get();
  }
  function set_data() {
    if(!empty($this->captcha_prefix) && !empty($this->captcha_code)) {
      return $this->redis->set($this->captcha_code);
    }
    return false;
  }
  function validate_prefix($prefix){
    $prefix=strip_tags($prefix);
    if (!is_string($prefix)) return false;
    if (strlen($prefix)!=12) return false;
    if (preg_match('/^[_'.$this->charset.']+$/',$prefix)) {
      return true;
    }
    return false;
  }
  function validate_code($code){
    $code=strip_tags($code);
    if (!is_string($code)) return false;
    if (strlen($code)!=$this->code_length) return false;
    if (preg_match('/^[_'.$this->charset.']+$/',$code)) {
      return true;
    }
    return false;
  }
  function check_captcha($code) {
    $code=strip_tags($code);
    if (!$this->validate_code($code)) return false;
    $data_code=$this->get_data_code();
    if (strtolower($code)==strtolower($data_code)) {
      return true;
    }
    return false;
  }
  function imagelinethick($image, $x1, $y1, $x2, $y2, $color, $thick = 1) {
    if ($thick == 1) {
      return imageline($image, $x1, $y1, $x2, $y2, $color);
    }
    $t = $thick / 2 - 0.5;
    if ($x1 == $x2 || $y1 == $y2) {
      return imagefilledrectangle($image, round(min($x1, $x2) - $t), round(min($y1, $y2) - $t), round(max($x1, $x2) + $t), round(max($y1, $y2) + $t), $color);
    }
    $k = ($y2 - $y1) / ($x2 - $x1); //y = kx + q
    $a = $t / sqrt(1 + pow($k, 2));
    $points = array(
      round($x1 - (1+$k)*$a), round($y1 + (1-$k)*$a),
      round($x1 - (1-$k)*$a), round($y1 - (1+$k)*$a),
      round($x2 + (1+$k)*$a), round($y2 - (1-$k)*$a),
      round($x2 + (1-$k)*$a), round($y2 + (1+$k)*$a),
    );
    imagefilledpolygon($image, $points, 4, $color);
    return imagepolygon($image, $points, 4, $color);
  }
}
