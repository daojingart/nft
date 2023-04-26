<?php
/**
 * +----------------------------------------------------------------------
 * | 河南八六互联信息技术有限公司
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang
 * +----------------------------------------------------------------------
 * | Time:  2021/4/26   9:23 下午
 * +----------------------------------------------------------------------
 * | Email: 18519179004@163.com
 * +----------------------------------------------------------------------
 * | className:微信小程序
 * +----------------------------------------------------------------------
 */

namespace app\admin\controller\store;


use app\admin\controller\Controller;
use app\common\model\UploadFile;
use app\common\model\WxSetting as WxSettingModel;

class Wapp extends Controller
{

    /**
     * @Notes: 微信公众号配置
     * @Interface wx_index
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/4/8   5:25 下午
     */
    public function index()
    {
        $WxSetting = new WxSettingModel();
        $getWxSettingInfo = $WxSetting->getWxSettingInfo();
        $getWxSettingInfo['wxapp_template'] = json_decode($getWxSettingInfo['wxapp_template'],true);
        $getWxSettingInfo['share_info'] = json_decode($getWxSettingInfo['share_info'],true);
        //获取图片
        $getWxSettingInfo['share_info']['images'] = '';
        if(isset($getWxSettingInfo['share_info']['images'])){
            $getWxSettingInfo['share_info']['images'] = UploadFile::getFileLinkUrl($getWxSettingInfo['share_info']['images']);
        }
        $value = json_encode($getWxSettingInfo);
        return $this->fetch('',compact('value'));
    }

    /**
     * @Notes: 公众号配置
     * @Interface setting
     * @return mixed
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/4/8   5:33 下午
     */
    public function setting()
    {
        if($this->request->isAjax()){
            if((new WxSettingModel())->add($this->postData('wx'))){
                return $this->renderSuccess('保存成功');
            }
            return $this->renderError("保存失败");
        }
    }

    /**
     * @Notes:微信小程序订阅消息
     * @Interface template
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/4/9   5:25 下午
     */
    public function template()
    {
        if($this->request->isAjax()){
            if((new WxSettingModel())->add($this->postData('wxapp_template'))){
                return $this->renderSuccess('保存成功');
            }
            return $this->renderError("保存失败");
        }
    }

}