<?php
/**
 * +----------------------------------------------------------------------
 * | 河南八六互联信息技术有限公司
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang
 * +----------------------------------------------------------------------
 * | Time:  2021/4/28   3:18 下午
 * +----------------------------------------------------------------------
 * | Email: 18519179004@163.com
 * +----------------------------------------------------------------------
 * | className: 微信小程序
 * +----------------------------------------------------------------------
 */

namespace app\common\extend\wx;


use exception\BaseException;

class Qrcode extends Wx
{
    /**
     * @Notes: 获取小程序码
     * @Interface getQrcode
     * @param $scene
     * @param null $page
     * @param int $width
     * @return mixed
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2020/12/29   10:50 上午
     */
    public function getQrcode($scene, $page = null,$goods_id, $width = 430)
    {
        // 微信接口url
        $access_token = (new Subscribe())->getAccessToken();
        $url = "https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token={$access_token}";
        // 构建请求
        if($goods_id){
            $scene = $goods_id.'_'.$scene;
        }
        $data = compact('scene', 'width');
        !is_null($page) && $data['page'] = $page;
        // 返回结果
        $result = $this->curlRequest($url, json_encode($data, JSON_UNESCAPED_UNICODE));
        // 记录日志
        write_log([
            'describe' => '获取小程序码',
            'params' => $data,
            'result' => strpos($result, 'errcode') ? $result : 'image'
        ],__DIR__);
        if (!strpos($result, 'errcode')) {
            return  $this->data_uri($result,'image/png');
        }
        $data = json_decode($result, true);
        $error = '小程序码获取失败 ' . $data['errmsg'];
        if ($data['errcode'] == 41030) {
            $error = '小程序页面不存在，请先发布上线后再生成';
        }
        throw new BaseException(['msg' => $error]);
    }

    /**
     * 小程序转换成base64的
     */
    public function data_uri($contents, $mime)
    {
        $base64   = base64_encode($contents);
        return ('data:' . $mime . ';base64,' . $base64);
    }

}