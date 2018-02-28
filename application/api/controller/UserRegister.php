<?php
/**
 * Class: 会员注册
 * Created by PhpStorm.
 * User: 陈锦洁
 * Date: 2018/2/28
 * Time: 10:22
 */
namespace app\api\controller;

use app\api\model\User;
use app\api\model\UserAssets;
use think\Request;
use think\Controller;
use think\Loader;

class UserRegister extends Controller
{
    /**
     * 注册会员
     * @access public
     * @param  string  user_phone 电话号码
     * @param  string  user_password 密码
     * @param  string  code 短信验证码
     * @return json 返回注册结果
     */
    public function register(Request $request){
        $user_phone=$request->post("user_phone",'');
        $user_password=$request->post("user_password",sha1('123456'));

        $code=$request->post("code",'');

        if($code==''||noSqlGet('registerMessage'.$user_phone)!=$code){
            //删除短信记录防止爆破登陆
            noSqlDel('registerMessage'.$user_phone);
            return back('300', '注册失败,验证码不正确');
        }

        $time=time();
        $data=[
            'user_phone'=>$user_phone,
            'user_password'=>$user_password,
            'create_time'=>$time,
            'update_time'=>$time,
        ];
        $result=User::create($data);

        //数据初始化
        $this->initializeUserAssets($result->user_id);

        if(!$result)
            return back('400','注册失败,网络故障，重新注册');

        noSqlDel('registerMessage'.$user_phone);

        return back('200','注册成功');
    }

    /**
     * 注册短信发送
     * @access public
     * @param  string user_phone 电话号码
     * @return json 发送结果
     */
    public function registerSendMessage(Request $request){
        $user_phone=$request->post("user_phone",'');
        if(!preg_match('/^1[34578]{1}\d{9}$/',$user_phone)) return back('400','手机号码格式不正确');

        $user=User::get(['user_phone'=>$user_phone]);
        if($user) return back('301','手机号已被注册');

        //发送验证码
        Loader::import('message',EXTEND_PATH,'.php');
        $message=mt_rand('1000','9999');
        $info="您的验证码为：".$message."，为了保护您的账户安全，验证码请勿转发他人，有效时间15分钟。";
        if(!itcc8($user_phone,$info))
            return back('300','短信发送失败请重新发送');

        //验证码持久化
        noSqlSet('registerMessage'.$user_phone,$message);

        return back('200','短信发送成功');
    }

    /**
     * 初始化用户资产
     * @access public
     * @param  string
     * @return void
     */
    protected function initializeUserAssets($user_id){
        $time=time();
        $data=[
            'user_id'=>$user_id,
            'user_account'=>0,
            'user_integral'=>0,
            'user_vip'=>0,
            'user_experience'=>0,
            'create_time'=>$time,
            'update_time'=>$time
        ];

        $result= UserAssets::create($data);
        return $result ? true:false;
    }


}