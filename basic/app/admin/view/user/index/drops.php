<link rel="stylesheet" href="/assets/admin/css/amui/bladmin.css" />
<link rel="stylesheet" href="/assets/admin/css/goods.css" />
<script src="/assets/admin/amazeui.min.js"></script>
<style>
    .hold_goods_list{
        display: none;
    }
    .hold_goods{
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
                <p>注意:1>标签空投：标签空投是根据标签内的会员进行空投藏品</p>
                <p>注意:2>持有特定藏品：根据用户持有的藏品进行空投，空投藏品数量自己填写</p>
                <p>注意:3>叠加空投：根据用户持有的藏品的数量；进行一比一空投</p>
                <p>注意:4>空投采用异步队列发放空投藏品,藏品到账会有延迟请悉知！！！</p>
            </blockquote>
            <div class="widget am-cf">

                <form id="my-form" class="am-form tpl-form-line-form" enctype="multipart/form-data" method="post">
                    <div class="widget-body">
                        <fieldset>
                            <div class="widget-head am-cf">
                                <div class="widget-title am-fl">空投藏品 </div>
                            </div>
                            <div class="am-form-group layui-form">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">选择空投藏品</label>
                                <div class="am-u-sm-9 am-u-end" id="course_info">
                                    <input type="hidden" id="goods_id" class="layui-input layui-disabled" name="precedence[goods_id]"  value="">
                                    <i class="layui-btn layui-btn-normal addMember" id="goods_none" href="/admin/knowledge.video/add">
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
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label">空投条件</label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="radio" name="precedence[label_type]" value="1" title="标签空投" checked lay-filter="label_type">
                                    <input type="radio" name="precedence[label_type]" value="2" title="持有特定藏品" lay-filter="label_type">
                                    <input type="radio" name="precedence[label_type]" value="3" title="叠加空投" lay-filter="label_type">
                                </div>
                            </div>

                            <div class="collection_list">
                                <div class="am-form-group layui-form">
                                    <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">选择空投的分组</label>
                                    <div class="am-u-sm-9 am-u-end" id="course_info">
                                        <input type="hidden" id="label_id" class="layui-input layui-disabled" name="precedence[label_id]"  value="">
                                        <i class="layui-btn layui-btn-normal addMembers" id="label_none" href="/admin/knowledge.video/add">
                                            <i class="layui-icon layui-icon-add-1"></i>
                                            选择分组
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
                            </div>

                            <div class="hold_goods_list">
                                <div class="am-form-group">
                                    <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">持有藏品</label>
                                    <div class="am-u-sm-9 am-u-end">
                                        <select name="precedence[goods_ids][]"  multiple data-am-selected="{searchBox: 1, btnSize: 'sm',  placeholder:'请选择所属分组',maxHeight:'200px'}">
                                            <option value=""></option>
                                            <?php if (isset($member_goods_list)): foreach ($member_goods_list as $first): ?>
                                                <option value="<?= $first['goods_id'] ?>"><?= $first['goods_name'] ?></option>
                                            <?php endforeach; endif; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">空投数量 </label>
                                    <div class="am-u-sm-9 am-u-end">
                                        <input type="text" class="tpl-form-input" name="precedence[airdrop_number]" value="" placeholder="请输入空投藏品的数量">
                                    </div>
                                </div>

                            </div>

                            <div class="hold_goods">
                                <div class="am-form-group">
                                    <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">持有藏品</label>
                                    <div class="am-u-sm-9 am-u-end">
                                        <select name="precedence[c_goods_id]" data-am-selected="{searchBox: 1, btnSize: 'sm',  placeholder:'请选择持有藏品',maxHeight:'200px'}">
                                            <option value=""></option>
                                            <?php if (isset($member_goods_list)): foreach ($member_goods_list as $first): ?>
                                                <option value="<?= $first['goods_id'] ?>"><?= $first['goods_name'] ?></option>
                                            <?php endforeach; endif; ?>
                                        </select>
                                    </div>
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
    goods_type = '2';
    layui.use(['form','element','code','laydate'], function() {
        var form = layui.form;
        var laydate = layui.laydate;
        //是否开启转增
        form.on('radio(label_type)', function(data){
            var $eduPaidMany = $('.collection_list');
            var $holdGoodsList = $('.hold_goods_list');
            var $holdGoods = $('.hold_goods');
            switch (data.value)
            {
                case "1":
                    $eduPaidMany.show();
                    $holdGoodsList.hide();
                    $holdGoods.hide();
                    break;
                case "2":
                    $eduPaidMany.hide()
                    $holdGoods.hide()
                    $holdGoodsList.show();
                    break;
                case "3":
                    $eduPaidMany.hide()
                    $holdGoodsList.hide();
                    $holdGoods.show();
                    break;
            }
        });
    });
    $(function () {
        /**
         * 表单验证提交
         * @type {*}
         */
        $('#my-form').superForm();
    });


    $(document).on("click",".addMember",function(){
        layer.open({
            type: 2,
            title: '选择空投藏品',
            shade: 0.1,
            btn:["确定","取消"],
            offset: 'auto',
            area: ['1020px','670px'],
            content: "{{:url('user.preference/goods')}}?types=kt"
        });
    })
    $(document).on("click",".addMembers",function(){
        layer.open({
            type: 2,
            title: '选择空投分组',
            shade: 0.1,
            btn:["确定","取消"],
            offset: 'auto',
            area: ['1020px','670px'],
            content: "{{:url('user.preference/label')}}"
        });
    })

</script>
