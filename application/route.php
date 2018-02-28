<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

return [
    'loginPassword'=>['api/UserLogin/loginPassword',["method"=>'post']],//密码登陆
    'loginPhone'=>['api/UserLogin/loginPhone',["method"=>'post']],//短信登陆登陆
    'register'=>['api/UserRegister/register',["method"=>'post']],//短信登陆登陆
    'register/sendMessage'=>['api/UserRegister/registerSendMessage',["method"=>'post']],//短信登陆登陆
];
