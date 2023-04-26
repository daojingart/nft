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
 * | className: 支付网关配置
 * +----------------------------------------------------------------------
 */

namespace app\common\model;

use think\Cache;

class PaySetting extends BaseModel
{
    protected $name = 'pay_setting';

    protected $createTime = false;

    /**
     * 设置项描述
     * @var array
     */
    private $describe = [
        'wxPay' => '微信',
        'aliPay' => '支付宝',
        'lianlianPay' => '连连',
        'sxPay' => '首信易',
        'sdPay' => '杉德',
        'hfPay' => '汇付',
        'hyPay' => '汇元',
        'hftPay' => '汇付通',
        'yeePay' => '易宝',
        'balance' => '余额'
    ];

    /**
     * 设置项图标
     * @var array
     */
    private $payIcons = [
        'wxPay' => HOST.'/assets/payicon/wx.png',
        'aliPay' => HOST.'/assets/payicon/alipay.png',
        'lianlianPay' => HOST.'/assets/payicon/lianlian.png',
        'sxPay' => HOST.'/assets/payicon/sxy.png',
        'sdPay' => HOST.'/assets/payicon/sd.png',
        'hfPay' => HOST.'/assets/payicon/hf.png',
        'hyPay' => HOST.'/assets/payicon/hy.png',
        'hftPay' => HOST.'/assets/payicon/hft.png',
        'yeePay' => HOST.'/assets/payicon/yb.png',
        'balance' => HOST.'/assets/payicon/balance.png',
    ];

    /**
     * @Notes: 获取器转化数组格式输出
     * @Interface getValuesAttr
     * @param $value
     * @return mixed
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/3/27   10:34 上午
     */
    public function getValuesAttr($value)
    {
        return json_decode($value, true);
    }

    /**
     * @Notes: 修改器转化json格式输入
     * @Interface setValuesAttr
     * @param $value
     * @return false|string
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/3/27   10:35 上午
     */
    public function setValuesAttr($value)
    {
        return json_encode($value);
    }

    /**
     * @Notes: 根据键值获取配置内容信息
     * @Interface getItem
     * @param $key
     * @return array|mixed
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/3/27   10:35 上午
     */
    public static function getItem($key)
    {
        $redis = initRedis();
        $redisKey = 'setting_pay' . $key;
        if(!$redis->get($redisKey)){
            $data = self::getAll();
            $settingKey = isset($data[$key]) ? $data[$key]['values'] : [];
            $redis->set($redisKey, json_encode($settingKey));
        }
        return json_decode($redis->get($redisKey),true);
    }

    /**
     * @Notes: 获取键值详情信息
     * @Interface detail
     * @param $key
     * @return Setting|null
     * @throws \think\exception\DbException
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/3/27   10:36 上午
     */
    public static function detail($key)
    {
        return self::get(compact('key'));
    }

    /**
     * @Notes: 系统设置
     * @Interface getAll
     * @param null $wxapp_id
     * @return array
     * @throws \think\exception\DbException
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/3/27   10:36 上午
     */
    public static function getAll()
    {
        $self = new static;
        $data = array_column(collection($self::all())->toArray(), null, 'key');
        return array_merge_multiple($data,[]);
    }


    /**
     * 更新系统设置
     * @param $key
     * @param $values
     * @return bool
     * @throws \think\exception\DbException
     */
    public function edit($key, $values)
    {
        $redis = initRedis();
        $model = self::detail($key) ?: $this;
        // 删除客服配置的缓存 ：非全局系统配置
        $redisKey = 'setting_pay' . $key;
        $redis->del($redisKey);
        return $model->save([
                'key' => $key,
                'describe' => $this->describe[$key],
                'values' => $values,
                'app_id' => self::$app_id,
            ]) !== false;
    }

    /**
     * 获取开启的支付网关
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @param $type 1:钱包  2:一级支付  3:二级支付
     */
    public function getPayGetWay($type)
    {
        $where = [];
        if($type == 1){
            $where['id'] = ['not in',['1','2']];
        }
        $list = $this->where($where)->select()->toArray();
        $new_list = [];
        foreach ($list as $key=>$value){

            //钱包和快捷都包含在内的
            if(isset($value['values']['open_status']) && $value['values']['open_purse_status']){
                if($value['values']['open_status'] == 10 && $type != 3){
                    $amount_list = [
                        'title' => $value['describe'],
                        'icon' => $this->payIcons[$value['key']],
                        'open_status' => $value['values']['open_status'],
                        'open_purse_status' => $value['values']['open_purse_status'],
                    ];
                }

                if($value['values']['open_purse_status'] == 10){
                    $amount_list['title'] = $value['describe'];
                    $amount_list['icon'] = $this->payIcons[$value['key']];
                    $amount_list['open_status'] = $type==3?20:$value['values']['open_status'];
                    $amount_list['open_purse_status'] = $value['values']['open_purse_status'];
                }
                if($value['values']['open_status'] == 10 || $value['values']['open_purse_status'] == 10){
                    $new_list[] = $amount_list;
                }
            }

            //只有快捷支付 但是没有钱包支付的
            if(isset($value['values']['open_status']) && !isset($value['values']['open_purse_status']) && $value['values']['open_status']==10 && $type != 3){
                $amount_list = [
                    'title' => $value['describe'],
                    'icon' => $this->payIcons[$value['key']],
                    'open_status' => $value['values']['open_status'],
                    'open_purse_status' => 20,
                ];
                $new_list[] = $amount_list;
            }

            //只有钱包 但是没有快捷支付的
            if(isset($value['values']['open_purse_status']) && !isset($value['values']['open_status']) && $value['values']['open_purse_status']==10){
                $amount_list = [
                    'icon' => $this->payIcons[$value['key']],
                    'open_status' => 20,
                    'open_purse_status' => $value['values']['open_purse_status'],
                ];
                $new_list[] = $amount_list;
            }
			$new_list[$key]['icon'] = $this->payIcons[$value['key']];
			$new_list[$key]['title'] = $value['describe'];
            $new_list[$key]['id'] = $value['id'];

        }
        return $new_list;
    }
}
