<link rel="stylesheet" href="/assets/admin/css/order.css" />
<link rel="stylesheet" href="/assets/admin/css/amui/bladmin.css" />
<script src="/assets/admin/amazeui.min.js"></script>
<body class="pear-container pear-container">
<div class="layui-card">
    <div class="layui-card-body">
        <div class="layui-tab layui-tab-brief" lay-filter="shopOrder">
            <ul class="layui-tab-title">
                <li class="layui-this" data-template="balance" >余额支付</li>
                <li data-template="pay_wx" >微信支付</li>
                <li data-template="pay_alipay">支付宝支付</li>
                <li data-template="pay_card">连连支付</li>
                <li data-template="pay_sxybank">首信易支付</li>
                <li data-template="pay_sd">杉德收银台支付</li>
                <li data-template="pay_ada">汇付通支付(adaPay)</li>
                <li data-template="pay_hy">汇元银通</li>
                <li data-template="pay_hf">汇付通钱包(hfPay)</li>
                <li data-template="pay_yeepay">易宝支付</li>
            </ul>
            <!--内容区域-->
            <div class="layui-tab-content" id="table_content">
                <div class="layui-tab-item layui-show" id="content_initialization">

                </div>
            </div>
        </div>
    </div>
</div>
</body>
{{include file="store/pay/_template/pay_wx"/}}
{{include file="store/pay/_template/balance"/}}
{{include file="store/pay/_template/pay_alipay"/}}
{{include file="store/pay/_template/pay_card"/}}
{{include file="store/pay/_template/pay_sxybank"/}}
{{include file="store/pay/_template/pay_sd"/}}
{{include file="store/pay/_template/pay_ada"/}}
{{include file="store/pay/_template/pay_hy"/}}
{{include file="store/pay/_template/pay_hf"/}}
{{include file="store/pay/_template/pay_yeepay"/}}
<script>
    let data_value = {{$value}};
    let element;
    layui.use(['table', 'form', 'jquery','element'], function() {
         element = layui.element;
        let form = layui.form;
        let $ = layui.jquery;
        //一些事件触发
        element.on('tab(shopOrder)', function(data){
            let that = $(this);
            $("#content_initialization").html(template(that.attr('data-template'),data_value));
            form.render();
        });
        console.log(data_value);
        $("#content_initialization").html(template('balance',data_value));
        form.render();
    });
</script>