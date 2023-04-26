<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.Ai [18612421593@163.com]
 * +----------------------------------------------------------------------
 * | Time:  2022/6/16   11:53
 * +----------------------------------------------------------------------
 * | className:
 * +----------------------------------------------------------------------
 */

namespace app\admin\model\collection;

use app\common\model\Convert as ConvertModel;


class Convert extends ConvertModel
{
    /**
     * 获取藏品对应的兑换码列表
     * @param $param
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @Time: 2022/6/16   11:54
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface getList
     */
    public function getList($param)
    {
        $limit = $param['limit'];
        $offset = ($param['page'] - 1) * $limit;

        $where['goods_id'] = $param['goods_id'];
        $dataList = $this->where($where)
            ->order(['id' => 'desc'])
            ->limit($offset,$limit)
            ->select();
        foreach ($dataList as $k => $v){
            $dataList[$k]['operate'] = showNewOperate(self::makeButton($v['id']));
        }

        $return['count'] = $this->where($where)->count();
        $return['data'] = $dataList;
        $return['code'] = '0';
        $return['msg'] = 'OK';

        return $return;
    }

    /**
     * 操作按钮
     * @param $id
     * @return array
     * @Time: 2022/6/16   11:55
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface makeButton
     */
    private static  function makeButton($id)
    {
        return [
            '删除' => [
                'href' => "javascript:void(0)",
                'lay-event' => 'remove',
            ]
        ];
    }

    /**
     * 添加兑换码
     * @param $params
     * @return array|false|\think\Collection|\think\model\Collection
     * @throws \Exception
     * @Time: 2022/6/16   14:00
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface addConvert
     */
    public function addConvert($params)
    {
        for ($i = 0;$i < $params['num'];$i++){
            $data[$i] = [
                'code_num' => self::make_coupon_card(),
                'goods_id' => $params['goods_id'],
                'app_id' => self::$app_id
            ];
        }

        return $this->allowField(true)->saveAll($data);
    }

    /**
     * 兑换码生成规则
     * @return string
     * @Time: 2022/6/16   14:01
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface make_coupon_card
     */
    private function make_coupon_card()
    {
        $code = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $rand = $code[rand(0,25)]
            .strtoupper(dechex(date('m')))
            .date('d').substr(time(),-5)
            .substr(microtime(),2,5)
            .sprintf('%02d',rand(0,99));
        for(
            $a = md5( $rand, true ),
            $s = '0123456789ABCDEFGHIJKLMNOPQRSTUV',
            $d = '',
            $f = 0;
            $f < 8;
            $g = ord( $a[ $f ] ),
            $d .= $s[ ( $g ^ ord( $a[ $f + 8 ] ) ) - $g & 0x1F ],
            $f++
        );
        return $d;
    }

    /**
     * 删除兑换码
     * @param $convert_id
     * @return false|int
     * @Time: 2022/6/16   14:49
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface delConvert
     */
    public function delConvert($convert_id)
    {
        return $this->allowField(true)->where(['id' => $convert_id])->delete();
    }
}