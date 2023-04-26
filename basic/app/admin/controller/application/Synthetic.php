<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: sliyusheng@foxmail.com
 * +----------------------------------------------------------------------
 * | Time:  2022-06-15 10:03
 * +----------------------------------------------------------------------
 * | className:  合成
 * +----------------------------------------------------------------------
 */

namespace app\admin\controller\application;


use app\admin\controller\Controller;
use app\admin\model\application\GoodsSyntheticWhitelist;
use app\admin\model\collection\Writer as WriterModel;
use app\common\model\Goods;
use app\common\model\GoodsSynthetic;
use app\admin\service\Synthetic as SyntheticService;
use app\common\model\Member;


class Synthetic extends Controller
{
    /**
     * 合成列表
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-06-15 10:05
     */
    public function index()
    {
        if (request()->isAjax()){
            return $data = SyntheticService::getList();
        }
        return $this->fetch();
    }

    /**
     * 添加合成商品
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-06-15 10:34
     */
    public function add()
    {
        if (request()->isAjax()){
            $param = request()->param();
            $time = explode('~',$param['synthetic']['time']);
            $param['synthetic']['start_time'] = strtotime($time[0]);
            $param['synthetic']['end_time'] = strtotime($time[1]);
            $param['synthetic']['remain'] = $param['synthetic']['count'];
            $param['synthetic']['goods_id'] = implode(',', $param['synthetic']['goods_ids']);
            unset($param['synthetic']['goods_ids']);
            if (GoodsSynthetic::create($param['synthetic'],true)){
                return  $this->renderSuccess('创建成功',url('index'));
            }
            return  $this->renderError('创建失败');
        }
        $goods_list = (new Goods())->where(['product_types' => ['in',['1','3']],'is_del'=>'0'])->field("goods_name,goods_id,is_del")->select();
        return $this->fetch('',compact('goods_list'));
    }

    /**
     * 编辑商品
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-06-15 11:56
     */
    public function edit($id)
    {
        $data = GoodsSynthetic::with('goods')->where('id',$id)->find()->toArray();
        if (request()->isAjax()){
            $param = request()->param();
            $time = explode('~',$param['synthetic']['time']);
            $param['synthetic']['start_time'] = strtotime($time[0]);
            $param['synthetic']['end_time'] = strtotime($time[1]);
            $param['synthetic']['remain'] = $param['synthetic']['count']-$data['exchange'];
            $param['synthetic']['goods_id'] = implode(',', $param['synthetic']['goods_ids']);
            unset($param['synthetic']['goods_ids']);
            if (GoodsSynthetic::update($param['synthetic'],['id'=>$param['id']],true)){
                return  $this->renderSuccess('更新成功',url('index'));
            }
            return  $this->renderError('更新失败');
        }
        $goods_list = (new Goods())->where(['product_types' => ['in',['1','3']],'is_del'=>'0'])->field("goods_name,goods_id,is_del")->select();
        $data['goods_name'] = $data['goods']['goods_name'];
        $data['goods_id'] = explode(',', $data['goods_id']);
        return $this->fetch('',compact('data','goods_list'));
    }


    /**
     * 合成白名单列表
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiParams   (name="name", type="string", required=true, description="用户名")
     * @ApiReturnParams   (name="sale_number", type="string", description="1==实名审核中 2==已实名")
     */
    public function whitelist()
    {
        if (request()->isAjax()){
            return $data = GoodsSyntheticWhitelist::getList();
        }
        $id = $this->request->param('id');
        return $this->fetch('',compact('id'));
    }

    /**
     * 添加合成的白名单
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     */
    public function addWhitelist()
    {
        if (request()->isAjax()){
            $data = json_decode(html_entity_decode($this->request->param()['data']), true);
            if (empty($data)) {
                return $this->renderError('导入失败');
            }
            $synthetic_id = $this->request->param('synthetic_id');
            $new_array = [];
            foreach ($data as $key => $val){
                $member_info = (new Member())->where(['phone'=>$val['phone'],'is_del'=>1])->field("member_id,is_del")->find();
                if(empty($member_info)){
                    continue;
                }
                $new_data[] = [
                    'member_id' => $member_info['member_id'],
                    'synthetic_id' => $synthetic_id,
                    'app_id' => '10001',
                    'create_time' => time()
                ];
            }
            if (empty($new_data)) {
                return $this->renderError('导入失败');
            }
            if ((new GoodsSyntheticWhitelist())->addAll($new_data)){
                return  $this->renderSuccess('导入成功',url('application.synthetic/whitelist',['id'=>$synthetic_id]));
            }
            return  $this->renderError('创建失败');
        }
        return $this->fetch();
    }

    /**
     * 删除白名单
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  ()
     * @ApiParams   (name="name", type="string", required=true, description="用户名")
     * @ApiReturnParams   (name="sale_number", type="string", description="1==实名审核中 2==已实名")
     */
    public function delwhitelist()
    {
        $id = $this->request->param('id');
        (new GoodsSyntheticWhitelist())->where('id',$id)->delete();
        return $this->renderSuccess('删除成功');
    }
}