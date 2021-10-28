$(function () {
    var token = window.localStorage.getItem("token");
    var obj = JSON.parse(window.localStorage.getItem("cartCheckoutParam"));
    $.ajax({
        type : "get",
        dataType : "json",
        url : "/?s=/api/order/cart",
        data : obj,
        success : function (data) {
            if(data.data.address == null){
                $.toast("未添加收货地址");
                setTimeout(function () {
                    location.href="/?s=/mobile/address/address_list&from=checkout";
                },1500);
                return false;
            }
            /**
             * 地址处理
             */
            $(".address-select").html(`
                <div class="weui-media-box_appmsg">
                  <div class="weui-media-box__hd proinfo-txt-l" style="width:20px;"><span class="promotion-label-tit"><img src="/assets/mobile/images/icon_nav_city.png" /></span></div>
                  <div class="weui-media-box__bd">
                    <a href="/?s=/mobile/address/address_list&from=checkout" class="weui-cell_access">
                      <h4 class="address-name"><span>${data.data.address.name}</span><span>${data.data.address.phone}</span></h4>
                      <div class="address-txt">${data.data.address.region.province + "-" + data.data.address.region.city + "-" + data.data.address.region.region}${data.data.address.detail}</div>
                    </a>
                  </div>
                  <div class="weui-media-box__hd proinfo-txt-l" style="width:16px;"><div class="weui-cell_access"><span class="weui-cell__ft"></span></div></div>
                </div>
            `);
            /**
             * 商品处理显示
             */
            var goods_lists = data.data.goods_list;
            var shopView = ``;
            goods_lists.forEach(item => {
                shopView += `
                    <div class="weui-media-box_appmsg ord-pro-list">
                        <div class="weui-media-box__hd"><a href="/?s=/mobile/goods/detail&goods_id=${item.goods_id}"><img class="weui-media-box__thumb" src="${item.goods_image}" alt=""></a></div>
                        <div class="weui-media-box__bd">
                          <h1 class="weui-media-box__desc"><a href="/?s=/mobile/goods/detail&goods_id=${item.goods_id}" class="ord-pro-link">${item.goods_name}</a></h1>
                          <p class="weui-media-box__desc">规格：<span>${item.goods_sku.goods_attr}</span></p>
                          <div class="clear mg-t-10">
                            <div class="wy-pro-pri fl">¥<em class="num font-15">${item.goods_sku.goods_price}</em></div>
                            <div class="pro-amount fr"><span class="font-13">数量×<em class="name">${item.total_num}</em></span></div>
                          </div>
                        </div>
                    </div>
                `;
            });
            $(".weui-content-shopview").html(shopView);
            /**
             * 运费显示
             */
            $(".express_money").text(data.data.express_price);
            /**
             * 总价显示
             */
            $(".total_price_view").text(data.data.order_pay_price);
        }
    });
    $(".wechat_payment").click(function () {
        /**
         delivery: 10
         pay_type: 20
         shop_id: 0
         linkman:
         phone:
         coupon_id: 0
         is_use_points: 0
         remark:
         cart_ids: 10013_10065_10035,10034_10190
         wxapp_id: 10001
         token: 9f6212df31e55e552166a283304e1505
         */
        var payParam = {
            delivery : 10,
            pay_type : 20,
            shop_id : 0,
            linkman : "",
            phone : "",
            coupon_id : 0,
            is_use_points: 0,
            remark : "",
            cart_ids : obj.cart_ids,
            wxapp_id : 10001,
            token : token
        };
        $.ajax({
            type: "post",
            dataType: "json",
            url: "/?s=/api/order/cart",
            data: payParam,
            beforeSend: function () {
                loading_payOrder = layer.open({
                    type: 2
                    , shadeClose: false
                    , content: '加载中...'
                });
            },
            success: function (data) {
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
                                layer.open({
                                    type : 2,
                                    shadeClose : false,
                                    content : "支付成功，跳转中，请稍候..."
                                });
                                setTimeout(function () {
                                    location.href = "/?s=/mobile/order/order_lists";
                                },1000);
                            }else{
                                layer.open({
                                    type : 2,
                                    shadeClose : false,
                                    content : "支付失败，跳转中，请稍候..."
                                });
                                setTimeout(function () {
                                    location.href = "/?s=/mobile/order/order_lists";
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
            },
            complete: function () {
                layer.closeAll('loading_payOrder');
            }
        });
    });
});