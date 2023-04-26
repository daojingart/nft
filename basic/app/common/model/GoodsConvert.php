<?php
/**
 * @Email:sliyusheng@foxmail.com
 * @Company 河南八六互联信息技术有限公司
 * @DateTime 2022-06-21 15:29
 */


namespace app\common\model;

use app\admin\model\Setting;
use app\api\model\collection\Goods;
use app\common\components\helpers\StockUtils;
use app\common\controller\Task;
use app\common\model\Member as MemberModel;
use app\notice\model\Order as OrderNoticeModel;
use exception\BaseException;
use TencentCloud\Tcaplusdb\V20190823\Models\IdlFileInfo;
use think\Db;
/**兑换码关联模型
 * Class GoodsConvert
 * @package app\common\model
 * @Email:sliyusheng@foxmail.com
 * @Company 河南八六互联信息技术有限公司
 * @DateTime 2022-06-21 15:29
 */
class GoodsConvert extends BaseModel
{
    /**
     * 兑换藏品
     * @param $param
     * @param $member_id
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-06-21 15:32
     */
    public static function exchange($param,$member_id)
    {
        if (!array_key_exists('code',$param)) {
            throw new BaseException(['msg' => '请输入验证码']);
        }
        $code = $param['code'];
        $res = GoodsConvert::where(['code_num'=>$code,'status'=>0])->find();
        if (!$res){
            throw new BaseException(['msg' => '兑换码不存在']);
        }
        //增加一个判断 如果库存不足 无法空投
        $stock_number = StockUtils::getStock($res['goods_id']);
        if($stock_number<1){
            throw new BaseException(['msg' => '库存不足无法兑换']);
        }
        //减少库存
        StockUtils::deductStock($res['goods_id'], 1);
        $goods_id = $res['goods_id'];
        $member_data = MemberModel::where('member_id',$member_id)->find()->toArray();
        $goods_data = Goods::with('writer')->where('goods_id',$goods_id)->find();
        $data['member_id'] = $member_id;
        $data['goods_id'] = $goods_id;
        $data['phone'] = $member_data['phone'];
        $data['nickname'] = $member_data['name'];
        $data['goods_no'] = $goods_data->goods_no;
        $data['goods_name'] = $goods_data->goods_name;
        $data['goods_thumb'] = $goods_data->goods_thumb;
        $data['total_num'] = 1;
        $data['goods_price'] = $goods_data->goods_price;
        $data['writer_id'] = $goods_data->writer->id;
        $data['writer_name'] = $goods_data->writer->name;
        $data['hash_url'] = '';
        $data['source_type'] = 6;
        $data['app_id'] = 10001;
        Db::startTrans();
        try{
            //更新兑换码状态
            self::update(['status'=>1],['code_num'=>$code]);
            //兑换藏品
            MemberGoods::create($data);
            $member_goods_insertId = (new MemberGoods())->getLastInsID();
            //修改兑换码使用人、使用时间
            (new GoodsConvert())->where(['id'=>$res['id']])->update(['used_member_id'=>$member_id,'used_time'=>time()]);
            //加入铸造藏品的队列
            (new OrderNoticeModel())->castingQuestionList($member_goods_insertId,$goods_data['goods_id'],$member_id,"casting_question_list");
            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            throw new BaseException(['msg'=>$e->getMessage()]);
        }
    }
}