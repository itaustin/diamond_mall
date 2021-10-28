$(function () {
    var token = localStorage.getItem("token");

    $.get("/?s=/api/user.dealer/withdraw",{
        wxapp_id : 10001,
        token : token,
    },function (data) {
        if(data.code == -1){
            history.back();
        }
        var alipay_account = data.data.dealer.user.alipay_account;
        var alipay_name = data.data.dealer.user.alipay_name;
        if(alipay_account !== ""){
            var alipay_html = `<input name="alipay_account" placeholder="请输入支付宝账号" value="${alipay_account}" disabled />`;
        }else{
            var alipay_html = `<input name="alipay_account" placeholder="请输入支付宝账号" value="" />`;
        }
        if(alipay_name !== ""){
            var alipay_name_html = `<input name="alipay_name" placeholder="请输入支付宝账号" value="${alipay_name}" disabled />`;
        }else{
            var alipay_name_html = `<input name="alipay_name" placeholder="请输入支付宝账号" value="" />`;
        }
        data.data.settlement.pay_type.forEach(function (item, key) {
            if(Number(item) === 10){
                $(".applyItem").append(`
                    <block class="wechat">
                        <!-- 微信支付 -->
                        <div class="form__field clicks_this dis-flex flex-y-center">
                            <div class="form__radio dis-flex flex-y-center" data-payment="10">
                                <span class="radio__icon c-violet iconfont icon-radio"></span>
                                <span class="f-28">微信支付</span>
                            </div>
                        </div>
                    </block>
                `);
            }
            if(Number(item) === 20){

                $(".applyItem").append(`
                    <block class="alipay">
                        <!-- 支付宝 -->
                        <div class="form__field clicks_this dis-flex flex-y-center">
                            <div class="form__radio dis-flex flex-y-center" data-payment="20">
                                <span class="radio__icon col-bb iconfont icon-radio"></span>
                                <span class="f-28">支付宝</span>
                            </div>
                        </div>
                        <block class="alipay" style="display:none;">
                            <div class="form__field dis-flex flex-y-center">
                                <div class="field-input flex-box">
                                    ${alipay_name_html}
                                </div>
                            </div>
                            <div class="form__field dis-flex flex-y-center">
                                <div class="field-input flex-box">
                                    ${alipay_html}
                                </div>
                            </div>
                        </block>
                    </block>
                `);
            }
            if(Number(item) === 30){
                $(".applyItem").append(`
                    <block class="bank">
                        <!-- 银行卡 -->
                        <div class="form__field clicks_this dis-flex flex-y-center">
                            <div class="form__radio dis-flex flex-y-center" catchtap="toggleChecked" data-payment="30">
                                <span class="radio__icon col-bb iconfont icon-radio"></span>
                                <span class="f-28">银行卡</span>
                            </div>
                        </div>
                        <block class="bankcard" style="display:none;">
                            <div class="form__field dis-flex flex-y-center">
                                <div class="field-input flex-box">
                                    <input name="bank_name" placeholder="请输入姓名" />
                                </div>
                            </div>
                            <div class="form__field dis-flex flex-y-center">
                                <div class="field-input flex-box">
                                    <input name="bank_account" placeholder="请输入开户行名称/地址" />
                                </div>
                            </div>
                            <div class="form__field dis-flex flex-y-center">
                                <div class="field-input flex-box">
                                    <input name="bank_card" placeholder="请输入银行卡号" value="" />
                                </div>
                            </div>
                        </block>
                    </block>
                `);
            }
        });
        $(".clicks_this").click(function () {
            $(".radio__icon").siblings().prev().removeClass('c-violet').addClass('col-bb');
            $(this).find(".radio__icon").addClass('c-violet').removeClass('col-bb');
            var type = $(this).find(".radio__icon").parent().find(".f-28").text();
            if(type === "支付宝"){
                $(this).parent().find('.alipay').show();
                $(this).parent().next().find('.bankcard').hide();
            }else if(type == "银行卡"){
                $(this).parent().find('.bankcard').show();
                $(this).parent().prev().find('.alipay').hide();
            }else if(type == "微信支付"){
                $(this).parent().siblings().find('.alipay').hide();
                $(this).parent().siblings().find('.bankcard').hide();
            }
        });
        $(".f-34").text(data.data.dealer.money);
        $(".min-money").text(data.data.settlement.min_money);
    });

    var msg = "";
    function submit(data,type){
        $.ajax({
            type : "post",
            dataType : "json",
            url : "/?s=/api/user.dealer.withdraw/submit",
            data : {
                data : JSON.stringify(data),
                wxapp_id : 10001,
                token : token
            },
            beforeSend: function(){
                msg = layer.open({
                    type : 2,
                    shadeClose : false,
                    content : '提交中...'
                });
                $('button[type=submit]').attr('disabled',true);
            },
            success : function (data) {
                if(data.code === 1){
                    layer.open({
                        content: data.msg
                        ,skin: 'msg'
                        ,time: 2 //2秒后自动关闭
                    });
                    $('button[type=submit]').hide();
                    setTimeout(function () {
                        location.href="/?s=/mobile/mine/dealer";
                    },1000);
                }else{
                    layer.open({
                        content: data.msg
                        ,skin: 'msg'
                        ,time: 2 //2秒后自动关闭
                    });
                }
            },
            complete : function () {
                layer.close(msg);
                $('button[type=submit]').attr('disabled',false);
            }
        });
    }

    $("button[type=submit]").click(function () {
        var thisEleName = $(".c-violet").next().text();
        var money = $("input[name=money]").val();
        var data = null;
        if(!money){
            layer.open({
                content: '请填写提现金额'
                ,skin: 'msg'
                ,time: 2 //2秒后自动关闭
            });
        }else{
            if(thisEleName === "微信支付"){
                data = {
                    'money' : money,
                    'pay_type' : 10
                };
                submit(data);
            }else if(thisEleName === "支付宝"){
                if($('input[name=alipay_name]').val() == "" || $('input[name=alipay_account]').val() == ""){
                    layer.open({
                        content: '请填写完整信息后提交'
                        ,skin: 'msg'
                        ,time: 2 //2秒后自动关闭
                    });
                }else{
                    data = {
                        "money":money,
                        "alipay_name":$('input[name=alipay_name]').val(),
                        "alipay_account":$('input[name=alipay_account]').val(),
                        "pay_type":20
                    };
                    submit(data);
                }
            }else if(thisEleName === "微信支付"){
                if($('input[name=bank_name]').val() == "" || $('input[name=bank_account]').val() == "" || $('input[name=bank_card]').val() == ""){
                    layer.open({
                        content: '请填写完整信息后提交'
                        ,skin: 'msg'
                        ,time: 2 //2秒后自动关闭
                    });
                }else{
                    data = {
                        "money":money,
                        "bank_name":$('input[name=bank_name]').val(),
                        "bank_account":$('input[name=bank_account]').val(),
                        "bank_card":$('input[name=bank_card]').val(),
                        "pay_type":30
                    };
                    submit(data);
                }
            }
        }
        return false;
    });
});