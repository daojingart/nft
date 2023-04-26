<?php
/**
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang
 * +----------------------------------------------------------------------
 * | Time:  2021/4/12   2:28 下午
 * +----------------------------------------------------------------------
 * | Email: 18519179004@163.com
 * +----------------------------------------------------------------------
 * | className: Jwt token 生成
 * +----------------------------------------------------------------------
 */
namespace jwt;

use think\Config;
use Firebase\JWT\JWT as JWTToken;

class Jwt
{
    /**
     * @Notes: Token 生成
     * @Interface getToken
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/4/12   2:33 下午
     */
    public static function getToken($member_info)
    {
        $token = [
            "iss"=> base_url(),  //签发者 可以为空
            "aud"=> base_url(), //面象的用户，可以为空
            "iat" => time(), //签发时间
            "nbf" => time(), //在什么时候jwt开始生效  （这里表示生成100秒后才生效）
            "exp" => time()+Config::get("exp"), //设定过期时间一周
            "member_info" => $member_info //记录的userid的信息，这里是自已添加上去的，如果有其它信息，可以再添加数组的键值对
        ];
        return JWTToken::encode($token,Config::get("key"),"HS256"); //根据参数生成了 token
    }

    /**
     * @Notes: 验证Token
     * @Interface checkToken
     * @param $Token
     * @retuen array
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/4/12   2:47 下午
     */
    public static function checkToken($Token)
    {
        return JWTToken::decode($Token,Config::get("key"),["HS256"]); //解密jwt
    }

    /**
     * @Notes: 验证Token
     * @Interface checkToken
     * @param $Token
     * @retuen array
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/4/12   2:47 下午
     */
    public static function checkLoginToken($Token)
    {
        return JWTToken::decodeLogin($Token,Config::get("key"),["HS256"]); //解密jwt
    }


}