$(function () {
    var token = window.localStorage.getItem("token");
    $.ajax({
        type : "get",
        dataType : "json",
        url : "/?s=/api/user.order/detail",
        data: {
            order_id : order_id,
            wxapp_id : 10001,
            token : token
        },
        beforeSend:function(){
            layer.open({
                type : 2,
                shadeClose : false,
                content : "加载中..."
            });
        },
        success : function (data) {
            var address = data.data.order.address;
            var address = `
                    <div class="weui-media-box_appmsg">
                        <div class="weui-media-box__hd proinfo-txt-l" style="width:20px;">
                            <span class="promotion-label-tit"><img src="/assets/mobile/images/icon_nav_city.png" /></span>
                        </div>
                        <div class="weui-media-box__bd">
                            <a href="address_list.html" class="weui-cell_access"> <h4 class="address-name"><span>${address.name}</span><span>${address.phone}</span></h4>
                                <div class="address-txt">
                                    ${address.region.province} - ${address.region.city} - ${address.region.region}${address.detail}
                                </div>
                            </a>
                        </div>
                    </div>
                `;
            $(".address-content").html(address);
            var express = `
                <div class="weui-media-box_appmsg">
                    <div class="weui-media-box__bd">
                        <a href="/?s=/mobile/order/express&order_id=${data.data.order.order_id}" class="weui-cell_access">
                            <h4 class="address-name">
                                物流信息
                            </h4>
                            <div class="address-txt">物流公司：<em class="express_name">${data.data.order.express.express_name}</em></div>
                            <div class="address-txt">物流单号：<em class="express_no">${data.data.order.express_no}</em></div>
                        </a>
                    </div>
                    <div class="weui-media-box__hd proinfo-txt-l" style="width:16px;">
                        <div class="weui-cell_access">
                            <span class="weui-cell__ft"></span>
                        </div>
                    </div>
                </div>
            `;
            $(".express_content").html(express);
            var goods = ``;
            data.data.order.goods.forEach(function (item , itemKey) {
                goods += `
                    <div class="weui-media-box_appmsg ord-pro-list">
                        <div class="weui-media-box__hd">
                            <a href="pro_info.html"><img class="weui-media-box__thumb" src="${item.image.file_path}" alt="" /></a>
                        </div>
                        <div class="weui-media-box__bd">
                            <h1 class="weui-media-box__desc"><a href="pro_info.html" class="ord-pro-link">${item.goods_name}</a></h1>
                            <p class="weui-media-box__desc">规格：<span>${item.goods_attr}</span></p>
                            <div class="clear mg-t-10">
                                <div class="wy-pro-pri fl">
                                    &yen;
                                    <em class="num font-15">${item.total_pay_price}</em>
                                </div>
                                <div class="pro-amount fr">
                                    <span class="font-13">数量&times;<em class="name">${item.total_num}</em></span>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            $(".goods_lists").html(goods);
            $(".state").text(data.data.order.state_text);
            $(".express_price").text(data.data.order.express_price);
            $(".goods_price").text(data.data.order.total_price);
        },
        complete : function () {
            layer.closeAll();
        }
    });
});