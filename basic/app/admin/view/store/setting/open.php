<link rel="stylesheet" href="/assets/admin/css/amui/bladmin.css" />
<script src="/assets/admin/amazeui.min.js"></script>
<script type="application/javascript" src="/assets/admin/clipboard.min.js"></script>
<div class="am-cf">
    <div class="row">
        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
            <blockquote class="layui-elem-quote">
                <p>注意:1>开放API用于对接实时大盘、盯链类的应用</p>
                <p>注意:2>如需其他对外API请联系商务进行定制开发</p>
            </blockquote>
            <div class="widget am-cf">
                <form id="my-form" class="am-form tpl-form-line-form" enctype="multipart/form-data" method="post">
                    <div class="widget-body">
                        <fieldset>
                            <div class="widget-head am-cf">
                                <div class="widget-title am-fl">元数网开放API</div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">获取藏品列表</label>
                                <div class="am-u-sm-9 am-u-md-6 am-u-lg-5 am-u-end">
                                    <div class="am-input-group">
                                        <input disabled style="border: 1px solid #ccc;" type="text" class="am-form-field open_one_link" value="{{$open_api.one_link}}">
                                        <span style="cursor:pointer" class="am-input-group-label am-input-group-label__right copy_two_jiaoyi_link">copy</span>
                                    </div>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">获取寄售藏品列表</label>
                                <div class="am-u-sm-9 am-u-md-6 am-u-lg-5 am-u-end">
                                    <div class="am-input-group">
                                        <input disabled style="border: 1px solid #ccc;" type="text" class="am-form-field open_two_link"  value="{{$open_api.two_link}}">
                                        <span style="cursor:pointer" class="am-input-group-label am-input-group-label__right copy_notice_link">copy</span>
                                    </div>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">平台公告</label>
                                <div class="am-u-sm-9 am-u-md-6 am-u-lg-5 am-u-end">
                                    <div class="am-input-group">
                                        <input disabled style="border: 1px solid #ccc;" type="text" class="am-form-field y_three_link"  value="{{$open_api.three_link}}">
                                        <span style="cursor:pointer" class="am-input-group-label am-input-group-label__right y_three_link">copy</span>
                                    </div>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">藏品交易记录</label>
                                <div class="am-u-sm-9 am-u-md-6 am-u-lg-5 am-u-end">
                                    <div class="am-input-group">
                                        <input disabled style="border: 1px solid #ccc;" type="text" class="am-form-field y_four_link"  value="{{$open_api.four_link}}">
                                        <span style="cursor:pointer" class="am-input-group-label am-input-group-label__right y_four_link">copy</span>
                                    </div>
                                </div>
                            </div>
                            <div class="widget-head am-cf">
                                <div class="widget-title am-fl">MASCHI开放API</div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">行情实盘</label>
                                <div class="am-u-sm-9 am-u-md-6 am-u-lg-5 am-u-end">
                                    <div class="am-input-group">
                                        <input disabled style="border: 1px solid #ccc;" type="text" class="am-form-field m_one_link"  value="{{$maschi_api.one_link}}">
                                        <span style="cursor:pointer" class="am-input-group-label am-input-group-label__right m_one_link">copy</span>
                                    </div>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">公告接入</label>
                                <div class="am-u-sm-9 am-u-md-6 am-u-lg-5 am-u-end">
                                    <div class="am-input-group">
                                        <input disabled style="border: 1px solid #ccc;" type="text" class="am-form-field m_two_link"  value="{{$maschi_api.two_link}}">
                                        <span style="cursor:pointer" class="am-input-group-label am-input-group-label__right m_two_link">copy</span>
                                    </div>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">一键导入</label>
                                <div class="am-u-sm-9 am-u-md-6 am-u-lg-5 am-u-end">
                                    <div class="am-input-group">
                                        <input disabled style="border: 1px solid #ccc;" type="text" class="am-form-field m_three_link"  value="{{$maschi_api.three_link}}">
                                        <span style="cursor:pointer" class="am-input-group-label am-input-group-label__right m_three_link">copy</span>
                                    </div>
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
    let copy_push_link = new Clipboard('.copy_two_jiaoyi_link', {
        text: function() {
            return $(".open_one_link").val();
        }
    });
    //复制成功的回调
    copy_push_link.on('success',function(e) {
        console.log(e);
        layer.alert("复制成功~");
    });

    let copy_pull_link = new Clipboard('.copy_notice_link', {
        text: function() {
            return $(".open_two_link").val();
        }
    });
    //复制成功的回调
    copy_pull_link.on('success',function(e) {
        console.log(e);
        layer.alert("复制成功~");
    });
    let y_three_link = new Clipboard('.y_three_link', {
        text: function() {
            return $(".y_three_link").val();
        }
    });
    //复制成功的回调
    y_three_link.on('success',function(e) {
        console.log(e);
        layer.alert("复制成功~");
    });
    let y_four_link = new Clipboard('.y_four_link', {
        text: function() {
            return $(".y_four_link").val();
        }
    });
    //复制成功的回调
    y_four_link.on('success',function(e) {
        console.log(e);
        layer.alert("复制成功~");
    });



    let m_one_link = new Clipboard('.m_one_link', {
        text: function() {
            return $(".m_one_link").val();
        }
    });
    //复制成功的回调
    m_one_link.on('success',function(e) {
        console.log(e);
        layer.alert("复制成功~");
    });

    let m_two_link = new Clipboard('.m_two_link', {
        text: function() {
            return $(".m_two_link").val();
        }
    });
    //复制成功的回调
    m_two_link.on('success',function(e) {
        console.log(e);
        layer.alert("复制成功~");
    });

    let m_three_link = new Clipboard('.m_three_link', {
        text: function() {
            return $(".m_three_link").val();
        }
    });
    //复制成功的回调
    m_three_link.on('success',function(e) {
        console.log(e);
        layer.alert("复制成功~");
    });
</script>