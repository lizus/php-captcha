# PHP Captcha

一个简化的验证码程序，客户端表单根据prefix的值生成验证码图片，用户填写验证码图片上的code，然后提交的时候将prefix和code同时提交到服务器，服务器验证数据库存储的prefix中的code与用户提交的code比较，不计大小写，相同的话就验证通过。

本程序使用`lizus/php-redis`，需要有`redis`支持，相关使用见 [lizus/php-redis](https://packagist.org/packages/lizus/php-redis#dev-master)。

## 使用composer安装
```bash
composer require lizus/php-captcha
```

## 生成验证码prefix

```php
$captcha = new \LizusCaptcha\Captcha();
echo $captcha->get_prefix();
```

## 生成验证码图片

```php
$captcha = new \LizusCaptcha\Captcha();
echo $captcha->create_captcha_image();
```

## 将code和prefix传值服务器后验证

```php
$captcha = new \LizusCaptcha\Captcha();
echo $captcha->check_captcha($code);
```

## WordPress中使用

### 在functions.php中载入
```php
\LizusCaptcha\load_captcha();
```

### 引用地址，通过ajax获取
* 验证码prefix获取： `admin_url('admin-ajax.php').'?action=get_captcha_prefix'`
* 验证码图片获取： `admin_url('admin-ajax.php').'?action=get_captcha_image&prefix={prefix}'`

## 示例见/test