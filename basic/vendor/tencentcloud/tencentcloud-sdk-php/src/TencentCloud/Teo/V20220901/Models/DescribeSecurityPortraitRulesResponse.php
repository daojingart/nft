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
 * DescribeSecurityPortraitRules返回参数结构体
 *
 * @method integer getCount() 获取本次返回的规则数。
 * @method void setCount(integer $Count) 设置本次返回的规则数。
 * @method array getPortraitManagedRuleDetails() 获取Bot用户画像规则。
 * @method void setPortraitManagedRuleDetails(array $PortraitManagedRuleDetails) 设置Bot用户画像规则。
 * @method integer getTotal() 获取总规则数。
 * @method void setTotal(integer $Total) 设置总规则数。
 * @method string getRequestId() 获取唯一请求 ID，每次请求都会返回。定位问题时需要提供该次请求的 RequestId。
 * @method void setRequestId(string $RequestId) 设置唯一请求 ID，每次请求都会返回。定位问题时需要提供该次请求的 RequestId。
 */
class DescribeSecurityPortraitRulesResponse extends AbstractModel
{
    /**
     * @var integer 本次返回的规则数。
     */
    public $Count;

    /**
     * @var array Bot用户画像规则。
     */
    public $PortraitManagedRuleDetails;

    /**
     * @var integer 总规则数。
     */
    public $Total;

    /**
     * @var string 唯一请求 ID，每次请求都会返回。定位问题时需要提供该次请求的 RequestId。
     */
    public $RequestId;

    /**
     * @param integer $Count 本次返回的规则数。
     * @param array $PortraitManagedRuleDetails Bot用户画像规则。
     * @param integer $Total 总规则数。
     * @param string $RequestId 唯一请求 ID，每次请求都会返回。定位问题时需要提供该次请求的 RequestId。
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
        if (array_key_exists("Count",$param) and $param["Count"] !== null) {
            $this->Count = $param["Count"];
        }

        if (array_key_exists("PortraitManagedRuleDetails",$param) and $param["PortraitManagedRuleDetails"] !== null) {
            $this->PortraitManagedRuleDetails = [];
            foreach ($param["PortraitManagedRuleDetails"] as $key => $value){
                $obj = new PortraitManagedRuleDetail();
                $obj->deserialize($value);
                array_push($this->PortraitManagedRuleDetails, $obj);
            }
        }

        if (array_key_exists("Total",$param) and $param["Total"] !== null) {
            $this->Total = $param["Total"];
        }

        if (array_key_exists("RequestId",$param) and $param["RequestId"] !== null) {
            $this->RequestId = $param["RequestId"];
        }
    }
}
