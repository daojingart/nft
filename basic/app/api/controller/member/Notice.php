<?php

namespace app\api\controller\member;

use app\common\controller\Controller;
use app\common\model\ProductCategory;
use \app\common\model\Notice as NoticeModel;
/**
 * 公告中心
 */
class Notice extends Controller
{
    protected $noNeedLogin = [
        'getNewNoticeList','getcategory','list','info'
    ];
    /**
     * 首页公告列表
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/member.Notice/getNewNoticeList)
     * @ApiReturnParams   (name="category_id", type="string", description="分类的ID")
     * @ApiReturnParams   (name="name", type="string", description="分类的名称")
     * @ApiReturnParams   (name="notice_list.id", type="string", description="公告的ID")
     * @ApiReturnParams   (name="notice_list.title", type="string", description="公告的标题")
     */
    public function getNewNoticeList()
    {
        //读取最近的三个分类;分类排序正序
        $type_list = (new ProductCategory())->order("sort asc")->field("category_id,name,sort")->limit(3)->select();
        //获取这三个分类的每个分类的一个公告
        foreach ($type_list as $key => $value) {
            $notice_list = (new NoticeModel())->where(['category_id'=>$value['category_id'],'type'=>2,'disabled'=>10])->order("create_time desc")->field("id,title")->find();
            $type_list[$key]['notice_list'] =$notice_list;
        }
        $this->success('ok',$type_list);
    }

    /**
     * 公告的分类列表
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/member.Notice/getcategory)
     * @ApiReturnParams   (name="category_id", type="string", description="分类的ID")
     * @ApiReturnParams   (name="name", type="string", description="分类的名称")
     */
    public function getcategory()
    {
        $product_list = (new ProductCategory())->order("sort asc")->field("category_id,name")->select();
        $this->success('ok',$product_list);
    }

    /**
     * 根据分类获取公告列表
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/member.Notice/list)
     * @ApiParams   (name="category_id", type="string", required=true, description="分类的ID")
     * @ApiParams   (name="page", type="string", required=true, description="分页")
     * @ApiReturnParams   (name="id", type="string", description="公告的 ID")
     * @ApiReturnParams   (name="create_time", type="string", description="创建时间")
     * @ApiReturnParams   (name="title", type="string", description="标题")
     * @ApiReturnParams   (name="desc", type="string", description="简介")
     * @ApiReturnParams   (name="author", type="string", description="作者")
     * @ApiReturnParams   (name="image_url", type="string", description="封面图")
     */
    public function list()
    {
        $category_id = $this->request->post('category_id');
        $page = $this->request->post('page')?$this->request->post('page'):1;
        if(!$category_id || !$page){
            $this->error('参数错误');
        }
        $data = NoticeModel::list($category_id,$page);
        $this->success('ok',$data);
    }

    /**
     * 获取公告的详情
     * @ApiAuthor [Mr.Li]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/member.notice/info)
     * @ApiParams   (name="id", type="string", required=true, description="公告的ID")
     */
    public function info()
    {
        $id = $this->request->param('id');
        if(!$id){
            $this->error('参数传值错误');
        }
        $data = NoticeModel::where('id',$id)->field("id,title,content")->find();
        if ($data){
            $data['content'] = htmlspecialchars_decode($data['content']);
            $this->success('ok',$data);
        }
        $this->error($data);
    }


}