<link rel="stylesheet" href="/assets/admin/css/amui/bladmin.css" />
<link rel="stylesheet" href="/assets/admin/css/goods.css" />
<link rel="stylesheet" href="/assets/admin/modules/umeditor/themes/default/css/umeditor.css">
<script src="/assets/admin/amazeui.min.js"></script>
<script src="/assets/admin/modules/umeditor/umeditor.config.js"></script>
<script src="/assets/admin/modules/umeditor/umeditor.min.js"></script>
<style>
    .am-form .am-form-file .upload-file1{
        font-size: 1.24rem;
        padding: 0.6em 1em;
    }
    .edu-paid-trainee{
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
            <div class="widget am-cf">
                <form id="my-form" class="am-form tpl-form-line-form" method="post">
                    <input type="hidden" name="product[product_id]" value="{{$data.product_id?:''}}">
                    <div class="widget-body">
                        <fieldset>
                            <div class="widget-head am-cf">
                                <div class="widget-title am-fl">基本信息</div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">产品名称 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="text" class="tpl-form-input" name="product[product_name]" value="{{$data.product_name?:''}}" placeholder="请输入产品名称" required>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">所属分类</label>
                                <div class="am-u-sm-9 am-u-end">
                                    <select name="product[category_id]" required data-am-selected="{searchBox: 1, btnSize: 'sm',  placeholder:'请选择分类',maxHeight:'200px'}">
                                        <option value=""></option>
                                        <?php if (isset($list)): foreach ($list as $first): ?>
                                            <option value="<?= $first['category_id'] ?>" <?= $data['category_id'] == $first['category_id'] ? 'selected' : '' ?>><?= $first['name'] ?></option>
                                        <?php endforeach; endif; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">封面图 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <div class="am-form-file">
                                        <div class="am-form-file">
                                            <button type="button" class="upload-file am-btn am-btn-secondary am-radius">
                                                <i class="am-icon-cloud-upload"></i> 选择图片
                                            </button>
                                            <div class="uploader-list am-cf">
                                                <div class="file-item">
                                                    <img src="{{$data.thumbnail?:''}}">
                                                    <input type="hidden" name="product[thumbnail]" value="{{$data.thumbnail?:''}}">
                                                    <i class="iconfont icon-shanchu file-item-delete"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="help-block am-margin-top-sm">
                                            <small>尺寸待定</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">详情图 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <div class="am-form-file">
                                        <div class="am-form-file">
                                            <button type="button" class="upload-file1 am-btn am-btn-secondary am-radius">
                                                <i class="am-icon-cloud-upload"></i> 选择图片
                                            </button>
                                            <div class="uploader-list am-cf">
                                                {{volist name="data.image" id="item"}}
                                                <div class="file-item">
                                                    <img src="{{$item.image?:''}}">
                                                    <input type="hidden" name="product[image][]" value="{{$item.image?:''}}">
                                                    <i class="iconfont icon-shanchu file-item-delete"></i>
                                                </div>
                                                {{/volist}}
                                            </div>
                                        </div>
                                        <div class="help-block am-margin-top-sm">
                                            <small>尺寸待定</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="widget-head am-cf">
                                <div class="widget-title am-fl">规格/库存</div>
                            </div>

                            <div class="am-form-group layui-form">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">商品规格 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="radio" name="product[spec_type]" value="10" title="单规格" lay-filter="spec_type"	{{$data.spec_type=='10'?'checked':''}}>
                                    <input type="radio" name="product[spec_type]" value="20" title="多规格" lay-filter="spec_type" {{$data.spec_type=='20'?'checked':''}}>
                                </div>
                            </div>

                            <!-- 商品多规格 -->
                            <div class="goods-spec-many am-form-group"
                                 style="display: <?= $data['spec_type'] == 20 ? 'block' : 'none' ?>;">
                                <div class="goods-spec-box am-u-sm-9 am-u-sm-push-2 am-u-end">
                                    <!-- 规格属性 -->
                                    <div class="spec-attr"></div>

                                    <!-- 添加规格：按钮 -->
                                    <div class="spec-group-button">
                                        <button type="button" class="btn-addSpecGroup am-btn">添加规格</button>
                                    </div>

                                    <!-- 添加规格：表单 -->
                                    <div class="spec-group-add">
                                        <div class="spec-group-add-item am-form-group">
                                            <label class="am-form-label form-require">规格名 </label>
                                            <input type="text" class="input-specName tpl-form-input"
                                                   placeholder="请输入规格名称">
                                        </div>
                                        <div class="spec-group-add-item am-form-group">
                                            <label class="am-form-label form-require">规格值 </label>
                                            <input type="text" class="input-specValue tpl-form-input"
                                                   placeholder="请输入规格值">
                                        </div>
                                        <div class="spec-group-add-item am-margin-top">
                                            <button type="button" class="btn-addSpecName am-btn am-btn-xs
                                            am-btn-secondary"> 确定
                                            </button>
                                            <button type="button" class="btn-cancleAddSpecName am-btn am-btn-xs
                                              am-btn-default"> 取消
                                            </button>
                                        </div>
                                    </div>
                                    <!-- 商品多规格sku信息 -->
                                    <div class="goods-sku am-scrollable-horizontal">
                                        <!-- 分割线 -->
                                        <div class="goods-spec-line am-margin-top-lg am-margin-bottom-lg"></div>
                                        <!-- sku 批量设置 -->
                                        <div class="spec-batch am-form-inline">
                                            <div class="am-form-group">
                                                <label class="am-form-label">批量设置</label>
                                            </div>
                                            <div class="am-form-group">
                                                <input type="text" data-type="goods_no" placeholder="商家编码">
                                            </div>
                                            <div class="am-form-group">
                                                <input type="number" data-type="goods_price" placeholder="商品原价">
                                            </div>
                                            <div class="am-form-group">
                                                <input type="number" data-type="stock_num" placeholder="库存数量">
                                            </div>
                                            <div class="am-form-group">
                                                <button type="button" class="btn-specBatchBtn am-btn am-btn-sm am-btn-secondary
                                                 am-radius">确定
                                                </button>
                                            </div>
                                        </div>
                                        <!-- sku table -->
                                        <table class="spec-sku-tabel am-table am-table-bordered am-table-centered
                                     am-margin-bottom-xs am-text-nowrap"></table>
                                    </div>
                                </div>
                            </div>
                            <!-- 商品单规格 -->
                            <div class="goods-spec-single"
                                 style="display: <?= $data['spec_type'] == 10 ? 'block' : 'none' ?>;">
                                <div class="am-form-group">
                                    <label class="am-u-sm-3 am-u-lg-2 am-form-label">商品编码 </label>
                                    <div class="am-u-sm-9 am-u-end">
                                        <input type="text" class="tpl-form-input" name="product[spec][goods_no]"
                                               value="<?= $data['spec'][0]['goods_no'] ?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">商品售价</label>
                                    <div class="am-u-sm-9 am-u-end">
                                        <input type="number" class="tpl-form-input" name="product[spec][goods_price]"
                                               value="<?= $data['spec'][0]['goods_price'] ?>" required>
                                    </div>
                                </div>

                                <div class="am-form-group">
                                    <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">当前库存数量 </label>
                                    <div class="am-u-sm-9 am-u-end">
                                        <input type="number" class="tpl-form-input" name="product[spec][stock_num]"
                                               value="<?= $data['spec'][0]['stock_num'] ?>" required>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                            <div class="widget-head am-cf">
                                <div class="widget-title am-fl">销量设置</div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">初始销量 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="number" name="product[sales_initial]" placeholder="请输入实际销量" class="layui-input" value="{{$data.sales_initial?:''}}">
                                </div>
                            </div>
                            <div class="widget-head am-cf">
                                <div class="widget-title am-fl">详情</div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label">产品详情 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <textarea id="container" name="product[content]" type="text/plain">{{$data.content?:''}}</textarea>
                                </div>
                            </div>

                            <div class="widget-head am-cf">
                                <div class="widget-title am-fl">其他</div>
                            </div>
                        <div class="am-form-group">
                            <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">运费模板 </label>
                            <div class="am-u-sm-9 am-u-end">
                                <select name="product[delivery_id]" required
                                        data-am-selected="{searchBox: 1, btnSize: 'sm',  placeholder:'请选择运费模板'}">
                                    <option value="">请选择运费模板</option>
                                    <?php foreach ($delivery as $item): ?>
                                        <option value="<?= $item['delivery_id'] ?>"
                                            <?= $data['delivery_id'] == $item['delivery_id'] ? 'selected' : '' ?>>
                                            <?= $item['name'] ?> (<?= $item['method']['text'] ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="am-margin-left-xs">
                                    <a href="<?= url('goods.delivery/add') ?>">去添加</a>
                                </small>
                            </div>
                        </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">排序 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="number" name="product[sort]" placeholder="请输入排序值" class="layui-input" value="{{$data.sort?:''}}">
                                </div>
                            </div>
                            <div class="am-form-group layui-form">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label">状态 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="radio" name="product[goods_status]" value="10" title="上架" {{if $data.goods_status==10}}checked{{/if}} >
                                    <input type="radio" name="product[goods_status]" value="20" title="下架" {{if $data.goods_status==20}}checked{{/if}}>
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
{{include file="layouts/_template/spec_many" /}}
<script src="/assets/admin/amazeui.min.js"></script>
<script src="/assets/admin/modules/ddsort.js"></script>
<script src="/assets/admin/modules/goods.spec.js"></script>
<script>
    layui.use(['form','element','code','laydate'], function() {
        var form = layui.form
        element = layui.element;
        let laydate = layui.laydate;

        //  时间
        laydate.render({
            elem: '#start_time' //指定元素
            ,type: 'datetime'
            // ,format: 'HH:mm'
        });

        // 藏品方式
        form.on('radio(spec_type)', function(data){
            var $goodsSpecMany = $('.goods-spec-many')
                , $goodsSpecSingle = $('.goods-spec-single');
            if (data.value === '10') {
                $goodsSpecMany.hide() && $goodsSpecSingle.show();
            } else {
                $goodsSpecMany.show() && $goodsSpecSingle.hide();
            }
        });

        form.on('radio(label_type)', function(data){
            var $holdGoodsList = $('.hold_goods_list');
            switch (data.value)
            {
                case "10":
                    $holdGoodsList.hide();
                    break;
                case "20":
                    $holdGoodsList.show();
                    break;
            }
        });

        layui.code();
    });

    $(function () {

        var specMany = new GoodsSpec({
            container: '.goods-spec-many'
        }, <?= $specData ?>);
        // 富文本编辑器
        UM.getEditor('container');

        // 选择图片
        $('.upload-file').selectImages({
            name: 'product[thumbnail]',
        });

        // 选择图片
        $('.upload-file1').selectImages({
            name: 'product[image][]'
        });

        /**
         * 表单验证提交
         * @type {*}
         */
        $('#my-form').superForm({
            // form data
            buildData: function () {
                return {
                    product: {
                        spec_many: specMany.getData()
                    }
                };
            },

            // 自定义验证
            validation: function () {
                var specType = $('input:radio[name="goods[spec_type]"]:checked').val();
                if (specType === '20') {
                    var isEmpty = specMany.isEmptySkuList();
                    isEmpty === true && layer.msg('商品规格不能为空');
                    return !isEmpty;
                }
                return true;
            }
        });

        $(document).on("click",".addMember",function(){
            layer.open({
                type: 2,
                title: '选择藏品',
                shade: 0.1,
                btn:["确定","取消"],
                offset: 'auto',
                area: ['1020px','670px'],
                content: "{{:url('user.preference/goods')}}?types=kt"
            });
        })
    });
</script>

