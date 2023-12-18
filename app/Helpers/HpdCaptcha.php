<?php
namespace App\Helpers;
use Request;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\Str;
use Illuminate\Session\Store as Session;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

use App\Hpd\Captcha\helper;
use App\Hpd\Captcha\CaptchaServiceProvider;
use App\Hpd\Captcha\CaptchaController;
use App\Hpd\Captcha\Captcha;


class HpdCaptcha
{
    
      public static function captcha_get_html(string $config='default',array $attribs=[]):string{
            return app('captcha')->captchaGetImg($config,$attribs);
        }
      
      
      
}