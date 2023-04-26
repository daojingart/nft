<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang [1040657944@qq.com]
 * +----------------------------------------------------------------------
 * | Time:  2022/7/18   11:13
 * +----------------------------------------------------------------------
 * | className: 绑定银行卡
 * +----------------------------------------------------------------------
 */

namespace app\common\extend\sxy;

class Bindcard extends Base
{
    /**
     * 首信易绑定银行卡
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface buildCard
     * @Time: 2022/7/18   11:13
     */
    public function buildCard($member_id,$bank_info,$callback_url)
    {
        $url = "https://apis.5upay.com/cashier/bindcard/submit";
        $param = [
            'merchantId' => '896709587',
            'merchantUserId' => $member_id,
            'phoneNumber' => $bank_info['phone'],
            'bankCardNumber' => $bank_info['card_no'],
            'name' => $bank_info['card_name'],
            'idCardNum' => $bank_info['card'],
            'bindCardCallbackUrl' => '',
            'bindCardNotifyUrl' => '',
        ];
        $result = $this->getHttpResponse($url, $param, $member_id);
        pre($result);
    }

}