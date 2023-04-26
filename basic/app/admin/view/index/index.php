<link rel="stylesheet" href="/assets/admin/css/other/console2.css" />
<link rel="stylesheet" href="/assets/admin/css/other/result.css">
<body class="pear-container">
<!--  数据概览统计  -->
<div class="layui-row layui-col-space10">
    <div class="layui-col-xs6 layui-col-md2">
        <div class="layui-card top-panel">
            <div class="layui-card-header">今日营业额(元)</div>
            <div class="layui-card-body">
                <div class="layui-row layui-col-space5">
                    <div class="layui-col-xs12 layui-col-md12 top-panel-number" style="color: #28333E;" id="value1">
                        {{$data.today_price}}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="layui-col-xs6 layui-col-md2">
        <div class="layui-card top-panel">
            <div class="layui-card-header">累计营业额(元)</div>
            <div class="layui-card-body">
                <div class="layui-row layui-col-space5">
                    <div class="layui-col-xs12 layui-col-md12 top-panel-number" style="color: #28333E;" id="value2">
                        {{$data.sum_price}}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="layui-col-xs6 layui-col-md2">
        <div class="layui-card top-panel">
            <div class="layui-card-header">今日成交订单(单)</div>
            <div class="layui-card-body">
                <div class="layui-row layui-col-space5">
                    <div class="layui-col-xs12 layui-col-md12 top-panel-number" style="color: #28333E;" id="value4">
                        {{$data.today_order_count}}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="layui-col-xs6 layui-col-md2">
        <div class="layui-card top-panel">
            <div class="layui-card-header">累计成交订单(单)</div>
            <div class="layui-card-body">
                <div class="layui-row layui-col-space5">
                    <div class="layui-col-xs12 layui-col-md12 top-panel-number" style="color: #28333E;" id="value4">
                        {{$data.sum_order_count}}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="layui-col-xs6 layui-col-md2">
        <div class="layui-card top-panel">
            <div class="layui-card-header">今日新增会员(人)</div>
            <div class="layui-card-body">
                <div class="layui-row layui-col-space5">
                    <div class="layui-col-xs12 layui-col-md12 top-panel-number" style="color: #28333E;" id="value4">
                        {{$data.today_member_count}}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="layui-col-xs6 layui-col-md2">
        <div class="layui-card top-panel">
            <div class="layui-card-header">累计注册会员(人)</div>
            <div class="layui-card-body">
                <div class="layui-row layui-col-space5">
                    <div class="layui-col-xs12 layui-col-md12 top-panel-number" style="color: #28333E;" id="value4">
                        {{$data.member_count}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--  藏品指标数据统计  -->
<div class="layui-row layui-col-space10">
    <div class="layui-col-xs6 layui-col-md2">
        <div class="layui-card top-panel">
            <div class="layui-card-header">今日铸造藏品</div>
            <div class="layui-card-body">
                <div class="layui-row layui-col-space5">
                    <div class="layui-col-xs12 layui-col-md12 top-panel-number" style="color: #28333E;" id="value1">
                        {{$data.today_goods}}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="layui-col-xs6 layui-col-md2">
        <div class="layui-card top-panel">
            <div class="layui-card-header">累计铸造藏品</div>
            <div class="layui-card-body">
                <div class="layui-row layui-col-space5">
                    <div class="layui-col-xs12 layui-col-md12 top-panel-number" style="color: #28333E;" id="value2">
                        {{$data.sum_goods}}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="layui-col-xs6 layui-col-md2">
        <div class="layui-card top-panel">
            <div class="layui-card-header">今日空投藏品</div>
            <div class="layui-card-body">
                <div class="layui-row layui-col-space5">
                    <div class="layui-col-xs12 layui-col-md12 top-panel-number" style="color: #28333E;" id="value4">
                        {{$data.today_airdrop_goods}}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="layui-col-xs6 layui-col-md2">
        <div class="layui-card top-panel">
            <div class="layui-card-header">累计空投藏品</div>
            <div class="layui-card-body">
                <div class="layui-row layui-col-space5">
                    <div class="layui-col-xs12 layui-col-md12 top-panel-number" style="color: #28333E;" id="value4">
                        {{$data.sum_airdrop_goods}}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="layui-col-xs6 layui-col-md2">
        <div class="layui-card top-panel">
            <div class="layui-card-header">今日空投会员</div>
            <div class="layui-card-body">
                <div class="layui-row layui-col-space5">
                    <div class="layui-col-xs12 layui-col-md12 top-panel-number" style="color: #28333E;" id="value4">
                        {{$data.today_airdrop_member}}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="layui-col-xs6 layui-col-md2">
        <div class="layui-card top-panel">
            <div class="layui-card-header">累计空投会员</div>
            <div class="layui-card-body">
                <div class="layui-row layui-col-space5">
                    <div class="layui-col-xs12 layui-col-md12 top-panel-number" style="color: #28333E;" id="value4">
                        {{$data.sum_airdrop_member}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- 数据概览统计   -->
<div class="layui-row layui-col-space10">
    <!-- 左边数据概览 -->
<!--    <div class="layui-col-md6">-->
<!--        <div class="layui-card">-->
<!--            <div class="layui-card-header">-->
<!--                待办任务-->
<!--            </div>-->
<!--            <div class="layui-card-body" style="height: 214px;">-->
<!--                <div class="layui-row layui-col-space10">-->
<!--                    <div class="layui-col-md6 layui-col-sm6 layui-col-xs6">-->
<!--                        <div class="pear-card2">-->
<!--                            <div class="title">待发货订单</div>-->
<!--                            <div class="count pear-text">{{$upcoming.order_forwarding}}</div>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                    <div class="layui-col-md6 layui-col-sm6 layui-col-xs6">-->
<!--                        <div class="pear-card2">-->
<!--                            <div class="title">待收货订单</div>-->
<!--                            <div class="count pear-text">{{$upcoming.order_receipt}}</div>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                    <div class="layui-col-md6 layui-col-sm6 layui-col-xs6">-->
<!--                        <div class="pear-card2">-->
<!--                            <div class="title">待审核(提现)</div>-->
<!--                            <div class="count pear-text">{{$upcoming.apply_forwarding}}</div>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
    <!--左边数据概览-->
    <!--右边数据概览-->
<!--    <div class="layui-col-md6">-->
<!--        <div class="layui-card">-->
<!--            <div class="layui-card-header">待发货订单一览</div>-->
<!--            <div class="layui-card-body" style="height: 214px;">-->
<!--                <ul class="pear-card-status">-->
<!--                    {{volist name="order_list" id="vo"}}-->
<!--                    <li>-->
<!--                        <p>-->
<!--                            <span>订单号:{{$vo.order_no}}</span>-->
<!--                            <a href="/admin/trade.goods/details?order_id={{$vo.order_id}}#delivery" data-id="1" class="pear-btn pear-btn-xs pear-reply" style="color:#FFFFFF;background-color: #1890ff">前去发货</a>-->
<!--                        </p>-->
<!--                    </li>-->
<!--                    {{/volist}}-->
<!--                </ul>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
    <!--右边数据概览-->
    
    <div class="layui-col-md6">
        <div class="layui-card">
            <div class="layui-card-header">订单销量</div>
            <div class="layui-card-body">
                <div class="layui-tab custom-tab layui-tab-brief">
                    <div id="knowledge_product" style="background-color:#ffffff;min-height:400px;"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="layui-col-md6">
        <div class="layui-card">
            <div class="layui-card-header">会员注册量</div>
            <div class="layui-card-body">
                <div class="layui-tab custom-tab layui-tab-brief" lay-filter="docDemoTabBrief">
                    <div id="member_product" style="background-color:#ffffff;min-height:400px;"></div>
                </div>
            </div>
        </div>
    </div>

</div>

</body>

<script src="https://gw.alipayobjects.com/os/antv/pkg/_antv.g2-3.5.1/dist/g2.min.js"></script>
<script src="https://gw.alipayobjects.com/os/antv/pkg/_antv.data-set-0.10.1/dist/data-set.min.js"></script>
<script src="https://gw.alipayobjects.com/os/antv/assets/lib/jquery-3.2.1.min.js"></script>
<script type="application/javascript">
    var sortType = 'positive';
    var data = {{$order_value_list}};
    var chart = new G2.Chart({
        container: 'knowledge_product',
        forceFit: true,
        height: 380,
        padding: [20, 20, 30, 90]
    });
    chart.source(data, {
        value: {
            tickCount: 5
        }
    });
    chart.scale('value', {
        alias: '销售额(元)'
    });
    chart.axis('type', {
        label: {
            textStyle: {
                fill: '#aaaaaa'
            }
        },
        tickLine: {
            alignWithLabel: false,
            length: 0
        }
    });
    chart.axis('value', {
        label: {
            textStyle: {
                fill: '#aaaaaa'
            },
            formatter: function formatter(text) {
                return text.replace(/(\d)(?=(?:\d{3})+$)/g, '$1,');
            }
        },
        title: {
            offset: 70
        }
    });
    chart.tooltip({
        share: true
    });
    chart.interval().position('type*value').opacity(1);
    chart.render();

</script>
<script type="application/javascript">
    var sortType = 'positive';
    var data = {{$member_value_list}};
    var chart = new G2.Chart({
        container: 'member_product',
        forceFit: true,
        height: 380,
        padding: [20, 20, 30, 90]
    });
    chart.source(data, {
        value: {
            tickCount: 5
        }
    });
    chart.scale('value', {
        alias: '注册数量(个)'
    });
    chart.axis('type', {
        label: {
            textStyle: {
                fill: '#aaaaaa'
            }
        },
        tickLine: {
            alignWithLabel: false,
            length: 0
        }
    });
    chart.axis('value', {
        label: {
            textStyle: {
                fill: '#aaaaaa'
            },
            formatter: function formatter(text) {
                return text.replace(/(\d)(?=(?:\d{3})+$)/g, '$1,');
            }
        },
        title: {
            offset: 70
        }
    });
    chart.tooltip({
        share: true
    });
    chart.guide().dataMarker({
        top: true,
        content: '因政策调整导致注册数量下滑',
        position: ['2014-01', 1750],
        style: {
            text: {
                fontSize: 13
            }
        },
        lineLength: 30
    });
    chart.interval().position('type*value').opacity(1);
    chart.render();
    $('.sort-button').click(function() {
        sortType = sortType === 'positive' ? 'negative' : 'positive';
        sortData(sortType);
        chart.repaint();
    });

    function sortData(sortType) {
        if (sortType === 'positive') {
            data.sort(function(a, b) {
                return b.value - a.value;
            });
        } else {
            data.sort(function(a, b) {
                return a.value - b.value;
            });
        }
    }
</script>
