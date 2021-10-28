$(function () {
    //http://zuoweyshop.zuowey.com/index.php?s=/api/user.dealer.order/lists&settled=-1&page=1&wxapp_id=10001&token=a3d05fc5184c6b4b5ddcbc6b7f3713f8
    //http://zuoweyshop.zuowey.com/index.php?s=/api/user.dealer.order/lists&settled=0&page=1&wxapp_id=10001&token=a3d05fc5184c6b4b5ddcbc6b7f3713f8
    //http://zuoweyshop.zuowey.com/index.php?s=/api/user.dealer.order/lists&settled=1&page=1&wxapp_id=10001&token=a3d05fc5184c6b4b5ddcbc6b7f3713f8
    var token = localStorage.getItem('token');
    var user_id = localStorage.getItem('user_id');
    var itemIndex = 0;

    var counter = 0;
    // 每页展示6个
    var num = 6;
    var pageStart = 0,pageEnd = 0;
    function dropLoad(data){
        // var dropload = $('.orderList').parent().dropload({
        //     scrollArea : window,
        //     loadDownFn : function(me) {
        //         $.ajax({
        //             type: 'GET',
        //             url: '/?s=/api/user.dealer.order/lists',
        //             data: {
        //                 'settled' : 1,
        //                 'page' : 1,
        //                 'wxapp_id' : 10001,
        //                 'token' : token
        //             },
        //             dataType: 'json',
        //             success: function (data) {
        //                 if(data.data.list.data.length <= 0){
        //                     // 数据加载完
        //                     data = true;
        //                     // 锁定
        //                     me.lock();
        //                     // 无数据
        //                     me.noData();
        //                     me.resetload();
        //                     return false;
        //                 }
        //                 var result = '',money='';
        //                 counter++;
        //                 pageEnd = num * counter;
        //                 pageStart = pageEnd - num;
        //
        //                 var datas = data.data.list.data;
        //
        //                 if (pageStart <= datas.length) {
        //                     for (var i = pageStart; i < pageEnd; i++) {
        //                         if(datas[i].first_user_id === Number(user_id)){
        //                             money = `
        //                                 <span class="f-28"">${datas[i].first_money}</span>
        //                             `;
        //                         }
        //                         if(datas[i].second_user_id === Number(user_id)){
        //                             money = `
        //                                 <span class="f-28"">${datas[i].second_money}</span>
        //                             `;
        //                         }
        //                         if(datas[i].third_user_id === Number(user_id)){
        //                             money = `
        //                                 <span class="f-28"">${datas[i].third_money}</span>
        //                             `;
        //                         }
        //                         var team_commison = `
        //                             <div class="detail__row m-top10 dis-flex flex-x-between">
        //                         `;
        //                         var objString = "";
        //                         if(datas[i].team_money_resource !== ""){
        //                             var dealer_id = datas[i].team_money_resource;
        //                             dealer_id.forEach(function (item , key) {
        //                                 if(window.localStorage.getItem("user_id") == item.dealer_id){
        //                                     datas[i].team_money_resource.forEach(function (item , key) {
        //                                         if(item.level == "level_one"){
        //                                             grade = "一星补贴";
        //                                         }else if(item.level == "level_two"){
        //                                             grade = "二星补贴";
        //                                         }else if(item.level == "level_three"){
        //                                             grade = "三星补贴";
        //                                         }else if(item.level == "level_four"){
        //                                             grade = "四星补贴";
        //                                         }
        //                                         // objString += item.dealer.real_name+`获得`+grade+`<span style="color:red;">￥${item.money}</span>`+'|';
        //                                         objString += grade+`<span style="color:red;">￥${item.money}</span>`+'|';
        //                                     });
        //                                     objString = objString.substring(0,objString.length - 3);
        //                                     team_commison += `
        //                                             <p style="font-size:12px;">${objString}</p>
        //                                         </div>
        //                                     `;
        //                                 }else{
        //                                     team_commison = '';
        //                                 }
        //                             });
        //                         }else{
        //                             team_commison = '';
        //                         }
        //
        //                         result += `
        //                                 <div class="widget__detail">
        //                                     <div class="detail__row dis-flex flex-x-between">
        //                                         <div class="detail__left f-24">订单号：${datas[i].order_master.order_no}</div>
        //                                         <div class="detail__right f-24 c-violet">
        //                                             ${datas[i].order_master.state_text}
        //                                         </div>
        //                                     </div>
        //                                     <div class="detail__row m-top10 dis-flex flex-x-between">
        //                                         <div class="detail__left dis-flex flex-y-center">
        //                                             <div class="user-avatar">
        //                                                 <img style="width:54px;height:54px;border-radius:50%;" src="${datas[i].user.avatarUrl}">
        //                                             </div>
        //                                             <div class="user-info dis-flex flex-dir-column flex-x-center">
        //                                                 <div class="user-nickName f-28">${datas[i].user.nickName}</div>
        //                                                 <div class="user-time f-24 c-80">消费金额：￥${datas[i].order_master.order_price}</div>
        //                                             </div>
        //                                         </div>
        //                                         <div class="detail__right dis-flex flex-dir-column flex-x-center flex-y-center">
        //                                             <div class="detail__money t-r col-m">
        //                                                 <span class="f-26">+ </span>
        //                                                 ${money}
        //                                             </div>
        //                                             <div class="detail__time f-22 c-80">${datas[i].order_master.create_time}</div>
        //                                         </div>
        //                                     </div>
        //                                     ${team_commison}
        //                                 </div>
        //                             `;
        //                         if ((i + 1) >= data.data.list.data.length) {
        //                             // 数据加载完
        //                             data = true;
        //                             // 锁定
        //                             me.lock();
        //                             // 无数据
        //                             me.noData();
        //                             break;
        //                         }
        //                     }
        //                     $('.orderList').eq(itemIndex).append(result);
        //                     // 每次数据加载完，必须重置
        //                     me.resetload();
        //                 }
        //             },
        //             error: function (xhr, type) {
        //                 alert('Ajax error!');
        //                 // 即使加载出错，也得重置
        //                 me.resetload();
        //             }
        //         });
        //     }
        // });
        // $(this).parent().siblings().find('div').removeClass('on');
        // $(this).addClass('on');
        // $(".orderList").empty();
        // $('.dropload-down').remove();

        var itemIndex = 0;

        var counter = 0;
        // 每页展示6个
        var num = 6;
        var pageStart = 0,pageEnd = 0;
        var dropload = $('.orderList').parent().dropload({
            scrollArea : window,
            loadDownFn : function(me) {
                $.ajax({
                    type: 'GET',
                    url: '/?s=/api/user.dealer.order/withMeLists',
                    data: {
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
                            var money = '';
                            for (var i = pageStart; i < pageEnd; i++) {
                                if(datas[i].first_user_id === Number(user_id)){
                                    money = `
                                        <span class="f-28"">${datas[i].first_money}</span>
                                    `;
                                }
                                if(datas[i].second_user_id === Number(user_id)){
                                    money = `
                                        <span class="f-28"">${datas[i].second_money}</span>
                                    `;
                                }
                                if(datas[i].third_user_id === Number(user_id)){
                                    money = `
                                        <span class="f-28"">${datas[i].third_money}</span>
                                    `;
                                }
                                var team_commison = `
                                    <div class="detail__row m-top10 dis-flex flex-x-between">
                                `;
                                var objString = "";
                                if(datas[i].team_money_resource !== ""){
                                    var dealer_id = datas[i].team_money_resource;
                                    dealer_id.forEach(function (item , key) {
                                        if(window.localStorage.getItem("user_id") == item.dealer_id){
                                            datas[i].team_money_resource.forEach(function (item , key) {
                                                if(item.level == "level_one"){
                                                    grade = "一星补贴";
                                                }else if(item.level == "level_two"){
                                                    grade = "二星补贴";
                                                }else if(item.level == "level_three"){
                                                    grade = "三星补贴";
                                                }else if(item.level == "level_four"){
                                                    grade = "四星补贴";
                                                }
                                                // objString += item.dealer.real_name+`获得`+grade+`<span style="color:red;">￥${item.money}</span>`+'|';
                                                objString += grade+`<span style="color:red;">￥${item.money}</span>`+'|';
                                            });
                                            objString = objString.substring(0,objString.length - 3);
                                            team_commison += `
                                                    <p style="font-size:12px;">${objString}</p>
                                                </div>
                                            `;
                                        }else{
                                            team_commison = '';
                                        }
                                    });
                                }else{
                                    team_commison = '';
                                }
                                result += `
                                        <div class="widget__detail">
                                            <div class="detail__row dis-flex flex-x-between">
                                                <div class="detail__left f-24">订单号：${datas[i].order.order_no}</div>
                                                <div class="detail__right f-24 c-violet">
                                                    ${datas[i].order.state_text}
                                                </div>
                                            </div>
                                            <div class="detail__row m-top10 dis-flex flex-x-between">
                                                <div class="detail__left dis-flex flex-y-center">
                                                    <div class="user-avatar">
                                                        <img style="width:54px;height:54px;border-radius:50%;" src="${datas[i].user.avatarUrl}">
                                                    </div>
                                                    <div class="user-info dis-flex flex-dir-column flex-x-center">
                                                        <div class="user-nickName f-28">${datas[i].user.nickName}</div>
                                                        <div class="user-time f-24 c-80">消费金额：￥${datas[i].order.order_price}</div>
                                                    </div>
                                                </div>
                                                <div class="detail__right dis-flex flex-dir-column flex-x-center flex-y-center">
                                                    <div class="detail__money t-r col-m">
                                                        <span class="f-26">+ </span>
                                                        ${money}
                                                    </div>
                                                    <div class="detail__time f-22 c-80">${datas[i].order.create_time}</div>
                                                </div>
                                            </div>
                                            ${team_commison}
                                        </div>
                                    `;
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
                            $('.orderList').eq(itemIndex).append(result);
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

    $(document.body).delegate(".selling","click",function (e) {
        var _this_sell = $(this).find('div').data('sell');
        $(this).parent().siblings().find('div').removeClass('on');
        $(this).addClass('on');
        $(".orderList").empty();
        $('.dropload-down').remove();

        var itemIndex = 0;

        var counter = 0;
        // 每页展示6个
        var num = 6;
        var pageStart = 0,pageEnd = 0;
        var dropload = $('.orderList').parent().dropload({
            scrollArea : window,
            loadDownFn : function(me) {
                $.ajax({
                    type: 'GET',
                    url: '/?s=/api/user.dealer.order/lists',
                    data: {
                        'settled' : 1,
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
                            var money = '';
                            for (var i = pageStart; i < pageEnd; i++) {
                                if(datas[i].first_user_id === Number(user_id)){
                                    money = `
                                        <span class="f-28"">${datas[i].first_money}</span>
                                    `;
                                }
                                if(datas[i].second_user_id === Number(user_id)){
                                    money = `
                                        <span class="f-28"">${datas[i].second_money}</span>
                                    `;
                                }
                                if(datas[i].third_user_id === Number(user_id)){
                                    money = `
                                        <span class="f-28"">${datas[i].third_money}</span>
                                    `;
                                }
                                var team_commison = `
                                    <div class="detail__row m-top10 dis-flex flex-x-between">
                                `;
                                var objString = "";
                                if(datas[i].team_money_resource !== ""){
                                    var dealer_id = datas[i].team_money_resource;
                                    dealer_id.forEach(function (item , key) {
                                        if(window.localStorage.getItem("user_id") == item.dealer_id){
                                            datas[i].team_money_resource.forEach(function (item , key) {
                                                if(item.level == "level_one"){
                                                    grade = "一星补贴";
                                                }else if(item.level == "level_two"){
                                                    grade = "二星补贴";
                                                }else if(item.level == "level_three"){
                                                    grade = "三星补贴";
                                                }else if(item.level == "level_four"){
                                                    grade = "四星补贴";
                                                }
                                                // objString += item.dealer.real_name+`获得`+grade+`<span style="color:red;">￥${item.money}</span>`+'|';
                                                objString += grade+`<span style="color:red;">￥${item.money}</span>`+'|';
                                            });
                                            objString = objString.substring(0,objString.length - 3);
                                            team_commison += `
                                                    <p style="font-size:12px;">${objString}</p>
                                                </div>
                                            `;
                                        }else{
                                            team_commison = '';
                                        }
                                    });
                                }else{
                                    team_commison = '';
                                }
                                result += `
                                        <div class="widget__detail">
                                            <div class="detail__row dis-flex flex-x-between">
                                                <div class="detail__left f-24">订单号：${datas[i].order_master.order_no}</div>
                                                <div class="detail__right f-24 c-violet">
                                                    ${datas[i].order_master.state_text}
                                                </div>
                                            </div>
                                            <div class="detail__row m-top10 dis-flex flex-x-between">
                                                <div class="detail__left dis-flex flex-y-center">
                                                    <div class="user-avatar">
                                                        <img style="width:54px;height:54px;border-radius:50%;" src="${datas[i].user.avatarUrl}">
                                                    </div>
                                                    <div class="user-info dis-flex flex-dir-column flex-x-center">
                                                        <div class="user-nickName f-28">${datas[i].user.nickName}</div>
                                                        <div class="user-time f-24 c-80">消费金额：￥${datas[i].order_master.order_price}</div>
                                                    </div>
                                                </div>
                                                <div class="detail__right dis-flex flex-dir-column flex-x-center flex-y-center">
                                                    <div class="detail__money t-r col-m">
                                                        <span class="f-26">+ </span>
                                                        ${money}
                                                    </div>
                                                    <div class="detail__time f-22 c-80">${datas[i].order_master.create_time}</div>
                                                </div>
                                            </div>
                                            ${team_commison}
                                        </div>
                                    `;
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
                            $('.orderList').eq(itemIndex).append(result);
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

    $(document.body).delegate(".with-me","click",function (e) {
        $(this).parent().siblings().find('div').removeClass('on');
        $(this).addClass('on');
        $(".orderList").empty();
        $('.dropload-down').remove();

        var itemIndex = 0;

        var counter = 0;
        // 每页展示6个
        var num = 6;
        var pageStart = 0,pageEnd = 0;
        var dropload = $('.orderList').parent().dropload({
            scrollArea : window,
            loadDownFn : function(me) {
                $.ajax({
                    type: 'GET',
                    url: '/?s=/api/user.dealer.order/withMeLists',
                    data: {
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
                            var money = '';
                            for (var i = pageStart; i < pageEnd; i++) {
                                if(datas[i].first_user_id === Number(user_id)){
                                    money = `
                                        <span class="f-28"">${datas[i].first_money}</span>
                                    `;
                                }
                                if(datas[i].second_user_id === Number(user_id)){
                                    money = `
                                        <span class="f-28"">${datas[i].second_money}</span>
                                    `;
                                }
                                if(datas[i].third_user_id === Number(user_id)){
                                    money = `
                                        <span class="f-28"">${datas[i].third_money}</span>
                                    `;
                                }
                                var team_commison = `
                                    <div class="detail__row m-top10 dis-flex flex-x-between">
                                `;
                                var objString = "";
                                if(datas[i].team_money_resource !== ""){
                                    var dealer_id = datas[i].team_money_resource;
                                    dealer_id.forEach(function (item , key) {
                                        if(window.localStorage.getItem("user_id") == item.dealer_id){
                                            datas[i].team_money_resource.forEach(function (item , key) {
                                                if(item.level == "level_one"){
                                                    grade = "一星补贴";
                                                }else if(item.level == "level_two"){
                                                    grade = "二星补贴";
                                                }else if(item.level == "level_three"){
                                                    grade = "三星补贴";
                                                }else if(item.level == "level_four"){
                                                    grade = "四星补贴";
                                                }
                                                // objString += item.dealer.real_name+`获得`+grade+`<span style="color:red;">￥${item.money}</span>`+'|';
                                                objString += grade+`<span style="color:red;">￥${item.money}</span>`+'|';
                                            });
                                            objString = objString.substring(0,objString.length - 3);
                                            team_commison += `
                                                    <p style="font-size:12px;">${objString}</p>
                                                </div>
                                            `;
                                        }else{
                                            team_commison = '';
                                        }
                                    });
                                }else{
                                    team_commison = '';
                                }
                                result += `
                                        <div class="widget__detail">
                                            <div class="detail__row dis-flex flex-x-between">
                                                <div class="detail__left f-24">订单号：${datas[i].order.order_no}</div>
                                                <div class="detail__right f-24 c-violet">
                                                    ${datas[i].order.state_text}
                                                </div>
                                            </div>
                                            <div class="detail__row m-top10 dis-flex flex-x-between">
                                                <div class="detail__left dis-flex flex-y-center">
                                                    <div class="user-avatar">
                                                        <img style="width:54px;height:54px;border-radius:50%;" src="${datas[i].user.avatarUrl}">
                                                    </div>
                                                    <div class="user-info dis-flex flex-dir-column flex-x-center">
                                                        <div class="user-nickName f-28">${datas[i].user.nickName}</div>
                                                        <div class="user-time f-24 c-80">消费金额：￥${datas[i].order.order_price}</div>
                                                    </div>
                                                </div>
                                                <div class="detail__right dis-flex flex-dir-column flex-x-center flex-y-center">
                                                    <div class="detail__money t-r col-m">
                                                        <span class="f-26">+ </span>
                                                        ${money}
                                                    </div>
                                                    <div class="detail__time f-22 c-80">${datas[i].order.create_time}</div>
                                                </div>
                                            </div>
                                            ${team_commison}
                                        </div>
                                    `;
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
                            $('.orderList').eq(itemIndex).append(result);
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