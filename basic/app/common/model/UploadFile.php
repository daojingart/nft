<?php
/**
 * +----------------------------------------------------------------------
 * | 河南八六互联信息技术有限公司
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang
 * +----------------------------------------------------------------------
 * | Time:  2021/03/27   5:18 下午
 * +----------------------------------------------------------------------
 * | Email: 18519179004@163.com
 * +----------------------------------------------------------------------
 * | className: 文件库模型
 * +----------------------------------------------------------------------
 */

namespace app\common\model;

use think\Request;
use traits\model\SoftDelete;

class UploadFile extends BaseModel
{
    use SoftDelete;

    protected $name = 'upload_file';
    protected $updateTime = false;
    protected $deleteTime = false;
    protected $append = ['file_path'];

    /**
     * 获取图片完整路径
     * @param $value
     * @param $data
     * @return string
     */
    public function getFilePathAttr($value, $data)
    {
        if ($data['storage'] === 'local') {
            return self::$base_url . 'uploads/' . $data['file_name'];
        }
        return $data['file_url'] . '/' . $data['file_name'];
    }

    /**
     * 根据文件名查询文件id
     * @param $fileName
     * @return mixed
     */
    public static function getFildIdByName($fileName)
    {
        return (new static)->where(['file_name' => $fileName])->value('file_id');
    }

    /**
     * 查询文件id
     * @param $fileId
     * @return mixed
     */
    public static function getFileName($fileId)
    {
        return (new static)->where(['file_id' => $fileId])->value('file_name');
    }

    /**
     * @Notes: 获取文件路径URL
     * @Interface getFileLinkUrl
     * @param $fileId
     * @return float|mixed|string
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/4/9   6:09 下午
     */
    public static function getFileLinkUrl($fileId)
    {
        $file_info =  (new static)->where(['file_id' => $fileId])->find();
        if ($file_info['storage'] === 'local') {
            return self::$base_url . 'uploads/' . $file_info['file_name'];
        }
        return $file_info['file_url'] . '/' . $file_info['file_name'];
    }
    /**
     * 获取列表记录
     * @param $group_id
     * @param string $file_type
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public function getList($group_id, $file_type = 'image',$is_teacher=0)
    {
        $model = $this->where(['file_type' => $file_type, 'is_delete' => 0,'is_teacher'=>$is_teacher]);
        if ($group_id !== -1) {
            $model->where(compact('group_id'));
        }
        return $model->order(['file_id' => 'desc'])
            ->paginate(32, false, [
            'query' => Request::instance()->request()
        ]);
    }

}
