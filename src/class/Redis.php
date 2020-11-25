<?php
namespace LizusCaptcha;

use Lizus\PHPRedis\PHPRedis;

class Redis extends PHPRedis
{
    protected $database=10;//选择数据库
    protected $expire=300;//过期时间，一般为5分钟
}