<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang [1040657944@qq.com]
 * +----------------------------------------------------------------------
 * | Time:  2022/1/5   00:35
 * +----------------------------------------------------------------------
 * | className:
 * +----------------------------------------------------------------------
 */

namespace app\api\model;

use app\api\model\marketing\Active;
use app\api\model\marketing\Answer;
use app\api\model\marketing\Association;
use app\api\model\marketing\Card;
use app\api\model\marketing\ExamProject;
use app\common\model\AnswerMemberModel;
use app\common\model\ApplyOnlineRecord;
use app\common\model\AssociationMember;
use app\common\model\CardDiary;
use app\common\model\CardMemberLearn;
use app\common\model\Course;
use app\common\model\CourseLive;
use app\common\model\ExamPaper;
use app\common\model\Goods;
use app\common\model\Group;
use app\common\model\GroupRecord;
use app\common\model\NewArticle;
use app\common\model\Page as PageModel;
use app\api\model\teacher\Teacher as TeacherModel;
use app\common\model\Seckill;
use app\common\model\SeckillRecord;
use app\common\model\TimeLimit;
use app\common\model\TimeLimitCourse;
use think\Db;

class Page extends PageModel
{
    /**
     * 隐藏字段
     * @var array
     */
    protected $hidden = [
        'wxapp_id',
        'create_time',
        'update_time'
    ];

    /**
     * DIY页面详情
     * @param $user
     * @param null $page_id
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getPageData($page_id = null)
    {
        // 页面详情
        $detail = $page_id > 0 ? parent::detail($page_id) : parent::getHomePage();
        // 页面diy元素
        $items = $detail['page_data']['items'];
        // 页面顶部导航
        isset($detail['page_data']['page']) && $items['page'] = $detail['page_data']['page'];
        // 获取动态数据 由于个别数据没有进行选择只能手动智能推荐
        $model = new self;
        foreach ($items as $key => $item) {
            $items[$key]['item_id'] = $key;
            $items[$key]['page'] = 1;
            switch ($item['type'])
            {
                case "knowledge": //知识产品
                    $items[$key]['data'] = $model->getKnowledgeList($item);
                    break;
                case "goods": //实物商城
                    $items[$key]['data'] = $model->getGoodsList($item);
                    break;
                case "teacher": //导师组件
                    $items[$key]['data'] = $model->getTeacherList($item);
                    break;
                case "live": //直播组件
                    $items[$key]['data'] = $model->getLiveList($item);
                    break;
                case "online": //在线报名
                    $items[$key]['data'] = $model->getOnlineList($item);
                    break;
                case "community": //社群组件
                    $items[$key]['data'] = $model->getCommunityList($item);
                    break;
                case "evaluation": //在线测评
                    $items[$key]['data'] = $model->getEvaluationList($item);
                    break;
                case "Q&A": //付费问答
                    $items[$key]['data'] = $model->getAnswerList($item);
                    break;
                case "clock": //打卡挑战
                    $items[$key]['data'] = $model->getClockList($item);
                    break;
                case "team": //拼团组件
                    $items[$key]['data'] = $model->geTeamList($item);
                    break;
                case "limited": //限时免费组件
                    $items[$key]['data'] = $model->getLimitedList($item);
                    break;
                case "spike": //在线秒杀
                    $items[$key]['data'] = $model->getSpikeList($item);
                    break;
                case "article": //新闻组件
                    $items[$key]['data'] = $model->getArticleList($item);
                    break;
            }
            unset($items[$key]['defaultData']);
        }
        return ['page' => $items['page'], 'items' => $items];
    }

    /**
     * 知识产品组件
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface getKnowledgeList
     * @Time: 2022/1/17   23:34
     * @todo [描述] // 待办。提示自己或他人还需要做些什么
     */
    public function getKnowledgeList($item,$page=1,$type='change')
    {
        $pageNUmber = $item['params']['auto']['showNum'];
        if($type != 'change'){
            $pageNUmber = "10";
        }
        if($item['params']['source']=='auto'){ //选择的分类需要重新获取数据进行展示
            $data = (new Course())->getCategoryAllList(['category_id'=>$item['params']['auto']['category']],true,$page,$pageNUmber);
        }else{
            $data = $item['data'];
            $start = ($page-1)*$pageNUmber;//偏移量，当前页-1乘以每页显示条数
            $data = array_slice($data,$start,$pageNUmber);
        }
        return $data;
    }

    /**
     * 实物商城组件
     * @param $item
     * @Time: 2022/1/17   23:42
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface getGoodsList
     * @todo [描述] // 待办。提示自己或他人还需要做些什么
     */
    public function getGoodsList($item,$page=1,$type='change')
    {
        $pageNUmber = $item['params']['auto']['showNum'];
        if($type != 'change'){
            $pageNUmber = "10";
        }
        $new_data = [];
        if($item['params']['source']=='auto'){
            //根据分类的ID 去选择需要的数据
            $data = (new Goods())->getList([
                'category_id' =>  $item['params']['auto']['category'],
                'listRows' => $pageNUmber
            ])->toArray()['data'];
        }else{
            $data = $item['data'];
            $start = ($page-1)*$pageNUmber;//偏移量，当前页-1乘以每页显示条数
            $data = array_slice($data,$start,$pageNUmber);
        }
        foreach ($data as $key=>$val){
            $new_data[$key]['goods_id'] = $val['goods_id'];
            $new_data[$key]['goods_name'] = $val['goods_name'];
            $new_data[$key]['goods_image'] = $val['goods_image'];
            $new_data[$key]['sales_initial'] = $val['sales_initial']+$val['sales_actual'];
            $new_data[$key]['goods_price'] = $val['goods_min_price'];
        }
        return $new_data;
    }

    /**
     * 获取导师组件进行数据组装
     * @param $item
     * @Time: 2022/1/6   15:56
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface getTeacherList
     * @todo [描述] // 待办。提示自己或他人还需要做些什么
     */
    public function getTeacherList($item,$page=1,$type='change')
    {
        //查询数据 然后组装数据
        $teacher_list_obj = (new TeacherModel())->getTeacherList(['is_rack'=>10,'audit_status'=>30],$page);
        //安装数据
        $data = [];
        if(empty($teacher_list_obj)){
            return $data;
        }
        foreach ($teacher_list_obj as $key=>$val){
            $course_name = (new Course())->where(['is_teacher' => $val['id'],'is_rack' =>'10'])->order("id desc")->find()['course_name'];
            if(empty($course_name)){
                $course_name = "暂无最新上架课程";
            }else{
                $course_name = "最新上架:".$course_name;
            }
            $course_number = (new Course())->where(['is_teacher'=>$val['id'],'is_rack'=>10])->count();
            $data[] = [
                'teacher_id' => $val['id'],
                'image' => $val['teacher_thumb'],
                'name' => $val['teacher_name'],
                'teacher_name' => $val['teacher_title'],
                'teacher_content' => $val['teacher_content'],
                'course_name' => $course_name,
                'course_number' => $course_number
            ];
        }
        return $data;
    }

    /**
     * 获取在线列表
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface getOnlineList
     * @Time: 2022/1/6   16:10
     * @todo [描述] // 待办。提示自己或他人还需要做些什么
     */
    public function getOnlineList($item,$page=1,$type='change',$member_id='')
    {
        $limit = $item['params']['showNum'];
        if($type!='change'){
            $limit = "10";
        }
        $active_obj = (new Active())->getDataList(['is_delete'=>0,'status'=>1],$page,$limit);
        $data = [];
        if(empty($active_obj)){
            return $data;
        }
        foreach ($active_obj as $key=>$val){
            $data[] = [
                'online_id' => $val['apply_online_id'],
                'image' => $val['activity_image'],
                'desc' => $val['activity_intro'],
                'price' => $val['activity_price'],
                'pay_type' => $val['pay_type'],
                'title' => $val['activity_name'],
                'y_join_number' => (new ApplyOnlineRecord())->where(['apply_online_id'=>$val['apply_online_id'],'status'=>1])->count(),
                'x_join_number' => $val['join_num']
            ];
        }
        return $data;
    }

    /**
     * 社群组件
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface getCommunityList
     * @Time: 2022/1/6   16:38
     * @todo [描述] // 待办。提示自己或他人还需要做些什么
     */
    public function getCommunityList($item,$page=1,$type='change')
    {
        $limit = $item['params']['showNum'];
        if($type!='change'){
            $limit = "10";
        }
        $community_obj = (new Association())->getDataList(['is_delete'=>0,'disabled'=>1],$page,$limit);
        $data = [];
        if(empty($community_obj)){
            return $data;
        }
        foreach ($community_obj as $key=>$val){
            $data[] = [
                'pay_type' =>$val['pay_type'],
                'association_price' =>$val['association_price'],
                'association_number' => (new AssociationMember())->where(['association_id'=>$val['association_id']])->count(),
                'association_id' => $val['association_id'],
                'title' => $val['association_name'],
                'image' => $val['association_image'],
            ];
        }
        return $data;
    }

    /**
     * 获取考试组件列表数据
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface getEvaluationList
     * @Time: 2022/1/6   16:45
     * @todo [描述] // 待办。提示自己或他人还需要做些什么
     */
    public function getEvaluationList($item,$page=1,$type='change')
    {
        $limit = $item['params']['showNum'];
        if($type!='change'){
            $limit = "10";
        }
        $community_obj = (new ExamProject())->getDataList(['disabled'=>1],$page,$limit);
        $data = [];
        if(empty($community_obj)){
            return $data;
        }
        foreach ($community_obj as $key=>$val){
            $selling = $val['sales_initial']+$val['sales_actual'];
            $data[] = [
                'id' => $val['id'],
                'title' => $val['title'],
                'image' => $val['thumb'],
                'price' => $val['price'],
                'sales' => $selling,
                'count_number' => (new ExamPaper())->where(['project_id'=>$val['id'],'disabled'=>1])->count(),
            ];
        }
        return $data;
    }

    /**
     *  获取问答列表
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface getAnswerList
     * @Time: 2022/1/6   17:05
     * @todo [描述] // 待办。提示自己或他人还需要做些什么
     */
    public function getAnswerList($item,$page=1,$type='change')
    {
        $limit = $item['params']['showNum'];
        if($type!='change'){
            $limit = "10";
        }
        $answer_obj = (new Answer())->getDataList(['is_rack'=>10,'disabled'=>1],$page,$limit);
        $data = [];
        if(empty($answer_obj)){
            return $data;
        }
        foreach ($answer_obj as $key=>$val){
            $data[] = [
                'id' => $val['id'],
                'area_title' => $val['area_title'],
                'area_thumb' => $val['area_thumb'],
                'answer_count' => (new AnswerMemberModel())->where(['area_id'=>$val['id']])->count(),
                'number' => $val['number'],
            ];
        }
        return $data;
    }

    /**
     * 获取打卡列表
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface getClockList
     * @Time: 2022/1/6   17:12
     * @todo [描述] // 待办。提示自己或他人还需要做些什么
     */
    public function getClockList($item,$page=1,$type='change',$member_id=0)
    {
        $limit = $item['params']['showNum'];
        if($type!='change'){
            $limit = "10";
        }
        $card_obj = (new Card())->getDataList(['is_rack'=>10],$page,$limit);
        $data = [];
        if(empty($card_obj)){
            return $data;
        }
        foreach ($card_obj as $key=>$val){
            $status = "1";
            if($member_id){
                //判断今天是否打卡
                $CardDiary = (new CardDiary())->whereTime("create_time", "d")->where(['member_id'=>$member_id,'check_id'=>$val['id'],'status'=>1])->find();
                if(empty($CardDiary)){
                    $status = "2";
                    $CardMemberLearn = (new CardMemberLearn())->where(['member_id'=>$member_id,'card_id'=>$val['id']])->find();
                    if(empty($CardMemberLearn)){
                        $status = "1";
                    }
                }else{
                    $status = "3";
                }
            }
            $data[] = [
                'id' => $val['id'],
                'title' => $val['title'],
                'thumb' => $val['thumb'],
                'goods_sales' => $val['goods_sales'],
                'status' =>$status
            ];
        }
        return $data;
    }

    /**
     *  拼团组件
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface geTeamList
     * @Time: 2022/1/6   17:34
     * @todo [描述] // 待办。提示自己或他人还需要做些什么
     */
    public function geTeamList($item,$page=1,$type='change')
    {
        $limit = $item['params']['showNum'];
        if($type!='change'){
            $limit = "10";
        }
        $data = [];
        $GroupList = (new Group())->getModelMes(['is_delete'=>0],true,$page,$limit);
        foreach ($GroupList as $key=>$val){
            $count_number = (new GroupRecord())->where(['group_id'=>$val['group_id']])->count();
            $data[$key]['id'] = $val['group_id'];
            $data[$key]['title'] = $val['activity_name'];
            $data[$key]['image'] = $val['activity_image'];
            $data[$key]['price'] = $val['activity_price'];
            $data[$key]['sales'] = $val['activity_num'];
            $data[$key]['activity_start_time'] = $val['start_time'];
            $data[$key]['activity_end_time'] = $val['end_time'];
            $data[$key]['now_time'] = time();
            $data[$key]['count_number'] = $count_number;
        }
        return $data;
    }

    /**
     * 限时免费
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface geTeamList
     * @Time: 2022/1/6   17:34
     * @todo [描述] // 待办。提示自己或他人还需要做些什么
     */
    public function getLimitedList($item,$page=1,$type='change')
    {
        $limit = isset($item['params']['showNum'])?:10;
        if($type != 'change'){
            $limit = "10000";
        }
        $data = [];
        //获取限时免费更多数据列表
        $limitList = (new TimeLimit())->getModelMes('',true);
        foreach ($limitList as $key=>$val){
            $limitList[$key]['new_start_time'] = date("H:i",$val['start_time']);
            $limitList[$key]['new_end_time'] = date("H:i",$val['end_time']);
            //判断是否结束或者开始
            $limitList[$key]['status'] = "10"; //进行中
            if($val['start_time']>time()){ //开始时间大于现在时间
                $limitList[$key]['status'] = "20"; //未开始
            }
            if($val['end_time']<time()){ //开始时间大于现在时间
                $limitList[$key]['status'] = "30"; //已结束
            }
        }
        //查询当前第一个的免费课程数据
        $TimeLimitCourseList = (new TimeLimitCourse())->getModelMes(['time_limit_id'=>$page],true,$page,$limit);
        foreach ($TimeLimitCourseList as $key=>$val){
            $courseInfo = Course::detail($val['course_id']);
            $data[$key]['id'] = $val['course_id'];
            $data[$key]['course_name'] = $courseInfo['course_name'];
            $data[$key]['image'] = $courseInfo['course_thumb'];
            $data[$key]['course_price'] = $courseInfo['spec']['course_price'];
            $data[$key]['course_type'] = $courseInfo['course_type'];
            $data[$key]['sale_group'] = $courseInfo['spec']['sale_group'];
            $data[$key]['course_sales'] = $courseInfo['sales_initial']+$courseInfo['sales_actual'];
        }
        return ['data'=>$data,'limitList'=>$limitList];
    }

    /**
     * 秒杀组件
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface geTeamList
     * @Time: 2022/1/6   17:34
     * @todo [描述] // 待办。提示自己或他人还需要做些什么
     */
    public function getSpikeList($item,$page=1,$type='change')
    {
        $limit = $item['params']['showNum'];
        if($type != 'change'){
            $limit = "10";
        }
        $data = [];
        $SeckillList = (new Seckill())->getModelMes('',true,1,$limit);
        foreach ($SeckillList as $key=>$val){
            $count_number = (new SeckillRecord())->where(['seckill_id'=>$val['seckill_id']])->count();
            $course_info = Course::detail($val['product_id']);
            $data[$key]['line_price'] = $course_info['spec']['course_price'];

            $data[$key]['id'] = $val['seckill_id'];
            $data[$key]['title'] = $val['activity_name'];
            $data[$key]['image'] = $val['activity_image'];
            $data[$key]['price'] = $val['activity_price'];
            $data[$key]['sales'] = $val['activity_num'];
            $data[$key]['count_number'] = $count_number;
        }
        return $data;
    }

    /**
     *  新闻组件
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface getArticleList
     * @Time: 2022/1/17   22:34
     * @todo [描述] // 待办。提示自己或他人还需要做些什么
     */
    public function getArticleList($item,$page=1,$type='change')
    {
        $limit = 3;
        if($type !='change'){
            $limit = 10;
        }
        $data = [];
        $NewArticleList = (new NewArticle())->getModelMes('',"true",$page,$limit);
        foreach ($NewArticleList as $key=>$val){
            $data[$key]['id'] = $val['id'];
            $data[$key]['title'] = $val['title'];
        }
        return $data;
    }

    /**
     * 直播组件
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface getLiveList
     * @Time: 2022/1/17   22:47
     * @todo [描述] // 待办。提示自己或他人还需要做些什么
     */
    public function getLiveList($item,$page=1,$type='change')
    {
        $data = [];
        $pageNUmber = $item['params']['auto']['showNum'];
        if($type != 'change'){
            $pageNUmber = "10";
        }
        //判断当前组件是否是自动获取数据 是的话需要自己从数据库数据
        if($item['params']['source'] == 'auto'){
            $CourseLiveList = (new CourseLive())->getCategoryLiveAllList(['is_rack'=>10],"true",$page,$pageNUmber);
            foreach ($CourseLiveList as $key=>$val){
                $CourseLiveList[$key]['teacher_name'] = "平台直播";
                if($val['label_text']==20){
                    $CourseLiveList[$key]['label_text'] = "直播中";
                }
                if($val['label_text']==10){
                    $CourseLiveList[$key]['label_text'] = "未开始";
                }
                if($val['label_text']==30){
                    $CourseLiveList[$key]['label_text'] = "已结束";
                }
                if($val['label_text']==40){
                    $CourseLiveList[$key]['label_text'] = "回放中";
                }
            }
            return $CourseLiveList;
        }else{
            $data = $item['data'];
            foreach ($data as $key=>$val){
                $data[$key]['teacher_name'] = "平台直播";

                $data[$key]['line_price'] = "0";
                if($val['label_text']==20){
                    $data[$key]['label_text'] = "直播中";
                }
                if($val['label_text']==10){
                    $data[$key]['label_text'] = "未开始";
                }
                if($val['label_text']==30){
                    $data[$key]['label_text'] = "已结束";
                }
                if($val['label_text']==40){
                    $data[$key]['label_text'] = "回放中";
                }
            }
            $start = ($page-1)*$pageNUmber;//偏移量，当前页-1乘以每页显示条数
            $data = array_slice($data,$start,$pageNUmber);
            return $data;
        }


    }

    /**
     * 查看更多或者换一换
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface MoreList
     * @Time: 2022/1/18   13:57
     * @todo [描述] // 待办。提示自己或他人还需要做些什么
     */
    public function moreList($type_name,$type,$page_id,$item_id,$page,$member_id)
    {
        $detail = $page_id > 0 ? parent::detail($page_id) : parent::getHomePage();
        // 页面diy元素
        $items = $detail['page_data']['items'];
        $item = [];
        if($type_name == 'Q&amp;A'){
            $type_name = "Q&A";
        }
        foreach ($items as $key=>$val){
            if($type_name == $val['type'] && $key==$item_id){
                $item = $val;
                continue;
            }
        }
        $data = [];
        switch ($type_name)
        {
            case "knowledge": //知识组件
                $data = $this->getKnowledgeList($item,$page,$type);
                break;
            case "live": //直播组件
                $data = $this->getLiveList($item,$page,$type);
                break;
            case "goods": //商品组件
                $data = $this->getGoodsList($item,$page,$type);
                break;
            case "limited": //限时免费组件
                $data = $this->getLimitedList($item,$page,$type);
                break;
            case "teacher": //导师组件
                $data = $this->getTeacherList($item,$page,$type);
                break;
            case "article": //新闻组件
                $data = $this->getArticleList($item,$page,$type);
                break;
            case "online": //在线报名
                $data = $this->getOnlineList($item,$page,$type);
                break;
            case "community": //社群组件
                $data = $this->getCommunityList($item,$page,$type);
                break;
            case "evaluation": //在线测评
                $data = $this->getEvaluationList($item,$page,$type);
                break;
            case "Q&A": //付费问答
                $data = $this->getAnswerList($item,$page,$type);
                break;
            case "clock": //打卡挑战
                $data = $this->getClockList($item,$page,$type,$member_id);
                break;
            case "team": //拼团组件
                $data = $this->geTeamList($item,$page,$type);
                break;
            case "spike": //在线秒杀
                $data = $this->getSpikeList($item,$page,$type);
                break;
        }
        return $data;
    }


    /**
     * 获取列表
     * @param $type_name
     * @Time: 2022/1/22   23:56
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface moduleList
     * @todo [描述] // 待办。提示自己或他人还需要做些什么
     */
    public function moduleList($type_name,$page,$member_id)
    {
        $item = [
            'params' => [
                'showNum' => '10',
                'source' => 'auto',
                'auto' => [
                    'showNum' => 10,
                ]
            ]
        ];
        switch ($type_name)
        {
            case "live": //直播组件
                $data = $this->getLiveList($item,$page);
                break;
            case "evaluation": //在线测评
                $data = $this->getEvaluationList($item,$page);
                break;
            case "QA": //付费问答
                $data = $this->getAnswerList($item,$page);
                break;
            case "clock": //打卡挑战
                $data = $this->getClockList($item,$page,'',$member_id);
                break;
            case "team": //拼团组件
                $data = $this->geTeamList($item,$page);
                break;
        }
        return $data;
    }
}