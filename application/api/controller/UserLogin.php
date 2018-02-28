<?php
/**
 * Class:会员登陆
 * Created by PhpStorm.
 * User: 陈锦洁
 * Date: 2018/2/27
 * Time: 13:00
 */
namespace app\api\controller;

use app\api\model\User;
use think\Request;
use think\Controller;
use think\Loader;

class UserLogin extends Controller
{

    /**
     * 密码登陆
     * @access public
     * @param  string user_account 用户账户
     * @param  string password 用户密码
     * @return mixed 登陆信息
     */
    public function loginPassword(Request $request)
    {
        $user = User::get(['user_phone' => $request->post('user_account', ''), 'user_password' => $request->post('password', '')]);

        if (!$user) return back('300', '登陆失败');

        //持久化
        $userInfo = $this->userInfoLast($user->toArray());

        return back('200', '登陆成功', ['token' => $userInfo['token']]);
    }

    /**
     * 短信登陆
     * @access public
     * @param  string user_account 用户手机号
     * @param  string sendMessge  是否发送验证码，参数为：true 默认 false。
     * @param  string code 验证码
     * @return mixed 结果
     */
    public function loginPhone(Request $request){
        $user_phone=$request->post('user_account', '');
        $user = User::get(['user_phone' =>$user_phone]);
        if(!$user) return back('300','用户不存在');

        if(strtolower($request->post('sendMessage','false'))=='true'){
            Loader::import('message',EXTEND_PATH,'.php');
            $message=mt_rand(1000,9999);
            $info="您的验证码为：".$message."，为了保护您的账户安全，验证码请勿转发他人，有效时间15分钟。";
            if(!itcc8($user_phone,$info))
                return back('301','短信发送失败请重新发送');

            //持久化验证码
            noSqlSet('loginPhone'.$user_phone,$message);

            return back('200','短信发送成功');
        }else{
            $code=$request->post('code', '');

            //判断验证码是否正确
            if($code==''||$code!=noSqlGet('loginPhone'.$user_phone)){
                //删除短信记录防止爆破登陆
                noSqlDel('loginPhone'.$user_phone);
                return back('300', '登陆失败,验证码不正确');
            }

            //个人信息处理以及持久化
            $userInfo = $this->userInfoLast($user->toArray());

            //删除短信记录
            noSqlDel('loginPhone'.$user_phone);

            return back('200', '登陆成功', ['token' => $userInfo['token']]);
        }
    }

    /**
     * 用户登陆信息持久化
     * @access protected
     * @param  array $userInfo 用户基本信息
     * @return array 用户信息
     */
    protected function userInfoLast($userInfo)
    {
        //生成唯一token
        $token = strtoupper(sha1(time() . $userInfo['user_id'] . mt_rand(1, 9999)));

        //保存到内存数据库
        $userInfo['token'] = $token;
        $userInfo['login_ip'] = $_SERVER['REMOTE_ADDR'];
        unset($userInfo['user_password']);

        //资产处理

        noSqlSet($token, json_encode($userInfo));

        return $userInfo;
    }
}