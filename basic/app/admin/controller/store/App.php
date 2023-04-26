<?php
/**
 * +----------------------------------------------------------------------
 * | 河南八六互联信息技术有限公司
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang
 * +----------------------------------------------------------------------
 * | Time:  2021/5/7   1:48 下午
 * +----------------------------------------------------------------------
 * | Email: 18519179004@163.com
 * +----------------------------------------------------------------------
 * | className: APP 配置信息
 * +----------------------------------------------------------------------
 */

namespace app\admin\controller\store;


use app\admin\controller\Controller;
use app\admin\model\wx\WxSetting;

class App extends Controller
{
    /**
     * @Notes:APP基础配置信息
     * @Interface index
     * @return mixed
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/5/7   1:55 下午
     */
    public function index()
    {
        $WxSetting = new WxSetting();
        $getWxSettingInfo = $WxSetting->getWxSettingInfo();
        $getWxSettingInfo['app_upgrade'] = json_decode($getWxSettingInfo['app_upgrade'],true);
        $getWxSettingInfo['app_guide_page'] = json_decode($getWxSettingInfo['app_guide_page'],true);
        $getWxSettingInfo['wx_protocol'] = json_decode($getWxSettingInfo['wx_protocol'],true);
        $getWxSettingInfo['wx_protocol']['user_agreement'] = htmlspecialchars_decode($getWxSettingInfo['wx_protocol']['user_agreement']);
        $getWxSettingInfo['wx_protocol']['app_agreement'] = htmlspecialchars_decode($getWxSettingInfo['wx_protocol']['app_agreement']);
        $value = json_encode($getWxSettingInfo);
        return $this->fetch('',compact('value'));
    }

    /**
     * @Notes: 微信授权登录配置
     * @Interface setting
     * @return mixed
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/4/8   5:33 下午
     */
    public function setting()
    {
        if($this->request->isAjax()){
            if((new WxSetting())->add($this->postData('wx'))){
                return $this->renderSuccess('保存成功');
            }
            return $this->renderError("保存失败");
        }
    }

    /**
     * @Notes: APP升级配置
     * @Interface upgrade
     * @return array
     * @throws \think\exception\DbException
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/5/7   2:08 下午
     */
    public function upgrade()
    {
        if($this->request->isAjax()){
            if((new WxSetting())->add($this->postData('upgrade'))){
                return $this->renderSuccess('保存成功');
            }
            return $this->renderError("保存失败");
        }
    }

    /**
     * @Notes: APP 引导页配置
     * @Interface guide
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/5/7   2:19 下午
     */
    public function guide()
    {
        if($this->request->isAjax()){
            if((new WxSetting())->add($this->postData('guide'))){
                return $this->renderSuccess('保存成功');
            }
            return $this->renderError("保存失败");
        }
    }

    /**
     * @Notes: APP协议配置
     * @Interface protocol
     * @return array
     * @throws \think\exception\DbException
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/5/7   3:21 下午
     */
    public function protocol()
    {
        if($this->request->isAjax()){
            if((new WxSetting())->add($this->postData('protocol'))){
                return $this->renderSuccess('保存成功');
            }
            return $this->renderError("保存失败");
        }
    }


}