<?php
namespace app\api\controller;

use app\api\model\User;
use think\Request;
class Index
{
    public function index(Request $request)
    {
         $user=User::get(10002);
        return $user->user_id;
    }

}
