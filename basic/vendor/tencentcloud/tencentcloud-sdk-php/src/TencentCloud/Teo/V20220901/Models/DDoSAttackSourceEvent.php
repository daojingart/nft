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
 * DDoS攻击事件对象
 *
 * @method string getAttackSourceIp() 获取攻击源ip。
 * @method void setAttackSourceIp(string $AttackSourceIp) 设置攻击源ip。
 * @method string getAttackRegion() 获取地区（国家）。
 * @method void setAttackRegion(string $AttackRegion) 设置地区（国家）。
 * @method integer getAttackFlow() 获取累计攻击流量。
 * @method void setAttackFlow(integer $AttackFlow) 设置累计攻击流量。
 * @method integer getAttackPacketNum() 获取累计攻击包量。
 * @method void setAttackPacketNum(integer $AttackPacketNum) 设置累计攻击包量。
 */
class DDoSAttackSourceEvent extends AbstractModel
{
    /**
     * @var string 攻击源ip。
     */
    public $AttackSourceIp;

    /**
     * @var string 地区（国家）。
     */
    public $AttackRegion;

    /**
     * @var integer 累计攻击流量。
     */
    public $AttackFlow;

    /**
     * @var integer 累计攻击包量。
     */
    public $AttackPacketNum;

    /**
     * @param string $AttackSourceIp 攻击源ip。
     * @param string $AttackRegion 地区（国家）。
     * @param integer $AttackFlow 累计攻击流量。
     * @param integer $AttackPacketNum 累计攻击包量。
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
        if (array_key_exists("AttackSourceIp",$param) and $param["AttackSourceIp"] !== null) {
            $this->AttackSourceIp = $param["AttackSourceIp"];
        }

        if (array_key_exists("AttackRegion",$param) and $param["AttackRegion"] !== null) {
            $this->AttackRegion = $param["AttackRegion"];
        }

        if (array_key_exists("AttackFlow",$param) and $param["AttackFlow"] !== null) {
            $this->AttackFlow = $param["AttackFlow"];
        }

        if (array_key_exists("AttackPacketNum",$param) and $param["AttackPacketNum"] !== null) {
            $this->AttackPacketNum = $param["AttackPacketNum"];
        }
    }
}
