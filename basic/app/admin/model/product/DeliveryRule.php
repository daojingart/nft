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
 * | className: 模板规则模型
 * +----------------------------------------------------------------------
 */
namespace app\admin\model\product;

use app\common\model\DeliveryRule as DeliveryRuleModel;
use app\common\model\Region;


class DeliveryRule extends DeliveryRuleModel
{
    protected $append = ['region_content'];

    static $regionAll;
    static $regionTree;

    /**
     * 可配送区域
     * @param $value
     * @param $data
     * @return string
     */
    public function getRegionContentAttr($value, $data)
    {
        // 当前区域记录转换为数组
        $regionIds = explode(',', $data['region']);
        if (count($regionIds) === 373) return '全国';
        // 所有地区
        if (empty(self::$regionAll)) {
            self::$regionAll = Region::getCacheAll();
            self::$regionTree = Region::getCacheTree();
        }
        // 将当前可配送区域格式化为树状结构
        $alreadyTree = [];
        foreach ($regionIds as $regionId)
            $alreadyTree[self::$regionAll[$regionId]['pid']][] = $regionId;
        $str = '';
        foreach ($alreadyTree as $provinceId => $citys) {
            $str .= self::$regionTree[$provinceId]['name'];
            if (count($citys) !== count(self::$regionTree[$provinceId]['city'])) {
                $cityStr = '';
                foreach ($citys as $cityId)
                    $cityStr .= self::$regionTree[$provinceId]['city'][$cityId]['name'];
                $str .= ' (<span class="am-link-muted">' . mb_substr($cityStr, 0, -1, 'utf-8') . '</span>)';
            }
            $str .= '、';
        }
        return mb_substr($str, 0, -1, 'utf-8');
    }

}
