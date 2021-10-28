$(function () {
    var obj = JSON.parse(window.localStorage.getItem("orderCheckoutParam"));
    var token = window.localStorage.getItem("token");
    $.ajax({
        type : "post",
        dataType : "json",
        url : "/?s=/api/address/lists",
        data : {
            token : token,
            wxapp_id : 10001
        },
        success : function (data) {
            defaultData = null;
            data.data.list.forEach(item => {
                if(item.address_id === data.data.default_id){
                    defaultData = item;
                }
            });
            console.log(defaultData);
            if(defaultData == null) {
                $.toast("未添加收货地址");
                setTimeout(function () {
                    location.href="/?s=/mobile/address/address_list&from=checkout";
                },1500);
                return false;
            }
            var defaultHtml = `
                <div class="weui-media-box_appmsg">
                  <div class="weui-media-box__hd proinfo-txt-l" style="width:20px;"><span class="promotion-label-tit"><img src="/assets/mobile/images/icon_nav_city.png" /></span></div>
                  <div class="weui-media-box__bd">
                    <a href="/?s=/mobile/address/address_list&from=checkout" class="weui-cell_access">
                      <h4 class="address-name"><span>${defaultData['name']}</span><span>${defaultData['phone']}</span></h4>
                      <div class="address-txt">${defaultData.region.province + "-" + defaultData.region.city + "-" + defaultData.region.region}<br/>${defaultData.detail}</div>
                    </a>
                  </div>
                  <div class="weui-media-box__hd proinfo-txt-l" style="width:16px;"><div class="weui-cell_access"><span class="weui-cell__ft"></span></div></div>
                </div>
            `;
            $(".address-select").html(defaultHtml);
        }
    });
    var grantParam = [];
    if(obj !== null){
        $.ajax({
            type : "get",
            dataType : "json",
            url : "/?s=/api/order/buyNow",
            data : obj,
            success : function (data) {
                if(data.code == -1){
                    $.toast("您还未登录，请登录后再下单哦~~");
                    setTimeout(function () {
                        location.href="/?s=/mobile/mine";
                    },1000);
                    return false;
                }
                /**
                 * 输出商品组信息
                 */
                var goods_data = data.data.goods_list;
                if(goods_data.length == 1){
                    /**
                     * 输出商品名称
                     */
                    $(".goods_name").text(goods_data[0].goods_name);
                    $(".weui-media-box__thumb").attr("src",goods_data[0].goods_image);
                    $(".specData").text(goods_data[0].goods_sku.goods_attr);
                    //商品总个数
                    $(".total_num").text(goods_data[0].total_num);

                    if(goods_data[0].category_id === 10006){
                        $(".pay_types").html(`
                            <div class="mg10-0 t-c">总金额：<span class="wy-pro-pri mg-tb-5 total_pay_price">¥<em class="num font-20"></em></span></div>
                            <div class="mg10-0"><a href="javascript:void(0);" style="background:#1F99E1;" class="weui-btn weui-btn_primary alipay_pay_now">积分支付</a></div>
                        `);
                        $(".total_pay_price").html(`<em class="num font-20">`+goods_data[0].total_points+`积分</em>`);
                        $(".shop_total_pay_price").html(`<em class="num font-15">`+goods_data[0].total_points+`积分</em>`);
                        $(".express_price").hide();
                        //订单总金额
                        $(".total_price").html(`<em class="num font-20">`+data.data.order_pay_price+`积分</em>`);
                    } else {
                        //运费金额
                        $(".express_price em").text(data.data.express_price);
                        //商品金额
                        $(".shop_total_pay_price").html(`¥<em class="num font-15">`+goods_data[0].total_pay_price+`</em>`);
                        $(".total_pay_price").html("￥" + `<em class="num font-20">`+data.data.order_pay_price+`</em>`);
                    }
                }
            }
        });

        $(".wechat_pay_now").click(function () {
            var payParam = {
                delivery : 10,
                pay_type : 20,
                shop_id : 0,
                linkman : "",
                phone : "",
                coupon_id : 0,
                is_use_points: 0,
                remark : "",
                goods_id : obj.goods_id,
                goods_num : obj.goods_num,
                goods_sku_id : obj.goods_sku_id,
                wxapp_id : 10001,
                token : token
            };
            $.ajax({
                type: "post",
                dataType: "json",
                url: "/?s=/api/order/buyNow",
                data: payParam,
                beforeSend: function () {
                    loading_payOrder = layer.open({
                        type: 2
                        , shadeClose: false
                        , content: '加载中...'
                    });
                },
                success: function (data) {
                    if(data.code === 0){
                        var t = layer.open({
                            content : data.msg,
                            btn : "我知道了",
                            shadeClose : false
                        });
                        layer.close(loading_payOrder);
                    }
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
                    layer.close('loading_payOrder');
                }
            });
        });

        $(document.body).delegate(".alipay_pay_now","click",function () {
            var payParam = {
                delivery : 10,
                pay_type : 20,
                shop_id : 0,
                linkman : "",
                phone : "",
                coupon_id : 0,
                is_use_points: 0,
                remark : "",
                goods_id : obj.goods_id,
                goods_num : obj.goods_num,
                goods_sku_id : obj.goods_sku_id,
                wxapp_id : 10001,
                token : token
            };
            $.ajax({
                type: "post",
                dataType: "json",
                url: "/?s=/api/order/buyNowAlipay",
                data: payParam,
                beforeSend: function () {
                    loading_payOrder = layer.open({
                        type: 2
                        , shadeClose: false
                        , content: '加载中...'
                    });
                },
                success: function (data) {
                    if(data.code == 1){
                        $(document.body).html(data.data);
                    } else if(data.code == 2){
                        // 积分购买成功
                        layer.open({
                            content: '购买成功'
                            ,shadeClose: false
                            ,btn: '我知道了'
                            ,yes: function () {
                                location.href="/?s=/mobile/order/order_lists#all";
                            }
                        });
                    } else if(data.code == 0){
                        layer.open({
                            content: data.msg
                            ,shadeClose: false
                            ,btn: '我知道了'
                        });
                    }
                },
                complete: function () {
                    layer.close(loading_payOrder);
                }
            });
        });
    }else{
        history.back();
        $("html").remove();
    }
    /**
     delivery: 10
     pay_type: 20
     shop_id: 0
     linkman:
     phone:
     coupon_id: 0
     is_use_points: 0
     remark:
     goods_id: 10005
     goods_num: 1
     goods_sku_id: 10010_10013
     wxapp_id: 10001
     token: 28df77d6f377e887929dbe670e80dba5
     ?s=/api/order/buyNow
     */

});