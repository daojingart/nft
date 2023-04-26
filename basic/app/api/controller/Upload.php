<?php
/**
 * +----------------------------------------------------------------------
 * | 河南八六互联信息技术有限公司
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang
 * +----------------------------------------------------------------------
 * | Time:  2020/12/23   11:56 上午
 * +----------------------------------------------------------------------
 * | Email: 18519179004@163.com
 * +----------------------------------------------------------------------
 * | className: 文件上传
 * +----------------------------------------------------------------------
 */

namespace app\api\controller;

use app\api\model\upload\UploadFile;
use app\common\controller\Controller;
use app\common\extend\llp\Acctmgr;
use app\common\model\Setting;
use app\common\model\WxSetting;
use storage\Driver as StorageDriver;
use tx\Vod;

/**
 * 公共接口
 */

class Upload extends Controller
{
    private $config;
    private $user;

    /**
     * 构造方法
     * @throws \app\common\exception\BaseException
     * @throws \think\exception\DbException
     */
    public function _initialize()
    {
        parent::_initialize();
        // 存储配置信息
        $this->config = Setting::getItem('storage');
        // 验证用户
        $this->user = $this->auth->getUser();
    }

    /**
     * 图片上传
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiParams   (name="iFile", type="file", required=true, description="二进制文件流")
     * @ApiRoute  (/api/Upload/image)
     */
    public function image()
    {
        // 实例化存储驱动
        $StorageDriver = new StorageDriver($this->config);
        // 设置上传文件的信息
        // 上传图片
        if (!$StorageDriver->upload()) {
            $this->error('图片上传失败' . $StorageDriver->getError());
        }
        // 图片上传路径
        $fileName = $StorageDriver->getFileName();
        // 图片信息
        $fileInfo = $StorageDriver->getFileInfo();
        // 添加文件库记录
        $uploadFile = $this->addUploadFile($fileName, $fileInfo, 'image');
        // 图片上传成功
        $this->success('图片上传成功',$uploadFile->visible(['file_id']));
    }


    /**
     * 添加文件库上传记录
     * @param $fileName
     * @param $fileInfo
     * @param $fileType
     * @return UploadFile
     */
    private function addUploadFile($fileName, $fileInfo, $fileType)
    {
        // 存储引擎
        $storage = $this->config['default'];
        // 存储域名
        $fileUrl = isset($this->config['engine'][$storage]['domain'])
            ? $this->config['engine'][$storage]['domain'] : '';
        // 添加文件库记录
        $model = new UploadFile;
        $model->add([
            'storage' => $storage,
            'file_url' => $fileUrl,
            'file_name' => $fileName,
            'file_size' => $fileInfo['size'],
            'file_type' => $fileType,
            'extension' => pathinfo($fileInfo['name'], PATHINFO_EXTENSION),
            'is_member' => $this->user['member_id']
        ]);
        return $model;
    }
}