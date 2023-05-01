<?php
/**
 * @Email:sliyusheng@foxmail.com
 * @Company 河南八六互联信息技术有限公司
 * @DateTime 2022-06-13 15:23
 */


namespace app\admin\controller\user;
use app\admin\model\collection\Writer as WriterModel;
use app\admin\service\Member as MemberService;
use app\api\model\collection\Category as CategoryModel;
use app\api\model\member\Member;
use app\common\model\Member as MemberModel;
use app\admin\controller\Controller;
use app\common\model\MemberChain;
use app\common\model\MemberGoods;
use app\common\model\MemberLabel;
use app\common\model\MemberLabelList;
use app\common\model\MemberReal;
use app\common\model\Message;
use app\common\model\Notice;

/**
 * 会员管理
 * Class Index
 * @package app\admin\controller\user
 * @Email:sliyusheng@foxmail.com
 * @Company 河南八六互联信息技术有限公司
 * @DateTime 2022-06-13 15:24
 */
class Index extends Controller
{
    /**
     * 用户列表
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-06-13 15:38
     */
    public function index()
    {
        if (request()->isAjax()) {
            $list = MemberModel::getMemberList(array_merge($this->request->param()));
            return json($list);
        }
        return $this->fetch();
    }

    /**
     * 用户列表
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-06-13 15:38
     */
    public function test()
    {
        if (request()->isAjax()) {
            $list = MemberModel::getMemberList(array_merge($this->request->param(), [
                'member_type' => 10
            ]));
            return json($list);
        }
        return $this->fetch();
    }

    /**
     * 添加会员
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-06-13 20:21
     */
    public function add()
    {
        if ($this->request->isAjax()) {
            $param = $this->request->param();
            if (MemberModel::where('phone', $param['stores']['phone'])->find()) {
                return $this->renderError('当前手机号已存在');
            }
            $param['stores']['code'] = $str = substr(md5(time() . mt_rand(0, 99999)), 0, 12);
            if (MemberModel::create($param['stores'])) {
                return $this->renderSuccess('创建成功', url('index'));
            }
            return $this->renderError('创建失败');
        }
        return $this->fetch();
    }

    /**
     * 更新用户状态
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-06-13 18:04
     */
    public function upStatus()
    {
        if (MemberService::upStatus()) {
            return $this->renderSuccess('更新成功');
        }
        return $this->renderError('更新失败');
    }

    /**
     * @Notes:调整上下级
     * @Interface modifySubordinate
     * @return array|mixed
     * @throws \think\exception\DbException
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/4/26   3:00 下午
     */
    public function modifySubordinate($member_id)
    {
        if ($this->request->isAjax()) {
            //用户详情
            $model = MemberModel::where('member_id', $member_id)->find();
            if ($model->updatePid($this->postData('member'))) {
                return $this->renderSuccess('调整成功');
            }
            return $this->renderError($model->getError() ?: '操作失败');
        }
        $this->view->engine->layout(false);
        return $this->fetch('/user/index/_template/modifysubordinate');
    }

    /**
     * 账户余额充值
     * @param $member_id
     * @param int $source 充值类型
     * @return array|bool
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function recharge($member_id)
    {
        if ($this->request->isAjax()) {
            $model = MemberModel::detail(['member_id' => $member_id]);
            if ($model->recharge($this->postData('balance'))) {
                return $this->renderSuccess('操作成功');
            }
            return $this->renderError($model->getError() ?: '操作失败');
        }
        $this->view->engine->layout(false);
        return $this->fetch('/user/index/_template/recharge');
    }

    public function honor($member_id)
    {
        if ($this->request->isAjax()) {
            $model = MemberModel::detail(['member_id' => $member_id]);
            if ($model->recharge($this->postData('balance'))) {
                return $this->renderSuccess('操作成功');
            }
            return $this->renderError($model->getError() ?: '操作失败');
        }
        $this->view->engine->layout(false);
        return $this->fetch('/user/index/_template/honor');
    }

    /**
     * 给会员贴标签
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface pasteLabel
     * @Time: 2022/2/18   23:25
     * @todo [描述] // 待办。提示自己或他人还需要做些什么
     */
    public function pasteLabel($member_id)
    {
        $model = new MemberLabelList();
        if ($this->request->isAjax()) {
            if ($model->add($this->postData("member"))) {
                return $this->renderSuccess('添加成功');
            }
            return $this->renderError($model->getError());
        }
        $this->view->engine->layout(false);
        $member_label_list = (new MemberLabel())->field("id,label_title")->select();
        $label_ids = $model->getLabelIds(['member_id' => $member_id]);
        return $this->fetch('/user/index/_template/paste_label', compact('member_label_list', 'member_id', 'label_ids'));
    }

    /**
     * 获取所有的会员所属节点列表
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface getNodeList
     * @Time: 2022/3/26   16:24
     */
    public function getNodeList($member_id)
    {
        if (request()->isAjax()){
            $data = (new MemberModel())->getChildData($this->request->param());
            return json($data);
        }
        $this->assign([
            'member_id' => $member_id,
        ]);
        return $this->fetch('nodelist');
    }


    /**
     * 空投商品 单个
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-06-14 19:20
     */
    public function drop($member_id)
    {
        if (request()->isAjax()) {
            $param = request()->param();
            if (MemberService::dropGoods($param)) {
                return $this->renderSuccess('空投成功', url('index'));
            }
            return $this->renderError('空投失败');
        }
        $this->assign('member_id', $member_id);
        return $this->fetch();
    }

    /**
     * 发送站内信
     * @param $member_id
     * @return array|mixed
     * @throws \exception\BaseException
     * @throws \think\exception\DbException
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-06-08 10:59
     */
    public function zhanneixin($member_id)
    {
        if ($this->request->isAjax()) {
            $param = $this->request->param();
            if (Notice::create($param['data'])) {
                return $this->renderSuccess('发送成功');
            }
            return $this->renderError('发送失败');
        }
        $this->view->engine->layout(false);
        return $this->fetch('/user/index/_template/zhanneixin', compact('member_id'));
    }

    /**
     * 空投商品分组选择
     * @return mixed
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-06-14 20:38
     */
    public function drops()
    {
        if (request()->isAjax()) {
            $param = request()->param();
            if (MemberService::dropGoodsAll($param['precedence'])) {
                return $this->renderSuccess('空投成功', url('index'));
            }
            return $this->renderError('空投失败');
        }
        //查询持有的藏品列表
        $member_goods_list = (new MemberGoods())->group("goods_id")->field("goods_id,goods_name")->select();
        return $this->fetch('',compact('member_goods_list'));
    }

    /**
     * 查看持有商品
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-06-15 09:31
     */
    public function hold($member_id)
    {
        $this->assign('member_id', $member_id);
        return $this->fetch();
    }

    /**
     * 无限级查询
     */
    static public $treeList = array();

    public function create($data, $pid = 0)
    {
        foreach ($data as $key => $value) {
            if ($value['p_id'] == $pid) {
                self::$treeList[] = $value;
                unset($data[$key]);
                self::create($data, $value['member_id']);
            }
        }
        return self::$treeList;
    }

    /**
     * 会员注册
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface importOrder
     * @Time: 2022/6/27   18:00
     */
    public function importOrder()
    {
        $data = json_decode(html_entity_decode($this->request->param()['data']), true);
        if (empty($data)) {
            return $this->renderError('导入失败');
        }
        foreach ($data as $key => $val) {
            $new_array = [
                'phone' => trim($val['phone']),
                'password' => md5($val['password']),
                'client_type' => 1,
                'code' => $this->makeCouponCard(),
                'name' => "道友_" . getNicknameGuidV4(),
                'avatarUrl' => base_url() . '/assets/touxiang.png',
            ];
            if (MemberModel::where('phone', $new_array['phone'])->find()) {
                continue;
            }
            (new MemberModel)->allowField(true)->save($new_array);


        }
        return $this->renderSuccess('导入成功');
    }

    /**
     * 会员注册
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface importOrder
     * @Time: 2022/6/27   18:00
     */
    public function importOrderTest()
    {
        $data = json_decode(html_entity_decode($this->request->param()['data']), true);
        if (empty($data)) {
            return $this->renderError('导入失败');
        }
        foreach ($data as $key => $val) {
            $new_array = [
                'phone' => trim($val['phone']),
                'password' => md5($val['password']),
                'member_type' => 10,
                'client_type' => 1,
                'code' => $this->makeCouponCard(),
                'name' => "道友_" . getNicknameGuidV4(),
                'avatarUrl' => base_url() . 'assets/touxiang.png',
            ];
            if (MemberModel::where('phone', $new_array['phone'])->find()) {
               //用户已经存在 更新白名单
                MemberModel::update(['member_type'=>10],['phone'=>$new_array['phone']]);
            }else{
                (new MemberModel)->allowField(true)->save($new_array);
            }

        }
        return $this->renderSuccess('导入成功');
    }


    /**
     * @Notes: 制作邀请码
     * @Interface make_coupon_card
     * @return string
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/4/24   6:05 下午
     */
    public function makeCouponCard()
    {
        $code = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $rand = $code[rand(0, 25)]
            . strtoupper(dechex(date('m')))
            . date('d') . substr(time(), -5)
            . substr(microtime(), 2, 5)
            . sprintf('%02d', rand(0, 99));
        for (
            $a = md5($rand, true),
            $s = '0123456789ABCDEFGHIJKLMNOPQRSTUV',
            $d = '',
            $f = 0;
            $f < 8;
            $g = ord($a[$f]),
            $d .= $s[($g ^ ord($a[$f + 8])) - $g & 0x1F],
            $f++
        ) ;
        return $d;
    }

    /**
     * 修改邀请人数
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface editInvitationsNumber
     * @Time: 2022/7/15   15:59
     */
    public function editInvitationsNumber()
    {
        $param = $this->request->param();
        switch ($param['type'])
        {
            case "z":
                if((new \app\common\model\Member())->where(['member_id'=>$param['member_id']])->update(['invitations_number'=>$param['invitations_number']])){
                    return $this->renderSuccess('修改成功');
                }
                break;
            case "m":
                if((new \app\common\model\Member())->where(['member_id'=>$param['member_id']])->update(['moon_invitations_number'=>$param['invitations_number']])){
                    return $this->renderSuccess('修改成功');
                }
                break;
            case "w":
                if((new \app\common\model\Member())->where(['member_id'=>$param['member_id']])->update(['week_invitations_number'=>$param['invitations_number']])){
                    return $this->renderSuccess('修改成功');
                }
                break;
        }
        return $this->renderError('修改失败');
    }

    /**
     * 增加限购次数
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface increasePurchaseLimit
     * @Time: 2022/7/24   22:20
     */
    public function increasePurchaseLimit($member_id)
    {
        if($this->request->isAjax()){
            if ((new \app\common\model\Member())->updatePurchaseLimit($this->request->param())) {
                return $this->renderSuccess('增加成功');
            }
            return $this->renderError('增加失败');
        }
        $this->view->engine->layout(false);
        return $this->fetch('/user/index/_template/increasepurchaselimit', compact('member_id'));
    }

    /**
     * 批量增加空投表格
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface importAirdrop
     * @Time: 2022/8/3   14:05
     */
    public function importAirdrop()
    {
        $data = json_decode(html_entity_decode($this->request->param()['data']), true);
        if (empty($data)) {
            return $this->renderError('导入失败');
        }
        if((new MemberService())->importAllDrop($data)){
            return $this->renderSuccess('空投成功');
        }
        return $this->renderError('空投失败');
    }


    /**
     * 批量修改限购次数
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface importAirdrop
     * @Time: 2022/8/3   14:05
     */
    public function importBuyOrderAirdrop()
    {
        $data = json_decode(html_entity_decode($this->request->param()['data']), true);
        if (empty($data)) {
            return $this->renderError('导入失败');
        }
        if((new MemberService())->importBuyOrderAirdrop($data)){
            return $this->renderSuccess('导入成功');
        }
        return $this->renderError('导入失败');
    }

    /**
     * 藏品转增
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-08-22 11:04
     */
    public function turnadd($member_id)
    {
        $where = ['cast_status'=>2,'goods_status'=>0,'is_synthesis'=>0,'is_donation'=>0];
        if (request()->isAjax()){
            $param = request()->param();
            $goods = MemberGoods::where(['goods_id'=>$param['goods_id'],'member_id'=>$member_id,])->select()->toArray();
            if (count($goods)<$param['count']){
                return  $this->renderError('当前剩余藏品不足');
            }
            $member_info = \app\common\model\Member::where('member_id',$member_id)->find();
            $info = (new \app\common\model\Member())->where(['phone' => $param['phone']])->find();
            $goodss = MemberGoods::where(['goods_id'=>$param['goods_id'],'member_id'=>$member_id])->where($where)->limit($param['count'])->select()->toArray();
            foreach ($goodss as $item){
                if(!(new MemberGoods())->toAllDonation($member_info,$info,$item['id'])){
                    continue;
                }
            }
            return  $this->renderSuccess('转增成功');
        }
        $member_goods = MemberGoods::where('member_id',$member_id)->where($where)->group('goods_id')->select()->toArray();
        foreach ($member_goods as &$item){
            $item['goods_name'] = $item['goods_name'].'('.MemberGoods::where('goods_id',$item['goods_id'])->where($where)->where('member_id',$member_id)->count('id').'个)';
        }
        $member = \app\common\model\Member::where('real_status',2)->field('real_status,member_id,phone,name')->select();
        $this->assign(['member_goods'=>$member_goods,'member'=>$member]);
        return $this->fetch();
    }

    /**
     * 编辑会员信息
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface edituser
     * @Time: 2022/8/24   21:04
     */
    public function edituser($member_id)
    {
        if ($this->request->isAjax()) {
            $param = $this->postData('stores');
            if (!MemberModel::where('phone', $param['phone'])->find()) {
                return $this->renderError('手机号不存在！');
            }
            if(!empty($param['operation_pwd'])){
                $member_salt   = self::getMemberSalt((int)$member_id);
                $operation_pwd = self::encryptPassword($param['operation_pwd'], $member_salt);
                if ((new MemberModel())->where(['member_id'=>$member_id])->update(['password'=>md5($param['password']),'phone'=>$param['phone'],'name'=>$param['name'],'operation_pwd'=>$operation_pwd])) {
                    return $this->renderSuccess('编辑成功', url('index'));
                }
            }else{
                if ((new MemberModel())->where(['member_id'=>$member_id])->update(['password'=>md5($param['password']),'phone'=>$param['phone'],'name'=>$param['name']])) {
                    return $this->renderSuccess('编辑成功', url('index'));
                }
            }
            return $this->renderError('编辑失败');
        }
        $this->view->engine->layout(false);
        $member_info = MemberModel::where('member_id',$member_id)->find();
        return $this->fetch('/user/index/_template/edit', compact('member_info'));
    }

    /**
     * 加密密码
     * @param string $password
     * @param string $salt
     * @return string
     */
    public static function encryptPassword(string $password, string $salt = ''): string
    {
        return md5(md5($password . $salt) . base64_encode($salt));
    }

    /**
     * 获取会员salt
     * @param int $member_id
     * @return string
     */
    public static function getMemberSalt(int $member_id): string
    {
        return substr(md5(md5($member_id) . base64_encode($member_id)), 0, 8);
    }
}