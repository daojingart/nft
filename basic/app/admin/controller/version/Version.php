<?php
/**
 * +----------------------------------------------------------------------
 * | 河南八六互联信息技术有限公司
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang
 * +----------------------------------------------------------------------
 * | Time:  2021/5/28   10:46
 * +----------------------------------------------------------------------
 * | Email: 18519179004@163.com
 * +----------------------------------------------------------------------
 * | className: 版本控制器
 * +----------------------------------------------------------------------
 */

namespace app\admin\controller\version;


use app\admin\controller\Controller;

class Version extends Controller
{
    /**
     * @Notes:版本控制器
     * @Interface index
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/5/28   10:46
     */
    public function index()
    {
        $resultArray = file_get_contents("vision.json");
        $resultArray = json_decode($resultArray,true);
        $this->view->engine->layout(false);
        if(empty($resultArray)){
            $this->assign([
                'resultArray' => [],
                'is_version' => '暂无',
            ]);
        }else{
            $this->assign([
                'resultArray' => $resultArray['data'],
                'is_version' => getVersion(),
            ]);
        }
        return $this->fetch();
    }

    /**
     * 帮助手册
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface help
     * @Time: 2022/9/17   17:27
     */
    public function helpmanual()
    {
        $this->view->engine->layout(false);
        return $this->fetch('help');
    }
}