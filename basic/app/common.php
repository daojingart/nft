<?php

// 应用公共函数库文件

use app\admin\model\Setting;
use app\api\model\collection\Goods as GoodsModel;
use app\common\model\GoodsPrecedence;
use app\common\model\MemberGoods;
use app\common\model\MemberLabelList;
use exception\BaseException;
use think\Env;
use Bl\Nft\Common;
use think\Request;

error_reporting(3);

/**
 * 打印调试函数
 * @param $content
 * @param $is_die
 */
function pre($content, $is_die = true)
{
    Common::instance()->pre($content, $is_die);
}

/**
 * 驼峰命名转下划线命名
 * @param $str
 * @return string
 */
function toUnderScore($str)
{

    return Common::instance()->toUnderScore($str);
}

/**
 * 生成密码hash值
 * @param $password
 * @return string
 */
function blhlhash($password)
{
    return Common::instance()->blhlhash($password);
}

/**
 * 获取当前域名及根路径
 * @return string
 */
function base_url()
{
    $request = Request::instance();
    $subDir  = str_replace('\\', '/', dirname($request->server('PHP_SELF')));
    return 'https://' . $request->host() . $subDir . ($subDir === '/' ? '' : '/');
}

/**
 * 写入日志
 * @param string|array $values
 * @param string       $dir
 * @return bool|int
 */
function write_log($values, $dir)
{
    if (is_array($values))
        $values = print_r($values, true);
    // 日志内容
    $content = '[' . date('Y-m-d H:i:s') . ']' . PHP_EOL . $values . PHP_EOL . PHP_EOL;
    try {
        // 文件路径
        $filePath = $dir . '/logs/';
        // 路径不存在则创建
        !is_dir($filePath) && mkdir($filePath, 0755, true);
        // 写入文件
        return file_put_contents($filePath . date('Ymd') . '.log', $content, FILE_APPEND);
    } catch (\Exception $e) {
        return false;
    }
}

/**
 * 作品编号生成
 * @author    : [Mr.Zhang] [1040657944@qq.com]
 * @Interface createNumbering
 * @Time      : 2022/7/1   15:27
 */
function createNumbering($goods_id, $detail)
{
    if (!empty($detail['collection_number'])) {
        return $detail['collection_number'];
    }
    //重新编写编号生成的算fan
    $model      = new MemberGoods();
    $goodsModel = new GoodsModel();
    $goods_info = $goodsModel->detail($goods_id);
    //查询藏品流转的总数
    $memberGoodsCount = $model->where(['goods_id' => $goods_id, 'goods_status' => ['in', ['0', '1', '2']], 'is_donation' => 0, 'collection_number' => ['<>', '']])->count();
    //判断藏品发送总
    $memberGoodsCount = bcadd($memberGoodsCount, 1);
    $goods_no         = '';
    $stock_num        = $goods_info['original_number'];
    if ($memberGoodsCount > $stock_num) { //总数大于发行量则不再生成编号
        return $goods_no;
    }
    $length        = strlen($stock_num);
    $now_length    = strlen($memberGoodsCount);
    $repair_number = bcsub($length, $now_length);
    if ($repair_number > 0) {
        $repair_number_num = '0';
        for ($i = 1; $i < $repair_number; $i++) {
            $repair_number_num .= '0';
        }
        $goods_no = $detail['goods_no'] . "#" . $repair_number_num . $memberGoodsCount . '/' . $goods_info['original_number'];
    } else {
        $goods_no = $detail['goods_no'] . "#" . $memberGoodsCount . '/' . $goods_info['original_number'];
    }
    $model->where(['id' => $detail['id']])->update(['collection_number' => $goods_no]);
    return $goods_no;
}


/**
 * curl请求指定url
 * @param       $url
 * @param array $data
 * @return mixed
 */
function curl($url, $data = [])
{
    // 处理get数据
    if (!empty($data)) {
        $url = $url . '?' . http_build_query($data);
    }
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);//这个是重点。
    $result = curl_exec($curl);
    curl_close($curl);
    return $result;
}

function curlRequest($url, $data = '', $Authenticate = '')
{
    $ch = curl_init();
    if ($Authenticate) {
        $params[CURLOPT_HTTPHEADER] = [
            'Authorization:' . $Authenticate,
            'Content-Type: application/json; charset=utf-8',
        ];    //请求url地址
    }
    $params[CURLOPT_URL]            = $url;    //请求url地址
    $params[CURLOPT_HEADER]         = false; //是否返回响应头信息
    $params[CURLOPT_RETURNTRANSFER] = true; //是否将结果返回
    $params[CURLOPT_FOLLOWLOCATION] = true; //是否重定向
    $params[CURLOPT_TIMEOUT]        = 30; //超时时间

    if (!empty($data)) {
        $params[CURLOPT_POST]       = true;
        $params[CURLOPT_POSTFIELDS] = $data;
    }
    $params[CURLOPT_SSL_VERIFYPEER] = false;//请求https时设置,还有其他解决方案
    $params[CURLOPT_SSL_VERIFYHOST] = false;//请求https时,其他方案查看其他博文
    curl_setopt_array($ch, $params); //传入curl参数
    $content = curl_exec($ch); //执行
    curl_close($ch); //关闭连接
    return $content;
}


/**
 * 多维数组合并
 * @param $array1
 * @param $array2
 * @return array
 */
function array_merge_multiple($array1, $array2)
{

    return Common::instance()->array_merge_multiple($array1,$array2);
}

/**
 * 获取全局唯一标识符
 * @param bool $trim
 * @return string
 */
function getGuidV4($trim = true)
{
    // Windows
    if (function_exists('com_create_guid') === true) {
        $charid = com_create_guid();
        return $trim == true ? trim($charid, '{}') : $charid;
    }
    // OSX/Linux
    if (function_exists('openssl_random_pseudo_bytes') === true) {
        $data    = openssl_random_pseudo_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);    // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);    // set bits 6-7 to 10
        return vsprintf('%s%s%s%s%', str_split(bin2hex($data), 4));
    }
    // Fallback (PHP 4.2+)
    mt_srand((double)microtime() * 10000);
    $charid = strtolower(md5(uniqid(rand(), true)));
    $hyphen = chr(45);                  // "-"
    $lbrace = $trim ? "" : chr(123);    // "{"
    $rbrace = $trim ? "" : chr(125);    // "}"
    $guidv4 = $lbrace .
        substr($charid, 0, 8) . $hyphen .
        substr($charid, 8, 4) . $hyphen .
        substr($charid, 12, 4) . $hyphen .
        substr($charid, 16, 4) . $hyphen .
        substr($charid, 20, 12) .
        $rbrace;
    return $guidv4;
}

/**
 * 获取全局昵称唯一标识符
 * @param bool $trim
 * @return string
 */
function getNicknameGuidV4($trim = true)
{
    // Windows
    if (function_exists('com_create_guid') === true) {
        $charid = com_create_guid();
        return $trim == true ? trim($charid, '{}') : $charid;
    }
    // OSX/Linux
    if (function_exists('openssl_random_pseudo_bytes') === true) {
        $data    = openssl_random_pseudo_bytes(14);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);    // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);    // set bits 6-7 to 10
        return vsprintf('%s%s%s', str_split(bin2hex($data), 4));
    }
    // Fallback (PHP 4.2+)
    mt_srand((double)microtime() * 10000);
    $charid = strtolower(md5(uniqid(rand(), true)));
    $hyphen = chr(45);                  // "-"
    $lbrace = $trim ? "" : chr(123);    // "{"
    $rbrace = $trim ? "" : chr(125);    // "}"
    $guidv4 = $lbrace .
        substr($charid, 0, 8) . $hyphen .
        substr($charid, 8, 4) . $hyphen .
        substr($charid, 12, 4) . $hyphen .
        substr($charid, 16, 4) . $hyphen .
        substr($charid, 20, 12) .
        $rbrace;
    return $guidv4;
}


/**
 * 对象转换成数组
 * @param $obj
 */
function objToArray($obj)
{
    return Common::instance()->objToArray($obj);
}

/**
 * @Notes     : Redis
 * @Interface initRedis
 * @return mixed|Redis
 * @author    : Mr.Zhang
 * @copyright : 河南八六互联信息技术有限公司
 * @Time      : 2021/5/15   5:30 下午
 */
function initRedis()
{
    static $redis = null;
    if ($redis !== null) {
        return $redis;
    }
    $redis = new Redis();
    $redis->connect(Env::get('redis.host', '127.0.0.1'), Env::get('redis.port', 6379));

    if (Env::get('redis.password', '')) {
        $redis->auth(Env::get('redis.password'));
    }
    return $redis;
}



/**
 * @Notes     : 获取几天前，几秒前的函数
 * @Interface timeTran
 * @param $the_time
 * @return mixed|string
 * @author    : Mr.Zhang
 * @copyright : 河南八六互联信息技术有限公司
 * @Time      : 2021/5/31   00:48
 */
function timeTran($the_time)
{
    $now_time  = date("Y-m-d H:i:s", time());
    $now_time  = strtotime($now_time);
    $show_time = strtotime($the_time);
    $dur       = $now_time - $show_time;
    if ($dur < 0) {
        return $the_time;
    } else {
        if ($dur < 60) {
            return $dur . '秒前';
        } else {
            if ($dur < 3600) {
                return floor($dur / 60) . '分钟前';
            } else {
                if ($dur < 86400) {
                    return floor($dur / 3600) . '小时前';
                } else {
                    if ($dur < 259200) {//3天内
                        return floor($dur / 86400) . '天前';
                    } else {
                        return $the_time;
                    }
                }
            }
        }
    }
}

/**
 * 笛卡尔积算法
 * @param $goods
 * @return array
 */
function CartesianProduct($goods)
{
    $totalRow = 1;
    for ($i = 0; $i < count($goods); $i++) {
        $totalRow *= count($goods[$i]['spec_items']);
    }
    $spec_list = [];
    for ($i = 0; $i < $totalRow; $i++) {
        $rowData                   = [];
        $rowCount                  = 1;
        $specSkuIdAttr             = [];
        $specSkuIdAttrs            = [];
        $specSkuSpecValueIdIdAttrs = [];
        for ($j = 0; $j < count($goods); $j++) {
            $skuValues      = $goods[$j]['spec_items'];
            $rowCount       *= count($skuValues);
            $anInterBankNum = ($totalRow / $rowCount);
            $point          = (($i / $anInterBankNum) % count($skuValues));
            if (0 === ($i % $anInterBankNum)) {
                array_push($rowData, [
                    'rowspan'        => $anInterBankNum,
                    'item_id'        => $skuValues[$point]['item_id'],
                    'spec_value_id'  => isset($skuValues[$point]['spec_value_id']) ? $skuValues[$point]['spec_value_id'] : $skuValues[$point]['item_id'],
                    'spec_sku_value' => $skuValues[$point]['spec_value'],
                    'spec_value'     => $skuValues[$point]['spec_value'],
                ]);
            }
            array_push($specSkuIdAttr, $skuValues[intval($point)]['spec_value']);
            array_push($specSkuIdAttrs, $skuValues[intval($point)]['item_id']);
            array_push($specSkuSpecValueIdIdAttrs, $skuValues[intval($point)]['spec_value_id']);
        }
        array_push($spec_list, [
            'spec_sku_value'    => implode("_", $specSkuIdAttr),
            'spec_sku_value_id' => implode("_", $specSkuSpecValueIdIdAttrs),
            'spec_sku_id'       => implode("_", $specSkuIdAttrs),
            'rows'              => $rowData,
            'form'              => [],
        ]);
    }
    return $spec_list;
}

/**
 * 展示金额
 *
 * @param $value
 * @return string
 * @author    Mr.Liu
 * @copyright 河南八六互联信息技术有限公司
 * @Time      2021/12/4 13:48
 */
function moneyToShow($value): string
{
    $value = floatval($value);
    return bcdiv((string)$value, '100', 2);
}

/**
 *  生成唯一HASH
 * @return string
 * @Time      : 2022/8/10   00:16
 * @author    : [Mr.Zhang] [1040657944@qq.com]
 * @Interface get_hash
 */
function get_hash()
{

    return Common::instance()->get_hash();
}

/**
 * 保存金额
 *
 * @param $value
 * @return string
 * @author    Mr.Liu
 * @copyright 河南八六互联信息技术有限公司
 * @Time      2021/12/4 13:49
 */
function moneyToSave($value): string
{
    $value = floatval($value);
    return bcmul((string)$value, '100');
}

/**
 * 时间戳转换
 *
 * @param $timestamp
 * @return string
 * @author    Mr.Liu
 * @copyright 河南八六互联信息技术有限公司
 * @Time      2021/12/4 13:56
 */
function timeToDate($timestamp): string
{
    return $timestamp > 0 ? date('Y-m-d H:i:s', $timestamp) : '';
}

/**
 * 保存时间
 *
 * @param string $date
 * @return int
 * @author    Mr.Liu
 * @copyright 河南八六互联信息技术有限公司
 * @Time      2021/12/4 15:02
 */
function dateToTime(string $date): int
{
    $value = strtotime($date);
    if ($value === false) {
        return 0;
    }
    return $value;
}


/**
 * 公共处理抛异常
 *
 * @param Exception $e
 * @param string    $dirName
 * @param string    $fileName
 * @param array     $logArray
 * @return bool
 * @copyright 河南八六互联信息技术有限公司
 * @Time      2021/12/4 14:04
 * @author    Mr.Liu
 */
function handleResult(Exception $e, string $dirName, string $fileName, array &$logArray = []): bool
{
    $errCode    = $e->getCode();
    $logArray[] = [
        'method'   => __METHOD__,
        'err_code' => $errCode,
        'err_msg'  => $e->getMessage(),
        'err_file' => $e->getFile(),
        'err_line' => $e->getLine(),
    ];
    try {

        switch ($errCode) {
            // 记录错误日志
            case 0:
                setLog($dirName, $fileName . '_error', $logArray);
                break;
            // 不记录日志
            case 1:
                break;
            // 记录警告日志
            default:
                setLog($dirName, $fileName . '_warning', $logArray);
        }
        return true;
    } catch (Exception $exception) {
        return false;
    }
}

/**
 * 记录错误日志
 *
 * @param string $dirName
 * @param string $fileName
 * @param array  $logArray
 * @param string $mark
 * @throws Exception
 * @copyright 河南八六互联信息技术有限公司
 * @Time      2021/12/6 17:02
 * @author    Mr.Liu
 */
function setLog(string $dirName, string $fileName, array $logArray, string $mark = '')
{
    $mark     = empty($mark) ? '日志内容' : $mark;
    $message  = date('Y-m-d H:i:s') . " 【{$mark}】" . " ==> " . json_encode($logArray, JSON_UNESCAPED_UNICODE) . "\r\n\r\n";
    $fileName = $fileName . '.log';
    $filePath = RUNTIME_PATH . '/logs/' . date('Ymd') . '/' . $dirName . '/';
    // 路径不存在则创建
    isDir($filePath);
    // 写入文件
    file_put_contents($filePath . $fileName, $message, FILE_APPEND);
}

/**
 * 验证地址是否存在 ，不存在创建
 *
 * @param string $dir
 * @return string
 * @throws Exception
 * @copyright 河南八六互联信息技术有限公司
 * @Time      2021/12/4 17:00
 * @author    Mr.Liu
 */
function isDir(string $dir): string
{
    if (!is_dir($dir)) {
        if (!mkdir($dir, 0755, true)) {
            throw new Exception('路径创建失败');
        }
    }
    return $dir;
}

/**
 * 验证是否是手机号
 *
 * @param       $mobile
 * @param false $mobile_prefix
 * @return bool
 * @copyright 河南八六互联信息技术有限公司
 * @Time      2021/12/4 16:57
 * @author    Mr.Liu
 */
function checkMobile($mobile, $mobile_prefix = false): bool
{
    if ($mobile_prefix == '+86' || !$mobile_prefix) {
        if (strlen($mobile) != 11) {
            return false;
        }
        if (true == preg_match('/^1[3|4|5|6|7|8|9]\d{9}$/', $mobile)) {
            return true;
        }
        return false;
    } elseif (strlen($mobile) < 14 and $mobile_prefix and $mobile_prefix != '+86') {
        return true;
    } else {
        return false;
    }
}

/**
 * 高精度计算
 *
 * @param     $n1
 * @param     $symbol
 * @param     $n2
 * @param int $scale
 * @return string
 * @throws Exception
 * @author    Mr.Liu
 * @copyright 河南八六互联信息技术有限公司
 * @Time      2021/12/6 16:50
 */
function binaryCalculator($n1, $symbol, $n2, $scale = 4): string
{
    $n1 = (string)$n1;
    $n2 = (string)$n2;
    switch ($symbol) {
        case "+"://加法
            $result = bcadd($n1, $n2, $scale);
            break;
        case "-"://减法
            $result = bcsub($n1, $n2, $scale);
            break;
        case "*"://乘法
            $result = bcmul($n1, $n2, $scale);
            break;
        case "/"://除法
            $result = bcdiv($n1, $n2, $scale);
            break;
        default:
            throw new Exception('不支持的计算方式：' . $symbol);
    }
    return $result;
}

/**
 * 经典的概率算法，
 * $proArr是一个预先设置的数组，
 * 假设数组为：array(100,200,300，400)，
 * 开始是从1,1000 这个概率范围内筛选第一个数是否在他的出现概率范围之内，
 * 如果不在，则将概率空间，也就是k的值减去刚刚的那个数字的概率空间，
 * 在本例当中就是减去100，也就是说第二个数是在1，900这个范围内筛选的。
 * 这样 筛选到最终，总会有一个数满足要求。
 * 就相当于去一个箱子里摸东西，
 * 第一个不是，第二个不是，第三个还不是，那最后一个一定是。
 */
function get_rand($proArr)
{
    return Common::instance()->get_rand($proArr);
}

/**
 * 防止重复多次提交
 * @author    : [Mr.Zhang] [1040657944@qq.com]
 * @Interface lockRedis
 * @Time      : 2022/7/4   13:41
 */
function lockRedis($action_name, $member_id, $ttl = 5)
{
    $redis      = initRedis();
    $requestKey = $action_name . $member_id;
    if ($redis->get($requestKey)) {
        throw new BaseException(['msg' => '点击的太快了呢,请您稍后重试!']);
    }
    $redis->set($requestKey, '1');
    $redis->expire($requestKey, $ttl);
}


/**
 * 获取优先购买列表
 * @author    : [Mr.Zhang] [1040657944@qq.com]
 * @Interface buyFirstInfo
 * @Time      : 2022/7/12   17:05
 */
function buyFirstInfo($goods_id, $member_id, $start_time)
{
	$start_time = strtotime($start_time);
    $goodsPrecedenceList  = (new GoodsPrecedence())->where(['goods_id' => $goods_id,'status' => 1])->field("label_id,purchase_time,purchase_quantity,label_type")->find();
    $new_start_time = $start_time;
    if(empty($goodsPrecedenceList)){
        return $new_start_time;
    }
    switch ($goodsPrecedenceList['label_type'])
    {
        case "1": //标签
            $member_label_list = (new MemberLabelList())->where(['lable_id' => $goodsPrecedenceList['label_id']])->column("member_id");
            if (!empty($member_label_list)) {
                //获取会员ID
                if (in_array($member_id, $member_label_list)) {
                    //重新计算开始时间
                    $precedence     = $goodsPrecedenceList['purchase_time']*60;
                    $new_start_time = $start_time - $precedence;
                    break;
                }
            }
            break;
        case "2": //持有藏品
            $GoodsPrecedenceList_list = (new \app\common\model\GoodsPrecedenceList())->where(['precedence_goods_id' => $goods_id])->order("hold_goods_number desc")->select();
            foreach ($GoodsPrecedenceList_list as $key => $value) {
                $my_goods_count = (new MemberGoods())->where(['goods_id' => $value['goods_id'], 'member_id' => $member_id, 'goods_status' => 0, 'is_donation' => 0, 'is_synthesis' => 0])->count();
                if ($my_goods_count > 0 && $value['hold_goods_number'] <= $my_goods_count) {
                    $precedence     = $value['purchase_time'] * 60;
                    $new_start_time = $start_time - $precedence;
                    break;
                }
            }
            break;
        case "3": //权益叠加
            $GoodsPrecedenceList_list = (new \app\common\model\GoodsPrecedenceList())->where(['precedence_goods_id' => $goods_id])->order("id asc")->select();
            foreach ($GoodsPrecedenceList_list as $key => $value) {
                $my_goods_count = (new MemberGoods())->where(['goods_id' => $value['goods_id'], 'member_id' => $member_id, 'goods_status' => 0, 'is_donation' => 0, 'is_synthesis' => 0])->count();
                if($my_goods_count>0){
                    $precedence     = $value['purchase_time'] * 60;
                    $new_start_time = $start_time - $precedence;
                    break;
                }
            }
            break;
    }
    return $new_start_time;
}

/**
 * 获取优先购买次数
 * @author    : [Mr.Zhang] [1040657944@qq.com]
 * @Interface getFirstInfoNumber
 * @Time      : 2022/9/3   18:05
 */
function getFirstInfoNumber($goods_id, $member_id)
{
    $goodsPrecedenceList  = (new GoodsPrecedence())->where(['goods_id' => $goods_id,'status' => 1])->field("label_id,purchase_time,purchase_quantity,label_type")->find();
    $buy_number      = 0;
    if(empty($goodsPrecedenceList)){
        return $buy_number;
    }
    switch ($goodsPrecedenceList['label_type'])
    {
        case "1": //按照标签计算
            $member_label_list = (new MemberLabelList())->where(['lable_id' => $goodsPrecedenceList['label_id']])->column("member_id");
            if (!empty($member_label_list)) {
                //获取会员ID
                if (in_array($member_id, $member_label_list)) {
                    //重新计算开始时间
                    $buy_number = $goodsPrecedenceList['purchase_quantity'];
                    break;
                }
            }
            break;
        case "2": //持有藏品计算
            $GoodsPrecedenceList_list = (new \app\common\model\GoodsPrecedenceList())->where([
                'precedence_goods_id' => $goods_id
            ])->order("hold_goods_number desc")->select();
            foreach ($GoodsPrecedenceList_list as $key => $value) {
                $my_goods_count = (new MemberGoods())->where(['goods_id' => $value['goods_id'], 'member_id' => $member_id, 'goods_status' => 0, 'is_donation' => 0, 'is_synthesis' => 0])->count();
                if ($my_goods_count > 0 && $value['hold_goods_number'] <= $my_goods_count) {
                    $buy_number = $value['purchase_quantity'];
                    break;
                }
            }
            break;
        case "3": //藏品权益叠加
            $GoodsPrecedenceList_list = (new \app\common\model\GoodsPrecedenceList())->where([
                'precedence_goods_id' => $goods_id
            ])->order("id asc")->select();
            foreach ($GoodsPrecedenceList_list as $key => $value) {
                //获取这个会员藏品的数量
                $my_goods_count = (new MemberGoods())->where(['goods_id' => $value['goods_id'], 'member_id' => $member_id, 'goods_status' => 0, 'is_donation' => 0, 'is_synthesis' => 0])->count();
                $buy_number = $my_goods_count;
            }
            break;
    }
    return $buy_number;
}

/**
 * 查询获取商品的流通量
 * @author    : [Mr.Zhang] [1040657944@qq.com]
 * @Interface goodsCirculation
 * @Time      : 2022/9/5   18:52
 */
function goodsCirculation($goods_id, $is_goods_type)
{
    if ($is_goods_type == 1) {
        return (new MemberGoods())->where(['goods_id' => $goods_id, 'goods_status' => 1, 'is_synthesis' => 0, 'is_donation' => 0])->count();
    } else {
        return (new \app\common\model\MemberBox())->where(['box_status' => ['in', ['20', '30']], 'goods_id' => $goods_id])->count();
    }
}


/**
 * 格式化数字
 */
function float_number($number)
{
    return Common::instance()->float_number($number);
}


if (!function_exists('get_runtime_data')) {

    /**
     * 获取runtime数据
     * @return array
     */
    function get_runtime_data(): array
    {
        $return_data[] = $used_time = round(microtime(true) - THINK_START_TIME, 10);
        $return_data[] = $used_time > 0 ? number_format(1 / $used_time, 2) : '∞';
        $return_data[] = Request::instance()->server('REQUEST_TIME');
        return $return_data;
    }

}

if (!function_exists('phone_substr_replace')) {

    /**
     * 替换手机号
     * @param string $phone
     * @return string
     */
    function phone_substr_replace(string $phone): string
    {
        return Common::instance()->phone_substr_replace($phone);
    }
}

if (!function_exists('base_exception')) {

    /**
     * 抛出base异常
     * @param array $params
     * @return mixed
     * @throws BaseException
     */
    function base_exception(array $params = [])
    {
        throw new BaseException($params);
    }
}

/**
 * 用户订单数量锁
 * @param $member_id
 * @Time: 2022/11/7   09:28
 * @author: [Mr.Zhang] [1040657944@qq.com]
 * @Interface memberOrderLock
 */
function memberOrderLock($member_id)
{
    $order_values = Setting::getItem("order");
    $redis = initRedis();
    $member_lock_key = "member_lock_member_id:".$member_id;
    $redis->Incr($member_lock_key);
    $redis->Expire($member_lock_key,60*60);
    if($redis->get($member_lock_key) >= isset($order_values['order_lock_number'])?intval($order_values['order_lock_number']):3){
        $redis->Expire($member_lock_key,isset($order_values['order_lock_time'])?$order_values['order_lock_time']*3600:2*3600);
    }
}

if (!function_exists('check_cors_request')) {
    /**
     * 跨域检测
     */
    function check_cors_request()
    {
        if (isset($_SERVER['HTTP_ORIGIN']) && $_SERVER['HTTP_ORIGIN']) {
            $info        = parse_url($_SERVER['HTTP_ORIGIN']);
            $domainArr   = explode(',', config('fastadmin.cors_request_domain'));
            $domainArr[] = request()->host(true);
            if (in_array("*", $domainArr) || in_array($_SERVER['HTTP_ORIGIN'], $domainArr) || (isset($info['host']) && in_array($info['host'], $domainArr))) {
                header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
            } else {
                $response = \think\Response::create('跨域检测无效', 'html', 403);
                throw new \think\exception\HttpResponseException($response);
            }

            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age: 86400');

            if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
                if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
                    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
                }
                if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
                    header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
                }
                $response = \think\Response::create('', 'html');
                throw new \think\exception\HttpResponseException($response);
            }
        }
    }
}

function strreplace($str, $startlen = 0, $endlen = 4) {
    $repstr = "";
    if (strlen($str) < ($startlen + $endlen+1)) {
        return $str;
    }
    $count = strlen($str) - $startlen - $endlen;
    for ($i = 0; $i < $count; $i++) {
        $repstr.="*";
    }
    return preg_replace('/(\d{' . $startlen . '})\d+(\d{' . $endlen . '})/', '${1}' . $repstr . '${2}', $str);
}

if (!function_exists('convert_str_to_pem')) {


    /**
     * 转化字符串到证书中
     * @param $rsaStr
     * @return false|string
     * @throws Exception
     */
    function convert_str_to_pem($rsaStr)
    {
        $filePath = RUNTIME_PATH . 'temp' . DS . md5($rsaStr) . '.pem';
        if (file_exists($filePath)) {
            return realpath($filePath);
        }
        if (!file_exists(dirname($filePath))) {
            mkdir(dirname($filePath), 0755, true);//创建临时目录
        }
        @file_put_contents($filePath, $rsaStr);
        return realpath($filePath);
    }
}

if (!function_exists('isAmount')) {
    /**
     * 金额校验函数
     * @param $value
     * @param bool $isZero
     * @param bool $negative
     * @return bool
     */
    function isAmount($value, $isZero=false, $negative=false){
        // 必须是整数或浮点数，且允许为负
        if (!preg_match("/^[-]?\d+(.\d{1,2})?$/", $value)){
            return false;
        }
        // 不为 0
        if (!$isZero && empty((int)($value*100))){
            return false;
        }
        // 不为负数
        if (!$negative && (int)($value * 100) < 0){
            return false;
        }
        return true;
    }
}

function Draw($prize, $count)
{
    $chance = 0;
    foreach ($prize as &$item) {
        $chance              += $item['probability'];
        $item['probability'] = $chance;
    }
    for ($i = 0; $i < $count; $i++) {
        unset($rand);
        $rand = mt_rand(1, $chance);
        foreach ($prize as $_k => $_v) {
            if ($_k == 0) {
                if ($rand > 0 && $rand <= $_v['probability']) {
                    return $_v['id'];
                }
            } else {
                if ($rand > $prize[$_k - 1]['probability'] && $rand <= $_v['probability']) {
                    return $_v['id'];
                }
            }
        }
    }
}

