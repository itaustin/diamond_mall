$(function () {
    var token = window.localStorage.getItem("token");
    function getTypeParam(orderType){
        return param = {
            page : 1,
            dataType : orderType,
            wxapp_id : 10001,
            token : token
        };
    }

    /**
     * 全部订单
     * api/user.order/lists&
     * page: 1
     * dataType: all
     * wxapp_id: 10001
     * token: 28df77d6f377e887929dbe670e80dba5
     */
    var itemIndex = 0;
    var data = false;

    var counter = 0;
    // 每页展示6个
    var num = 6;
    var pageStart = 0,pageEnd = 0;

    // dropload
    var dropload = $('#all').parent().dropload({
        scrollArea : window,
        loadDownFn : function(me) {
            $.ajax({
                type: 'GET',
                url: '/?s=/api/user.order/lists',
                data: getTypeParam("all"),
                dataType: 'json',
                success: function (data) {
                    if(data.code == -1){
                        $.toast("未登录，系统即将引导您登陆");
                        setTimeout(function () {
                            location.href="/?s=/mobile/mine";
                        },1000);
                        return false;
                    }
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
                        var shopItem = "";
                        var orderBottomItem = "";
                        var payment = "";//待付款
                        var delivery = "";//待发货
                        var received = "";//待收货
                        var comment = "";//待评论
                        for (var i = pageStart; i < pageEnd; i++) {
                            if(datas[i].order_status.value == 21){
                                orderBottomItem = `
                                    <div class="weui-cell weui-cell_access weui-cell_link oder-opt-btnbox">
                                            订单取消中...
                                    </div>
                                `;
                            }else if(datas[i].order_status.value == 20){
                                orderBottomItem = ``;
                            }else{
                                if(datas[i].pay_status.value == 20){
                                    //已支付
                                    if(datas[i].delivery_status.value == 10){
                                        //待发货状态
                                        orderBottomItem = `
                                            <div class="weui-cell weui-cell_access weui-cell_link oder-opt-btnbox">
                                              <a href="javascript:void(0);" class="ords-btn-com cancelApply" data-order_id="${datas[i].order_id}">申请取消</a>
                                              <a href="javascript:void(0);" readonly class="ords-btn-com">包裹打包中...</a>
                                            </div>
                                        `;
                                    }else{
                                        //已发货状态
                                        if(datas[i].receipt_status.value == 10){
                                            //未确认收货
                                            orderBottomItem = `
                                            <div class="weui-cell weui-cell_access weui-cell_link oder-opt-btnbox">
                                              <a href="javascript:;" data-order_id="${datas[i].order_id}" class="ords-btn-com receipt">确认收货</a>
                                            </div>
                                        `;
                                        }else{
                                            //已收货
                                            if(datas[i].is_comment == 0){
                                                //未评价
                                                orderBottomItem = `
                                                <div class="weui-cell weui-cell_access weui-cell_link oder-opt-btnbox">
                                                  <a href="/?s=/mobile/order/comment&order_id=${datas[i].order_id}" class="ords-btn-com comment">去评价</a>
                                                </div>
                                            `;
                                            }else{
                                                //已评价
                                                orderBottomItem = `
                                                <div class="weui-cell weui-cell_access weui-cell_link oder-opt-btnbox">
                                                  订单完成
                                                </div>
                                            `;
                                            }
                                        }
                                    }
                                }else{
                                    //待付款
                                    orderBottomItem = `
                                    <div class="weui-cell weui-cell_access weui-cell_link oder-opt-btnbox">
                                      <a href="javascript:;" data-order_id="${datas[i].order_id}" class="ords-btn-dele order_cancen_notpay">取消订单</a>
<!--                                      <a href="javascript:void(0);" class="ords-btn-com paymentNow" data-order_id="${datas[i].order_id}">去付款</a>-->
                                    </div>
                                `;
                                }
                            }
                            datas[i].goods.forEach(item => {
                                shopItem += `
                                    <div class="weui-media-box_appmsg ord-pro-list">
                                        <div class="weui-media-box__hd"><a href="/?s=/mobile/goods/detail/goods_id/${item.goods_id}"><img class="weui-media-box__thumb" src="${item.image.file_path}" alt=""></a></div>
                                            <div class="weui-media-box__bd" onclick="location.href='/?s=/mobile/order/detail/order_id/${datas[i].order_id}';">
                                                <h1 class="weui-media-box__desc"><a class="ord-pro-link">${item.goods_name}</a></h1>
                                                <p class="weui-media-box__desc">规格：<span>${item.goods_attr}</span></p>
                                                <div class="clear mg-t-10">
                                                    <div class="wy-pro-pri fl">实付金额：¥<em class="num font-15">${item.total_pay_price}</em></div>
                                                    <div class="pro-amount fr"><span class="font-13">数量×<em class="name">${item.total_num}</em></span></div>
                                                </div>
                                        </div>
                                    </div>
                                `;
                            });

                            result += `
                                    <div class="weui-panel weui-panel_access">
                                      <div class="weui-panel__hd"><span>单号：${datas[i].order_no}</span> | <span>${datas[i].create_time}</span><span class="ord-status-txt-ts fr">${datas[i].state_text}</span></div>
                                      <div class="weui-media-box__bd  pd-10">
                                      `+shopItem+`
                                      </div>
                                      <div class="ord-statistics">
                                        <span>共<em class="num">${datas[i].goods.length}</em>件商品，</span>
                                        <span class="wy-pro-pri">总金额：¥<em class="num font-15">${datas[i].pay_price}</em></span>
                                        <span>(含运费<b>￥${datas[i].express_price}</b>)</span>
                                      </div>
                                      <div class="weui-panel__ft">
                                        `+orderBottomItem+`
                                      </div>
                                    </div>
                                `;
                            if(datas[i].pay_status.value == 20){
                                //已支付
                                if(datas[i].delivery_status.value == 10){
                                    //待发货状态
                                    delivery += `
                                        <div class="weui-panel weui-panel_access">
                                          <div class="weui-panel__hd"><span>单号：${datas[i].order_no}</span> | <span>${datas[i].create_time}</span><span class="ord-status-txt-ts fr">${datas[i].state_text}</span></div>
                                          <div class="weui-media-box__bd  pd-10">
                                          `+shopItem+`
                                          </div>
                                          <div class="ord-statistics">
                                            <span>共<em class="num">${datas[i].goods.length}</em>件商品，</span>
                                            <span class="wy-pro-pri">总金额：¥<em class="num font-15">${datas[i].pay_price}</em></span>
                                            <span>(含运费<b>￥${datas[i].express_price}</b>)</span>
                                          </div>
                                          <div class="weui-panel__ft">
                                            `+orderBottomItem+`
                                          </div>
                                        </div>
                                    `;
                                }else{
                                    //已发货状态
                                    if(datas[i].receipt_status.value == 10){
                                        //未确认收货
                                        received += `
                                            <div class="weui-panel weui-panel_access">
                                              <div class="weui-panel__hd"><span>单号：${datas[i].order_no}</span><span class="ord-status-txt-ts fr">${datas[i].state_text}</span></div>
                                              <div class="weui-media-box__bd  pd-10">
                                              `+shopItem+`
                                              </div>
                                              <div class="ord-statistics">
                                                <span>共<em class="num">${datas[i].goods.length}</em>件商品，</span>
                                                <span class="wy-pro-pri">总金额：¥<em class="num font-15">${datas[i].pay_price}</em></span>
                                                <span>(含运费<b>￥${datas[i].express_price}</b>)</span>
                                              </div>
                                              <div class="weui-panel__ft">
                                                `+orderBottomItem+`
                                              </div>
                                            </div>
                                        `;
                                    }else{
                                        //已收货
                                        if(datas[i].is_comment == 0){
                                            //未评价
                                            comment += `
                                                <div class="weui-panel weui-panel_access">
                                                  <div class="weui-panel__hd"><span>单号：${datas[i].order_no}</span> | <span>${datas[i].create_time}</span><span class="ord-status-txt-ts fr">${datas[i].state_text}</span></div>
                                                  <div class="weui-media-box__bd  pd-10">
                                                  `+shopItem+`
                                                  </div>
                                                  <div class="ord-statistics">
                                                    <span>共<em class="num">${datas[i].goods.length}</em>件商品，</span>
                                                    <span class="wy-pro-pri">总金额：¥<em class="num font-15">${datas[i].pay_price}</em></span>
                                                    <span>(含运费<b>￥${datas[i].express_price}</b>)</span>
                                                  </div>
                                                  <div class="weui-panel__ft">
                                                    `+orderBottomItem+`
                                                  </div>
                                                </div>
                                            `;
                                        }
                                    }
                                }
                            }else{
                                //待付款
                                payment += `
                                    <div class="weui-panel weui-panel_access">
                                      <div class="weui-panel__hd"><span>单号：${datas[i].order_no}</span> | <span>${datas[i].create_time}</span><span class="ord-status-txt-ts fr">${datas[i].state_text}</span></div>
                                      <div class="weui-media-box__bd  pd-10">
                                      `+shopItem+`
                                      </div>
                                      <div class="ord-statistics">
                                        <span>共<em class="num">${datas[i].goods.length}</em>件商品，</span>
                                        <span class="wy-pro-pri">总金额：¥<em class="num font-15">${datas[i].pay_price}</em></span>
                                        <span>(含运费<b>￥${datas[i].express_price}</b>)</span>
                                      </div>
                                      <div class="weui-panel__ft">
                                        `+orderBottomItem+`
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
                            shopItem = "";
                        }
                        $('#all').eq(itemIndex).append(result);
                        $("#payment").html(payment);
                        $("#delivery").html(delivery);
                        $("#received").html(received);
                        $("#comment").html(comment);
                        // 每次数据加载完，必须重置
                        var url = window.location.toString();//进这个页面的url

                        var id = url.split("#")[1];//url例如： www.baidu.com#maodian(这个是你锚点的位置)

                        $("."+id).click();
                        me.resetload();
                    }
                },
                error: function (xhr, type) {
                    console.log('Ajax error!');
                    // 即使加载出错，也得重置
                    me.resetload();
                }
            });
        }
    });
    $(document.body).delegate(".cancelApply","click",function () {
        var order_id  = $(this).data("order_id");
        var cancel;
        $.confirm("您确定要取消此订单吗?", "确认取消?", function() {
            $.ajax({
                type : "post",
                dataType : "json",
                url : "/?s=/api/user.order/cancel",
                data : {
                    order_id : order_id,
                    wxapp_id : 10001,
                    token : token
                },
                beforeSend: function () {
                    cancel = layer.open({
                        type : 2,
                        shadeClose : false,
                        content : "取消中..."
                    });
                },
                success : function (data) {
                    if(data.code == 1){
                        $.toast(data.data);
                    }
                },
                complete : function (data) {
                    layer.close(cancel);
                }
            });
        }, function() {
            //取消操作
        });
    });
    $(document.body).delegate(".order_cancen_notpay","click",function () {
        var order_id  = $(this).data("order_id");
        var cancel;
        $.confirm("您确定要取消此订单吗?", "确认取消?", function() {
            $.ajax({
                type : "post",
                dataType : "json",
                url : "/?s=/api/user.order/cancel",
                data : {
                    order_id : order_id,
                    wxapp_id : 10001,
                    token : token
                },
                beforeSend: function () {
                    cancel = layer.open({
                        type : 2,
                        shadeClose : false,
                        content : "取消中..."
                    });
                },
                success : function (data) {
                    if(data.code == 1){
                        $.toast(data.msg);
                    }
                },
                complete : function (data) {
                    layer.close(cancel);
                }
            });
        }, function() {
            //取消操作
        });
    })
    $(document.body).delegate(".receipt","click",function () {
        var order_id = $(this).data("order_id");
        $.confirm("您确认收到货物了吗?", "确认收货?", function() {
            $.ajax({
                type : "post",
                dataType : "json",
                url : "/?s=/api/user.order/receipt",
                data : {
                    order_id : order_id,
                    wxapp_id : 10001,
                    token : token
                },
                beforeSend: function () {
                    cancel = layer.open({
                        type : 2,
                        shadeClose : false,
                        content : "收货中..."
                    });
                },
                success : function (data) {
                    if(data.code == 1){
                        $.alert("五星好评送好礼哦，赶快去评价吧！", "收货完成！",function () {
                            location.reload();
                        });
                    }
                },
                complete : function (data) {
                    layer.close(cancel);
                }
            });
        }, function() {
            //取消操作
        });
    });
    $(document.body).delegate(".paymentNow","click",function(){
        $.ajax({
            type : "post",
            dataType : "json",
            url : "/?s=/api/user.order/pay",
            data : {
                order_id : $(this).data("order_id"),
                wxapp_id : 10001,
                token : token
            },success:function (data) {
                function onBridgeReady(){
                    WeixinJSBridge.invoke(
                        'getBrandWCPayRequest', {
                            "appId":"wx38d44f6a1bc6904d",     //公众号名称，由商户传入
                            "timeStamp":data.data.payment.timeStamp,         //时间戳，自1970年以来的秒数
                            "nonceStr":data.data.payment.nonceStr, //随机串
                            "package":"prepay_id="+data.data.payment.prepay_id,
                            "signType":"MD5",         //微信签名方式：
                            "paySign":data.data.payment.paySign //微信签名
                        },
                        function(res){
                            if(res.err_msg == "get_brand_wcpay_request:ok" ){
                                // 使用以上方式判断前端返回,微信团队郑重提示：
                                // res.err_msg将在用户支付成功后返回ok，但并不保证它绝对可靠。
                                window.localStorage.removeItem("orderCheckoutParam");
                                $.ajax({
                                    type : "post",
                                    dataType : "json",
                                    url : "/?s=/mobile/order/grant",
                                    data : {
                                        order_id : data.data.order_id
                                    },
                                    success : function (grant) {
                                        layer.open({
                                            type : 2,
                                            shadeClose : false,
                                            content : "支付成功，跳转中，请稍候..."
                                        });
                                        setTimeout(function () {
                                            location.href = "/?s=/mobile/order/order_lists";
                                        },1000);
                                    }
                                });
                            }else{
                                layer.open({
                                    type : 2,
                                    shadeClose : false,
                                    content : "支付失败，跳转中，请稍候..."
                                });
                                setTimeout(function () {
                                    location.reload();
                                },1000);
                            }
                        });
                }
                if (typeof WeixinJSBridge == "undefined"){
                    if( document.addEventListener ){
                        document.addEventListener('WeixinJSBridgeReady', onBridgeReady, false);
                    }else if (document.attachEvent){
                        document.attachEvent('WeixinJSBridgeReady', onBridgeReady);
                        document.attachEvent('onWeixinJSBridgeReady', onBridgeReady);
                    }
                }else{
                    onBridgeReady();
                }
            }
        });
    });
});