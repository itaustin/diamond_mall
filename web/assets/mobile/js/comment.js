$(function () {
    var token = window.localStorage.getItem("token");

    function initFormData(goodsList){
        let data = [];
        data['formData'] = [];
        data['goodsList'] = [];
        data['options'] = null;
        goodsList.forEach(function (item) {
            data['formData'].push({
                goods_id: item.goods_id,
                order_goods_id: item.order_goods_id,
                score: 10,
                content: '',
                image_list: [

                ],
                uploaded: []
            });
            data['goodsList'].push(item);
        });
        data["options"] = {order_id : goodsList[0].order_id};
        return data;
    }

    $.ajax({
        type : "get",
        dataType : "json",
        url : "/?s=/api/user.comment/order",
        data : {
            order_id : order_id,
            wxapp_id : 10001,
            token : token
        },
        success : function (data) {
            if(data.code == 1){
                var card = '';
                var goodsList = data.data.goodsList;

                for (var i = 0; i < data.data.goodsList.length; i++){
                    card += `
                        <div class="goods-item goods-item-${goodsList[i].order_id}" data-index="${i}" data-goods_id = "${goodsList[i].goods_id}" data-order_id = "${goodsList[i].order_id}" data-order_goods_id = "${goodsList[i].order_goods_id}" style="margin-bottom:20px;">
                            <div class="weui-media-box__bd  pd-10 " style="background:#fff;border-top:1px solid #ccc;border-bottom:1px solid #ccc;">
                                <div class="weui-media-box_appmsg ord-pro-list">
                                    <div class="weui-media-box__hd">
                                        <a href="/?s=/mobile/goods/detail/goods_id/10006">
                                            <img class="weui-media-box__thumb" src="${goodsList[i].image.file_path}"
                                                 alt="">
                                        </a>
                                    </div>
                                    <div class="weui-media-box__bd">
                                        <h1 class="weui-media-box__desc">
                                            <a href="/?s=/mobile/order/detail/order_id/10015" class="ord-pro-link">
                                                ${goodsList[i].goods_name}
                                            </a>
                                        </h1>
                                        <div class="clear mg-t-10">
                                            <div class="wy-pro-pri fl">¥<em class="num font-15">${goodsList[i].total_price}</em></div>
                                            <div class="pro-amount fr">
                                                <span class="font-13">数量×<em class="name">${goodsList[i].total_num}</em></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="order-list-Below clear">
                                <h1>商品评价</h1>
                                <ul data-index="${i}">
                                    <li class="on"></li>
                                    <li class="on"></li>
                                    <li class="on"></li>
                                    <li class="on"></li>
                                    <li class="on"></li>
                                </ul>
                            </div>
                            <div class="weui-cells weui-cells_form com-txt-area commentText">
                                <div class="weui-cell">
                                    <div class="weui-cell__bd">
                                        <textarea placeholder="请输入评价内容 (留空则不评价)" data-index="${i}" class="weui-textarea txt-area commentTextarea" rows="3"></textarea>
                                        <div class="weui-textarea-counter font-12 num">
                                            <span>
                                            0/200
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="weui-cells weui-cells_form">
                                    <div class="weui-cell">
                                        <div class="weui-cell__bd">
                                            <div class="weui-uploader">
                                                <div class="weui-uploader__hd">
                                                    <p class="weui-uploader__title font-14">
                                                        图片上传
                                                    </p>
                                                    <!--<div class="weui-uploader__info font-12">
                                                        0/2
                                                    </div>-->
                                                </div>
                                                <div class="weui-uploader__bd">
                                                    <ul class="weui-uploader__files" class="uploaderFiles">
                                                    </ul>
                                                    <div class="weui-uploader__input-box">
                                                        <input data-index="${i}" class="weui-uploader__input uploaderInput" type="file" accept="image/*" multiple="">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                }
                $(".weui-content").html(card);

                var init = initFormData(data.data.goodsList);

                /**
                 * star处理
                 */
                $(".order-list-Below ul li").click(function(e) {
                        var num = $(this).index() + 1;
                        var len = $(this).index();
                        var thats = $(this).parent(".order-list-Below ul").find("li");
                        if ($(thats).eq(len).attr("class") == "on") {
                            if (num == 2 || num == 4) {
                                $.alert("1颗星-差评，3颗星-中评，5颗星-好评");
                                return false;
                            }
                            if ($(thats).eq(num).attr("class") == "on") {
                                $(thats).removeClass();
                                for (var i = 0; i < num; i++) {
                                    $(thats).eq(i).addClass("on");
                                }
                            } else {
                                $(thats).removeClass();
                                for (var k = 0; k < len; k++) {
                                    $(thats).eq(k).addClass("on");
                                }
                            }
                        } else {
                            if (num == 2 || num == 4) {
                                $.alert("1颗星-差评，3颗星-中评，5颗星-好评");
                                return false;
                            }
                            $(thats).removeClass();
                            for (var j = 0; j < num; j++) {
                                $(thats).eq(j).addClass("on");
                            }
                        }
                    }
                );
                $(".order-list-Below ul li").click(function(e) {
                    var num = $(this).parent().find("li.on").length;
                    if (num == 1) {
                        init['formData'][$(this).parent().data('index')].score = 30;
                    }else if(num == 3){
                        init['formData'][$(this).parent().data('index')].score = 20;
                    }else if(num == 5){
                        init['formData'][$(this).parent().data('index')].score = 10;
                    }
                });

                $("textarea").keyup(function () {
                    init['formData'][$(this).data('index')].content = $(this).val();
                });

                $(document).delegate(".uploaderInput","change",function () {
                    /**
                     * 设置文件加载到li标签
                     * @type {jQuery|HTMLElement}
                     * @private
                     */
                    var _this = $(this);
                    var file = _this[0].files[0];    //获取文件信息
                    if(file)
                    {
                        var reader=new FileReader();  //调用FileReader
                        reader.readAsDataURL(file); //将文件读取为 DataURL(base64)
                        reader.onload=function(evt){   //读取操作完成时触发。
                            _this.parent().parent().find("ul").append(`
                                <li class="weui-uploader__file" style="background-image:url(`+evt.target.result+`)"></li>
                            `);
                        };
                    }
                    init['formData'][$(this).data('index')].image_list.push(file);
                });

                $(".evaluate").click(function () {

                    let fromPostCall = function(formData){
                        $.ajax({
                            type : "post",
                            dataType : "json",
                            url : "/?s=/api/user.comment/order",
                            data : {
                                order_id : init['options']['order_id'],
                                formData : JSON.stringify(init['formData']),
                                wxapp_id : 10001,
                                token : token
                            },
                            success : function (data) {
                                if(data.code == 1){
                                    $.toast("评价发表成功");
                                    setTimeout(function () {
                                        history.back();
                                    },1000);
                                }
                            },
                            complete : function () {
                                layer.closeAll();
                            }
                        });
                    };

                    let formDatas = init['formData'];

                    function uploadFile(imagesLength,formData,callBack){
                        layer.open({
                            type : 2,
                            shadeClose : false,
                            content : "处理中..."
                        });
                        var form = new FormData();
                        let i = 0;
                        formData.forEach(function (item , formIndex) {
                            if(item.content !== ''){
                                item.image_list.forEach(function (filePath, fileKey) {
                                    form.append("iFile",filePath);
                                    form.append("wxapp_id",10001);
                                    form.append("token",token);
                                    $.ajax({
                                        type : "post",
                                        dataType : "json",
                                        url : "/?s=/api/upload/image",
                                        data : form,
                                        cache : false,
                                        processData : false ,//不处理数据
                                        contentType : false, //不设置内容类型
                                        success : function (data) {
                                            if(data.code === 1){
                                                item.uploaded[fileKey] = data.data.file_id;
                                                item.image_list[fileKey] = data.data.file_path;
                                            }
                                            form.delete('iFile');
                                        },
                                        complete : function () {
                                            i++;
                                            if(imagesLength === i){
                                                callBack && callBack(formData);
                                            }
                                        }
                                    });
                                });
                            }
                        });
                    }

                    let imagesLength = 0;
                    formDatas.forEach(function (item, formIndex) {
                        item.content !== '' && (imagesLength += item.image_list.length);
                    });

                    imagesLength > 0 ? uploadFile(imagesLength, formDatas,fromPostCall) : fromPostCall(formDatas);
                });

            } else if(data.code == -1){
                $.toast("未登录，系统即将引导您登陆");
                setTimeout(function () {
                    location.href="/?s=/mobile/mine";
                },1000);
                return false;
            }else if(data.code == 0){
                $.toast(data.msg);
                setTimeout(function () {
                    history.back();
                },1000);
            }
            return false;
        }
    });



});