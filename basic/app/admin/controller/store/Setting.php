<?php
/**
 * +----------------------------------------------------------------------
 * | 河南八六互联信息技术有限公司
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang
 * +----------------------------------------------------------------------
 * | Time:  2021/3/23   3:35 下午
 * +----------------------------------------------------------------------
 * | Email: 18519179004@163.com
 * +----------------------------------------------------------------------
 * | className: 配置控制器
 * +----------------------------------------------------------------------
 */

namespace app\admin\controller\store;

//use app\common\library\sms\Driver as SmsDriver;
use app\admin\controller\Controller;
use app\admin\model\Setting as SettingModel;
use app\common\model\Goods;

class Setting extends Controller
{
    /**
     * @Notes: 系统基础配置
     * @Interface store
     * @return array|mixed
     * @throws \think\exception\DbException
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/3/27   11:33 上午
     */
    public function store()
    {
        return $this->updateEvent('store');
    }


    /**
     * 上传设置
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function storage()
    {
        return $this->updateEvent('storage');
    }

    /**
     * 视频存储
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function resource()
    {
        return $this->updateEvent('resource');
    }


    /**
     * 交易设置
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function trade()
    {
        return $this->updateEvent('trade');
    }

    /**
     * 短信通知
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function sms()
    {
        return $this->updateEvent('sms');
    }

    /**
     * 客服配置
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function customer()
    {
        return $this->updateEvent('customer');
    }

    /**
     * 藏品配置
     * @return array|mixed
     * @throws \think\exception\DbException
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-06-13 13:49
     */
    public function collection()
    {
        //查找空投产品
        $goods = Goods::where(['product_types'=>2,'is_del'=>0])->field('product_types,is_del,goods_name,goods_id')->select();
        $this->assign(['goods'=>$goods]);
        return $this->updateEvent('collection');
    }

    /**
     * 实名认证
     * @return array|mixed
     * @throws \think\exception\DbException
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-06-13 13:49
     */
    public function certification()
    {
        return $this->updateEvent('certification');
    }

    /**
     * 协议配置
     * @return array|mixed
     * @throws \think\exception\DbException
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-06-13 14:11
     */
    public function agreement()
    {
        return $this->updateEvent('agreement');
    }

    /**
     * 提现配置
     * @return array|mixed
     * @throws \think\exception\DbException
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-06-13 14:18
     */
    public function withdrawal()
    {
        return $this->updateEvent('withdrawal');
    }

    /**
     * 服务费配置
     * @return array|mixed
     * @throws \think\exception\DbException
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-06-13 14:50
     */
    public function service()
    {
        return $this->updateEvent('service');
    }

    /**
     * 分销配置
     *
     * @return array|mixed
     * @throws \think\exception\DbException
     * @author Mr.Liu
     * @copyright 河南八六互联信息技术有限公司
     * @Time 2022/3/7 21:59
     */
    public function distribution()
    {
        return $this->updateEvent('distribution');
    }

    /**
     * 订单配置
     *
     * @return array|mixed
     * @throws \think\exception\DbException
     * @author Mr.Liu
     * @copyright 河南八六互联信息技术有限公司
     * @Time 2022/3/7 21:59
     */
    public function order()
    {
        return $this->updateEvent('order');
    }

    /**
     * 空投配置
     * @return array|mixed
     * @throws \think\exception\DbException
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-06-13 14:57
     */
    public function drop()
    {
        return $this->updateEvent('drop');
    }


    /**
     * 上链配置
     * @return array|mixed
     * @throws \think\exception\DbException
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-06-13 15:10
     */
    public function blockchain()
    {
        return $this->updateEvent('blockchain');
    }


    /**
     * 行为验证码
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface behaviorcode
     * @Time: 2022/7/6   10:25
     */
    public function behaviorcode()
    {
        return $this->updateEvent('behaviorcode');
    }


    /**
     * 日历分享配置
     * @return array|mixed
     * @throws \think\exception\DbException
     * @Time: 2022/6/22   9:56
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface calendar
     */
    public function calendar()
    {
        return $this->updateEvent('calendar');
    }

    /**
     * 支付协议配置
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface payprotocol
     * @Time: 2022/7/16   15:42
     */
    public function payprotocol()
    {
        return $this->updateEvent('payprotocol');
    }
    /**
     * 个人中心
     * @ApiAuthor 2023/4/13-liyusheng
     */
    public function my_personal()
    {
        return $this->updateEvent('my_personal');
    }

    /**
     * 更新配置
     * @param $key
     * @return array|mixed
     * @throws \think\exception\DbException
     */
    private function updateEvent($key)
    {
        if (!$this->request->isAjax()) {
            $values = SettingModel::getItem($key);
            return $this->fetch($key, compact('values'));
        }
        $model = new SettingModel;
        $param = $this->postData($key);
        if($key == 'calendar'){
            if(mb_strlen($param['title'],'utf-8') > 15){
                return $this->renderError('标题长度必须小于10个汉字');
            }
            if(mb_strlen($param['subtitle'],'utf-8') > 18){
                return $this->renderError('标题长度必须小于10个汉字');
            }
            if($param['image'] == ''){
                return $this->renderError('请上传图片');
            }
        }
        if ($model->edit($key, $param)) {
            return $this->renderSuccess('更新成功');
        }
        return $this->renderError('更新失败');
    }

    /**
     * 开放API
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface open
     * @Time: 2022/8/14   23:03
     */
    public function open()
    {
        $open_api = [
            'one_link' => HOST."/api/opening/getCollectionList",
            'two_link' => HOST."/api/opening/getCllectList",
            'three_link' => HOST."/api/opening/getPlatformAnnouncements",
            'four_link' => HOST."/api/opening/getGoodsOrderList",
        ];
		$maschi_api = [
			'one_link' => HOST."/api/opening/getCollectionListmaschi",
			'two_link' => HOST."/api/opening/getPlatformAnnouncementsMaschi",
			'three_link' => HOST."/api/opening/getMyGoodsListMaschi",
		];
        return $this->fetch('',compact('open_api','maschi_api'));
    }


}
