$(function () {
    token = window.localStorage.getItem("token");

    /**
     * 商品处理
     * @type {string}
     */
    var goodsid = window.localStorage.getItem("goodsDetail");
    $.ajax({
        type : "get",
        dataType : "json",
        url : "/?s=/api/goods/detail",
        data : {
            wxapp_id : 10001,
            goods_id : goods_id
        },
        beforeSend: function(){
            $.showLoading("加载中...");
        },
        success:function (data) {
            /**
             * 主要轮播图接收处理。
             * @type {string}
             */
            var wrapper_image = "";

            data.data.detail.image.forEach(item => {
                wrapper_image += `
                    <div class="swiper-slide">
                        <img src="${item.file_path}" />
                    </div>
                `;
            });
            $(".wrapper-jqweui-init").html(wrapper_image);
            /**
             * 主图轮播初始化
             */
            $(".swiper-zhutu").swiper({
                loop: true,
                paginationType:'fraction',
                autoplay:5000
            });
            //Title赋值
            $(".wy-media-box__title").text(data.data.detail.goods_name);
            /**
             * 商品价格处理
             */
            if(data.data.specData == null ||  data.data.specData == undefined){
                if(data.data.detail.category_id == 10006){
                    $(".wy-pro-pri").find("em.font-20").parent().append("积分");
                    $(".wy-pro-pri").find("em.font-20").text(data.data.detail.points);
                } else {
                    $(".wy-pro-pri").find("em").text("￥" + data.data.detail.goods_sku.goods_price);
                    $(".wy-pro-pri").find("em.font-13").text(data.data.detail.goods_sku.line_price);
                }
            }else{
                var specData = [];
                var linePrice = [];
                data.data.specData.spec_list.forEach(item => {
                    specData.push(item.form.goods_price);
                    linePrice.push(item.form.line_price);
                });
                specData.sort(function (a,b) {
                    return a-b;
                });
                linePrice.sort(function (a,b) {
                    return a-b;
                });
                var linemin = linePrice[0];
                var linemax = linePrice[linePrice.length -1];
                linemin == linemax ? $(".wy-pro-pri").find("em.font-13").text(linemin) : $(".wy-pro-pri").find("em.font-13").text(min + "_" + linemax) ;
                var min = specData[0];
                var max = specData[specData.length -1];
                min == max ? $(".wy-pro-pri").find("em").text(min) : $(".wy-pro-pri").find("em").text(min + "_" + max);
            }
            //weui-media-box__desc描述信息
            $(".selling_point").text(data.data.detail.selling_point);
            /**
             * 遍历产品规格
             */
            var specDataList = "";
            if(data.data.specData == null || data.data.specData == undefined){
                $(".weui-spec").remove();
            }else{
                data.data.specData.spec_attr.forEach(item => {
                    specDataList += `
                      <div class="weui-media-box_appmsg">
                        <div class="weui-media-box__hd proinfo-txt-l"><span class="promotion-label-tit">${item.group_name}</span></div>
                        <div class="weui-media-box__bd">
                          <div class="promotion-sku clear">
                            <ul>
                `;
                    item.spec_items.forEach(items => {
                        specDataList += `
                        <li data-itemid="${items.item_id}"><a href="javascript:;">${items.spec_value}</a></li>
                    `;
                    });
                    specDataList += `
                            </ul>
                          </div>
                        </div>
                      </div>
                `;
                });
                $(".weui-spec").html(specDataList);
                $(".weui-spec-buy").html(specDataList);
                $(".weui-spec-buy").append(`
                    <div class="weui-media-box_appmsg">
                        <div class="weui-media-box__hd proinfo-txt-l"><span class="promotion-label-tit">数量</span></div>
                        <div class="weui-media-box__bd">
                            <div class="promotion-sku clear">
                                <div class="Spinner">
                                    <a class="DisDe sub" href="javascript:void(0)"><i>-</i></a>
                                    <input class="Amount" value="1" readonly type="number" autocomplete="off" maxlength="3">
                                    <a class="Increase add" href="javascript:void(0)"><i>+</i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                `);
                $(".promotion-sku li").click(function(){
                    $(this).addClass("active").siblings("li").removeClass("active");
                })
            }

            /**
             * 详情页遍历数据
             */
            $(".pro-detail").html(data.data.detail.content);

            /**
             * 评论数据，暂时不写
             */

            if(data.data.detail.mall_no == 2){
                $.get("/?s=/mobile/goods/checkMallNo",{user_id:localStorage.getItem('user_id')},function (mallresult) {
                    if(mallresult.code == 1){
                        var specData = [];
                        var nameData = [];
                        $(".weui-spec .weui-media-box_appmsg ul").click(function () {
                            var index = $(".weui-spec .weui-media-box_appmsg ul").index(this);
                            specData[index] = $(this).find(".active").data("itemid");
                            nameData[index] = $(this).find(".active").text();
                        });
                        /**
                         * 加入购物车
                         */
                        $(".join-shop-cart").click(function () {
                            if(token == null){
                                $.toast("请先登陆...");
                                setTimeout(function () {
                                    location.href="/?s=/mobile/mine";
                                },1000);
                                return false;
                            }
                            //定义goods_sku_id暂用
                            //定义nameMsg暂用
                            var goods_sku_id = "";
                            var nameMsg = "";
                            //遍历specData
                            specData.forEach(item => {
                                goods_sku_id += item + "_";
                            });
                            nameData.forEach(item => {
                                nameMsg += item + "、";
                            });
                            goods_sku_id = goods_sku_id.substring(0,goods_sku_id.length-1);
                            nameMsg = nameMsg.substring(0,nameMsg.length-1);
                            var eleLength = $(".weui-spec .weui-media-box_appmsg ul").length;

                            if(eleLength == specData.length && eleLength > 0){
                                /**
                                 * 添加购物车操作
                                 */
                                $.ajax({
                                    type : "post",
                                    dataType : "json",
                                    url : "/?s=/api/cart/add",
                                    data : {
                                        goods_id:data.data.detail.goods_id,
                                        goods_num : 1,
                                        goods_sku_id : goods_sku_id,
                                        wxapp_id : 10001,
                                        token : token
                                    },
                                    success:function (data) {
                                        if(data.code == -1){
                                            $.toast("您还未登录，请登录后再下单哦~~");
                                            setTimeout(function () {
                                                location.href="/?s=/mobile/mine";
                                            },1000);
                                            return false;
                                        }else if(data.code == 1){
                                            $.toast(data.msg)
                                        }else if(data.code == 0){
                                            $.toast(data.msg);
                                        }
                                    }
                                });
                            }else if(eleLength == 0){
                                /**
                                 * 单规格添加购物车操作
                                 */
                                $.ajax({
                                    type : "post",
                                    dataType : "json",
                                    url : "/?s=/api/cart/add",
                                    data : {
                                        goods_id: data.data.detail.goods_id,
                                        goods_num : 1,
                                        goods_sku_id : 0,
                                        wxapp_id : 10001,
                                        token : token
                                    },
                                    success: function (data) {
                                        if(data.code == 1){
                                            $.toast(data.msg);
                                            setTimeout(function () {
                                                location.href="/?s=/mobile/mine";
                                            },1000);
                                        }else if(data.code == 0){
                                            $.toast(data.msg);
                                        }
                                    }
                                });
                            }else{
                                $.toast("请选择商品规格", "cancel");
                                $(".weui-spec").slideUp(500);
                                $(".weui-spec").fadeIn(1000);
                                return false;
                            }
                        });

                        /**
                         * 立即购买下单的数据处理
                         */
                        var buySpecData = [];
                        var buyNameData = [];
                        $(".weui-spec-buy .weui-media-box_appmsg ul").click(function () {
                            var index = $(".weui-spec-buy .weui-media-box_appmsg ul").index(this);
                            buySpecData[index] = $(this).find(".active").data("itemid");
                            buyNameData[index] = $(this).find(".active").text();
                        });
                        /**
                         * 立即购买下单
                         */
                        $(".weui-btn-buynow").click(function () {
                            if(token == null){
                                $.toast("请先登陆...");
                                setTimeout(function () {
                                    location.href="/?s=/mobile/mine";
                                },1000);
                                return false;
                            }
                            var goods_sku_id = "";
                            var nameMsg = "";

                            buySpecData.forEach(item => {
                                goods_sku_id += item + "_";
                            });
                            buyNameData.forEach(item => {
                                nameMsg += item + "、";
                            })
                            if(goods_sku_id !== 0){
                                goods_sku_id = goods_sku_id.substring(0,goods_sku_id.length-1);
                            }else{
                                goods_sku_id = 0;
                            }
                            if(nameMsg == ""){
                                nameMsg = nameMsg.substring(0,nameMsg.length-1);
                            }else{
                                nameMsg = "";
                            }
                            var eleLength = $(".weui-spec-buy .weui-media-box_appmsg ul").length;
                            if(eleLength == buySpecData.length && eleLength > 0){
                                var obj = {
                                    delivery : 0,
                                    shop_id : 0,
                                    coupon_id : 0,
                                    is_use_points : 0,
                                    goods_id : data.data.detail.goods_id,
                                    goods_num : Number($(".Amount").val()) ?Number($(".Amount").val()): 1 ,
                                    goods_sku_id : goods_sku_id,
                                    wxapp_id : 10001,
                                    token : token
                                };
                                window.localStorage.setItem("orderCheckoutParam",JSON.stringify(obj));
                                location.href="/?s=/mobile/order/checkout";
                            }else if(eleLength == 0){
                                /**
                                 * 单规格添加购物车操作
                                 */
                                var obj = {
                                    delivery : 0,
                                    shop_id : 0,
                                    coupon_id : 0,
                                    is_use_points : 0,
                                    goods_id : data.data.detail.goods_id,
                                    goods_num : Number($(".Amount").val()) ?Number($(".Amount").val()): 1 ,
                                    goods_sku_id : Number(goods_sku_id),
                                    wxapp_id : 10001,
                                    token : token
                                };
                                window.localStorage.setItem("orderCheckoutParam",JSON.stringify(obj));
                                location.href="/?s=/mobile/order/checkout";
                            }else{
                                $.toast("请选择商品规格", "cancel");
                                $(".weui-spec").slideUp(500);
                                $(".weui-spec").fadeIn(1000);
                                return false;
                            }
                            return false;
                        });
                    }else if(mallresult.code == 0){
                        $(".join-shop-cart").click(function () {
                            $.toast('您未在1号商城消费','error');
                            return false;
                        });
                        $(".weui-btn-buynow").click(function () {
                            $.toast('您未在1号商城消费','error');
                            return false;
                        });
                    }
                })
            }else{
                var specData = [];
                var nameData = [];
                $(".weui-spec .weui-media-box_appmsg ul").click(function () {
                    var index = $(".weui-spec .weui-media-box_appmsg ul").index(this);
                    specData[index] = $(this).find(".active").data("itemid");
                    nameData[index] = $(this).find(".active").text();
                });
                /**
                 * 加入购物车
                 */
                $(".join-shop-cart").click(function () {
                    if(token == null){
                        $.toast("请先登陆...");
                        setTimeout(function () {
                            location.href="/?s=/mobile/mine";
                        },1000);
                        return false;
                    }
                    //定义goods_sku_id暂用
                    //定义nameMsg暂用
                    var goods_sku_id = "";
                    var nameMsg = "";
                    //遍历specData
                    specData.forEach(item => {
                        goods_sku_id += item + "_";
                    });
                    nameData.forEach(item => {
                        nameMsg += item + "、";
                    });
                    goods_sku_id = goods_sku_id.substring(0,goods_sku_id.length-1);
                    nameMsg = nameMsg.substring(0,nameMsg.length-1);
                    var eleLength = $(".weui-spec .weui-media-box_appmsg ul").length;

                    if(eleLength == specData.length && eleLength > 0){
                        /**
                         * 添加购物车操作
                         */
                        $.ajax({
                            type : "post",
                            dataType : "json",
                            url : "/?s=/api/cart/add",
                            data : {
                                goods_id:data.data.detail.goods_id,
                                goods_num : 1,
                                goods_sku_id : goods_sku_id,
                                wxapp_id : 10001,
                                token : token
                            },
                            success:function (data) {
                                if(data.code == -1){
                                    $.toast("您还未登录，请登录后再下单哦~~");
                                    return false;
                                }else if(data.code == 1){
                                    $.toast(data.msg)
                                }else if(data.code == 0){
                                    $.toast(data.msg);
                                }
                            }
                        });
                    }else if(eleLength == 0){
                        /**
                         * 单规格添加购物车操作
                         */
                        $.ajax({
                            type : "post",
                            dataType : "json",
                            url : "/?s=/api/cart/add",
                            data : {
                                goods_id: data.data.detail.goods_id,
                                goods_num : 1,
                                goods_sku_id : 0,
                                wxapp_id : 10001,
                                token : token
                            },
                            success: function (data) {
                                if(data.code == 1){
                                    $.toast(data.msg)
                                }else if(data.code == 0){
                                    $.toast(data.msg);
                                }
                            }
                        });
                    }else{
                        $.toast("请选择商品规格", "cancel");
                        $(".weui-spec").slideUp(500);
                        $(".weui-spec").fadeIn(1000);
                        return false;
                    }
                });

                /**
                 * 立即购买下单的数据处理
                 */
                var buySpecData = [];
                var buyNameData = [];
                $(".weui-spec-buy .weui-media-box_appmsg ul").click(function () {
                    var index = $(".weui-spec-buy .weui-media-box_appmsg ul").index(this);
                    buySpecData[index] = $(this).find(".active").data("itemid");
                    buyNameData[index] = $(this).find(".active").text();
                });
                /**
                 * 立即购买下单
                 */
                $(".weui-btn-buynow").click(function () {
                    if(token == null){
                        $.toast("请先登陆...");
                        setTimeout(function () {
                            location.href="/?s=/mobile/mine";
                        },1000);
                        return false;
                    }
                    var goods_sku_id = "";
                    var nameMsg = "";

                    buySpecData.forEach(item => {
                        goods_sku_id += item + "_";
                    });
                    buyNameData.forEach(item => {
                        nameMsg += item + "、";
                    })
                    if(goods_sku_id !== 0){
                        goods_sku_id = goods_sku_id.substring(0,goods_sku_id.length-1);
                    }else{
                        goods_sku_id = 0;
                    }
                    if(nameMsg == ""){
                        nameMsg = nameMsg.substring(0,nameMsg.length-1);
                    }else{
                        nameMsg = "";
                    }
                    var eleLength = $(".weui-spec-buy .weui-media-box_appmsg ul").length;
                    if(eleLength == buySpecData.length && eleLength > 0){
                        var obj = {
                            delivery : 0,
                            shop_id : 0,
                            coupon_id : 0,
                            is_use_points : 0,
                            goods_id : data.data.detail.goods_id,
                            goods_num : Number($(".Amount").val()) ?Number($(".Amount").val()): 1 ,
                            goods_sku_id : goods_sku_id,
                            wxapp_id : 10001,
                            token : token
                        };
                        window.localStorage.setItem("orderCheckoutParam",JSON.stringify(obj));
                        location.href="/?s=/mobile/order/checkout";
                    }else if(eleLength == 0){
                        /**
                         * 单规格添加购物车操作
                         */
                        var obj = {
                            delivery : 0,
                            shop_id : 0,
                            coupon_id : 0,
                            is_use_points : 0,
                            goods_id : data.data.detail.goods_id,
                            goods_num : Number($(".Amount").val()) ?Number($(".Amount").val()): 1 ,
                            goods_sku_id : Number(goods_sku_id),
                            wxapp_id : 10001,
                            token : token
                        };
                        window.localStorage.setItem("orderCheckoutParam",JSON.stringify(obj));
                        location.href="/?s=/mobile/order/checkout";
                    }else{
                        $.toast("请选择商品规格", "cancel");
                        $(".weui-spec").slideUp(500);
                        $(".weui-spec").fadeIn(1000);
                        return false;
                    }
                });
            }
        },
        complete:function () {
            $.hideLoading();
        }
    });
    /**
     * 商品数量增长减少的操作
     */
    $(document.body).delegate(".sub","click",function () {
        var nowNumber = Number($(this).parent().find(".Amount").val());
        if(nowNumber == 2){
            $(this).parent().find(".Amount").val(nowNumber - 1);
            $(this).parent().find(".sub").removeClass("Decrease").addClass("DisDe");
        }else if(nowNumber == 1){

        }else{
            $(this).parent().find(".Amount").val(nowNumber - 1);
        }
    });
    $(document.body).delegate(".add","click",function () {
        var nowNumber = Number($(this).parent().find(".Amount").val());
        if(nowNumber == 1){
            $(this).parent().find(".sub").removeClass("DisDe").addClass("Decrease");
        }
        $(this).parent().find(".Amount").val(nowNumber + 1);
    });

    var itemIndex = 0;
    var data = false;

    var counter = 0;
    // 每页展示4个
    var num = 15;
    var pageStart = 0,pageEnd = 0;

    // dropload
    var dropload = $('.comment').parent().dropload({
        scrollArea : window,
        loadDownFn : function(me) {
            $.ajax({
                type: 'GET',
                url: '/?s=/api/comment/lists',
                data: {
                    goods_id: goods_id,
                    scoreType: -1,
                    page: 1,
                    wxapp_id:10001,
                    token : token
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
                    var imgStr = '';
                    if (pageStart <= datas.length) {
                        for (var i = pageStart; i < pageEnd; i++) {
                            if(datas[i].score == 10){
                                t = 5;
                            }else if(datas[i].score == 20){
                                t = 3;
                            }else if(datas[i].score == 30){
                                t = 1;
                            }
                            datas[i].image.forEach(item => {
                                imgStr += `
                                    <li class="weui-uploader__file" style="background-image:url(${item.file_path})"></li>
                                `;
                            });
                            var avatarUrl = datas[i].user !== null ? datas[i].user.avatarUrl : '';
                            var nickName = datas[i].user !== null ? datas[i].user.nickName : '';
                            var content = datas[i].user !== null ? datas[i].user.content : '';
                            result += `
                                <div class="weui-panel__bd">
                                  <div class="wy-media-box weui-media-box_text">
                                    <div class="weui-cell nopd weui-cell_access">
                                      <div class="weui-cell__hd"><img src="${avatarUrl}" alt="" style="width:20px;margin-right:5px;display:block"></div>
                                      <div class="weui-cell__bd weui-cell_primary"><p>${nickName}</p></div>
                                      <span class="weui-cell__time">${datas[i].create_time}</span>
                                    </div>
                                    <div class="comment-item-star"><span class="real-star comment-stars-width${t}"></span></div>
                                    <p class="weui-media-box__desc">${datas[i].content}</p>
                                    <ul class="weui-uploader__files clear mg-com-img">
                                        `+imgStr+`
                                    </ul>
                                  </div>
                                </div>
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
                        $('.comment').eq(itemIndex).append(result);
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