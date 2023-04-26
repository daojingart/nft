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
 * | className: 文件上传控制器
 * +----------------------------------------------------------------------
 */

namespace app\admin\controller\upload;

use app\admin\controller\Controller;
use app\admin\model\upload\UploadFile;
use app\admin\model\Setting as SettingModel;
use storage\Driver as StorageDriver;


class Upload extends Controller
{
    private $config;

    /**
     * 构造方法
     */
    public function _initialize()
    {
        parent::_initialize();
        // 存储配置信息
        $this->config = SettingModel::getItem('storage');
    }

    /**
     * 图片上传接口
     * @param int $group_id
     * @return array
     * @throws \think\Exception
     */
    public function image($group_id = -1)
    {
        // 实例化存储驱动
        $StorageDriver = new StorageDriver($this->config);
        // 上传图片
        if (!$StorageDriver->upload())
            return json(['code' => 0, 'msg' => '图片上传失败' . $StorageDriver->getError()]);
//        return $this->renderError('图片上传失败'.$StorageDriver->getError(),'');
        // 图片上传路径
        $fileName = $StorageDriver->getFileName();
        // 图片信息
        $fileInfo = $StorageDriver->getFileInfo();
        // 添加文件库记录
        $uploadFile = $this->addUploadFile($group_id, $fileName, $fileInfo, 'image');
        // 图片上传成功
        return json(['code' => 1, 'msg' => '图片上传成功', 'data' => $uploadFile]);
//        return $this->renderSuccess('图片上传成功'.$StorageDriver->getError(),'',$uploadFile);
    }

    /**
     * 文件上传
     * @param int $group_id
     * @return array
     * @throws \think\Exception
     */
    public function files($group_id = -1)
    {
        // 实例化存储驱动
        $StorageDriver = new StorageDriver($this->config);
        // 上传图片
        if (!$StorageDriver->upload())
            return json(['code' => 0, 'msg' => '图片上传失败' . $StorageDriver->getError()]);

        $fileName = $StorageDriver->getFileName();

        $storage = $this->config['default'];
        // 存储域名
        $fileUrl = isset($this->config['engine'][$storage]) ? $this->config['engine'][$storage]['domain'] : '';
        $fileUrl_link =  $fileUrl.'/'.$fileName;
        return json(['code' => 1, 'msg' => '图片上传成功', 'data' => $fileUrl_link]);
    }


    /**
     * 添加文件库上传记录
     * @param $group_id
     * @param $fileName
     * @param $fileInfo
     * @param $fileType
     * @return UploadFile
     */
    private function addUploadFile($group_id, $fileName, $fileInfo, $fileType)
    {
        // 存储引擎
        $storage = $this->config['default'];
        // 存储域名
        $fileUrl = isset($this->config['engine'][$storage]) ? $this->config['engine'][$storage]['domain'] : '';
        // 添加文件库记录
        $model = new UploadFile;
        $model->add([
            'group_id' => $group_id > 0 ? (int)$group_id : 0,
            'storage' => $storage,
            'file_url' => $fileUrl,
            'file_name' => $fileName,
            'file_size' => $fileInfo['size'],
            'file_type' => $fileType,
            'extension' => pathinfo($fileInfo['name'], PATHINFO_EXTENSION),
        ]);
        return $model;
    }


    /**
     * 本地文件上传
     */
    public function originalUpload()
    {
        $file = request()->file('file');
        // 移动到框架应用根目录/public/uploads/ 目录下
        $info = $file->move(WEB_PATH , '');
        if ($info) {
            $src = $info->getFilename();
            return json(['code'=>'1','data'=>['url'=>HOST.'/'.$src],'msg'=>'上传成功']);
        } else {
            // 上传失败获取错误信息
            return json(['code'=>'-1','data'=>'','msg'=>$file->getError()]);
        }
    }


    /**
     * 杉德支付证书文件上传
     */
    public function localCertificateUpload()
    {
        $file = request()->file('file');
        //获取文件的信息
        $file_info = $file->getInfo();
        if(!in_array($file_info['type'],['application/x-x509-ca-cert','application/x-pkcs12'])){
            return json(['code'=>'-1','data'=>'','msg'=>'文件格式不正确']);
        }
        $base_dir = ROOT_PATH."app".DS.'common'.DS.'cert';
        // 移动到框架应用根目录/public/uploads/ 目录下
        $info = $file->move($base_dir,'');
        if ($info) {
            $src = $info->getFilename();
            return json(['code'=>'1','data'=>['url'=>$base_dir.DS.$file_info['name']],'msg'=>'上传成功']);
        } else {
            // 上传失败获取错误信息
            return json(['code'=>'-1','data'=>'','msg'=>$file->getError()]);
        }
    }


}
