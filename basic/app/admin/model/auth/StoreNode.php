<?php
/**
 * +----------------------------------------------------------------------
 * | 河南八六互联信息技术有限公司
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang
 * +----------------------------------------------------------------------
 * | Time:  2020/12/13   3:33 下午
 * +----------------------------------------------------------------------
 * | Email: 18519179004@163.com
 * +----------------------------------------------------------------------
 * | className: 节点控制模型
 * +----------------------------------------------------------------------
 */

namespace app\admin\model\auth;


use app\common\model\StoreNode as StoreNodeModel;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\exception\DbException;

class StoreNode extends StoreNodeModel
{
    /**
     * @Notes: 操作按钮
     * @Interface makeButton
     * @return array
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2020/12/12   6:41 下午
     * @author: Mr.Zhang
     */
    private static  function makeButton($p_id)
    {
        return [
            '添加子节点' => [
                'href' => "javascript:void(0)",
                'lay-event' => 'addChild',
            ],
            '编辑' => [
                'href' => "javascript:void(0)",
                'lay-event' => 'edit',
            ],
            '删除' => [
                'href' => "javascript:void(0)",
                'lay-event' => 'remove',
            ]
        ];

    }
    /**
     * @Notes: 插入节点名称
     * @Interface insertNode
     * @param $data
     * @return bool
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2020/12/9   11:41 上午
     */
    public  function insertNode($data)
    {
        $data['app_id'] = self::$app_id;
        return $this->allowField(true)->save($data);
    }

    /**
     * @Notes: 获取节点列表数据
     * @Interface getNodeList
     * @param array $where
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2020/12/14  1:45 下午
     */
    public function getNodeList($where = [])
    {
        $node_list = $this->where(['disabled'=>['<>',self::$disabled]])
            ->order(['sort' => 'asc'])
            ->where($where)
            ->select();
        foreach ($node_list as $k=>$v){
            $node_list[$k]['operate'] = showNewOperate(self::makeButton($v['p_id']));

        }
        $return['total'] = '';
        $return['data'] = $node_list;
        $return['code'] = '0';
        $return['msg'] = '...';
        return $return;
    }



    /**
     * @Notes: 获取详情信息
     * @Interface getDetails
     * @param $id
     * @return StoreNode|string[]|null
     * @throws DbException
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2020/12/14   3:07 下午
     */
    public static function getDetails($id)
    {
        $info = [
            'id' => '0',
            'node_name' => '顶级节点',
        ];
        if($id != '0') {
            $info = self::get($id);
        }
        return $info;
    }

    /**
     * @Notes:编辑节点信息
     * @Interface renew
     * @param $data
     * @return bool
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2020/12/14   3:08 下午
     */
    public function renew($data)
    {
        // 更新节点管理信息
        if ($this->where(['id'=>$data['id']])->update($data) === false) {
            return false;
        }
        return true;
    }

    /**
     * @Notes: 移除节点
     * @Interface setDel
     * @param $model
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2020/12/14   4:02 下午
     */
    public function setDel($model)
    {
        if($model['p_id'] == '0'){
            $this->where(['p_id'=>$model['id']])->update(['disabled' => self::$disabled]);
        }
        return $this->save(['disabled' => self::$disabled]);
    }

    /**
     * @Notes: 组装树形结构数据
     * @Interface getNodeTrees
     * @param $node_list
     * @return array
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2020/12/14   6:56 下午
     */
    public function getNodeTrees($node_list)
    {

        $new_node_list = [];
        foreach ($node_list as $k=>$v){
            if(!strrpos($v['controller_name'],".")){
                $new_node_list[$k]['index_str'] = substr($v['controller_name'],0);
            }else{
                $new_node_list[$k]['index_str'] = substr($v['controller_name'],0,strrpos($v['controller_name'],"."));
            }
            $new_node_list[$k]['name']  = $v['node_name'];
            $new_node_list[$k]['icon'] = $v['menu_icon'];
            $new_node_list[$k]['disabled'] = $v['disabled'];
            $new_node_list[$k]['index'] = $v['controller_name'].'/'.$v['action_name'];
//            if($v['p_id'] != '0'){
//                if($v['controller_name'] !='index'){
//                    $new_node_list[$k]['index'] = $v['controller_name'].'/'.$v['action_name'];
//                }
//            }else{
//                if($v['controller_name'] =='index'){
//                    $new_node_list[$k]['index'] = $v['controller_name'].'/'.$v['action_name'];
//                }
//            }
            $new_node_list[$k]['p_id'] = $v['p_id'];
            $new_node_list[$k]['id'] = $v['id'];
        }
        $new_node_list = getTree($new_node_list);
        $new_node_list_array = [];
        foreach ($new_node_list as $k=>$v){
            $new_node_list_array[$v['index_str']] = $v;
        }
        return $new_node_list_array;
    }



    /**
     * @Notes: 获取节点列表
     * @Interface getNodeTreeList
     * @param array $where
     * @return bool|\PDOStatement|string|\think\Collection
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2020/12/15   3:40 下午
     */
    public function getNodeTreeList($where = [])
    {
         return $this->order(['sort' => 'asc'])
            ->where($where)
             ->where(['disabled'=>1])
            ->select();
    }

}