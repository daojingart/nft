<script  id="feedback" type="text/template">
<link rel="stylesheet" href="/assets/admin/css/member.css">
    <!--        表格上方的数据-->
    <div class="layui-card-body">
        <div class="layui-row layui-col-space12">
            <div class="layui-col-md3 layui-float-left">
            </div>
            <div class="layui-col-md9">
                <form class="layui-form layui-float-right" action="">
                    <input type="hidden" name="member_id" value="{{id}}">
                    <div class="layui-form-item">
                        <label class="layui-form-label">课程名称</label>
                        <div class="layui-input-inline">
                            <label>
                                <input type="text" name="course_name" placeholder="" class="layui-input">
                            </label>
                        </div>
                        <div class="layui-btn-group">
                            <button class="layui-btn layui-btn-normal" lay-submit lay-filter="user-query">
                                <i class="layui-icon layui-icon-search"></i>
                                提交查询
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="layui-row layui-col-space12">
            <div class="layui-btn-group">
            </div>
        </div>
    </div>
    <!--表格上方的数据-->
    <table id="user-table" lay-filter="user-table"></table>
    <script type="text/javascript">
        layui.use(['table', 'form', 'jquery','toast'], function() {
            let table = layui.table;
            let form = layui.form;
            let toast = layui.toast;
            let $ = layui.jquery;
            let cols = [
                [
                    {
                        title: '编号ID',
                        field: 'id',
                        align: 'center',
                        type: 'checkbox'
                    },
                    {
                        title: '排序',
                        field: 'sort',
                        align: 'center',
                        sort:true
                    },
                    {
                        title: '章节名称',
                        field: 'video_name',
                        align: 'center',
                    },
                    {
                        title: '章节状态',
                        field: 'goods_name',
                        align: 'center',
                        templet: '#chapter_disabled',
                        minWidth: 160,
                    },
                    {
                        title: '点击播放量',
                        field: 'course_sales',
                        align: 'center',
                    },
                    {
                        title: '添加时间',
                        field: 'create_time',
                        align: 'center',
                    },
                    {
                        title: '操作',
                        field: 'operate',
                        align: 'center',
                        minWidth: 190,
                    }
                ]
            ]
            //渲染加载数据
            let tableIns = table.render({
                elem: '#user-table',
                id:'videoID',
                url: "{{:url('knowledge.video/getChapterList')}}",
                page: {
                    curr:localStorage.getItem("course_video_chapter_page_curr")?localStorage.getItem("course_video_chapter_page_curr"):1,
                },
                cols: cols,
                skin: 'line',
                where: {
                    'member_id':{{id}}
                },
            })

            //搜索条件查询操作
            form.on('submit(user-query)', function(data) {
                tableIns.reload({
                    where: data.field,
                    page: {
                        curr:1,
                    },
                })
                return false;
            });

            //监听操作
            table.on('tool(user-table)', function(obj) {
                //监听到事件的删除操作
                if (obj.event === 'edit') {
                    window.edit(obj);
                }else if(obj.event === 'remove'){
                    window.remove(obj);
                }
            });


            //删除
            window.remove = function(obj) {
                layer.confirm('确认删除这个章节内容吗？', {
                    icon: 3,
                    title: '提示'
                }, function(index) {
                    layer.closeAll(index);
                    let loading = layer.load();
                    $.getJSON("{{:url('knowledge.video/removechapter')}}", {'id' : obj.data.id}, function(result){
                        layer.close(loading);
                        if(1 === result.code){
                            layer.msg(result.msg, {
                                icon: 1,
                                time: 1000
                            }, function() {
                                tableIns.reload({
                                    page: {
                                        curr: localStorage.getItem("course_video_chapter_page_curr")
                                    },
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

            //编辑章节
            window.edit = function(obj) {
                layer.open({
                    type: 2,
                    title: '编辑章节视频',
                    shade: 0.1,
                    offset: 'auto',
                    area: ['950px','800px'],
                    content:"{{:url('knowledge.video/editChapter')}}?id="+obj.data.id,
                });
            }


            //批量操作数据 执行上架还是下架
            $(document).on("click",".puton",function(){
                let value = table.checkStatus('videoID').data;
                if(value.length<='0'){
                    layer.alert("请您先选择数据在进行对应的数据操作！",{
                        title:'提示',
                        id:'LAY_CHAPTER_PUTTON'
                    });
                    return;

                }
                let ids = [];//如果你想获得每个选中行的ID,如下操作
                for(var i=0;i<value.length;i++){
                    ids[i] = value[i].id;
                }
                const is_rack = $(this).attr('data-value');
                $.ajax({
                    type: "POST",
                    url: "{{:url('knowledge.video/editChapterStatus')}}",
                    data: {id:ids,is_rack:is_rack},
                    success: function(result){
                        if(result.code === 1){
                            toast.success({title: '成功提示',message: result.msg,position: 'topCenter'});
                            tableIns.reload({
                                page: {
                                    curr: localStorage.getItem("course_video_chapter_page_curr")
                                },
                            });
                        }else{
                            toast.error({title: '错误提示',message: '执行错误,请检查勾选状态！',position: 'topCenter'});
                        }
                    }
                });

            })

            //批量操作 执行批量删除
            $(document).on("click",".chapterDeleteALL",function(){
                var value = table.checkStatus('videoID').data;
                if(value.length<='0'){
                    layer.alert("请先选择需要删除的章节！",{
                        title:'提示',
                        id:'LAY_CHAPTER_DEL'
                    });
                    return;
                }
                var ids=[];//如果你想获得每个选中行的ID,如下操作
                for(var i=0;i<value.length;i++){
                    ids[i] = value[i].id;
                }

                layer.confirm('确认删除全部章节吗？', {
                    icon: 3,
                    title: '提示',
                    id:'LAY_CHAPTER_DEL_TIP'
                }, function(index) {
                    layer.close(index);
                    let loading = layer.load();
                    $.ajax({
                        type: "POST",
                        url: "{{:url('knowledge.video/batchChapterRemoveAll')}}",
                        data: {id:ids},
                        success: function(result){
                            layer.close(loading);
                            if(result.code === 1){
                                toast.success({title: '成功提示',message: result.msg,position: 'topCenter'});
                                tableIns.reload({
                                    page: {
                                        curr: localStorage.getItem("course_video_chapter_page_curr")
                                    },
                                });
                            }
                        }
                    });
                });




            })
        })

        /**
         * 添加章节
         */
        $(document).on("click",".addVideo",function () {
            layer.open({
                type: 2,
                title: '添加章节视频',
                shade: 0.1,
                offset: 'auto',
                area: ['950px','800px'],
                content:"{{:url('knowledge.video/addResource')}}?id={{id}}",
                id: 'LAY_ADD' //设定一个id，防止重复弹出
            });
        })

    </script>
</script>


<!--章节状态-->
<script type="text/html" id="chapter_disabled">
    {{#  if(d.is_rack === 10){ }}
    <button  type="button" class="layui-btn layui-btn-normal layui-btn-xs">已上架</button>
    {{#  } else { }}
    <button  type="button" class="layui-btn layui-btn-danger layui-btn-xs">已下架</button>
    {{#  } }}
</script>
<!--章节状态-->

