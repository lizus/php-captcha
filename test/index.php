<?php
require dirname(__DIR__).'/vendor/autoload.php';

use LizusCaptcha\Captcha;

/* ---=*--*=*-=*-=-*-=* 🌹 *---=*--*=*-=*-=-*-=*
简单测试
---=*--*=*-=*-=-*-=* 🌹 *---=*--*=*-=*-=-*-=* */

$img = new Captcha();
if (!empty($_POST)) {
  $code=$_POST['v'];

  //验证输入是否匹配
  var_dump($img->check_captcha($code));
}
$prefix=$img->get_prefix();
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Test</title>
  </head>
  <body>
    <form class="form" action="./" method="post">
      <input type="text" name="v" value="">
      <img src="./img.php?prefix=<?php echo $prefix; ?>" alt="captcha">
      <input type="hidden" name="prefix" value="<?php echo $prefix; ?>">
      <button type="submit" name="submit" value="me">提交</button>
    </form>
  </body>
</html>
