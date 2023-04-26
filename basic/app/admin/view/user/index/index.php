<link rel="stylesheet" href="/assets/admin/css/knowledge.css">
<style>
    .layui-table tbody tr td .layui-table-cell {
        height: 70px !important;
        line-height: 70px !important;
    }
    .bl-table-name-title {
        text-align: left;
        float: left;
        color: #353535;
        width: 100%;
        height: 42px;
         line-height: inherit;
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        word-wrap: break-word;
    }

</style>
<body class="pear-container pear-container">
<div class="layui-card">
    <!--        表格上方的数据-->
    <div class="layui-card-body">
        <div class="layui-row layui-col-space12">
            <div class="layui-col-md12  layui-float-left">
                <form class="layui-form" action="">
                    <div class="layui-form-item">
                        <div class="layui-input-inline">
                            <label>
                                <input type="text" name="name" placeholder="请输入会员昵称" class="layui-input">
                            </label>
                        </div>
                        <div class="layui-input-inline">
                            <label>
                                <input type="text" name="phone" placeholder="请输入手机号" class="layui-input">
                            </label>
                        </div>
                        <div class="layui-input-inline">
                            <label>
                                <input type="text" name="member_id" placeholder="请输入会员iD" class="layui-input">
                            </label>
                        </div>
                        <div class="layui-input-inline">
                            <select name="shiming">
                                <option value="">实名认证</option>
                                <option value="0">未实名</option>
                                <option value="2">实名</option>
                            </select>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <div class="layui-input-inline">
                            <select name="status">
                                <option value="">会员状态</option>
                                <option value="0">禁用</option>
                                <option value="1">正常</option>
                            </select>
                        </div>
                        <div class="layui-inline">
                            <div class="layui-input-inline" style="float: left;width: 290px;margin-right: 10px;">
                                <input size="12" type="text" name="create_time"  id="create_time" class="layui-input" placeholder="请输入注册时间范围">
                            </div>
                        </div>
                        <div class="layui-btn-group">
                            <button class="layui-btn layui-btn-normal" lay-submit lay-filter="user-query">
                                <i class="layui-icon layui-icon-search"></i>
                                提交查询
                            </button>
                        </div>
                        <button class="layui-btn  layui-btn-danger" lay-submit lay-filter="order-export">
                            <i class="layui-icon layui-icon-export"></i>
                            导出会员
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <div class="layui-row layui-col-space12">
            <div class="layui-col-md12">
                <a class="layui-btn layui-btn-normal" href="<?= url('user.index/add') ?>">添加会员</a>
                <a class="layui-btn layui-btn-normal" href="<?= url('user.index/drops') ?>">空投藏品</a>
                <a class="layui-btn layui-btn-normal layui-bg-orange" id="importExcel" href="javascript:void(0)">导入会员</a>
                <a href="/assets/user.xlsx" style="font-size: 12px">下载导入模板</a>
                <button class="layui-btn layui-btn-primary demo1">
                    操作排序
                    <i class="layui-icon layui-icon-down layui-font-12"></i>
                </button>
                <a class="layui-btn layui-btn-normal layui-bg-orange" id="importExcelCollection" href="javascript:void(0)">导入空投表单</a>
                <a href="/assets/kt.xlsx" style="font-size: 12px">下载导入模板</a>
                <a class="layui-btn layui-btn-normal layui-bg-orange" id="importExcelCollectionBuyOrder" href="javascript:void(0)">批量增加购买次数</a>
                <a href="/assets/user_buy_num.xlsx" style="font-size: 12px">下载导入模板</a>
            </div>
        </div>
    </div>

    <!--表格上方的数据-->
    <table id="user-table" lay-filter="user-table"></table>
</div>
</body>
<!--  图片替换-->
<script type="text/html" id="head-thumb">
    <img style="border-radius: 50%;width: 60px;height: 60px" src="{{d.avatarUrl}}">
</script>

<script type="text/html" id="operate">
    <a href="javascript:void(0)" lay-event="{{d.member_id}}" lay-type="2">更多</a>
</script>

<script type="text/html" id="status">
    {{#  if(d.status.value == '0'){ }}
    <a class="layui-btn layui-btn-danger layui-btn-xs" data-member_id="{{d.memebr_id}}" lay-event="status">禁用</a>
    {{#  } }}
    {{#  if(d.status.value == 1){ }}
    <a class="layui-btn layui-btn-xs"  data-member_id="{{d.memebr_id}}" lay-event="status">正常</a>
    {{#  } }}
</script>

<script type="text/html" id="address">
    {{#  if(!d.address){ }}
    <a class="layui-btn layui-btn-danger layui-btn-xs" data-member_id="{{d.memebr_id}}" lay-event="address">创建账户</a>
    {{#  } }}
    {{#  if(d.address){ }}
    {{d.address}}
    {{#  } }}
</script>

<!--藏品编号 -->
<script type="text/html" id="invitations_number">
    <span style="cursor:pointer;text-align: center;" class="bl-table-name-title bl-Edit-title" data-id="{{d.member_id}}" data-field="z" data-name="{{d.invitations_number}}">
            {{d.invitations_number}} <i class="layui-icon layui-icon-survey" style="font-size: 18px !important;"></i>
        </span>
</script>
<!-- 藏品编号 -->

<!-- 图片替换-->
<script>
    layui.use(['table', 'form', 'jquery','toast','dropdown','laydate','upload','excel'], function() {
        let table = layui.table;
        let form = layui.form;
        let toast = layui.toast;
        let upload = layui.upload;
        let excel = layui.excel;
        var laydate = layui.laydate;
        let dropdown = layui.dropdown;
        let $ = layui.jquery;
        let cols = [
            [
                {
                    title: '用户ID',
                    field: 'member_id',
                    align: 'center',
                    type:'checkbox'
                },
                {
                    title: '用户ID',
                    field: 'member_id',
                    align: 'center',
                },
                {
                    title: '头像',
                    field: 'member_id',
                    align: 'center',
                    templet: '#head-thumb',
                },
                {
                    title: '昵称',
                    field: 'name',
                    align: 'center',
                },
                {
                    title: '排行榜数据',
                    field: 'invitations_number',
                    align: 'center',
                    templet: '#invitations_number',
                    width:120,
                },
                {
                    title: '手机号',
                    field: 'phone',
                    align: 'center',
                },
                {
                    title: '邀请码',
                    field: 'code',
                    align: 'center',
                },
                {
                    title: '账户余额',
                    field: 'account',
                    align: 'center',
                },
                {
                    title: '荣誉余额',
                    field: 'glory',
                    align: 'center',
                },
                {
                    field: 'status',
                    width:120,
                    align: 'center',
                    title: '状态',
                    templet: '#status'
                },
                {
                    title: '实名状态',
                    field: 'real_status_text',
                    align: 'center',
                },
                {
                    title: '上级用户',
                    field: 'p_name',
                    align: 'center',
                },
                {
                    title: '注册时间',
                    field: 'create_time',
                    align: 'center',
                },
                {
                    title: '账户地址',
                    field: 'address',
                    align: 'center',
                    templet: '#address'
                },
                {
                    title: '限购次数(增加)',
                    field: 'purchase_limit',
                    align: 'center',
                },
                {
                    title: '操作',
                    field: 'operate',
                    align: 'center',
                    minWidth: 260,
                }
            ]
        ]
        //渲染加载数据
        let tableIns = table.render({
            elem: '#user-table',
            id:'videoID',
            url: "{{:url('user.index/index')}}",
            page: {
                curr:localStorage.getItem("member_page_curr")?localStorage.getItem("member_page_curr"):1,
            },
            cols: cols,
            skin: 'line',
            done: function (res, curr, count) {
                localStorage.setItem("member_page_curr",curr);//存储页码
                dropdown.render({
                    elem: '.blMore'
                    ,trigger: 'hover'
                    ,data: [
                        {
                            title: '贴标签',
                            id: 'pasteLabel',
                        },
                        {
                            title: '会员编辑',
                            id: 'userEdit',
                        },
                        {
                            title: '调整上级'
                            ,id: 'adjustSuperior'
                        },{
                            title: '调整余额'
                            ,id: 'Balance'
                        },
                        {
                            title: '调整荣誉'
                                ,id: 'honor'
                        },
                        {
                            title: '发送站内信'
                            , id: 'zhanneixin'
                        },
                        {
                            title: '增减限购次数'
                            , id: 'increasePurchaseLimit'
                        },
                    ]
                    ,click: function(obj){
                        switch (obj.id)
                        {
                            case "pasteLabel":
                                window.pasteLabel(id_values);
                                break;
                            case "adjustSuperior":
                                window.adjustSuperior(id_values);
                                break;
                            case "Balance":
                                window.Balance(id_values);
                                break;
                            case "drop":
                                window.drop(id_values);
                                break;
                            case "honor":
                                window.honor(id_values);
                                break;
                            case "zhanneixin":
                                window.zhanneixin(id_values);
                                break;
                            case "increasePurchaseLimit":  //限购次数
                                window.increasePurchaseLimit(id_values);
                                break;
                            case "userEdit":
                                window.userEdit(id_values);
                                break;
                        }
                    },ready: function(elemPanel, elem){
                        id_values = elem[0].attributes[1].value;
                    }
                });
            }
        });
        form.on('submit(order-export)', function (data) {
            var ress = data.field;
            var recodePage = $(".layui-laypage-skip .layui-input").val();
            var recodeLimit = $(".layui-laypage-limits").find("option:selected").val();
            ress['page'] = recodePage;
            ress['limit'] = 10000;
            console.log(ress)
            $.ajax({
                url: "{{:url('user.index/index')}}"
                , data: ress
                , dataType: 'json'
                , success(res) {
                    var data = res.data;
                    console.log(data);
                    data = excel.filterExportData(data, {
                        member_id: 'member_id',
                        name: 'name',
                        invitations_number: 'invitations_number',
                        phone: 'phone',
                        code: 'code',
                        account: 'account',
                        glory: 'glory',
                        status: function (value) {
                            return value.text
                        },
                        real_status_text: 'real_status_text',
                        p_name: 'p_name',
                        create_time: 'create_time',
                        address: 'address',
                        purchase_limit: 'purchase_limit',
                        address_name: 'address_name',
                        address_phone: 'address_phone',
                        address_info: 'address_info',
                    });
                    // console.log(data);return;
                    data.unshift({
                        member_id: '用户id',
                        name: '昵称',
                        invitations_number: '排行榜数据',
                        phone: '手机号',
                        code: '邀请码',
                        account: '账户余额',
                        glory: '荣誉余额',
                        status: '状态',
                        real_status_text: '实名状态',
                        p_name: '上级用户',
                        create_time: '注册时间',
                        address: '账户地址',
                        purchase_limit: '限够次数(增加)',
                        address_name: '收货人姓名',
                        address_phone: '收货人手机号',
                        address_info: '收货地址(默认)',
                    });
                    var colConf = excel.makeColConfig({
                        'A': 80,
                        'B': 150,
                        'C': 100,
                        'D': 100,
                        'E': 180,
                        'F': 180,
                        'G': 100,
                        'H': 100,
                        'I': 100,
                        'J': 100,
                        'K': 100,
                        'L': 100,
                        'M': 100,
                    }, 100);
                    var time = new Date();
                    var date = time.getFullYear() + '-' + (time.getMonth() + 1) + '-' + time.getDate() + ' ' + time.getHours()
                        + ':' + time.getMinutes() + ':' + time.getSeconds();
                    let ecel_name = date + ".xlsx";
                    excel.exportExcel({
                        sheet1: data
                    }, ecel_name, 'xlsx', {
                        extend: {
                            '!cols': colConf
                        }
                    });
                    layer.close(loading);
                }
                , error() {
                    layer.alert('获取数据失败，请检查是否部署在本地服务器环境下');
                }
            });
            return false;
        });

        //日期范围
        laydate.render({
            elem: '#create_time'
            ,range: '~' //或 range: '~' 来自定义分割字符
            ,type: 'datetime'
        });

        // 搜索条件查询操作
        form.on('submit(user-query)', function(data) {
            tableIns.reload({
                where: data.field,
                page: {
                    curr:1,
                },
            })
            return false;
        });

        window.zhanneixin  = function (member_id) {
            layer.open({
                type: 2,
                title: '发送站内信',
                shade: 0.1,
                offset: 'auto',
                area: ['700px', '540px'],
                content: "zhanneixin?member_id=" + member_id,
                success:function(layero,index){
                },
                end: function () {
                    //  关闭按钮 刷新页面
                    var index = parent.layer.getFrameIndex(window.name);
                    parent.layer.close(index);// 关闭弹出层
                    parent.window.location.reload(); // 刷新父页面
                }
            });
        }

        // 增加限购次数
        window.increasePurchaseLimit = function(id) {
            layer.open({
                type: 2,
                title: '增加限购次数',
                shade: 0.1,
                offset: 'auto',
                area: ['600px','400px'],
                content:"increasePurchaseLimit?member_id="+id,
            });
        }

        table.on('tool(user-table)', function(obj) {
            // 监听到事件的删除操作
            if (obj.event === 'status') {
                window.remove(obj);
            }else if(obj.event === 'address'){
                window.address(obj);
            }
        });

        // 给会员贴标签
        window.pasteLabel = function(id) {
            layer.open({
                type: 2,
                title: '贴标签',
                shade: 0.1,
                offset: 'auto',
                area: ['600px','500px'],
                content:"pasteLabel?member_id="+id,
            });
        }

        // 空投藏品
        window.drop = function(id) {
            layer.open({
                type: 2,
                title: '空投藏品',
                shade: 0.1,
                offset: 'auto',
                area: ['600px','500px'],
                content:"drop?member_id="+id,
            });
        }

        //编辑会员信息
        window.userEdit = function (id) {
            layer.open({
                type: 2,
                title: '编辑信息',
                shade: 0.1,
                offset: 'auto',
                area: ['700px','650px'],
                content:"edituser?member_id="+id,
            });
        }

        // 修改会员状态
        window.remove = function(obj) {
            layer.confirm('确认更新用户状态吗？', {
                icon: 3,
                title: '提示'
            }, function(index) {
                layer.close(index);
                let loading = layer.load();
                $.getJSON("{{:url('user.index/upStatus')}}", {'id' : obj.data['member_id']}, function(result){
                    layer.close(loading);
                    if(1 === result.code){
                        layer.msg(result.msg, {
                            icon: 1,
                            time: 1000
                        }, function() {
                            tableIns.reload({
                                url: "{{:url('user.index/index')}}",
                                page: {
                                    curr:localStorage.getItem("member_page_curr")?localStorage.getItem("member_page_curr"):1,
                                },
                                cols: cols,
                                skin: 'line',
                                done: function (res, curr, count) {
                                    localStorage.setItem("member_page_curr",curr);//存储页码
                                }
                            });
                        });
                    }else{
                        layer.msg(result.msg, {
                            icon: 2,
                            time: 1000
                        });
                    }
                });

            });
        }

        // 修改会员状态
        window.address = function(obj) {
            layer.confirm('确认帮助用户创建账户吗？', {
                icon: 3,
                title: '提示'
            }, function(index) {
                layer.close(index);
                let loading = layer.load();
                $.getJSON("/common/task/testCreateAccount", {'member_id' : obj.data['member_id']}, function(result){
                    layer.close(loading);
                    if(1 === result.code){
                        layer.msg(result.msg, {
                            icon: 1,
                            time: 1000
                        }, function() {
                            tableIns.reload({
                                url: "{{:url('user.index/index')}}",
                                page: {
                                    curr:localStorage.getItem("member_page_curr")?localStorage.getItem("member_page_curr"):1,
                                },
                                cols: cols,
                                skin: 'line',
                                done: function (res, curr, count) {
                                    localStorage.setItem("member_page_curr",curr);//存储页码
                                }
                            });
                        });
                    }else{
                        layer.msg(result.msg, {
                            icon: 2,
                            time: 1000
                        });
                    }
                });

            });
        }


        // 调整上级
        window.adjustSuperior = function (member_id){
            layer.open({
                type: 2,
                title: '调整上级用户',
                shade: 0.1,
                offset: 'auto',
                area: ['500px','340px'],
                content:"modifySubordinate?member_id="+member_id,
            });
        }

        // 调整余额
        window.Balance = function (member_id){
            layer.open({
                type: 2,
                title: '调整余额',
                shade: 0.1,
                offset: 'auto',
                area: ['500px','340px'],
                content:"recharge?member_id="+member_id,
            });
        }
        window.honor = function (member_id){
            layer.open({
                type: 2,
                title: '调整余额',
                shade: 0.1,
                offset: 'auto',
                area: ['500px','340px'],
                content:"honor?member_id="+member_id,
            });
        }

        // 充值积分
        window.Score = function (member_id){
            layer.open({
                type: 2,
                title: '调整积分',
                shade: 0.1,
                offset: 'auto',
                area: ['500px','340px'],
                content:"rechargeScore?member_id="+member_id,
            });
        }

        //执行实例
        uploadInst = upload.render({
            elem: '#importExcel',
            /*method: 'POST',*/
            url: "{{:url('upload/originalUpload')}}",
            accept: 'file', //普通文件
            exts: 'xls|excel|xlsx', //导入表格
            auto: false,  //选择文件后不自动上传
            before: function (obj) {
                layer.load(); //上传loading
            },
            choose: function (obj) {  // 选择文件回调
                layer.load();
                var files = obj.pushFile();
                var fileArr = Object.values(files);// 注意这里的数据需要是数组，所以需要转换一下
                //console.debug(fileArr)
                // 用完就清理掉，避免多次选中相同文件时出现问题
                for (var index in files) {
                    if (files.hasOwnProperty(index)) {
                        delete files[index];
                    }
                }
                uploadExcel(fileArr); // 如果只需要最新选择的文件，可以这样写： uploadExcel([files.pop()])
            },
            error : function(){
                setTimeout(function () {
                    layer.msg("上传失败！", {
                        icon : 1
                    });
                    //关闭所有弹出层
                    layer.closeAll(); //疯狂模式，关闭所有层
                },1000);
            }

        });

        function uploadExcel(files) {
            try {
                excel.importExcel(files, {
                    // 读取数据的同时梳理数据
                    fields: {
                        'phone' : 'A',
                        'password' : 'B',
                    }
                }, function (data) {
                    // console.log(data);return;
                    var arr = new Array();
                    for(i = 1; i < data[0].Sheet1.length; i++){
                        var tt = {
                            phone : data[0].Sheet1[i].phone,
                            password: data[0].Sheet1[i].password,
                        };
                        arr.push(tt);
                    }
                    $.ajax({
                        async: false,
                        url: "{{:url('user.index/importOrder')}}",
                        type: 'post',
                        dataType: "json",
                        contentType: "application/x-www-form-urlencoded",
                        data: {
                            data : JSON.stringify(arr)
                        },
                        success: function (data) {
                            if(data.code ==1){
                                layer.msg(data.msg);
                                setTimeout(function () {
                                    layer.closeAll(); //疯狂模式，关闭所有层
                                },1000);
                                //表格导入成功后，重载表格
                                location.reload();
                            }else{
                                //表格导入失败后，重载文件上传
                                layer.alert(data.error+"请重新上传",{icon : 2});
                            }
                        },
                        error: function (msg) {
                            layer.msg('请联系管理员!!!');
                        }
                    });
                });
            } catch (e) {
                layer.alert(e.message);
            }
        }



        //执行表格批量导入空投
        uploadInst = upload.render({
            elem: '#importExcelCollection',
            /*method: 'POST',*/
            url: "{{:url('upload/originalUpload')}}",
            accept: 'file', //普通文件
            exts: 'xls|excel|xlsx', //导入表格
            auto: false,  //选择文件后不自动上传
            before: function (obj) {
                layer.load(); //上传loading
            },
            choose: function (obj) {  // 选择文件回调
                var files = obj.pushFile();
                var fileArr = Object.values(files);// 注意这里的数据需要是数组，所以需要转换一下
                //console.debug(fileArr)
                // 用完就清理掉，避免多次选中相同文件时出现问题
                for (var index in files) {
                    if (files.hasOwnProperty(index)) {
                        delete files[index];
                    }
                }
                collectionUploadExcel(fileArr); // 如果只需要最新选择的文件，可以这样写： uploadExcel([files.pop()])
            },
            error : function(){
                setTimeout(function () {
                    layer.msg("上传失败！", {
                        icon : 1
                    });
                    //关闭所有弹出层
                    layer.closeAll(); //疯狂模式，关闭所有层
                },1000);
            }
        });
        //处理空投藏品的表格
        function collectionUploadExcel(files) {
            var load = layer.load();
            try {
                excel.importExcel(files, {
                    // 读取数据的同时梳理数据
                    fields: {
                        'phone' : 'A',
                        'goods_id' : 'B',
                        'number' : 'C',
                    }
                }, function (data) {
                    var arr = new Array();
                    for(i = 2; i < data[0].Sheet1.length; i++){
                        var tt = {
                            phone : data[0].Sheet1[i].phone,
                            goods_id: data[0].Sheet1[i].goods_id,
                            number: data[0].Sheet1[i].number,
                        };
                        arr.push(tt);
                    }
                    $.ajax({
                        async: false,
                        url: "{{:url('user.index/importAirdrop')}}",
                        type: 'post',
                        dataType: "json",
                        contentType: "application/x-www-form-urlencoded",
                        data: {
                            data : JSON.stringify(arr)
                        },
                        success: function (data) {
                            if(data.code ==1){
                                layer.msg(data.msg);
                                setTimeout(function () {
                                    layer.closeAll(); //疯狂模式，关闭所有层
                                },1000);
                                //表格导入成功后，重载表格
                                location.reload();
                            }else{
                                layer.close(load); //疯狂模式，关闭所有层
                                //表格导入失败后，重载文件上传
                                layer.alert(data.msg+"请重新上传",{icon : 2});
                            }
                        },
                        error: function (msg) {
                            layer.msg('请联系管理员!!!');
                        }
                    });
                });
            } catch (e) {
                layer.alert(e.message);
            }
        }


        //执行批量导入限制购买次数
        uploadInst = upload.render({
            elem: '#importExcelCollectionBuyOrder',
            /*method: 'POST',*/
            url: "{{:url('upload/originalUpload')}}",
            accept: 'file', //普通文件
            exts: 'xls|excel|xlsx', //导入表格
            auto: false,  //选择文件后不自动上传
            before: function (obj) {
                layer.load(); //上传loading
            },
            choose: function (obj) {  // 选择文件回调
                var files = obj.pushFile();
                var fileArr = Object.values(files);// 注意这里的数据需要是数组，所以需要转换一下
                //console.debug(fileArr)
                // 用完就清理掉，避免多次选中相同文件时出现问题
                for (var index in files) {
                    if (files.hasOwnProperty(index)) {
                        delete files[index];
                    }
                }
                collectionUploadBuyOrderExcel(fileArr); // 如果只需要最新选择的文件，可以这样写： uploadExcel([files.pop()])
            },
            error : function(){
                setTimeout(function () {
                    layer.msg("上传失败！", {
                        icon : 1
                    });
                    //关闭所有弹出层
                    layer.closeAll(); //疯狂模式，关闭所有层
                },1000);
            }
        });
        //处理空投藏品的表格
        function collectionUploadBuyOrderExcel(files) {
            var load = layer.load();
            try {
                excel.importExcel(files,{
                    // 读取数据的同时梳理数据
                    fields: {
                        'phone' : 'A',
                        'number' : 'B'
                    }
                }, function (data) {
                    var arr1 = new Array();
                    for(i = 2; i < data[0].Sheet1.length; i++){
                        var tt = {
                            number: data[0].Sheet1[i].number,
                            phone : data[0].Sheet1[i].phone,
                        };
                        arr1.push(tt);
                    }
                    $.ajax({
                        async: false,
                        url: "{{:url('user.index/importBuyOrderAirdrop')}}",
                        type: 'post',
                        dataType: "json",
                        contentType: "application/x-www-form-urlencoded",
                        data: {
                            data : JSON.stringify(arr1)
                        },
                        success: function (data) {
                            if(data.code ==1){
                                layer.msg(data.msg);
                                setTimeout(function () {
                                    layer.closeAll(); //疯狂模式，关闭所有层
                                },1000);
                                //表格导入成功后，重载表格
                                location.reload();
                            }else{
                                layer.close(load); //疯狂模式，关闭所有层
                                //表格导入失败后，重载文件上传
                                layer.alert(data.msg+"请重新上传",{icon : 2});
                            }
                        },
                        error: function (msg) {
                            layer.msg('请联系管理员!!!');
                        }
                    });
                });
            } catch (e) {
                layer.alert(e.message);
            }
        }



        /**
         * 修改邀请人数
         */
        $(document).on("click",".bl-Edit-title",function () {
            let id = $(this).attr('data-id');
            let title = $(this).attr('data-name');
            let type = $(this).attr('data-field');
            let title_name = '';
            switch (type)
            {
                case "z":
                    title_name = "修改总榜邀请人数";
                    break;
                case "m":
                    title_name = "修改月榜邀请人数";
                    break;
                case "w":
                    title_name = "修改周榜邀请人数";
                    break;
            }

            layer.prompt({title: title_name, formType: 3,value:title}, function(text, index){
                layer.close(index);
                let loading = layer.load();
                $.getJSON("{{:url('user.index/editInvitationsNumber')}}", {'member_id' : id,'invitations_number':text,'type':type}, function(result){
                    layer.close(loading);
                    if(1 === result.code){
                        layer.msg(result.msg, {
                            icon: 1,
                            time: 1000
                        }, function() {
                            tableIns.reload({
                                url: "{{:url('user.index/index')}}",
                                page: {
                                    curr:localStorage.getItem("member_page_curr")?localStorage.getItem("member_page_curr"):1,
                                },
                                cols: cols,
                                skin: 'line',
                                done: function (res, curr, count) {
                                    localStorage.setItem("member_page_curr",curr);//存储页码
                                }
                            });
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

        //排序下拉菜单处理
        dropdown.render({
            elem: '.demo1',
            data: [{
                title: '荣誉值升序',
                id: 100
            }, {
                title: '荣誉值降序',
                id: 101
            }, {
                title: '余额升序',
                id: 102
            },{
                title: '余额降序',
                id: 103
            },{
                title: '邀请会员升序',
                id: 104
            },{
                title: '邀请会员降序',
                id: 105
            }
            ],
            click: function(obj) {
                //调用接口 然后重新渲染这个数据表
                tableIns.reload({
                    url: "{{:url('user.index/index')}}",
                    page: {
                        curr:1,
                    },
                    cols: cols,
                    skin: 'line',
                    where:{
                        'type_sort':obj.id
                    },
                    done: function (res, curr, count) {
                        localStorage.setItem("member_page_curr",curr);//存储页码
                        dropdown.render({
                            elem: '.blMore'
                            ,trigger: 'hover'
                            ,data: [
                                {
                                    title: '贴标签',
                                    id: 'pasteLabel',
                                },
                                {
                                    title: '会员编辑',
                                    id: 'userEdit',
                                },
                                {
                                    title: '调整上级'
                                    ,id: 'adjustSuperior'
                                },{
                                    title: '调整余额'
                                    ,id: 'Balance'
                                },
                                {
                                    title: '调整荣誉'
                                    ,id: 'honor'
                                },
                                {
                                    title: '发送站内信'
                                    , id: 'zhanneixin'
                                },
                                {
                                    title: '增减限购次数'
                                    , id: 'increasePurchaseLimit'
                                },
                            ]
                            ,click: function(obj){
                                switch (obj.id)
                                {
                                    case "pasteLabel":
                                        window.pasteLabel(id_values);
                                        break;
                                    case "adjustSuperior":
                                        window.adjustSuperior(id_values);
                                        break;
                                    case "Balance":
                                        window.Balance(id_values);
                                        break;
                                    case "drop":
                                        window.drop(id_values);
                                        break;
                                    case "honor":
                                        window.honor(id_values);
                                        break;
                                    case "zhanneixin":
                                        window.zhanneixin(id_values);
                                        break;
                                    case "increasePurchaseLimit":  //限购次数
                                        window.increasePurchaseLimit(id_values);
                                        break;
                                    case "userEdit":
                                        window.userEdit(id_values);
                                        break;
                                }
                            },ready: function(elemPanel, elem){
                                id_values = elem[0].attributes[1].value;
                            }
                        });
                    }
                });

            }
        });

    })

</script>