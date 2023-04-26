<?php
/*
 * Copyright (c) 2017-2018 THL A29 Limited, a Tencent company. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
namespace TencentCloud\Teo\V20220901\Models;
use TencentCloud\Common\AbstractModel;

/**
 * CreateLogSet请求参数结构体
 *
 * @method string getLogSetName() 获取日志集名称。
 * @method void setLogSetName(string $LogSetName) 设置日志集名称。
 * @method string getLogSetRegion() 获取日志集归属的地域。
 * @method void setLogSetRegion(string $LogSetRegion) 设置日志集归属的地域。
 */
class CreateLogSetRequest extends AbstractModel
{
    /**
     * @var string 日志集名称。
     */
    public $LogSetName;

    /**
     * @var string 日志集归属的地域。
     */
    public $LogSetRegion;

    /**
     * @param string $LogSetName 日志集名称。
     * @param string $LogSetRegion 日志集归属的地域。
     */
    function __construct()
    {

    }

    /**
     * For internal only. DO NOT USE IT.
     */
    public function deserialize($param)
    {
        if ($param === null) {
            return;
        }
        if (array_key_exists("LogSetName",$param) and $param["LogSetName"] !== null) {
            $this->LogSetName = $param["LogSetName"];
        }

        if (array_key_exists("LogSetRegion",$param) and $param["LogSetRegion"] !== null) {
            $this->LogSetRegion = $param["LogSetRegion"];
        }
    }
}
