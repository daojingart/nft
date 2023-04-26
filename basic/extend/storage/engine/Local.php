<?php
/**
 * +----------------------------------------------------------------------
 * | 河南八六互联信息技术有限公司
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang
 * +----------------------------------------------------------------------
 * | Time:  2021/3/23   3:35 下午
 * +----------------------------------------------------------------------
 * | Email: 18519179004@163.com
 * +----------------------------------------------------------------------
 * | className: 本地存储驱动服务类
 * +----------------------------------------------------------------------
 */

namespace storage\engine;


class Local extends Server
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 上传图片文件
     * @return array|bool
     */
    public function upload()
    {
        // 上传目录
        $uplodDir = WEB_PATH . 'uploads';
        // 验证文件并上传
        $info = $this->file->validate(['size' => 4 * 1024 * 1024, 'ext' => 'jpg,jpeg,png,gif'])
            ->move($uplodDir, $this->fileName);
        if (empty($info)) {
            $this->error = $this->file->getError();
            return false;
        }
        return true;
    }

    /**
     * 返回文件路径
     * @return mixed
     */
    public function getFileName()
    {
        return $this->fileName;
    }

}
