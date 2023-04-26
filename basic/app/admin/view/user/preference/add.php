<link rel="stylesheet" href="/assets/admin/css/amui/bladmin.css" />
<link rel="stylesheet" href="/assets/admin/css/goods.css" />
<script src="/assets/admin/amazeui.min.js"></script>
<style>
    .hold_collection{
        display: none;
    }
    .hold_superimposing_collection{
        display: none;
    }
</style>
<style>
    .chose-class-wrap{
        position: relative;
        margin-top: 10px;
    }
    .assort-class-wrap {
        padding: 8px 0 8px 18px;
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        width: 640px;
        height: 92px;
        -webkit-box-align: center;
        -ms-flex-align: center;
        align-items: center;
        background: rgba(245,247,250,0.5);
    }
    .assort-class-img {
        height: 60px;
        -webkit-border-radius: 2px;
        border-radius: 2px;
    }
    .assort-class-img img {
        max-width: 100%;
    }
    .class-title-wrap {
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-orient: vertical;
        -webkit-box-direction: normal;
        -ms-flex-direction: column;
        flex-direction: column;
        -webkit-box-flex: 1;
        -ms-flex: 1;
        flex: 1;
        height: 60px;
        margin: 0 10px;
    }
    .class-title {
        color: #353535;
        -webkit-box-flex: 1;
        -ms-flex: 1;
        flex: 1;
        max-width: 430px;
        white-space: nowrap;
        -o-text-overflow: ellipsis;
        text-overflow: ellipsis;
        overflow: hidden;
    }
    .class-type {
        margin-bottom: auto;
        font-size: 12px;
        color: #666;
    }
    .change-class-btn {
        width: 64px;
        height: 28px;
        margin-left: 10px;
        -webkit-border-radius: 2px;
        border-radius: 2px;
        border: solid 1px #e5e7eb;
        line-height: 26px;
        text-align: center;
        font-size: 12px;
        color: #666;
        cursor: pointer;
        margin-right: 19px;
    }
</style>
<div class="am-cf">
    <div class="row">
        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
            <blockquote class="layui-elem-quote" style="font-family: normal">
                <p>注意:1>标签分组：标签分组是标签内的会员享有优先购权益</p>
                <p>注意:2>持有藏品：根据用户持有的藏品数量进行规则列表设定，符合规则条件的用户可以享有优先购</p>
                <p>注意:3>权益叠加：根据用户持有的藏品的数量；进行1:1额外购买次数赠送</p>
            </blockquote>
            <div class="widget am-cf">
                <form id="my-form" class="am-form tpl-form-line-form" enctype="multipart/form-data" method="post">
                    <div class="widget-body">
                        <fieldset>
                            <div class="widget-head am-cf">
                                <div class="widget-title am-fl">添加优先购</div>
                            </div>
                            <div class="am-form-group layui-form">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">选择优先购藏品</label>
                                <div class="am-u-sm-9 am-u-end" id="course_info">
                                    <input type="hidden" id="goods_id" class="layui-input layui-disabled" name="precedence[goods_id]"  value="">
                                    <i class="layui-btn layui-btn-normal addMember" id="goods_none">
                                        <i class="layui-icon layui-icon-add-1"></i>
                                        选择藏品
                                    </i>
                                    <div style="display: none" id="goods_show" class="chose-class-wrap"><div class="assort-class-wrap">
                                            <img src="" id="goods_thumb" class="assort-class-img">
                                            <div class="class-title-wrap">
                                                <p class="class-title" id="goods_name" ></p>
                                            </div>
                                            <p class="change-class-btn addProduct addMember">更换藏品</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="am-form-group layui-form">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label">规则类型</label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="radio" name="precedence[label_type]" value="1" title="标签分组" checked lay-filter="label_type">
                                    <input type="radio" name="precedence[label_type]" value="2" title="持有藏品" lay-filter="label_type">
                                    <input type="radio" name="precedence[label_type]" value="3" title="权益叠加" lay-filter="label_type">
                                </div>
                            </div>
                            <div id="collection_list" class="collection_list">
                                <div class="am-form-group layui-form">
                                    <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">选择标签</label>
                                    <div class="am-u-sm-9 am-u-end" id="course_info">
                                        <input type="hidden" id="label_id" class="layui-input layui-disabled" name="precedence[label_id]"  value="">
                                        <i class="layui-btn layui-btn-normal addMembers" id="label_none" href="/admin/knowledge.video/add">
                                            <i class="layui-icon layui-icon-add-1"></i>
                                            选择标签
                                        </i>
                                        <div style="display: none" id="label_show" class="chose-class-wrap">
                                            <div style="width: 440px"  class="assort-class-wrap">
                                                <div class="class-title-wrap">
                                                    <p class="class-title" id="label_title" ></p>
                                                </div>
                                                <p class="change-class-btn addProduct addMembers">更换分组</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">提前购买时间 </label>
                                    <div class="am-u-sm-9 am-u-end">
                                        <input type="text" class="tpl-form-input" name="precedence[purchase_time]" value="" placeholder="请输入提前购买的分钟" required>
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">额外购买数量 </label>
                                    <div class="am-u-sm-9 am-u-end">
                                        <input type="text" class="tpl-form-input" name="precedence[purchase_quantity]" value="" placeholder="请输入额外购买的数量" required>
                                    </div>
                                </div>
                            </div>


                            <!--持有藏品的规则-->
                            <div class="hold_collection">
                                <div class="am-form-group layui-form" >
                                    <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">规则列表</label>
                                    <div class="am-u-sm-9 am-u-end" id="course_info">
                                        <i class="layui-btn layui-btn-normal addRule" id="label_none">
                                            <i class="layui-icon layui-icon-add-1"></i>
                                            添加规则
                                        </i>
                                    </div>
                                </div>
                                <div class="am-form-group layui-form">
                                    <label class="am-u-sm-3 am-u-lg-2 am-form-label"></label>
                                    <div class="am-u-sm-9 am-u-end">
                                        <table class="layui-table">
                                            <thead>
                                            <tr>
                                                <th>编号</th>
                                                <th>持有藏品</th>
                                                <th>持有数量</th>
                                                <th>提前时间(分钟)</th>
                                                <th>额外增加购买次数</th>
                                                <th>操作</th>
                                            </tr>
                                            </thead>
                                            <tbody id="push_content">

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <!--持有藏品的规则-->

                            <!--持有藏品的规则-->
                            <div class="hold_superimposing_collection">
                                <div class="am-form-group layui-form" >
                                    <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">规则列表</label>
                                    <div class="am-u-sm-9 am-u-end" id="course_info">
                                        <i class="layui-btn layui-btn-normal addSuperimposingRule" id="label_none">
                                            <i class="layui-icon layui-icon-add-1"></i>
                                            添加藏品规则
                                        </i>
                                    </div>
                                </div>
                                <div class="am-form-group layui-form">
                                    <label class="am-u-sm-3 am-u-lg-2 am-form-label"></label>
                                    <div class="am-u-sm-9 am-u-end">
                                        <table class="layui-table">
                                            <thead>
                                            <tr>
                                                <th>编号</th>
                                                <th>持有藏品</th>
                                                <th>提前时间(分钟)</th>
                                                <th>操作</th>
                                            </tr>
                                            </thead>
                                            <tbody id="push_content_Superimposing">

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <!--持有藏品的规则-->

                            <div class="am-form-group layui-form">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">状态</label>
                                <div class="am-u-sm-4 am-u-end">
                                    <input type="radio" name="precedence[status]" value="1" title="正常" 	  checked>
                                    <input type="radio" name="precedence[status]" value="0" title="禁用" >
                                </div>
                            </div>
                            <div class="am-form-group">
                                <div class="am-u-sm-9 am-u-sm-push-6 am-margin-top-lg">
                                    <button type="submit" class="j-submit am-btn am-btn-secondary">提交

                                    </button>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    layui.use(['form','element','code'], function() {
        var form = layui.form;
        var element = layui.element;

        //是否开启转增
        form.on('radio(label_type)', function(data){
            var $eduPaidMany = $('.collection_list');
            var $hold_collection = $('.hold_collection');
            var $hold_superimposing_collection = $('.hold_superimposing_collection');
            switch (data.value)
            {
                case "1":
                    $eduPaidMany.show();
                    $hold_collection.hide();
                    $hold_superimposing_collection.hide();
                    break;
                case "2":
                    $eduPaidMany.hide()
                    $hold_superimposing_collection.hide()
                    $hold_collection.show();
                    break;
                case "3":
                    $eduPaidMany.hide()
                    $hold_superimposing_collection.show()
                    $hold_collection.hide();
                    break;
            }
        });
        layui.code();
    });

    //选择优先购买藏品
    $(document).on("click",".addMember",function(){
        layer.open({
            type: 2,
            title: '选择优先购藏品',
            shade: 0.1,
            btn:["确定","取消"],
            offset: 'auto',
            area: ['1020px','670px'],
            content: "{{:url('goods')}}?types=yxg"
        });
    })

    //选择优先购买标签分组
    $(document).on("click",".addMembers",function(){
        layer.open({
            type: 2,
            title: '选择优先购标签分组',
            shade: 0.1,
            btn:["确定","取消"],
            offset: 'auto',
            area: ['1020px','670px'],
            content: "{{:url('label')}}"
        });
    })

    /**
     * 添加藏品规则
     */
    $(document).on("click",".addRule",function(){
        layer.open({
            type: 2,
            title: '新增优先购买规则',
            shade: 0.1,
            btn:["确定","取消"],
            offset: 'auto',
            area: ['600px','470px'],
            content: "{{:url('rule')}}",
            yes: function (index, layero) {
                goods_id = layero.find("iframe")[0].contentWindow.$("#goods_id").val();
                goods_name = layero.find("iframe")[0].contentWindow.$("#goods_id").find("option:selected").text();
                hold_goods_number = layero.find("iframe")[0].contentWindow.$("#hold_goods_number").val();
                purchase_time = layero.find("iframe")[0].contentWindow.$("#purchase_time").val(); //提前时间
                purchase_quantity = layero.find("iframe")[0].contentWindow.$("#purchase_quantity").val(); //额外购买催熟
                if(!goods_id || !hold_goods_number || !purchase_time || !purchase_quantity){
                    layer.alert("请先选择持有藏品或者完善相关内容");
                }
                layer.closeAll();
               //组装一个表格的规则 进去 然后展示 提交保存
               let html_content = '<tr>';
               html_content += '<td>'+goods_id+'</td>'
               html_content += '<td>'+goods_name+'</td>'
               html_content += '<td>'+hold_goods_number+'</td>'
               html_content += '<td>'+purchase_time+'</td>'
                html_content += '<td>'+purchase_quantity+'</td>'
                html_content += '<td><button type="button" class="layui-btn layui-btn layui-btn-normal remove_node">删除</button> <input style="display: none" type="text" name="goods[goods_id][]" value="'+goods_id+'" hidden><input style="display: none" type="text" name="goods[hold_goods_number][]" value="'+hold_goods_number+'" hidden><input style="display: none" type="text" name="goods[purchase_time][]" value="'+purchase_time+'" hidden><input style="display: none" type="text" name="goods[purchase_quantity][]" value="'+purchase_quantity+'" hidden></td>'
               html_content += '</tr>'
               $("#push_content").append(html_content)

            },
        });
    })

    $(document).on("click",".remove_node",function(){
        $(this).parent().parent().remove()
    })

    /**
     * 添加藏品规则
     */
    $(document).on("click",".addSuperimposingRule",function(){
        layer.open({
            type: 2,
            title: '新增优先购买规则',
            shade: 0.1,
            btn:["确定","取消"],
            offset: 'auto',
            area: ['600px','470px'],
            content: "{{:url('superimposingrule')}}",
            yes: function (index, layero) {
                goods_id = layero.find("iframe")[0].contentWindow.$("#goods_id").val();
                goods_name = layero.find("iframe")[0].contentWindow.$("#goods_id").find("option:selected").text();
                purchase_time = layero.find("iframe")[0].contentWindow.$("#purchase_time").val(); //提前时间
                if(!goods_id ||  !purchase_time){
                    layer.alert("请先选择持有藏品或者完善相关内容");
                }
                layer.closeAll();
                //组装一个表格的规则 进去 然后展示 提交保存
                let html_content = '<tr>';
                html_content += '<td>'+goods_id+'</td>'
                html_content += '<td>'+goods_name+'</td>'
                html_content += '<td>'+purchase_time+'</td>'
                html_content += '<td><button type="button" class="layui-btn layui-btn layui-btn-normal remove_node">删除</button> <input style="display: none" type="text" name="goods[c_goods_id][]" value="'+goods_id+'" hidden><input style="display: none" type="text" name="goods[c_purchase_time][]" value="'+purchase_time+'" hidden></td>'
                html_content += '</tr>'
                $("#push_content_Superimposing").append(html_content)

            },
        });
    })

    $(function () {
        /**
         * 表单验证提交
         * @type {*}
         */
        $('#my-form').superForm();

    });
</script>
