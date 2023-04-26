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
 * | className: 编辑器上传文件
 * +----------------------------------------------------------------------
 */

namespace app\admin\controller\upload;

use app\admin\model\upload\UploadFile;
use app\admin\model\Setting as SettingModel;
use app\admin\model\upload\UploadFile as UploadFileModel;
use app\admin\model\upload\UploadGroup as UploadGroupModel;
use storage\Driver as StorageDriver;
use think\Controller;


class Ueditor extends Controller
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
     * 根据不同的操作类型处理不同的操作
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface getHandle
     * @Time: 2022/12/9   15:41
     */
    public function getHandle($action)
    {
        switch ($action)
        {
            case "config": //获取基础配置
                $config = [
                    // 上传图片配置项
                    "imageActionName" => "image",
                    "imageFieldName" => "iFile",
                    "imageMaxSize" => 1024 * 1024 * 10,
                    "imageAllowFiles" => ['.jpg', '.png', '.jpeg'],
                    "imageCompressEnable" => true,
                    "imageCompressBorder" => 5000,
                    "imageInsertAlign" => "none",
                    "imageUrlPrefix" => "",

                    // 涂鸦图片上传配置项
                    "scrawlActionName" => "crawl",
                    "scrawlFieldName" => "file",
                    "scrawlMaxSize" => 1024 * 1024 * 10,
                    "scrawlUrlPrefix" => "",
                    "scrawlInsertAlign" => "none",

                    // 截图工具上传
                    "snapscreenActionName" => "snap",
                    "snapscreenUrlPrefix" => "",
                    "snapscreenInsertAlign" => "none",

                    // 抓取
                    "catcherLocalDomain" => [
                        "127.0.0.1",
                        "localhost",
                    ],
                    "catcherActionName" => "catch",
                    "catcherFieldName" => "source",
                    "catcherUrlPrefix" => "",
                    "catcherMaxSize" => 1024 * 1024 * 10,
                    "catcherAllowFiles" => ['.jpg', '.png', '.jpeg'],

                    // 上传视频配置
                    "videoActionName" => "video",
                    "videoFieldName" => "file",
                    "videoUrlPrefix" => "",
                    "videoMaxSize" => 1024 * 1024 * 100,
                    "videoAllowFiles" => ['.mp4'],

                    // 上传文件配置
                    "fileActionName" => "file",
                    "fileFieldName" => "file",
                    "fileUrlPrefix" => "",
                    "fileMaxSize" => 1024 * 1024 * 100,
                    "fileAllowFiles" => ['.zip', '.pdf', '.doc'],

                    // 列出图片
                    "imageManagerActionName" => "listImage",
                    "imageManagerListSize" => 20,
                    "imageManagerUrlPrefix" => "",
                    "imageManagerInsertAlign" => "none",
                    "imageManagerAllowFiles" => ['.jpg', '.png', '.jpeg'],

                    // 列出指定目录下的文件
                    "fileManagerActionName" => "listFile",
                    "fileManagerUrlPrefix" => "",
                    "fileManagerListSize" => 20,
                    "fileManagerAllowFiles" => ['.zip', '.pdf', '.doc'],

                    // 公式渲染
                    "formulaConfig" => [
                        "imageUrlTemplate"=>"https://latex.codecogs.com/svg.image?{}",
                    ]

                ];
                return json($config);
            case "image": //图片上传
                return $this->image();
            case "listImage": //图片列表
                return $this->fileList();
            case 'video':
                return $this->video();

        }

    }

    /**
     * 文件库列表
     * @param string $type
     * @param int $group_id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function fileList($type = 'image', $group_id = -1)
    {
        // 文件列表
        $start = $this->request->get("start",0);
        $page = ceil($start/30)==0?1:ceil($start/30)+1;

        $file_list = (new UploadFileModel)->where(['file_type' => $type, 'is_delete' => 0,'is_teacher'=>0])->page($page,30)->order("file_id desc")->select();
        $total = (new UploadFileModel)->where(['file_type' => $type, 'is_delete' => 0,'is_teacher'=>0])->count();
        $result = [];
        foreach ($file_list as $key=>$value){
            $result[$key]['url'] = $value['file_path'];
            $result[$key]['mtime'] = $value['file_name'];
            $result[$key]['title'] = $value['file_name'];
        }
        return json(['state' => 'SUCCESS', 'list' => $result,'start'=>intval(@$_GET['start']),'total'=>$total]);
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
            return json(['state' => "error", 'msg' => '图片上传失败' . $StorageDriver->getError()]);
        // 图片上传路径
        $fileName = $StorageDriver->getFileName();
        // 图片信息
        $fileInfo = $StorageDriver->getFileInfo();
        // 添加文件库记录
        $uploadFile = $this->addUploadFile($group_id, $fileName, $fileInfo, 'image');
        // 图片上传成功
        return json(['state' => 'SUCCESS', 'msg' => '图片上传成功', 'url' => $uploadFile['file_path']]);
    }

    /**
     * 视频资源上传
     * @param int $group_id
     * @return array
     * @throws \think\Exception
     */
    public function video($group_id = -1)
    {
        $file = request()->file('file');
        // 移动到框架应用根目录/public/uploads/ 目录下
        $info = $file->move(WEB_PATH.'/mp4','');
        if ($info) {
            $src = $info->getFilename();
            $src = HOST."/mp4/".$src;
            return json(['state'=>'SUCCESS','url'=>$src,'msg'=>'上传成功']);
        } else {
            // 上传失败获取错误信息
            return json(['state'=>'error','url'=>'','msg'=>$file->getError()]);
        }
    }

    /**
     * 文件上传
     * @param int $group_id
     * @return array
     * @throws \think\Exception
     */
    public function files($group_id = -1)
    {
        if($this->config['default']=='local'){
            $file = request()->file('iFile');
            // 移动到框架应用根目录/public/uploads/ 目录下
            $info = $file->move(WEB_PATH.'/files','');
            if ($info) {
                $src = $info->getFilename();
                $src = HOST."/files/".$src;
                return json(['code'=>'1','data'=>$src,'msg'=>'上传成功']);
            } else {
                // 上传失败获取错误信息
                return json(['code'=>'-1','data'=>'','msg'=>$file->getError()]);
            }
        }
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
     * @return UploadFileModel
     */
    private function addUploadFile($group_id, $fileName, $fileInfo, $fileType)
    {
        // 存储引擎
        $storage = $this->config['default'];
        // 存储域名
        $fileUrl = isset($this->config['engine'][$storage]) ? $this->config['engine'][$storage]['domain'] : '';
        // 添加文件库记录
        $model = new UploadFileModel;
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
     * 文件上传
     */
    public function originalUpload()
    {
        $file = request()->file('file');
        // 移动到框架应用根目录/public/uploads/ 目录下
        $info = $file->move(WEB_PATH , '');
        if ($info) {
            $src = $info->getFilename();
            return json(['code'=>'1','data'=>['url'=>$src],'msg'=>'上传成功']);
        } else {
            // 上传失败获取错误信息
            return json(['code'=>'-1','data'=>'','msg'=>$file->getError()]);
        }
    }


}
