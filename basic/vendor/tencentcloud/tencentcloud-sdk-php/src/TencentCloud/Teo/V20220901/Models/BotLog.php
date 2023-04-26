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
 * Bot攻击日志
 *
 * @method integer getAttackTime() 获取攻击时间，采用unix秒级时间戳。
 * @method void setAttackTime(integer $AttackTime) 设置攻击时间，采用unix秒级时间戳。
 * @method string getAttackIp() 获取攻击源（客户端）ip。
 * @method void setAttackIp(string $AttackIp) 设置攻击源（客户端）ip。
 * @method string getDomain() 获取受攻击域名。
 * @method void setDomain(string $Domain) 设置受攻击域名。
 * @method string getRequestUri() 获取URI。
 * @method void setRequestUri(string $RequestUri) 设置URI。
 * @method string getRequestMethod() 获取请求方法。
 * @method void setRequestMethod(string $RequestMethod) 设置请求方法。
 * @method string getAttackContent() 获取攻击内容。
 * @method void setAttackContent(string $AttackContent) 设置攻击内容。
 * @method string getSipCountryCode() 获取IP所在国家iso-3166中alpha-2编码，编码信息请参考[ISO-3166](https://git.woa.com/edgeone/iso-3166/blob/master/all/all.json)。
 * @method void setSipCountryCode(string $SipCountryCode) 设置IP所在国家iso-3166中alpha-2编码，编码信息请参考[ISO-3166](https://git.woa.com/edgeone/iso-3166/blob/master/all/all.json)。
 * @method string getUa() 获取user agent。
 * @method void setUa(string $Ua) 设置user agent。
 * @method string getEventId() 获取攻击事件ID。
 * @method void setEventId(string $EventId) 设置攻击事件ID。
 * @method integer getRuleId() 获取规则ID。
注意：此字段可能返回 null，表示取不到有效值。
 * @method void setRuleId(integer $RuleId) 设置规则ID。
注意：此字段可能返回 null，表示取不到有效值。
 * @method string getAttackType() 获取攻击类型。
注意：此字段可能返回 null，表示取不到有效值。
 * @method void setAttackType(string $AttackType) 设置攻击类型。
注意：此字段可能返回 null，表示取不到有效值。
 * @method string getDisposalMethod() 获取处置方式。
注意：此字段可能返回 null，表示取不到有效值。
 * @method void setDisposalMethod(string $DisposalMethod) 设置处置方式。
注意：此字段可能返回 null，表示取不到有效值。
 * @method string getHttpLog() 获取HTTP日志。
注意：此字段可能返回 null，表示取不到有效值。
 * @method void setHttpLog(string $HttpLog) 设置HTTP日志。
注意：此字段可能返回 null，表示取不到有效值。
 * @method string getRiskLevel() 获取攻击等级。
注意：此字段可能返回 null，表示取不到有效值。
 * @method void setRiskLevel(string $RiskLevel) 设置攻击等级。
注意：此字段可能返回 null，表示取不到有效值。
 * @method string getDetectionMethod() 获取检出方法。
注意：此字段可能返回 null，表示取不到有效值。
 * @method void setDetectionMethod(string $DetectionMethod) 设置检出方法。
注意：此字段可能返回 null，表示取不到有效值。
 * @method string getConfidence() 获取置信度。
注意：此字段可能返回 null，表示取不到有效值。
 * @method void setConfidence(string $Confidence) 设置置信度。
注意：此字段可能返回 null，表示取不到有效值。
 * @method string getMaliciousness() 获取恶意度。
注意：此字段可能返回 null，表示取不到有效值。
 * @method void setMaliciousness(string $Maliciousness) 设置恶意度。
注意：此字段可能返回 null，表示取不到有效值。
 * @method array getRuleDetailList() 获取规则相关信息列表。
注意：此字段可能返回 null，表示取不到有效值。
 * @method void setRuleDetailList(array $RuleDetailList) 设置规则相关信息列表。
注意：此字段可能返回 null，表示取不到有效值。
 * @method string getLabel() 获取Bot标签。
注意：此字段可能返回 null，表示取不到有效值。
 * @method void setLabel(string $Label) 设置Bot标签。
注意：此字段可能返回 null，表示取不到有效值。
 * @method string getArea() 获取日志所属的区域。
注意：此字段可能返回 null，表示取不到有效值。
 * @method void setArea(string $Area) 设置日志所属的区域。
注意：此字段可能返回 null，表示取不到有效值。
 */
class BotLog extends AbstractModel
{
    /**
     * @var integer 攻击时间，采用unix秒级时间戳。
     */
    public $AttackTime;

    /**
     * @var string 攻击源（客户端）ip。
     */
    public $AttackIp;

    /**
     * @var string 受攻击域名。
     */
    public $Domain;

    /**
     * @var string URI。
     */
    public $RequestUri;

    /**
     * @var string 请求方法。
     */
    public $RequestMethod;

    /**
     * @var string 攻击内容。
     */
    public $AttackContent;

    /**
     * @var string IP所在国家iso-3166中alpha-2编码，编码信息请参考[ISO-3166](https://git.woa.com/edgeone/iso-3166/blob/master/all/all.json)。
     */
    public $SipCountryCode;

    /**
     * @var string user agent。
     */
    public $Ua;

    /**
     * @var string 攻击事件ID。
     */
    public $EventId;

    /**
     * @var integer 规则ID。
注意：此字段可能返回 null，表示取不到有效值。
     */
    public $RuleId;

    /**
     * @var string 攻击类型。
注意：此字段可能返回 null，表示取不到有效值。
     */
    public $AttackType;

    /**
     * @var string 处置方式。
注意：此字段可能返回 null，表示取不到有效值。
     */
    public $DisposalMethod;

    /**
     * @var string HTTP日志。
注意：此字段可能返回 null，表示取不到有效值。
     */
    public $HttpLog;

    /**
     * @var string 攻击等级。
注意：此字段可能返回 null，表示取不到有效值。
     */
    public $RiskLevel;

    /**
     * @var string 检出方法。
注意：此字段可能返回 null，表示取不到有效值。
     */
    public $DetectionMethod;

    /**
     * @var string 置信度。
注意：此字段可能返回 null，表示取不到有效值。
     */
    public $Confidence;

    /**
     * @var string 恶意度。
注意：此字段可能返回 null，表示取不到有效值。
     */
    public $Maliciousness;

    /**
     * @var array 规则相关信息列表。
注意：此字段可能返回 null，表示取不到有效值。
     */
    public $RuleDetailList;

    /**
     * @var string Bot标签。
注意：此字段可能返回 null，表示取不到有效值。
     */
    public $Label;

    /**
     * @var string 日志所属的区域。
注意：此字段可能返回 null，表示取不到有效值。
     */
    public $Area;

    /**
     * @param integer $AttackTime 攻击时间，采用unix秒级时间戳。
     * @param string $AttackIp 攻击源（客户端）ip。
     * @param string $Domain 受攻击域名。
     * @param string $RequestUri URI。
     * @param string $RequestMethod 请求方法。
     * @param string $AttackContent 攻击内容。
     * @param string $SipCountryCode IP所在国家iso-3166中alpha-2编码，编码信息请参考[ISO-3166](https://git.woa.com/edgeone/iso-3166/blob/master/all/all.json)。
     * @param string $Ua user agent。
     * @param string $EventId 攻击事件ID。
     * @param integer $RuleId 规则ID。
注意：此字段可能返回 null，表示取不到有效值。
     * @param string $AttackType 攻击类型。
注意：此字段可能返回 null，表示取不到有效值。
     * @param string $DisposalMethod 处置方式。
注意：此字段可能返回 null，表示取不到有效值。
     * @param string $HttpLog HTTP日志。
注意：此字段可能返回 null，表示取不到有效值。
     * @param string $RiskLevel 攻击等级。
注意：此字段可能返回 null，表示取不到有效值。
     * @param string $DetectionMethod 检出方法。
注意：此字段可能返回 null，表示取不到有效值。
     * @param string $Confidence 置信度。
注意：此字段可能返回 null，表示取不到有效值。
     * @param string $Maliciousness 恶意度。
注意：此字段可能返回 null，表示取不到有效值。
     * @param array $RuleDetailList 规则相关信息列表。
注意：此字段可能返回 null，表示取不到有效值。
     * @param string $Label Bot标签。
注意：此字段可能返回 null，表示取不到有效值。
     * @param string $Area 日志所属的区域。
注意：此字段可能返回 null，表示取不到有效值。
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
        if (array_key_exists("AttackTime",$param) and $param["AttackTime"] !== null) {
            $this->AttackTime = $param["AttackTime"];
        }

        if (array_key_exists("AttackIp",$param) and $param["AttackIp"] !== null) {
            $this->AttackIp = $param["AttackIp"];
        }

        if (array_key_exists("Domain",$param) and $param["Domain"] !== null) {
            $this->Domain = $param["Domain"];
        }

        if (array_key_exists("RequestUri",$param) and $param["RequestUri"] !== null) {
            $this->RequestUri = $param["RequestUri"];
        }

        if (array_key_exists("RequestMethod",$param) and $param["RequestMethod"] !== null) {
            $this->RequestMethod = $param["RequestMethod"];
        }

        if (array_key_exists("AttackContent",$param) and $param["AttackContent"] !== null) {
            $this->AttackContent = $param["AttackContent"];
        }

        if (array_key_exists("SipCountryCode",$param) and $param["SipCountryCode"] !== null) {
            $this->SipCountryCode = $param["SipCountryCode"];
        }

        if (array_key_exists("Ua",$param) and $param["Ua"] !== null) {
            $this->Ua = $param["Ua"];
        }

        if (array_key_exists("EventId",$param) and $param["EventId"] !== null) {
            $this->EventId = $param["EventId"];
        }

        if (array_key_exists("RuleId",$param) and $param["RuleId"] !== null) {
            $this->RuleId = $param["RuleId"];
        }

        if (array_key_exists("AttackType",$param) and $param["AttackType"] !== null) {
            $this->AttackType = $param["AttackType"];
        }

        if (array_key_exists("DisposalMethod",$param) and $param["DisposalMethod"] !== null) {
            $this->DisposalMethod = $param["DisposalMethod"];
        }

        if (array_key_exists("HttpLog",$param) and $param["HttpLog"] !== null) {
            $this->HttpLog = $param["HttpLog"];
        }

        if (array_key_exists("RiskLevel",$param) and $param["RiskLevel"] !== null) {
            $this->RiskLevel = $param["RiskLevel"];
        }

        if (array_key_exists("DetectionMethod",$param) and $param["DetectionMethod"] !== null) {
            $this->DetectionMethod = $param["DetectionMethod"];
        }

        if (array_key_exists("Confidence",$param) and $param["Confidence"] !== null) {
            $this->Confidence = $param["Confidence"];
        }

        if (array_key_exists("Maliciousness",$param) and $param["Maliciousness"] !== null) {
            $this->Maliciousness = $param["Maliciousness"];
        }

        if (array_key_exists("RuleDetailList",$param) and $param["RuleDetailList"] !== null) {
            $this->RuleDetailList = [];
            foreach ($param["RuleDetailList"] as $key => $value){
                $obj = new SecRuleRelatedInfo();
                $obj->deserialize($value);
                array_push($this->RuleDetailList, $obj);
            }
        }

        if (array_key_exists("Label",$param) and $param["Label"] !== null) {
            $this->Label = $param["Label"];
        }

        if (array_key_exists("Area",$param) and $param["Area"] !== null) {
            $this->Area = $param["Area"];
        }
    }
}
