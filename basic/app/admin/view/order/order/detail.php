<link rel="stylesheet" href="/assets/admin/css/amui/bladmin.css" />
<script src="/assets/admin/amazeui.min.js"></script>
<style>
    /*订单详情步骤条颜色修改*/
    .order-detail-progress:before, .order-detail-progress:after {
        background: #038ef9;
    }
</style>
<div class="am-cf">
    <div class="row">
        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
            <div class="widget am-cf">
                <div class="widget-body  am-margin-bottom-lg">
                    <div class="am-u-sm-12">
                        <?php
                        // 计算当前步骤位置
                        $progress = 1;
                        $detail['pay_status'] == 2 && $progress += 1;
                        $detail['delivery_status'] == 2 && $progress += 1;
                        $detail['receipt_status'] == 2 && $progress += 1;
                        // $detail['order_status']['value'] == 30 && $progress += 1;
                        ?>
                        <ul class="order-detail-progress progress-<?= $progress ?>">
                            <li>
                                <span>创建订单</span>
                                <div class="tip"><?= $detail['create_time'] ?></div>
                            </li>
                            <li>
                                <span>付款</span>
                                <?php if ($detail['pay_status']['value'] == 2): ?>
                                    <div class="tip">
                                        付款于 <?= date('Y-m-d H:i:s', $detail['pay_time']) ?>
                                    </div>
                                <?php endif; ?>
                            </li>

                            <li>
                                <span>发货</span>
                                <?php if ($detail['delivery_status']['value'] == 2): ?>
                                    <div class="tip">
                                        发货于 <?= date('Y-m-d H:i:s', $detail['delivery_time']) ?>
                                    </div>
                                <?php endif; ?>
                            </li>
                            <li>
                                <span>收货</span>
                                <?php if ($detail['receipt_status']['value'] == 2): ?>
                                    <div class="tip">
                                        收货于 <?= date('Y-m-d H:i:s', $detail['receipt_time']) ?>
                                    </div>
                                <?php endif; ?>
                            </li>
                            <li>
                                <span>完成</span>
                                <?php if ($detail['order_status']['value'] == 4): ?>
                                    <div class="tip">
                                        完成于 <?= date('Y-m-d H:i:s', $detail['receipt_time']) ?>
                                    </div>
                                <?php endif; ?>
                            </li>
                        </ul>
                    </div>
                    <div class="widget-head am-cf">
                        <div class="widget-title am-fl">基本信息</div>
                    </div>
                    <div class="am-scrollable-horizontal">
                        <table class="regional-table am-table am-table-bordered am-table-centered
                            am-text-nowrap am-margin-bottom-xs">
                            <tbody>
                            <tr>
                                <th>订单号</th>
                                <th>实付款</th>
                                <th>买家</th>
                                <th>交易状态</th>
                            </tr>
                            <tr>
                                <td><?= $detail['order_no'] ?></td>
                                <td>
                                    <p>￥<?= $detail['pay_price'] ?></p>
                                    <p class="am-link-muted">(含运费：￥<?= $detail['freight_price'] ?>)</p>
                                </td>
                                <td>
                                    <p><?= $detail['user']['name'] ?? '' ?></p>
                                    <p class="am-link-muted">(用户id：<?= $detail['user']['member_id'] ?? '' ?>)</p>
                                </td>
                                <td>
                                    <p>付款状态：
                                        <span class="layui-badge <?= $detail['pay_status']['value'] == 2 ? 'layui-bg-blue' : '' ?>"><?= $detail['pay_status']['value'] == 2 ? '已付款' : '未付款' ?></span>
                                    </p>
                                    <p>发货状态：
                                        <span class="layui-badge <?= $detail['delivery_status']['value'] == 2 ? 'layui-bg-blue' : '' ?>"><?= $detail['delivery_status']['value'] == 2 ? '已发货' : '未发货' ?></span>
                                    </p>
                                    <p>收货状态：
                                        <span class="layui-badge <?= $detail['receipt_status']['value'] == 2 ? 'layui-bg-blue' : '' ?>"><?= $detail['receipt_status']['value'] == 2 ? '已收货' : '未收货' ?></span>
                                    </p>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="widget-head am-cf">
                        <div class="widget-title am-fl">商品信息</div>
                    </div>
                    <div class="am-scrollable-horizontal">
                        <table class="regional-table am-table am-table-bordered am-table-centered
                            am-text-nowrap am-margin-bottom-xs">
                            <tbody>
                            <tr>
                                <th>商品名称</th>
                                //<th>商品编码</th>
                                <th>单价</th>
                                <th>购买数量</th>
                                <th>商品总价</th>
                            </tr>
                            <?php foreach ($detail['goods'] as $goods): ?>
                                <tr>
                                    <td class="goods-detail am-text-middle">
                                        <div class="goods-image">
                                            <img src="<?= $goods['goods_image']?>" alt="">
                                        </div>
                                        <div class="goods-info">
                                            <p class="goods-title"><?= $goods['goods_name'] ?></p>
                                            {{if $detail['order_type'] != '3'}}
                                            <p class="goods-spec am-link-muted">
                                                <?= $goods['sku_value'] ?>
                                            </p>
                                            {{/if}}
                                        </div>
                                    </td>
                                    {{if $detail['order_type'] != '3'}}
                                    <td><?= $goods['goods_no'] ?: '--' ?></td>
                                    {{/if}}
                                    <td>
                                        <p>￥<?= $goods['pay_price'] ?></p>

                                    </td>

                                    <td>×<?= $goods['product_num'] ?></td>
                                    <td>
                                        <p>￥<?= $goods['total_price'] ?></p>

                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <tr>
                                <td colspan="6" class="am-text-right">总计金额：￥<?= $detail['total_price'] ?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="widget-head am-cf">
                        <div class="widget-title am-fl">收货信息</div>
                    </div>
                    <div class="am-scrollable-horizontal">
                        <table class="regional-table am-table am-table-bordered am-table-centered
                            am-text-nowrap am-margin-bottom-xs">
                            <tbody>
                            <tr>
                                <th>收货人</th>
                                <th>收货电话</th>
                                <th>收货地址</th>
                            </tr>
                            <tr>
                                <td><?= $detail['address']['name'] ?></td>
                                <td><?= $detail['address']['phone'] ?></td>
                                <td>
                                    <?= $detail['address']['province_name'] ?>
                                    <?= $detail['address']['city_name'] ?>
                                    <?= $detail['address']['region_name'] ?>
                                    <?= $detail['address']['detail'] ?>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <!--付款信息-->
                    <?php if ($detail['pay_status']['value'] == 2): ?>
                        <div class="widget-head am-cf">
                            <div class="widget-title am-fl">付款信息</div>
                        </div>
                        <div class="am-scrollable-horizontal">
                            <table class="regional-table am-table am-table-bordered am-table-centered
                                am-text-nowrap am-margin-bottom-xs">
                                <tbody>
                                <tr>
                                    <th>应付款金额</th>
                                    <th>支付方式</th>
                                    <th>付款状态</th>
                                    <th>付款时间</th>
                                </tr>
                                {{if $detail['pay_price'] != '0'}}
                                <tr>
                                    <td>￥<?= $detail['pay_price'] ?></td>
                                    <td><?= $detail['pay_type_text']?></td>
                                    <td>
                                        <span class="layui-badge <?= $detail['pay_status']['value'] == 2 ? 'layui-bg-blue' : '' ?>"><?= $detail['pay_status']['value'] == 2 ? '已付款' : '未付款' ?></span>
                                    </td>
                                    <td>
                                        <?= $detail['pay_time'] ? date('Y-m-d H:i:s', $detail['pay_time']) : '--' ?>
                                    </td>
                                </tr>
                                {{/if}}
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                    <!--付款信息-->


                    <?php if ($detail['pay_status']['value'] == 2 ): ?>
                        <div class="widget-head am-cf">
                            <div class="widget-title am-fl">发货信息</div>
                        </div>
                        <?php if ($detail['delivery_status']['value'] == 1): ?>
                            <!-- 去发货 -->
                            <form id="delivery" class="my-form am-form tpl-form-line-form" method="post">
                                <div class="am-form-group">
                                    <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">物流公司名称 </label>
                                    <div class="am-u-sm-9 am-u-end">
                                        <select name="order[express_company]" required
                                                data-am-selected="{searchBox: 1, btnSize: 'sm',  placeholder:'请选择物流公司'}">
                                            <option value=""></option>
                                            <?php if (isset($express_list)): foreach ($express_list as $first): ?>
                                                <option value="<?= $first['express_id'] ?>"><?= $first['express_name'] ?></option>
                                            <?php endforeach; endif; ?>
                                        </select>
                                        <small>如：顺丰速运、申通快递</small>
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">物流单号 </label>
                                    <div class="am-u-sm-9 am-u-end">
                                        <input type="text" class="tpl-form-input" name="order[express_no]" required>
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <div class="am-u-sm-9 am-u-sm-push-3 am-margin-top-lg">
                                        <button type="submit" class="j-submit am-btn am-btn-sm am-btn-secondary">
                                            确认发货
                                        </button>
                                    </div>
                                </div>
                            </form>
                        <?php else: ?>
                            <div class="am-scrollable-horizontal">
                                <table class="regional-table am-table am-table-bordered am-table-centered
                                am-text-nowrap am-margin-bottom-xs">
                                    <tbody>
                                    <tr>
                                        <th>物流公司</th>
                                        <th>物流单号</th>
                                        <th>发货状态</th>
                                        <th>发货时间</th>
                                    </tr>
                                    <tr>
                                        <td><?= $detail['express_company'] ?></td>
                                        <td><?= $detail['express_no'] ?></td>
                                        <td>
                                            <span class="layui-badge <?= $detail['delivery_status']['value'] == 2 ? 'layui-bg-blue' : '' ?>"> <?= $detail['delivery_status']['value'] == 2 ? '已发货' : '未发货' ?></span>
                                        </td>
                                        <td>
                                            <?= date('Y-m-d H:i:s', $detail['delivery_time']) ?>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; endif; ?>

                    <?php if ($detail['pay_status']['value'] == 2 && $detail['order_status']['value'] == 3): ?>
                        <div class="widget-head am-cf">
                            <div class="widget-title am-fl">发货信息</div>
                        </div>
                        <?php if ($detail['delivery_status']['value'] == 1): ?>
                            <!-- 去发货 -->
                            <form id="delivery" class="my-form am-form tpl-form-line-form" method="post">
                                <div class="am-form-group">
                                    <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">物流公司名称 </label>
                                    <div class="am-u-sm-9 am-u-end">
                                        <select name="order[express_company]" required
                                                data-am-selected="{searchBox: 1, btnSize: 'sm',  placeholder:'请选择物流公司'}">
                                            <option value=""></option>
                                            <?php if (isset($express_list)): foreach ($express_list as $first): ?>
                                                <option value="<?= $first['express_id'] ?>"><?= $first['express_name'] ?></option>
                                            <?php endforeach; endif; ?>
                                        </select>
                                        <small>如：顺丰速运、申通快递</small>
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">物流单号 </label>
                                    <div class="am-u-sm-9 am-u-end">
                                        <input type="text" class="tpl-form-input" name="order[express_no]" required>
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <div class="am-u-sm-9 am-u-sm-push-3 am-margin-top-lg">
                                        <button type="submit" class="j-submit am-btn am-btn-sm am-btn-secondary">
                                            确认发货
                                        </button>
                                    </div>
                                </div>
                            </form>
                        <?php else: ?>
                            <div class="am-scrollable-horizontal">
                                <table class="regional-table am-table am-table-bordered am-table-centered
                                am-text-nowrap am-margin-bottom-xs">
                                    <tbody>
                                    <tr>
                                        <th>物流公司</th>
                                        <th>物流单号</th>
                                        <th>发货状态</th>
                                        <th>发货时间</th>
                                    </tr>
                                    <tr>
                                        <td><?= $detail['express_company'] ?></td>
                                        <td><?= $detail['express_no'] ?></td>
                                        <td>
                                            <span class="layui-badge <?= $detail['delivery_status']['value'] == 20 ? 'layui-bg-blue' : '' ?>"><?= $detail['delivery_status']['text'] ?></span>
                                        </td>
                                        <td>
                                            <?= date('Y-m-d H:i:s', $detail['delivery_time']) ?>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; endif; ?>
                </div>
            </div>

        </div>
    </div>
</div>
<script>
    $(function () {
        /**
         * 表单验证提交
         * @type {*}
         */
        $('.my-form').superForm({
            'url':'delivery?order_id='+{{$detail['order_id']}}
        });
    });
</script>
