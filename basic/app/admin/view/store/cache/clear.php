<link rel="stylesheet" href="/assets/admin/css/amui/bladmin.css" />
<script src="/assets/admin/amazeui.min.js"></script>
<style>
    .layui-form-checkbox {
        position: relative;
        height: 35px;
        line-height: 35px;
        margin-right: 20px;
        padding-right: 30px;
        cursor: pointer;
        font-size: 0;
        -webkit-transition: .1s linear;
        transition: .1s linear;
        box-sizing: border-box;
    }
    .layui-form-checkbox i {
        position: absolute;
        right: 0;
        top: 0;
        width: 30px;
        height: 35px;
        border: 1px solid #d2d2d2;
        border-left: none;
        border-radius: 0 2px 2px 0;
        color: #fff;
        font-size: 20px;
        text-align: center;
    }
    .layui-form-checked i, .layui-form-checked:hover i {
        color: #FFB800 !important;
    }
    .layui-form-checked span, .layui-form-checked:hover span {
        background-color: #FFB800;
    }
</style>
<div class="am-cf">
    <div class="row">
        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
            <div class="widget am-cf">
                <form id="my-form" class="am-form tpl-form-line-form" enctype="multipart/form-data" method="post">
                    <div class="widget-body">
                        <fieldset>
                            <div class="widget-head am-cf">
                                <div class="widget-title am-fl">清理缓存</div>
                            </div>

                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-form-label form-require">
                                    数据缓存
                                </label>
                                <div class="layui-input-block layui-form">
                                    <?php foreach ($cacheList as $key => $item): ?>
                                        <input value="<?= $key ?>" type="checkbox" name="cache[keys][]" title="<?= $item['name'] ?>" checked>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php if (isset($isForce) && $isForce === true): ?>
                                <div class="am-form-group layui-form">
                                    <label class="am-u-sm-3 am-form-label form-require"> 强制模式 </label>
                                    <div class="am-u-sm-9 ">
                                        <input type="radio" name="cache[isForce]" value="0" title="否" checked>
                                        <input type="radio" name="cache[isForce]" value="1" title="是">
                                        <div class="help-block">
                                            <small class="x-color-red">此操作将会强制清空缓存目录，包含用户授权登录状态、用户购物车数据，仅允许在开发环境中使用</small>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="am-form-group">
                                    <div class="am-u-sm-9 am-u-sm-push-3">
                                        <small>
                                            <a href="<?= url('', ['isForce' => true]) ?>">
                                                进入强制模式</a>
                                        </small>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <div class="am-form-group">
                                <div class="am-u-sm-9 am-u-sm-push-3 am-margin-top-lg am-u-lg-offset-2">
                                    <button type="submit" class="j-submit am-btn am-btn-secondary">
                                        确认提交
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
    });
    $(function () {

        /**
         * 表单验证提交
         * @type {*}
         */
        $('#my-form').superForm();

    });
</script>
