$(function () {
    var token = localStorage.getItem("token");
    var user_id = localStorage.getItem("user_id");
    // var url = "https://openauth.alipay.com/oauth2/publicAppAuthorize.htm?app_id=2019072565978562&scope=auth_user&redirect_uri=http://disinfectant.zmxxzx.com/?s=/mobile/alipay/getcode";
    $.ajax({
        type : "get",
        dataType : "json",
        url : "/?s=/api/user.index/checkGrade",
        data : {
            wxapp_id : 10001,
            token : token
        },
        success: function (data) {
            $(".first").text("会员等级："+data.first);
            $(".second").text("会员等级："+data.second);
            $(".people").text("☆☆☆☆☆有效客户：" + data.peopleCount + "人");
        }
    });
    if(token !== null){
        $.ajax({
            type : "get",
            dataType : "json",
            url : "/?s=/api/user.index/detail",
            data : {
                wxapp_id : 10001,
                token : token
            },
            success: function (data) {
                if(data.data.userInfo !== false){
                    var userInfo = data.data.userInfo;
                    if(userInfo.mobile_phone == '13303529522' || userInfo.mobile_phone == '18234975986'){
                        $.get("/?s=/api/order/getTotalData",{wxapp_id : 10001,token : token},function (data) {
                            layer.open({
                                title: [
                                    '收入情况',
                                    'background-color: #FF4351; color:#fff;'
                                ]
                                ,content: `
                                    <p>总收入：${data.all.total_money} - 应支付：${data.all.payable} - 已支付：${data.all.pay_after} - 沉淀资金：${data.all.precipitation}</p>
                                    <p>今日收入：${data.today.totay_money} - 今日应支付：${data.today.pay_able} - 今日已支付：${data.today.pay_after} - 今日沉淀资金：${data.today.precipitation}</p>
                                `,
                            });
                        });
                    }
                    $(".weui-get-qrcode").click(function () {
                        if(userInfo.pay_money > 0){
                            $.ajax({
                                type : "post",
                                dataType : "json",
                                url : "/?s=/api/user/getQrcode",
                                data : {
                                    token : token,
                                    user_id : user_id,
                                    wxapp_id : 10001
                                },
                                success: function (data) {
                                    var imgText = `
                                        <img style="width:200px;height:230px;margin:0 auto;" src="${data.msg}" />
                                    `;
                                    layer.open({
                                        title: [
                                            '扫描二维码注册',
                                            'background-color: #FF4351; color:#fff;'
                                        ]
                                        ,content: `
                                            <div id="qrcode" style="margin: 0 auto;">
                                                   ${imgText}
                                            </div>
                                        `,
                                    });
                                }
                            });
                        }else{
                            layer.open({
                                skin : 'msg',
                                content : '您未消费，无法生成二维码',
                                time : 2
                            });
                        }
                        return false;
                    });
                    /**
                     * 用户个人基本信息展示
                     */
                    if(userInfo.nickName === "" || userInfo.nickName == null){
                        $(".user-name").text('暂无昵称');
                    }else{
                        $(".user-name").text(userInfo.nickName);
                    }
                    //头像展示
                    $(".headimgurl").attr("src",userInfo.avatarUrl);
                    $(".badge-payment").text(data.data.orderCount.payment).text(data.data.orderCount.payment);
                    $(".badge-received").text(data.data.orderCount.received);
                    getCartNum(token);
                }
            }
        });
    } else {
        console.log(code);
        if(code == null || code == ""){
            // location.href="https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx38d44f6a1bc6904d&redirect_uri=https://paimaimall.zuowey.com/?s=/mobile/wechat/getcode&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect";
            location.href="/?s=/mobile/passport/login";
        }else{
            localStorage.setItem("code",code);
        }
        if(localStorage.getItem('code') !== null){
            $(".user-name").click(function () {
                $.ajax({
                    type : "post",
                    dataType : "json",
                    url : "/?s=/api/user/login",
                    beforeSend: function(){
                        localStorage.removeItem('code');
                        layer.open({
                            type : 2,
                            shadeClose : false,
                            content : "数据处理中..."
                        });
                    },
                    data : {
                        referee_id : localStorage.getItem("referee_id"),
                        code : code,
                        wxapp_id : 10001
                    },
                    success : function (data) {
                        if(data.code === 1){
                            localStorage.setItem("token",data.data.token);
                            localStorage.setItem("user_id",data.data.user_id);
                            localStorage.removeItem("code");
                            $.ajax({
                                type : "get",
                                dataType : "json",
                                url : "/?s=/api/user.index/detail",
                                data : {
                                    wxapp_id : 10001,
                                    token : localStorage.getItem("token")
                                },
                                success: function (data) {
                                    var userInfo = data.data.userInfo;
                                    $(".weui-get-qrcode").click(function () {
                                        if(userInfo.pay_money > 0){
                                            layer.open({
                                                title: [
                                                    '扫描二维码注册',
                                                    'background-color: #FF4351; color:#fff;'
                                                ]
                                                ,content: `
                                                    <div id="qrcode" style="margin: 0 auto;">
                                                            <img style="width:200px;height:200px;margin:0 auto;" src="http://disinfectant.zuowey.com/?s=/mobile/qrcode/view&url=http://disinfectant.zuowey.com/?s=/mobile/passport/register/--code=18234975986&size=300&mobile=18234975986" />
                                                    </div>
                                                `,
                                            });
                                        }else{
                                            layer.open({
                                                skin : 'msg',
                                                content : '您未消费，无法生成二维码',
                                                time : 2
                                            });
                                        }
                                        return false;
                                    });
                                    /**
                                     * 用户个人基本信息展示
                                     */
                                    $(".user-name").text(userInfo.nickName !== null || userInfo.nickName !== "" ? userInfo.nickName : '暂无昵称');
                                    //头像展示
                                    $(".headimgurl").attr("src",userInfo.avatarUrl);
                                    $(".badge-payment").text(data.data.orderCount.payment);
                                    $(".badge-received").text(data.data.orderCount.received);
                                    $(".badge-payment").text(data.data.orderCount.payment);
                                    var token = localStorage.getItem("token");
                                    getCartNum(token);
                                }
                            });
                        }else if(data.code === 10){
                            location.href="/?s=/mobile/passport/login";
                        }else if(data.code === 20){
                            alert('请扫描他人邀请二维码进入');
                            return false;
                        }else if(data.code === 0){
                            layer.open({
                                skin : 'msg',
                                content : '用户注册失败',
                                time : 2
                            });
                        }else{
                            location.href="/?s=/mobile/passport/login";
                        }

                    },
                    complete: function () {
                        layer.closeAll();
                    }
                });
            });
        }
    }

    /**
     * 通过token获取购物车的角标数量
     * @param token
     */
    function getCartNum(token){
        $.ajax({
            type: 'GET',
            url: '/?s=/api/cart/lists',
            data : {
                wxapp_id : 10001,
                token : token
            },
            dataType: 'json',
            success: function (data) {
                if(data.code == -1){
                    localStorage.removeItem('token');
                    localStorage.removeItem('user_id');
                    localStorage.removeItem('code');
                    location.reload();
                    return false;
                }
                if(data.code !== -1 || data.code !== 0){
                    $(".badge-nav-pre").prepend(`
                        <span class="weui-badge " style="position: absolute;top: -.4em;right: 1em;">${data.data.goods_list.length}</span>
                    `);
                }
            }
        });
    }

    $(".logout").click(function () {
        localStorage.removeItem('token');
        localStorage.removeItem('user_id');
        localStorage.removeItem('code');
        location.href="/?s=/mobile/mine";
        return false;
    });

    $(".user-name").click();
});
