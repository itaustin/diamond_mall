$(function () {
    //http://zuoweyshop.zuowey.com/index.php?s=/api/user.dealer.team/lists&level=1&page=1&wxapp_id=10001&token=a3d05fc5184c6b4b5ddcbc6b7f3713f8
    var total_num = 0;
    var first_num = 0;
    $.get("/?s=/api/user/countAll",{token : window.localStorage.getItem('token'), wxapp_id : 10001},function (returnsData) {
        total_num = returnsData[0];
        first_num = returnsData[1];
    });
    var token = localStorage.getItem('token');
    $.get("/?s=/api/user.dealer.team/lists",{
        'page' : 1,
        'wxapp_id' : 10001,
        'token' : token
    },function (datas) {
        if(datas.code === 1){
            var team_total = datas.data.dealer == null ? 0 : datas.data.dealer.first_num;
            var people_total = datas.data.has == null ? 0 :datas.data.has;
            var tabList = [{
                value : 1,
                text : datas.data.words.team.words.first.value,
                total : datas.data.dealer == null ? 0 : datas.data.dealer.first_num
            }];
            if(datas.data.setting.level >= 2){
                tabList.push({
                    value : 2,
                    text : datas.data.words.team.words.second.value,
                    total : datas.data.dealer == null ? 0 : datas.data.dealer.second_num
                });
                team_total += datas.data.dealer == null ? 0 : datas.data.dealer.second_num;
            }
            if(datas.data.setting.level >= 3){
                tabList.push({
                    value : 3,
                    text : datas.data.words.team.words.third.value,
                    total : datas.data.dealer == null ? 0 : datas.data.dealer.third_num
                });
                team_total += datas.data.dealer == null ? 0 : datas.data.dealer.third_num;
            }
            var topTabBar = '';
            tabList.forEach(function (item , key) {
                topTabBar += `
                    <block>
                        <div style="width:187.5px;height:40px;" data-id="${item.value}" class="swiper-tab-item flex-box">${item.text}(${item.total})</div>
                    </block>
                `;
            });
            $(".topTabBar").html(topTabBar);
            $(".total-num").text(total_num);
            $(".people-num").text(first_num);
            $(".topTabBar").find('block').eq(0).click();
        }
    });

    $(document.body).delegate(".topTabBar block","click",function (e) {
        var _this_level = $(this).find('div').data('id');
        $(".dropload-down").remove();
        $(".teamBody").empty();
        $(this).siblings().find("div").removeClass('on');
        $(this).find('div.flex-box').addClass('on');

        var itemIndex = 0;

        var counter = 0;
        // 每页展示6个
        var num = 6;
        var pageStart = 0,pageEnd = 0;

        var dropload = $('.teamBody').parent().dropload({
            scrollArea : window,
            loadDownFn : function(me) {
                $.ajax({
                    type: 'GET',
                    url: '/?s=/api/user.dealer.team/lists',
                    data: {
                        'level' : _this_level > 0 ? _this_level :1,
                        'page' : 1,
                        'wxapp_id' : 10001,
                        'token' : token
                    },
                    dataType: 'json',
                    success: function (data) {
                        if(data.data.list.length <= 0){
                            // 数据加载完
                            data = true;
                            // 锁定
                            me.lock();
                            // 无数据
                            me.noData();
                            me.resetload();
                            return false;
                        }
                        var result = '',team='';
                        counter++;
                        pageEnd = num * counter;
                        pageStart = pageEnd - num;

                        var datas = data.data.list;


                        if (pageStart <= datas.length) {
                            for (var i = pageStart; i < pageEnd; i++) {
                                if(datas[i].dealer !== null){
                                    team += `
                                        <div class="detail__member f-22">
                                            一级成员：${datas[i].dealer !== null ? datas[i].dealer.first_num : 0}人<br/>
                                        </div>
                                        <div class="detail__member f-22">
                                            二级成员：${datas[i].dealer.second_num}人<br/>
                                        </div>
                                        <div class="detail__member f-22">
                                            共${datas[i].dealer !== null ? (datas[i].dealer.first_num + datas[i].dealer.second_num + datas[i].dealer.third_num) : "" }个成员
                                        </div>
                                    `;
                                }else{
                                    team += `<div class="detail__member2 f-22">不是代理商</div>`;
                                }
                                result += `
                                    <div class="widget__detail dis-flex flex-x-between">
                                        <div class="detail__left dis-flex flex-y-center">
                                            <div class="user-avatar">
                                                <img style="width:54px;height:54px;border-radius:50%;" src="${datas[i].user.avatarUrl}"></>
                                            </div>
                                            <div class="user-info dis-flex flex-dir-column flex-x-center">
                                                <div class="user-nickName f-28">${datas[i].user.nickName}</div>
                                                <div class="user-time col-9 f-24">${datas[i].create_time}</div>
                                            </div>
                                        </div>
                                        <div class="detail__right dis-flex flex-dir-column flex-x-center flex-y-center">
                                            <div class="detail__money">
                                                <span class="f-24">消费：￥</span>
                                                <span class="f-34">${datas[i].user.pay_money}</span>
                                            </div>
                                            `+team+`
                                        </div>
                                    </div>
                                `;
                                team = '';
                                if ((i + 1) >= data.data.list.length) {
                                    // 数据加载完
                                    data = true;
                                    // 锁定
                                    me.lock();
                                    // 无数据
                                    me.noData();
                                    break;
                                }
                            }
                            $('.teamBody').eq(itemIndex).append(result);
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