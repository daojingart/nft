<link rel="stylesheet" href="/assets/admin/css/amui/bladmin.css" />
<link rel="stylesheet" href="/assets/admin/css/goods.css" />
<script src="/assets/admin/amazeui.min.js"></script>
<div class="am-cf">
    <div class="row">
        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
            <div class="widget am-cf">
                <form id="my-form" class="am-form tpl-form-line-form" enctype="multipart/form-data" method="post">
                    <div class="widget-body">
                        <fieldset>
                            <div class="widget-head am-cf">
                                <div class="widget-title am-fl">转增</div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">转增数量 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="text" class="tpl-form-input" name="count" value="" placeholder="转增数量" required>
                                </div>
                            </div>

                            <div class="am-form-group layui-form">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">选择转增用户 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <select style="display: none" name="phone" lay-verify="required" lay-search="">
                                        {{volist name="member" id="vo"}}
                                        <option value="{{$vo.phone}}">{{$vo.phone}}</option>
                                        {{/volist}}
                                    </select>
                                </div>
                            </div>


                            <div class="widget-head am-cf">
                                <div class="widget-title am-fl">选择赠送藏品</div>
                            </div>
                            <div class="am-form-group layui-form">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">藏品 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <select style="display: none" name="goods_id" lay-verify="required" lay-search="">
                                        {{volist name="member_goods" id="vo"}}
                                        <option value="{{$vo.goods_id}}">{{$vo.goods_name}}</option>
                                        {{/volist}}
                                    </select>
                                </div>
                            </div>

                            <div class="am-form-group">
                                <div class="am-u-sm-9 am-u-sm-push-6 am-margin-top-lg">
                                    <button type="submit" class="j-submit am-btn am-btn-secondary">提交</button>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- 图片文件列表模板 -->
{{include file="layouts/_template/tpl_file_item" /}}
<!-- 文件库弹窗 -->
{{include file="layouts/_template/file_library" /}}
<script>
    layui.use(['form','element','code','laydate'], function() {
        var form = layui.form;
        var laydate = layui.laydate;
        // laydate.render({
        //     elem: '#date1'
        //     ,type: 'time'
        //     ,range: true
        // });
        laydate.render({
            elem: '#shijian'
            ,type: 'time'
            ,range: true
        });
        form.on('radio(spec_type)', function(data){
            var $goodsSpecMany = $('.goods-spec-many')
                , $goodsSpecSingle = $('.goods-spec-single');
            if (data.value === '10') {
                $goodsSpecMany.hide() && $goodsSpecSingle.show();
            } else {
                $goodsSpecMany.show() && $goodsSpecSingle.hide();
            }
        });
    });
    $(function () {
        // 富文本编辑器
        // 选择图片
        $('.upload-file').selectImages({
            name: 'stores[avatarUrl]'
            , multiple: true
        });
        $('.upload-file12').selectImages({
            name: 'stores[oblong_image]'
            , multiple: true
        });
        /**
         * 表单验证提交
         * @type {*}
         */
        $('#my-form').superForm();
    });
</script>
