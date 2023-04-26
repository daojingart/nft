<link rel="stylesheet" href="/assets/admin/css/amui/bladmin.css" />
<div class="am-cf">
    <div class="row">
        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
            <div class="widget am-cf">
                <form id="my-form" class="am-form tpl-form-line-form" method="post">
                    <div class="widget-body">
                        <fieldset>
                            <div class="widget-head am-cf">
                                <div class="widget-title am-fl">添加银行</div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">银行图标</label>
                                <div class="am-u-sm-9 am-u-end">
                                    <div class="am-form-file">
                                        <div class="am-form-file">
                                            <button type="button" class="upload-file am-btn am-btn-secondary am-radius">
                                                <i class="am-icon-cloud-upload"></i> 选择图片
                                            </button>
                                            <div class="uploader-list am-cf">
                                                <img src="{{$data.thumb}}" alt="">
                                                <input type="hidden" name="card[thumb]" value="{{$data.thumb}}">
                                                <input type="hidden" name="card[id]" value="{{$data.id}}">
                                            </div>
                                        </div>
                                        <div class="help-block am-margin-top-sm">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="am-form-group layui-form">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label">银行名称 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="text" name="card[title]" value="{{$data.title}}" title="" >
                                </div>
                            </div>
                            <div class="am-form-group layui-form">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label">颜色选择 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <div style="margin-left: 30px;">
                                        <form class="layui-form" action="">
                                            <div class="layui-form-item">
                                                <div class="layui-input-inline" style="width: 120px;">
                                                    <input type="text" value="{{$data.color}}" name="card[color]" placeholder="请选择颜色" class="layui-input" id="test-form-input">
                                                </div>
                                                <div class="layui-inline" style="left: -11px;">
                                                    <div id="test-form"></div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <div class="am-u-sm-9 am-u-sm-push-3 am-margin-top-lg">
                                    <button type="submit" class="j-submit am-btn am-btn-secondary">确认提交</button>
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
{{include file="layouts/_template/tpl_file_link_item" /}}
<!-- 文件库弹窗 -->
{{include file="layouts/_template/file_library" /}}

<script src="/assets/admin/amazeui.min.js"></script>
<script>
    layui.use(['form','element','code','colorpicker'], function() {
        var form = layui.form;
        var element = layui.element;
        var $ = layui.$
            ,colorpicker = layui.colorpicker;
        layui.code();
        //表单赋值
        colorpicker.render({
            elem: '#test-form'
            ,color: '#1c97f5'
            ,done: function(color){
                $('#test-form-input').val(color);
            }
        });

        //RGB 、RGBA
        colorpicker.render({
            elem: '#test3'
            ,color: 'rgb(68,66,66)'
            ,format: 'rgb' //默认为 hex
        });
        colorpicker.render({
            elem: '#test4'
            ,color: 'rgba(68,66,66,0.5)'
            ,format: 'rgb'
            ,alpha: true //开启透明度滑块
        });
    });
    $(function () {

        // 选择图片
        $('.upload-file').selectImages({
            name: 'card[thumb]',
        });

        /**
         * 表单验证提交
         * @type {*}
         */
        $('#my-form').superForm();

    });
</script>
