<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
use think\Db;

function back($code='401',$info="未设置",$data=[]){
    return [
        'code'=>$code,
        'info'=>$info,
        'data'=>$data
    ];
}

/**
 * 保存内存数据库
 * @access public
 * @param  string key 键
 * @param  string values 值
 * @param  string expire 过期时间 以分为单位 默认15分钟
 * @return boolean
 */
function noSqlSet($key,$value,$expire='15'){
    Db::execute("delete from  qg_redis where `key`=? ",[$key]);
    $result=Db::execute("insert into qg_redis(`key`,`value`) values(?,?)",[$key,$value]);
    return $result? true:false;
}

/**
 * 获取内存数据库数据
 * @param  string key 键
 * @return string values 值
 */
function noSqlGet($key){
    $result=Db::query("select value from qg_redis where `key`=? limit 1 ",[$key]);
    return $result?$result[0]['value']:'';
}

/**
 * 删除内存数据库键值
 * @access public
 * @param  string key 键
 * @return boolean
 */
function noSqlDel($key){
    $result=Db::execute("delete from  qg_redis where `key`=? ",[$key]);
    return $result? true:false;
}


