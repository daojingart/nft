<link rel="stylesheet" href="/assets/admin/css/amui/bladmin.css" />
<script src="/assets/admin/amazeui.min.js"></script>
<style>
    .am-checkbox, .am-checkbox-inline, .am-radio, .am-radio-inline {
        padding-left: 0;
    }
</style>
<div class="am-cf">
    <div class="row">
        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
            <div class="widget am-cf">
                <form id="my-form" class="am-form tpl-form-line-form" method="post">
                    <div class="widget-body">
                        <fieldset>
                            <div class="widget-head am-cf">
                                <div class="widget-title am-fl">编辑运费模版</div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">模版名称 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="text" class="tpl-form-input" name="delivery[name]"
                                           value="{{$model['name']}}" required>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">计费方式 </label>
                                <div class="am-u-sm-9 am-u-end layui-form">
                                    <label class="am-radio-inline">
                                        <input type="radio" name="delivery[method]" value="10" title="按件数"  {{$model['method']['value'] == '10' ? 'checked' : ''}}>
                                    </label>
                                    <label class="am-radio-inline">
                                        <input type="radio" name="delivery[method]" value="20" title="按重量"  {{$model['method']['value'] == '20' ? 'checked' : ''}}>
                                    </label>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">
                                    配送区域及运费
                                </label>
                                <div class="am-u-sm-9 am-u-lg-10 am-u-end">
                                    <div class=" am-scrollable-horizontal">
                                        <table class="regional-table am-table am-table-bordered
                                         am-table-centered am-margin-bottom-xs">
                                            <tbody>
                                            <tr>
                                                <th width="42%">可配送区域</th>
                                                <th>
                                                    <span class="first">
                                                       {{$model['method']['value'] == 10 ? '首件 (个)' : '首重 (Kg)'}}
                                                    </span>
                                                </th>
                                                <th>运费 (元)</th>
                                                <th>
                                                    <span class="additional">
                                                          {{$model['method']['value'] == 10 ? '续件 (个)' : '续重 (Kg)'}}
                                                    </span>
                                                </th>
                                                <th>续费 (元)</th>
                                            </tr>
                                            {{volist name="$model['rule']" id="item"}}

                                                <tr>
                                                    <td class="am-text-left">
                                                        <p class="selected-content am-margin-bottom-xs">
                                                           {{$item['region_content']}}
                                                        </p>
                                                        <p class="operation am-margin-bottom-xs">
                                                            <a class="edit" href="javascript:;">编辑</a>
                                                            <a class="delete" href="javascript:;">删除</a>
                                                        </p>
                                                        <input type="hidden" name="delivery[rule][region][]"
                                                               value="{{$item['region']}}">
                                                    </td>
                                                    <td>
                                                        <input type="number" name="delivery[rule][first][]"
                                                               value="{{$item['first']}}" required>
                                                    </td>
                                                    <td>
                                                        <input type="number" name="delivery[rule][first_fee][]"
                                                               value="{{$item['first_fee']}}" required>
                                                    </td>
                                                    <td>
                                                        <input type="number" name="delivery[rule][additional][]"
                                                               value="{{$item['additional']}}">
                                                    </td>
                                                    <td>
                                                        <input type="number" name="delivery[rule][additional_fee][]"
                                                               value="{{$item['additional_fee']}}">
                                                    </td>
                                                </tr>
                                            {{/volist}}
                                            <tr>
                                                <td colspan="5" class="am-text-left">
                                                    <a class="add-region am-btn am-btn-default am-btn-xs"
                                                       href="javascript:;">
                                                        <i class="iconfont icon-dingwei"></i>
                                                        点击添加可配送区域和运费
                                                    </a>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">排序 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="number" class="tpl-form-input" name="delivery[sort]"
                                           value="{{$model['sort']}}" required>
                                    <small>数字越小越靠前</small>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <div class="am-u-sm-9 am-u-sm-push-3 am-margin-top-lg am-u-lg-offset-3">
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
<div class="regional-choice"></div>
<script src="/assets/admin/modules/delivery.js"></script>
<script>
    $(function () {
        // 初始化区域选择界面
        var datas = JSON.parse('<?= $regionData ?>');
        // 配送区域表格
        new Delivery({
            table: '.regional-table',
            regional: '.regional-choice',
            datas: datas
        });

        /**
         * 表单验证提交
         * @type {*}
         */
        $('#my-form').superForm();

    });
</script>
