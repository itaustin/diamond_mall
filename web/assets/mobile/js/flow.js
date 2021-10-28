var itemIndex = 0;
var data = false;

var counter = 0;
// 每页展示4个
var num = 6;
var pageStart = 0,pageEnd = 0;

var token = window.localStorage.getItem("token");

var dropload = $('.weui-content').parent().find(".foot-black").dropload({
    scrollArea : window,
    loadDownFn : function(me) {
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
                    $.toast("未登录，系统即将引导您去登录");
                    setTimeout(function () {
                        location.href="/?s=/mobile/mine";
                    },1500);
                    return false;
                }else if(data.data.goods_list.length == 0){
                    // 数据加载完
                    data = true;
                    // 锁定
                    me.lock();
                    // 无数据
                    me.noData();
                    me.resetload();
                    $(".total_money").text("0.00");
                    layer.open({
                        skin: 'footer',
                        content : '您还未添加商品到购物车',
                        shadeClose : false
                    });
                    return false;
                }
                var result = '';
                counter++;
                pageEnd = num * counter;
                pageStart = pageEnd - num;

                var datas = data.data.goods_list;
                var allData = data.data;

                if (pageStart <= datas.length) {
                    for (var i = pageStart; i < pageEnd; i++) {
                        var attr = datas[i].goods_sku.goods_attr == "" ? "无规格" : datas[i].goods_sku.goods_attr;
                        var isN = datas[i].total_num > 1 ? `<a class="Decrease sub" data-goods_id="${datas[i].goods_id}" data-goods_sku_id="${datas[i].goods_sku.spec_sku_id}" href="javascript:void(0)"><i>-</i></a>` : `<a class="DisDe sub" data-goods_id="${datas[i].goods_id}" data-goods_sku_id="${datas[i].goods_sku.spec_sku_id}" href="javascript:void(0)"><i>-</i></a>`;
                        result += `
                                <li data-cart_id="${datas[i].goods_id}_${datas[i].goods_sku_id}">
                                    <div class="shop-info">
                                        <input type="checkbox" class="check goods-check goodsCheck">
                                        <div class="shop-info-img"><a href="/?s=/mobile/goods/detail&goods_id=${datas[i].goods_id}"><img src="${datas[i].goods_image}" /></a></div>
                                        <div class="shop-info-text">
                                            <h4>${datas[i].goods_name}</h4>
                                            <div class="shop-brief"><span>${attr}</span></div>
                                            <div class="shop-price">
                                                <div class="shop-pices">￥<b class="price">${datas[i].goods_sku.goods_price}</b></div>
                                                <div class="shop-arithmetic">
                                                    <a href="javascript:;" data-goods_id="${datas[i].goods_id}" data-goods_sku_id="${datas[i].goods_sku.spec_sku_id}" class="minus">-</a>
                                                    <span class="num total_num_fr" >${datas[i].total_num}</span>
                                                    <a href="javascript:;" data-goods_id="${datas[i].goods_id}" data-goods_sku_id="${datas[i].goods_sku.spec_sku_id}" class="plus">+</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            `;
                        if ((i + 1) >= datas.length) {
                            // 数据加载完
                            data = true;
                            // 锁定
                            me.lock();
                            // 无数据
                            me.noData();
                            break;
                        }
                    }
                    $('.content').eq(itemIndex).append(result);
                    // 每次数据加载完，必须重置
                    me.resetload();
                }
                $(".total_money").text("总价：" + allData.order_total_price);
                // $(".Spinner").Spinner({value:1, len:3, max:999});
                $(document).delegate(".allselect","click",function () {
                    if($(this).find("input[name=all-sec]").prop("checked")){
                        $("input[name=cartpro]").each(function () {
                            $(this).prop("checked", true);
                        });
                    }
                    else
                    {
                        $("input[name=cartpro]").each(function () {
                            if ($(this).prop("checked")) {
                                $(this).prop("checked", false);
                            } else {
                                $(this).prop("checked", true);
                            }
                        });
                    }
                });
                /**
                 * /api/cart/delete
                 * goods_sku_id
                 */
                $(document).on("click", ".wy-dele", function() {
                    var _this = $(this);
                    var goods_sku_id = $(this).data("goods_id")+ "_" + $(this).data("goods_sku_id");
                    $.confirm("您确定要把此商品从购物车删除吗?", "确认删除?", function() {
                        $.ajax({
                            type : "post",
                            dataType : "json",
                            url : "/?s=/api/cart/delete",
                            data : {
                                goods_sku_id : goods_sku_id,
                                wxapp_id : 10001,
                                token : token
                            },
                            success : function (data) {
                                if(data.code == 1){
                                    _this.parent().parent().remove();
                                    $.toast("删除成功");
                                }
                            }
                        });
                    }, function() {
                        //取消操作
                    });
                });
            },
            error: function (xhr, type) {
                alert('Ajax error!');
                // 即使加载出错，也得重置
                me.resetload();
            }
        });
    }
});
$(document).delegate(".plus","click",function () {
    var _this = $(this);
    var goods_sku_id = $(this).data("goods_sku_id") == "" ? 0 : $(this).data("goods_sku_id");
    var goods_id = $(this).data("goods_id");
    var open;
    $.ajax({
        type: "post",
        url: "/?s=/api/cart/add",
        data: {
            goods_id: goods_id,
            goods_sku_id: goods_sku_id,
            goods_num: 1,
            wxapp_id: 10001,
            token: token
        },
        beforeSend : function(){
            open = layer.open({
                shadeClose: false,
                type:2
            });
        },
        success: function (data) {
            if (data.code == 1) {
                var datamsg = data.msg;
                $.ajax({
                    type: 'GET',
                    url: '/?s=/api/cart/lists',
                    data: {
                        wxapp_id: 10001,
                        token: token
                    },
                    dataType: 'json',
                    success: function (data) {
                        $(".total_money").text("总价：" + data.data.order_total_price);
                        var now = Number(_this.parent().find('span').text());
                        _this.parent().find('span').text(now+1);
                        layer.open({
                            skin : 'msg',
                            content : '操作成功'
                        });
                    }
                });
            } else if (data.code == 0) {
                layer.open({
                    skin : 'msg',
                    content : data.msg
                });
            }
        },
        complete : function () {
            layer.close(open);
        }
    });
});
$(document).delegate(".minus","click",function () {
    var _this = $(this);
    var goods_sku_id = $(this).data("goods_sku_id") == "" ? 0 : $(this).data("goods_sku_id");
    var goods_id = $(this).data("goods_id");
    var open;
    $.ajax({
        type : "post",
        url : "/?s=/api/cart/sub",
        data : {
            goods_id : goods_id,
            goods_sku_id : goods_sku_id,
            wxapp_id : 10001,
            token : token
        },
        beforeSend : function(){
            open = layer.open({
                shadeClose: false,
                type:2
            });
        },
        success: function (data) {
            if(data.code == 1){
                $.ajax({
                    type: 'GET',
                    url: '/?s=/api/cart/lists',
                    data: {
                        wxapp_id: 10001,
                        token: token
                    },
                    dataType: 'json',
                    success: function (data) {
                        $(".total_money").text("总价：" + data.data.order_total_price);
                        var now = Number(_this.parent().find('span').text());
                        _this.parent().find('span').text(now-1);
                        layer.open({
                            skin : 'msg',
                            content : '操作成功'
                        });
                    }
                });
            }else{
                layer.open({
                    skin : 'msg',
                    content : '操作失败'
                });
            }
        },
        complete : function () {
            layer.close(open);
        }
    });
});

$(".settlement").click(function () {
    var cart_ids = "";
    $(".goodsCheck:checked").each(function () {
        cart_ids += $(this).parents("li").data("cart_id") + ",";
    });
    cart_ids = cart_ids.substring(0,cart_ids.length-1);
    if(cart_ids == null || cart_ids == ''){
        layer.open({
            skin : 'msg',
            content : '请选择结算的商品'
        });
        return false;
    }
    window.localStorage.setItem("cartCheckoutParam",JSON.stringify({
        delivery : 0,
        shop_id : 0,
        coupon_id : 0,
        is_use_points : 0,
        cart_ids : cart_ids,
        wxapp_id : 10001,
        token : token
    }));
    console.log({
        delivery : 0,
        shop_id : 0,
        coupon_id : 0,
        is_use_points : 0,
        cart_ids : cart_ids,
        wxapp_id : 10001,
        token : token
    });
    location.href="/?s=/mobile/order/cartorder";
});