<?php

namespace app\api\controller;

use app\common\controller\Controller;
use app\common\model\News;


/**
 * 帮助中心
 */
class Help extends Controller
{
    protected $noNeedLogin = [
        'getHelpList'
    ];
    /**
     * 帮助中心列表
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/help/getHelpList)
     * @ApiReturnParams   (name="title", type="string", description="帮助的标题")
     * @ApiReturnParams   (name="content", type="string", description="帮助的内容")
     */
    public function getHelpList()
    {
        $this->success('',(new News())->where(['type'=>1,'status'=>1])->field("title,content")->order("sort desc")->select());
    }


}