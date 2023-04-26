<?php
/**
 * @Email:sliyusheng@foxmail.com
 * @Company 河南八六互联信息技术有限公司
 * @DateTime 2022-07-26 15:34
 */


namespace app\api\controller\member;


use app\api\model\member\MemberAddress;
use app\api\validate\AddressValidate;
use app\api\validate\IDMustBePositiveInt;
use app\common\controller\Controller;
use think\Request;


/**
 * 收货地址
 */
class Address extends Controller
{
    private $member_id = '';

    /**
     * 初始化
     * Card constructor.
     * @param Request|null $request
     * @throws \exception\BaseException
     * @throws \think\exception\DbException
     */
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->member_id = $this->auth->getUser()['member_id'];
    }

    /**
     * 获取用户地址列表
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/member.address/MemberAddressLists)
     * @ApiReturnParams   (name="data", type="string", required=true, description="1==实名审核中 2==已实名")
     */
    public function MemberAddressLists()
    {
        $model = new MemberAddress();
        $list = $model->getList($this->member_id);
        $this->success('ok',$list);
    }

    /**
     * 添加用户地址
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/member.address/addMemberAddress)
     * @ApiParams   (name="name", type="integer", required=true, description="姓名")
     * @ApiParams   (name="phone", type="integer", required=true, description="手机号")
     * @ApiParams   (name="region", type="integer", required=true, description="选择城市列表的ID，使用逗号隔开传值")
     * @ApiParams   (name="detail", type="integer", required=true, description="详细的地址")
     * @ApiParams   (name="select_status", type="integer", required=true, description="是否默认 0==否 1==是")
     */
    public function addMemberAddress()
    {
        (new AddressValidate())->goCheck('add');
        $model = new MemberAddress;
        if ($model->add($this->member_id,\request()->param())) {
            $this->success('添加成功');
        }
        $this->error('编辑失败');
    }

    /**
     * 编辑用户地址
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/member.address/editMemberAddress)
     * @ApiParams   (name="name", type="integer", required=true, description="姓名")
     * @ApiParams   (name="phone", type="integer", required=true, description="手机号")
     * @ApiParams   (name="region", type="integer", required=true, description="选择城市列表的ID，使用逗号隔开传值")
     * @ApiParams   (name="detail", type="integer", required=true, description="详细的地址")
     * @ApiParams   (name="id", type="integer", required=true, description="地址的ID")
     * @ApiParams   (name="select_status", type="integer", required=true, description="是否默认 0==否 1==是")
     */
    public function editMemberAddress()
    {
        (new AddressValidate())->goCheck('edit');
        $model = new MemberAddress;
        if ($model->edit($this->member_id,\request()->param())) {
            $this->success('编辑成功');
        }
        $this->error('编辑失败');
    }

    /**
     * 删除收货地址
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/member.address/delMemberAddress)
     * @ApiParams   (name="id", type="integer", required=true, description="收货地址的ID")
     */
    public function delMemberAddress()
    {
        (new IDMustBePositiveInt())->goCheck();
        $model = new MemberAddress;
        if ($model->del($this->member_id,\request()->param())) {
            $this->success('删除成功');
        }
        $this->error('删除失败');
    }

    /**
     * 获取收货地址详情
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/member.address/MmeberAddressInfo)
     * @ApiParams   (name="id", type="integer", required=true, description="收货地址的ID")
     */
    public function MmeberAddressInfo()
    {
        (new IDMustBePositiveInt())->goCheck();
        $model = new MemberAddress;
        $data = $model->MmeberAddressInfo($this->member_id,\request()->param());
        if ($data) {
            $this->success('ok',$data);
        }
        $this->error('地址不存在');
    }

    /**
     * 设置默认收货地址
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/member.address/settingDefault)
     * @ApiParams   (name="address_id", type="string", required=true, description="收获地址列表ID")
     */
    public function settingDefault()
    {
        $address_id = $this->request->post('address_id');
        if(!$address_id){
            $this->error('请选择收货地址');
        }
        $member_address_info = (new MemberAddress)->where(['address_id'=>$address_id,'member_id'=>$this->auth->member_id])->find();
        if(empty($member_address_info)){
            $this->error('收货地址不存在');
        }
        (new MemberAddress)->where(['member_id'=>$this->auth->member_id])->update(['select_status'=>0]);
        (new MemberAddress)->where(['address_id'=>$address_id])->update(['select_status'=>1]);
        $this->success('设置成功');
    }
}