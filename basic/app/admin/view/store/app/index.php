<link rel="stylesheet" href="/assets/admin/css/order.css" />
<link rel="stylesheet" href="/assets/admin/css/amui/bladmin.css" />
<link rel="stylesheet" href="/assets/admin/modules/umeditor/themes/default/css/umeditor.css">

<script src="/assets/admin/amazeui.min.js"></script>
<script src="/assets/admin/modules/umeditor/umeditor.config.js"></script>
<script src="/assets/admin/modules/umeditor/umeditor.min.js"></script>
<body class="pear-container pear-container">
<div class="layui-card">
    <div class="layui-card-body">
        <div class="layui-tab layui-tab-brief" lay-filter="shopOrder">
            <ul class="layui-tab-title">
                <li class="layui-this" data-template="wx_system">微信配置</li>
                <li data-template="template_system">升级配置</li>
                <li data-template="share_system">引导页配置</li>
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
<!-- 图片文件列表模板 -->
{{include file="layouts/_template/tpl_file_link_item" /}}
<!-- 文件库弹窗 -->
{{include file="layouts/_template/file_library" /}}
{{include file="store/app/_template/wxSystem"/}}
{{include file="store/app/_template/templateSystem"/}}
{{include file="store/app/_template/shareSystem"/}}
{{include file="store/app/_template/protocol"/}}

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
        });
        console.log(data_value);
        $("#content_initialization").html(template('wx_system',data_value));
    });
</script>