<?php
/**
 * +----------------------------------------------------------------------
 * | 河南八六互联信息技术有限公司
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang
 * +----------------------------------------------------------------------
 * | Time:  2020/12/12   5:29 下午
 * +----------------------------------------------------------------------
 * | Email: 18519179004@163.com
 * +----------------------------------------------------------------------
 * | className:
 * +----------------------------------------------------------------------
 */

use Bl\Nft\AdminController;
use Bl\Nft\AdminCommon;


/**
 * @Notes: 列表的操作按钮
 * @Interface showNewOperate
 * @param array $makeButton
 * @return string
 * @author: Mr.Zhang
 * @copyright: 河南八六互联信息技术有限公司
 * @Time: 2020/12/12   5:01 下午
 * @array lay-type 类型 1点击  2下拉
 */
function showNewOperate(array $makeButton)
{
    if(empty($makeButton)){
        return '';
    }
    $option = '';
    foreach($makeButton as $key=>$vo){
        if(!isset($vo['target'])){
            $vo['target'] = "";
        }
        if(isset($vo['lay-type']) && $vo['lay-type'] == '2'){
            $option .= '<span class="bl-btn-operate bl_more blMore" data-value="'.$vo['lay-event'].'">'.$key.'<i class="layui-icon layui-icon-down layui-font-12"></i></span><div class="bl-line">|</div>';
        }else{
            $option .= ' <a href="' . $vo['href'] . '"  lay-event="' .$vo['lay-event']. '" target="'.$vo['target'].'">
                    <div class="bl-btn-operate">'. $key . '</div>
                    </a> 
                    <div class="bl-line">|</div> ';
        }
    }
    return "<div class='operate-box'>".$option."</div>";
}

/**
 * @Notes: 组装树形结构菜单
 * @Interface getTree
 * @param $pInfo
 * @return array
 * @author: Mr.Zhang
 * @copyright: 河南八六互联信息技术有限公司
 * @Time: 2020/12/14   6:28 下午
 */
function getTree($pInfo)
{
    return AdminCommon::instance()->getTree($pInfo);
}


/**
 * @Notes: 题库树柱状
 * @Interface getTree
 * @param $pInfo
 * @return array
 * @author: Mr.Zhang
 * @copyright: 河南八六互联信息技术有限公司
 * @Time: 2020/12/14   6:28 下午
 */
function getQuestionTree($pInfo)
{
    $res = [];
    $tree = [];
    //整理数组
    foreach($pInfo as $key=>$vo){
        $res[$vo['id']] = $vo;
    }
    unset($pInfo);
    //查找子孙
    foreach($res as $key=>$vo){
        if(0 != $vo['p_id']){
            $res[$vo['p_id']]['children'][] = &$res[$key];
        }
    }
    //过滤杂质
    foreach($res as $key=>$vo ){
        if(isset($vo['p_id'])){
            if(0 == $vo['p_id']){
                $tree[] = $vo;
            }
        }
    }
    unset( $res );
    return $tree;
}
function getTrees($array, $pid =0, $level = 0)
{

    //声明静态数组,避免递归调用时,多次声明导致数组覆盖
    static $list = [];
    foreach ($array as $key => $value) {
        //第一次遍历,找到父节点为根节点的节点 也就是pid=0的节点
        if ($value['parent_id'] == $pid) {
            //父节点为根节点的节点,级别为0，也就是第一级
            $value['level'] = $level;
            //把数组放到list中
            $list[] = $value;
            //把这个节点从数组中移除,减少后续递归消耗
            unset($array[$key]);
            //开始递归,查找父ID为该节点ID的节点,级别则为原级别+1
            getTrees($array, $value['category_id'], $level + 1);

        }
    }
    return $list;
}
