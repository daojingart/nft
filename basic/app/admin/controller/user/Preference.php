<?php
/**
 * @Email:sliyusheng@foxmail.com
 * @Company 河南八六互联信息技术有限公司
 * @DateTime 2022-06-14 15:18
 */


namespace app\admin\controller\user;


use app\admin\controller\Controller;
use app\admin\model\collection\Writer as WriterModel;
use app\api\model\collection\Category as CategoryModel;
use app\common\model\Goods;
use app\common\model\GoodsPrecedence;
use app\common\model\GoodsPrecedenceList;
use app\common\model\MemberGoods;

/**
 * 会员优先购
 * Class Preference
 * @package app\admin\controller\user
 * @Email:sliyusheng@foxmail.com
 * @Company 河南八六互联信息技术有限公司
 * @DateTime 2022-06-14 15:18
 */
class Preference extends Controller
{
    /**
     * 会员优先购列表
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-06-14 15:18
     */
    public function index()
    {
        if (request()->isAjax()){
            return GoodsPrecedence::getList();
        }
        return $this->fetch();
    }

    /**
     * 添加优先购
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-06-14 15:19
     */
    public function add()
    {
        if (request()->isAjax()){
            $param = request()->param();
            //增加效验
            if(!$param['precedence']['goods_id']){
                return  $this->renderError('优先购藏品对象不能为空');
            }
            if (GoodsPrecedence::where(['goods_id'=>$param['precedence']['goods_id']])->find()){
                return  $this->renderError('规则已存在无法再次添加');
            }
            switch ($param['precedence']['label_type'])
            {
                case "1":
                    if (GoodsPrecedence::where(['goods_id'=>$param['precedence']['goods_id'],'label_id'=>$param['precedence']['label_id']])->find()){
                        return  $this->renderError('创建失败当前分组已经存在');
                    }
                    if (GoodsPrecedence::create($param['precedence'])) {
                        return $this->renderSuccess('创建成功',url('index'));
                    }
                    break;
                case "2":
                    if(!isset($param['goods'])){
                        return  $this->renderError('请先制定规则对象');
                    }
                    if(isset($param['goods']) && empty($param['goods'])){
                        return  $this->renderError('请先制定规则对象');
                    }
                    $goods_precedence_id = (new GoodsPrecedence())->insertGetId([
                        'goods_id' => $param['precedence']['goods_id'],
                        'label_id' => 0,
                        'status' => $param['precedence']['status'],
                        'app_id' => '10001',
                        'update_time' => time(),
                        'create_time' => time(),
                        'label_type' => $param['precedence']['label_type'],
                    ]);
                    //添加规则列表 保存到新库中
                    $new_data = [];
                    foreach ($param['goods']['goods_id'] as $key=>$val){
                        $new_data[$key]['goods_id'] = $val;
                        $new_data[$key]['hold_goods_number'] = $param['goods']['hold_goods_number'][$key];
                        $new_data[$key]['purchase_time'] = $param['goods']['purchase_time'][$key];
                        $new_data[$key]['purchase_quantity'] = $param['goods']['purchase_quantity'][$key];
                        $new_data[$key]['precedence_goods_id'] = $param['precedence']['goods_id'];
                    }
                    (new GoodsPrecedenceList())->insertData($new_data);
                    return $this->renderSuccess('创建成功',url('index'));
                case "3":
                    if(!isset($param['goods'])){
                        return  $this->renderError('请先制定规则对象');
                    }
                    if(isset($param['goods']) && empty($param['goods'])){
                        return  $this->renderError('请先制定规则对象');
                    }
                    (new GoodsPrecedence())->insertGetId([
                        'goods_id' => $param['precedence']['goods_id'],
                        'label_id' => 0,
                        'status' => $param['precedence']['status'],
                        'app_id' => '10001',
                        'update_time' => time(),
                        'create_time' => time(),
                        'label_type' => $param['precedence']['label_type'],
                    ]);
                    //添加规则列表 保存到新库中
                    $new_data = [];
                    foreach ($param['goods']['c_goods_id'] as $key=>$val){
                        $new_data[$key]['goods_id'] = $val;
                        $new_data[$key]['hold_goods_number'] = 1;
                        $new_data[$key]['purchase_time'] = $param['goods']['c_purchase_time'][$key];
                        $new_data[$key]['purchase_quantity'] = 1;
                        $new_data[$key]['precedence_goods_id'] = $param['precedence']['goods_id'];
                    }
                    (new GoodsPrecedenceList())->insertData($new_data);
                    return $this->renderSuccess('创建成功',url('index'));

            }
            return  $this->renderError('创建失败');
        }
        return $this->fetch();
    }

    /**
     * 编辑优先购
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-06-14 15:19
     */
    public function edit()
    {
        if (request()->isAjax()){
            $param = request()->param();
            if(!$param['precedence']['goods_id']){
                return  $this->renderError('优先购藏品对象不能为空');
            }
            $goods_Precedence = (new GoodsPrecedence())->where(['id'=>$param['id']])->find();
            if($goods_Precedence['goods_id'] !=$param['precedence']['goods_id']){
                if (GoodsPrecedence::where(['goods_id'=>$param['precedence']['goods_id']])->find()){
                    return  $this->renderError('规则已存在无法再次添加');
                }
            }
            switch ($param['precedence']['label_type'])
            {
                case "1":
                    if (GoodsPrecedence::update($param['precedence'],['id'=>$param['id']])){
                        return  $this->renderSuccess('更新成功',url('index'));
                    }
                    break;
                case "2":
                    if(!isset($param['goods'])){
                        return  $this->renderError('请先制定规则对象');
                    }
                    if(isset($param['goods']) && empty($param['goods'])){
                        return  $this->renderError('请先制定规则对象');
                    }
                    GoodsPrecedence::update($param['precedence'],['id'=>$param['id']]);
                    //添加规则列表 保存到新库中
                    $new_data = [];
                    foreach ($param['goods']['goods_id'] as $key=>$val){
                        $new_data[$key]['goods_id'] = $val;
                        $new_data[$key]['hold_goods_number'] = $param['goods']['hold_goods_number'][$key];
                        $new_data[$key]['purchase_time'] = $param['goods']['purchase_time'][$key];
                        $new_data[$key]['purchase_quantity'] = $param['goods']['purchase_quantity'][$key];
                        $new_data[$key]['precedence_goods_id'] = $param['precedence']['goods_id'];
                    }
                    //清空所有的规则
                    (new GoodsPrecedenceList())->where(['precedence_goods_id'=>$param['precedence']['goods_id']])->delete();
                    (new GoodsPrecedenceList())->insertData($new_data);
                    return $this->renderSuccess('创建成功',url('index'));
                    break;
                case "3":
                    if(!isset($param['goods'])){
                        return  $this->renderError('请先制定规则对象');
                    }
                    if(isset($param['goods']) && empty($param['goods'])){
                        return  $this->renderError('请先制定规则对象');
                    }
                    GoodsPrecedence::update($param['precedence'],['id'=>$param['id']]);
                    //添加规则列表 保存到新库中
                    $new_data = [];
                    foreach ($param['goods']['c_goods_id'] as $key=>$val){
                        $new_data[$key]['goods_id'] = $val;
                        $new_data[$key]['hold_goods_number'] = 1;
                        $new_data[$key]['purchase_time'] = $param['goods']['c_purchase_time'][$key];
                        $new_data[$key]['purchase_quantity'] = 1;
                        $new_data[$key]['precedence_goods_id'] = $param['precedence']['goods_id'];
                    }
                    //清空所有的规则
                    (new GoodsPrecedenceList())->where(['precedence_goods_id'=>$param['precedence']['goods_id']])->delete();
                    (new GoodsPrecedenceList())->insertData($new_data);
                    return $this->renderSuccess('创建成功',url('index'));
                    break;
            }
            return $this->renderError('更新失败');
        }
        $precedence_id = input('id');
        //查询这个编辑的是标签 还是什么
        $precedence_info = (new GoodsPrecedence())->where(['id'=>$precedence_id])->find();
        $data = GoodsPrecedence::with(['goods','label'])->where('id',$precedence_id)->find();
        $this->assign('data',$data);
        if($precedence_info['label_type']==2 || $precedence_info['label_type']==3){
            $GoodsPrecedenceList = (new GoodsPrecedenceList())->where(['precedence_goods_id'=>$precedence_info['goods_id']])->select();
            foreach ($GoodsPrecedenceList as $key=>$val){
                $goods_info = Goods::detail($val['goods_id']);
                $GoodsPrecedenceList[$key]['goods_name'] = $goods_info['goods_name'];
            }
            $this->assign('GoodsPrecedenceList',$GoodsPrecedenceList);
        }else{
            $this->assign('GoodsPrecedenceList',[]);
        }
        return $this->fetch();
    }

    /**
     * 藏品选择
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-06-14 16:17
     */
    public function goods()
    {
        $writer = WriterModel::where(['is_del' => 0])->field('id,name')->select();
        $types = $this->request->param('types');
        $this->assign(['writer' => $writer,'types'=>$types]);
        $this->view->engine->layout(false);
        return $this->fetch();
    }

    /**
     * 用户选择
     * @return mixed
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-06-14 16:17
     */
    public function user()
    {
        $this->view->engine->layout(false);
        return $this->fetch();
    }

    /**
     * 标签选择
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-06-14 19:00
     */
    public function label()
    {
        $this->view->engine->layout(false);
        return $this->fetch();
    }

    /**
     * 添加规则
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface rule
     * @Time: 2022/9/3   11:11
     */
    public function rule()
    {
        $this->view->engine->layout(false);
        //查询发行的藏品
        $goods_list = (new MemberGoods())->group("goods_id")->field("goods_id,goods_name")->select();
        return $this->fetch('',compact('goods_list'));
    }

    /**
     * 添加规则
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface rule
     * @Time: 2022/9/3   11:11
     */
    public function superimposingrule()
    {
        $this->view->engine->layout(false);
        //查询发行的藏品
        $goods_list = (new MemberGoods())->group("goods_id")->field("goods_id,goods_name")->select();
        return $this->fetch('',compact('goods_list'));
    }


}