<?php

namespace app\admin\service;

use app\admin\service\Auth;

/**
 * 商家后台菜单业务
 * Class Menus
 * @package app\store\service
 */
class Menus
{
    /**
     * 后台菜单配置
     * @param $routeUri
     * @param $group
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getMenus($routeUri,$group,$menus)
    {
        // 菜单列表数据
        //判断下当前登录角色
//        $nodeStr = rtrim($this->store['user']['user_rule'], ',');
//        // 超级管理员没有节点数组 * 号表示
//        $where = '*' == $nodeStr ? []: 'id in(' . $nodeStr . ')'."";
        $this->first($menus, $routeUri, $group);
        return $menus;
    }

    /**
     * 一级菜单
     * @param $menus
     * @param $routeUri
     * @param $group
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function first(&$menus, $routeUri, $group)
    {
//        echo $group;die;
        foreach ($menus as $key => &$first) :
            // 一级菜单索引url
            $indexData = $this->getMenusIndexUrls($first, 1);
            // 权限验证
            $first['index'] = $this->getAuthUrl($indexData);
            if ($first['index'] === false) {
                unset($menus[$key]);
                continue;
            }
            // 菜单聚焦
            $first['active'] = $key === $group;
            // 遍历：二级菜单
            if (isset($first['submenu'])) {
                $this->second($first['submenu'], $routeUri);
            }
        endforeach;
    }

    /**
     * 二级菜单
     * @param array $menus
     * @param $routeUri
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function second(&$menus, $routeUri)
    {

        foreach ($menus as $key => &$second) :
            // 二级菜单索引url
            $indexData = $this->getMenusIndexUrls($second, 2);
            // 权限验证
            $second['index'] = $this->getAuthUrl($indexData);
            if ($second['index'] === false) {
                unset($menus[$key]);
                continue;
            }
            // 二级菜单所有uri
            $secondUris = [];
            // 遍历：三级菜单
            if (isset($second['submenu'])) {
                $this->third($second['submenu'], $routeUri, $secondUris);
            } else {
                if (isset($second['uris']))
                    $secondUris = array_merge($secondUris, $second['uris']);
                else
                    $secondUris[] = $second['index'];
            }
//            pre($secondUris);
            // 二级菜单：active
            !isset($second['active']) && $second['active'] = in_array($routeUri, $secondUris);
        endforeach;
        // 删除空数组
        $menus = array_filter($menus);
    }

    /**
     * 三级菜单
     * @param array $menus
     * @param $routeUri
     * @param $secondUris
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function third(&$menus, $routeUri, &$secondUris)
    {
        foreach ($menus as $key => &$third):
            // 三级菜单索引url
            $indexData = $this->getMenusIndexUrls($third, 3);
            // 权限验证
            $third['index'] = $this->getAuthUrl($indexData);
            if ($third['index'] === false) {
                unset($menus[$key]);
                continue;
            }
            // 三级菜单所有uri
            $thirdUris = [];
            if (isset($third['uris'])) {
                $secondUris = array_merge($secondUris, $third['uris']);
                $thirdUris = array_merge($thirdUris, $third['uris']);
            } else {
                $secondUris[] = $third['index'];
                $thirdUris[] = $third['index'];
            }
            $third['active'] = in_array($routeUri, $thirdUris);
        endforeach;
    }

    /**
     * 获取指定菜单下的所有索引url
     * @param array $menus
     * @param int $level
     * @return array|null
     */
    private function getMenusIndexUrls(&$menus, $level = 1)
    {
        // 判断是否存在url
        if (!isset($menus['index']) && !isset($menus['submenu'])) {
            return null;
        }
        $data = [];
        if (isset($menus['index']) && !empty($menus['index'])) {
            $data[] = $menus['index'];
        }
        if (isset($menus['submenu']) && !empty($menus['submenu'])) {
            foreach ($menus['submenu'] as $submenu) {
                $submenuIndex = $this->getMenusIndexUrls($submenu, ++$level);
                !is_null($submenuIndex) && $data = array_merge($data, $submenuIndex);
            }
        }
        return array_unique($data);
    }

    /**
     * 取出通过权限验证urk作为index
     * @param $urls
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function getAuthUrl($urls)
    {
        // 取出通过权限验证urk作为index
        foreach ($urls as $url) {
            return $url;
        }
        return false;
    }

}