<?php
/**
 * @Email:sliyusheng@foxmail.com
 * @Company 河南八六互联信息技术有限公司
 * @DateTime 2022-06-15 15:22
 */


namespace app\common\model;

/**
 * 合成记录表
 * Class SyntheticLog
 * @package app\common\model
 * @Email:sliyusheng@foxmail.com
 * @Company 河南八六互联信息技术有限公司
 * @DateTime 2022-06-15 15:22
 */
class SyntheticLog extends BaseModel
{
    public function getCastTimeAttr($value)
    {
        return date('Y-m-d H:i:s',$value);
    }
    public function member()
    {
        return $this->belongsTo('member','member_id','member_id');
    }

    public function synthetic()
    {
        return $this->belongsTo('GoodsSynthetic','synthetic_id','id');
    }

    /**
     * 获取合成记录
     * @return \think\response\Json
     * @throws \think\exception\DbException
     * @Time: 2022/7/21   10:09
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface getList
     */
    public static function getList()
    {
        $param = request()->param();
        $where = [];
        if (array_key_exists('name',$param)&&!empty($param['name'])){
            $where['name']=['like','%'.$param['name'].'%'];//搜索关键字
        }

        if (array_key_exists('time',$param)&&!empty($param['time'])){
            $time = explode('~',$param['time']);
            $where['cast_time'] = ['between',[strtotime($time[0]),strtotime($time[1])]];
        }
        $page = request()->param('page')?:1; //当前第几页
        $list = request()->param('limit')?:10; //每页显示几条
        $data = self::with('member')->where($where)->paginate($list,false,$config = ['page'=>$page])->toArray();
        foreach ($data['data'] as &$item){
            $item['member_name'] = $item['member']['name'];
            $item['phone'] = $item['member']['phone'];
            $item['avatarUrl'] = $item['member']['avatarUrl'];
        }
        $arr['data'] = $data['data'];
        $arr['code'] = '0';
        $arr['msg'] = 'OK';
        $arr['count'] = $data['total'];
        return  json($arr);
    }
}