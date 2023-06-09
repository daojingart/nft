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
 * DDoS端口过滤
 *
 * @method array getDDoSAclRules() 获取端口过滤规则数组。
 * @method void setDDoSAclRules(array $DDoSAclRules) 设置端口过滤规则数组。
 * @method string getSwitch() 获取清空规则标识，取值有：
<li>off ：清空端口过滤规则列表，DDoSAclRules无需填写；</li>
<li>on ：配置端口过滤规则，需填写DDoSAclRules参数。</li>
 * @method void setSwitch(string $Switch) 设置清空规则标识，取值有：
<li>off ：清空端口过滤规则列表，DDoSAclRules无需填写；</li>
<li>on ：配置端口过滤规则，需填写DDoSAclRules参数。</li>
 */
class DDoSAcl extends AbstractModel
{
    /**
     * @var array 端口过滤规则数组。
     */
    public $DDoSAclRules;

    /**
     * @var string 清空规则标识，取值有：
<li>off ：清空端口过滤规则列表，DDoSAclRules无需填写；</li>
<li>on ：配置端口过滤规则，需填写DDoSAclRules参数。</li>
     */
    public $Switch;

    /**
     * @param array $DDoSAclRules 端口过滤规则数组。
     * @param string $Switch 清空规则标识，取值有：
<li>off ：清空端口过滤规则列表，DDoSAclRules无需填写；</li>
<li>on ：配置端口过滤规则，需填写DDoSAclRules参数。</li>
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
        if (array_key_exists("DDoSAclRules",$param) and $param["DDoSAclRules"] !== null) {
            $this->DDoSAclRules = [];
            foreach ($param["DDoSAclRules"] as $key => $value){
                $obj = new DDoSAclRule();
                $obj->deserialize($value);
                array_push($this->DDoSAclRules, $obj);
            }
        }

        if (array_key_exists("Switch",$param) and $param["Switch"] !== null) {
            $this->Switch = $param["Switch"];
        }
    }
}
