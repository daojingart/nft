<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang [1040657944@qq.com]
 * +----------------------------------------------------------------------
 * | Time:  2023/2/21   15:34
 * +----------------------------------------------------------------------
 * | className:
 * +----------------------------------------------------------------------
 */

namespace storage\engine;


use App\Services\OSS;
use JohnLui\AliyunOSS;

class Aliyun extends Server
{
    private $config;

    private $networkType = '经典网络';

    protected $CityURLArray = [
        'oss-cn-hangzhou.aliyuncs.com' => '杭州',
        'oss-cn-shanghai.aliyuncs.com' => '上海',
        'oss-cn-qingdao.aliyuncs.com' => '青岛',
        'oss-cn-beijing.aliyuncs.com' => '北京',
        'oss-cn-zhangjiakou.aliyuncs.com' => '张家口',
        'oss-cn-shenzhen.aliyuncs.com' => '深圳',
        'oss-cn-hongkong.aliyuncs.com' => '香港',
        'oss-us-west-1.aliyuncs.com' => '硅谷',
        'oss-us-east-1.aliyuncs.com' => '弗吉尼亚',
        'oss-ap-southeast-1.aliyuncs.com' => '新加坡',
        'oss-ap-southeast-2.aliyuncs.com' => '悉尼',
        'oss-ap-northeast-1.aliyuncs.com' => '日本',
        'oss-eu-central-1.aliyuncs.com' => '法兰克福',
        'oss-me-east-1.aliyuncs.com' => '迪拜',
    ];

    private $ossClient;

    /**
     * 构造方法
     * Qiniu constructor.
     * @param $config
     * @throws \think\Exception
     */
    public function __construct($config)
    {
        parent::__construct();
        $this->config = $config;
        $this->ossClient = AliyunOSS::boot(
            $this->CityURLArray[$config['endpoint']],
            $this->networkType,
            false,
            $config['access_key'],
            $config['secret_key']
        );
    }

    /**
     * 执行上传
     * @return bool|mixed
     * @throws \Exception
     */
    public function upload()
    {
        // 要上传图片的本地路径
        $realPath = $this->file->getRealPath();
        $this->ossClient->setBucket($this->config['bucket']);
        return $this->ossClient->uploadFile($this->fileName, $realPath, []);
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