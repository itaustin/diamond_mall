$(function () {
    var token = localStorage.getItem('token');
    var itemIndex = 0;

    var counter = 0;
    // 每页展示4个
    var num = 6;
    var pageStart = 0,pageEnd = 0;

    function dropLoad(data){
        var dropload = $('.widget-list').parent().dropload({
            scrollArea : window,
            loadDownFn : function(me) {
                $.ajax({
                    type: 'GET',
                    url: '/?s=/api/user.dealer.withdraw/lists',
                    data: {
                        'status' : -1,
                        'page' : 1,
                        'wxapp_id' : 10001,
                        'token' : token
                    },
                    dataType: 'json',
                    success: function (data) {
                        if(data.data.list.data.length <= 0){
                            // 数据加载完
                            data = true;
                            // 锁定
                            me.lock();
                            // 无数据
                            me.noData();
                            me.resetload();
                            return false;
                        }
                        var result = '';
                        counter++;
                        pageEnd = num * counter;
                        pageStart = pageEnd - num;

                        var datas = data.data.list.data;

                        if (pageStart <= datas.length) {
                            for (var i = pageStart; i < pageEnd; i++) {
                                if(Number(datas[i].apply_status) === 30){
                                    result += `
                                    <div class="widget__detail dis-flex flex-x-between">
                                        <div class="detail__left dis-flex flex-dir-column flex-x-around">
                                            <div class="detail__money f-30">提现 ${datas[i].money}元</div>
                                            <div class="detail__time col-9 f-24">${datas[i].create_time}</div>
                                        </div>
                                        <div class="detail__right dis-flex flex-dir-column flex-x-center flex-y-center">
                                            <div class="detail__status f-28 col-m">当前状态</div>
                                            <div class="detail__reason f-24"  onclick="
                                                layer.open({
                                                    content: '${datas[i].reject_reason}'
                                                    ,skin: 'msg'
                                                    ,time: 2 //2秒后自动关闭
                                                });
                                            ">查看原因</div>
                                        </div>
                                    </div>
                                `;
                                }else if(Number(datas[i].apply_status) === 20){//审核通过
                                    result += `
                                    <div class="widget__detail dis-flex flex-x-between">
                                        <div class="detail__left dis-flex flex-dir-column flex-x-around">
                                            <div class="detail__money f-30">提现 ${datas[i].money}元</div>
                                            <div class="detail__time col-9 f-24">${datas[i].create_time}</div>
                                        </div>
                                        <div class="detail__right dis-flex flex-dir-column flex-x-center flex-y-center">
                                            <div class="detail__status f-28 col-m">审核通过，待打款</div>
                                        </div>
                                    </div>
                                `;
                                }else if(Number(datas[i].apply_status) === 10){//待审核
                                    result += `
                                    <div class="widget__detail dis-flex flex-x-between">
                                        <div class="detail__left dis-flex flex-dir-column flex-x-around">
                                            <div class="detail__money f-30">提现 ${datas[i].money}元</div>
                                            <div class="detail__time col-9 f-24">${datas[i].create_time}</div>
                                        </div>
                                        <div class="detail__right dis-flex flex-dir-column flex-x-center flex-y-center">
                                            <div class="detail__status f-28" style="color:gray;">待审核</div>
                                            
                                        </div>
                                    </div>
                                `;
                                }else if(Number(datas[i].apply_status) === 40){
                                    result += `
                                    <div class="widget__detail dis-flex flex-x-between">
                                        <div class="detail__left dis-flex flex-dir-column flex-x-around">
                                            <div class="detail__money f-30">提现 ${datas[i].money}元</div>
                                            <div class="detail__time col-9 f-24">${datas[i].create_time}</div>
                                        </div>
                                        <div class="detail__right dis-flex flex-dir-column flex-x-center flex-y-center">
                                            <div class="detail__status f-28 col-green">提现成功</div>
                                        </div>
                                    </div>
                                `;
                                }
                                if ((i + 1) >= data.data.list.data.length) {
                                    // 数据加载完
                                    data = true;
                                    // 锁定
                                    me.lock();
                                    // 无数据
                                    me.noData();
                                    break;
                                }
                            }
                            $('.widget-list').eq(itemIndex).append(result);
                            // 每次数据加载完，必须重置
                            me.resetload();
                        }
                    },
                    error: function (xhr, type) {
                        alert('Ajax error!');
                        // 即使加载出错，也得重置
                        me.resetload();
                    }
                });
            }
        });
    }

    dropLoad();

    $(document.body).delegate(".top-select-item block","click",function () {
        var thisId = $(this).find('div').data('id');
        $(this).find('div').addClass('on').parent().siblings().find('div').removeClass('on');
        var _this_data_id = $(this).find('div').data('id');
        $('.widget-list').empty();
        $('.dropload-down').remove();

        var token = localStorage.getItem('token');
        var itemIndex = 0;

        var counter = 0;
        // 每页展示6个
        var num = 6;
        var pageStart = 0,pageEnd = 0;

        var dropload = $('.widget-list').parent().dropload({
            scrollArea : window,
            loadDownFn : function(me) {
                $.ajax({
                    type: 'GET',
                    url: '/?s=/api/user.dealer.withdraw/lists',
                    data: {
                        'status' : _this_data_id,
                        'page' : 1,
                        'wxapp_id' : 10001,
                        'token' : token
                    },
                    dataType: 'json',
                    success: function (data) {
                        if(data.data.list.data.length <= 0){
                            // 数据加载完
                            data = true;
                            // 锁定
                            me.lock();
                            // 无数据
                            me.noData();
                            me.resetload();
                            return false;
                        }
                        var result = '';
                        counter++;
                        pageEnd = num * counter;
                        pageStart = pageEnd - num;

                        var datas = data.data.list.data;

                        if (pageStart <= datas.length) {
                            for (var i = pageStart; i < pageEnd; i++) {
                                if(Number(datas[i].apply_status) === 30){
                                    result += `
                                <div class="widget__detail dis-flex flex-x-between">
                                    <div class="detail__left dis-flex flex-dir-column flex-x-around">
                                        <div class="detail__money f-30">提现 ${datas[i].money}元</div>
                                        <div class="detail__time col-9 f-24">${datas[i].create_time}</div>
                                    </div>
                                    <div class="detail__right dis-flex flex-dir-column flex-x-center flex-y-center">
                                        <div class="detail__status f-28 col-m">驳回</div>
                                        <div class="detail__reason f-24" onclick="
                                            layer.open({
                                                content: '${datas[i].reject_reason}'
                                                ,skin: 'msg'
                                                ,time: 2 //2秒后自动关闭
                                            });
                                        ">查看原因</div>
                                    </div>
                                </div>
                            `;
                                }else if(Number(datas[i].apply_status) === 20){//审核通过
                                    result += `
                                <div class="widget__detail dis-flex flex-x-between">
                                    <div class="detail__left dis-flex flex-dir-column flex-x-around">
                                        <div class="detail__money f-30">提现 ${datas[i].money}元</div>
                                        <div class="detail__time col-9 f-24">${datas[i].create_time}</div>
                                    </div>
                                    <div class="detail__right dis-flex flex-dir-column flex-x-center flex-y-center">
                                        <div class="detail__status f-28 col-m">审核通过，待打款</div>
                                    </div>
                                </div>
                            `;
                                }else if(Number(datas[i].apply_status) === 10){//待审核
                                    result += `
                                <div class="widget__detail dis-flex flex-x-between">
                                    <div class="detail__left dis-flex flex-dir-column flex-x-around">
                                        <div class="detail__money f-30">提现 ${datas[i].money}元</div>
                                        <div class="detail__time col-9 f-24">${datas[i].create_time}</div>
                                    </div>
                                    <div class="detail__right dis-flex flex-dir-column flex-x-center flex-y-center">
                                        <div class="detail__status f-28" style="color:gray;">待审核</div>
                                        
                                    </div>
                                </div>
                            `;
                                }else if(Number(datas[i].apply_status) === 40){
                                    result += `
                                <div class="widget__detail dis-flex flex-x-between">
                                    <div class="detail__left dis-flex flex-dir-column flex-x-around">
                                        <div class="detail__money f-30">提现 ${datas[i].money}元</div>
                                        <div class="detail__time col-9 f-24">${datas[i].create_time}</div>
                                    </div>
                                    <div class="detail__right dis-flex flex-dir-column flex-x-center flex-y-center">
                                        <div class="detail__status f-28 col-green">提现成功</div>
                                    </div>
                                </div>
                            `;
                                }
                                if ((i + 1) >= data.data.list.data.length) {
                                    // 数据加载完
                                    data = true;
                                    // 锁定
                                    me.lock();
                                    // 无数据
                                    me.noData();
                                    break;
                                }
                            }
                            $('.widget-list').eq(itemIndex).append(result);
                            // 每次数据加载完，必须重置
                            me.resetload();
                        }
                    },
                    error: function (xhr, type) {
                        alert('Ajax error!');
                        // 即使加载出错，也得重置
                        me.resetload();
                    }
                });
            }
        });
    });

});