<link rel="stylesheet" href="/assets/admin/css/amui/bladmin.css" />
<link rel="stylesheet" href="/assets/admin/css/goods.css" />
<script src="/assets/admin/amazeui.min.js"></script>
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
<div class="am-cf" style="overflow: hidden">
    <div class="row">
        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
            <blockquote class="layui-elem-quote" style="font-family: normal">
                <p>注意:1>合成结果可以添加多个藏品,合成结果为随机合成</p>
                <p>注意:2>合成概率配置,总概率为10 10则百分之百合成,低于10则概率随机合成，合成失败消耗的材料不进行退回</p>
                <p>注意:3>发行总量是指可以合成的库存和合成的藏品库存无关</p>
            </blockquote>
            <div class="widget am-cf">
                <form id="my-form" class="am-form tpl-form-line-form" enctype="multipart/form-data" method="post">
                    <div class="widget-body">
                        <fieldset>
                            <div class="widget-head am-cf">
                                <div class="widget-title am-fl">添加合成商品</div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">合成名称</label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="text" name="synthetic[name]">
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">合成藏品</label>
                                <div class="am-u-sm-9 am-u-end">
                                    <select name="synthetic[goods_ids][]"  multiple data-am-selected="{searchBox: 1, btnSize: 'sm',  placeholder:'请选择合成的藏品支持多选',maxHeight:'200px'}">
                                        <option value=""></option>
                                        <?php if (isset($goods_list)): foreach ($goods_list as $first): ?>
                                            <option value="<?= $first['goods_id'] ?>"><?= $first['goods_name'] ?></option>
                                        <?php endforeach; endif; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">发行总量</label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="text" name="synthetic[count]">
                                    <small>发行总量 是指合成的库存量,请合理设置库存量</small>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">成功概率</label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="text" name="synthetic[chance]" value="5">
                                    <small>合成总概率为10,请填写小于10 的数字</small>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">排序</label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="text" name="synthetic[sort]">
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">白名单提前时间(分钟)</label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="text" name="synthetic[whitelist_time]" placeholder="请输入提前进场的时间分钟">
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">选择时间</label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="text" name="synthetic[time]" autocomplete="off" id="test6" >
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">合成介绍</label>
                                <div class="am-u-sm-9 am-u-end">
                                    <textarea id="container" name="synthetic[content]"></textarea>
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
<link rel="stylesheet" href="/assets/admin/modules/umeditor/themes/default/css/umeditor.css">


<script src="/assets/admin/modules/umeditor/umeditor.config.js"></script>
<script src="/assets/admin/modules/umeditor/umeditor.min.js"></script>
<script>
    layui.use(['form','element','code','laydate'], function() {
        var form = layui.form;
        var laydate = layui.laydate;
        //日期范围
        laydate.render({
            elem: '#test6'
            ,type: 'datetime'
            ,range: '~'
        });

    });


    $(function () {
        UM.getEditor('container');

        /**
         * 表单验证提交
         * @type {*}
         */
        $('#my-form').superForm();
    });
</script>
