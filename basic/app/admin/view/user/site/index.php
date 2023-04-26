<link rel="stylesheet" href="/assets/admin/css/amui/bladmin.css" />
<style>
    .widget{
        padding: unset;
    }
</style>
<div class="am-cf">
    <div class="row">
        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
            <div class="widget am-cf">
                <ul class="layui-tab-title store_system">
                    <li class="layui-this"><a href="{{:url('user.site/index')}}">运营配置</a></li>
                    <li><a href="{{:url('user.site/internal')}}">内测名单</a></li>
                </ul>
                <form id="my-form" class="am-form tpl-form-line-form" method="post">
                    <div class="widget-body">
                        <fieldset>
<!--                            <div class="widget-head am-cf">-->
<!--                                <div class="widget-title am-fl">数据重置</div>-->
<!--                            </div>-->
<!--                            <div class="am-form-group">-->
<!--                                <div class="am-u-sm-9 am-u-sm-push-3 am-margin-top-lg">-->
<!--                                    <button type="button" class="am-btn am-btn-secondary leaderboard_reset">数据重置</button>-->
<!--                                </div>-->
<!--                            </div>-->
                            <div class="widget-head am-cf">
                                <div class="widget-title am-fl">运营配置</div>
                            </div>
                            <div class="am-form-group layui-form">
                                <label class="am-u-sm-2 am-form-label form-require">运营模式</label>
                                <div class="am-u-sm-10 layui-form">
                                    {{if isset($values['open_type'])}}
                                    <input type="radio" name="site_setting[open_type]" value="1" title="内测模式"   <?= $values['open_type'] === '1' ? 'checked' : '' ?>>
                                    <input type="radio" name="site_setting[open_type]" value="2" title="公测运营"  <?= $values['open_type'] === '2' ? 'checked' : '' ?>>
                                    {{else /}}
                                    <input type="radio" name="site_setting[open_type]" value="1" title="内测模式">
                                    <input type="radio" name="site_setting[open_type]" value="2" title="公测运营" checked>
                                    {{/if}}
                                </div>
                            </div>
                            <div class="am-form-group">
                                <div class="am-u-sm-9 am-u-sm-push-3 am-margin-top-lg">
                                    <button type="submit" class="j-submit am-btn am-btn-secondary">提交保存
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
<script src="/assets/admin/amazeui.min.js"></script>
<script>
    $(function () {
        /**
         * 表单验证提交
         * @type {*}
         */
        $('#my-form').superForm();

        /**
         * 排行榜数据重置
         */
        $(document).on("click",".leaderboard_reset",function (){
            layer.confirm('确认重置排行榜吗？重置后之前数据无法找回', {
                icon: 3,
                title: '提示'
            }, function(index) {
                layer.close(index);
                let loading = layer.load();
                $.getJSON("{{:url('application.task/resetLeaderboardData')}}", function(result){
                    layer.close(loading);
                    if(1 === result.code){
                        layer.msg(result.msg, {
                            icon: 1,
                            time: 1000
                        }, function() {

                        });
                    }else{
                        layer.msg(result.msg, {
                            icon: 2,
                            time: 1000
                        });
                    }
                });
            });
        })
    });
</script>
