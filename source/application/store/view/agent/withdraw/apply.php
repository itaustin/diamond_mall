<div class="row-content am-cf">
    <div class="row">
        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
            <div class="widget am-cf">
                <form id="my-form" class="am-form tpl-form-line-form" enctype="multipart/form-data" method="post">
                    <div class="widget-body">
                        <fieldset>
                            <div class="widget-head am-cf">
                                <div class="widget-title am-fl">提现申请</div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require"> 用户名 </label>
                                <div class=" am-u-sm-9 am-u-end">
                                    <input type="text" class="tpl-form-input"
                                           value="<?= $model['real_name'] ?>" required>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require"> 可提现余额 </label>
                                <div class=" am-u-sm-9 am-u-end">
                                    <input type="text" class="tpl-form-input" disabled
                                           value="<?= $model['money'] ?>">
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require"> 提现方式 </label>
                                <div class=" am-u-sm-9 am-u-end">
                                    <label class="am-radio-inline">
                                        <input type="radio" name="user[pay_type]" value="20" data-am-ucheck checked>
                                        支付宝
                                    </label>
                                    <label class="am-radio-inline">
                                        <input type="radio" name="user[pay_type]" value="30" data-am-ucheck>
                                        <span>银行卡</span>
                                    </label>
                                </div>
                            </div>
                            <div class="alipay">
                                <div class="am-form-group">
                                    <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require"> 真实姓名 </label>
                                    <div class=" am-u-sm-9 am-u-end">
                                        <input type="text" class="tpl-form-input" name="user[alipay_name]"
                                               value="" required>
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require"> 提现金额 </label>
                                    <div class=" am-u-sm-9 am-u-end">
                                        <input type="text" class="tpl-form-input" name="user[money]"
                                               value="" required>
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require"> 支付宝账号 </label>
                                    <div class=" am-u-sm-9 am-u-end">
                                        <input type="text" class="tpl-form-input" name="user[alipay_account]"
                                               value="" required>
                                    </div>
                                </div>
                            </div>
                            <div class="bank">

                            </div>
                            <div class="am-form-group">
                                <div class=" am-u-sm-9 am-u-end am-u-sm-push-3 am-margin-top-lg">
                                    <button type="submit" class="j-submit am-btn am-btn-secondary">提交
                                    </button>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    $(function () {

        // 切换提现方式
        $('input:radio[name="user[pay_type]"]').change(function (e) {
            var $bank = $('.bank')
                , $alipay = $('.alipay');
            if (e.currentTarget.value === '20') {
                $bank.empty();
                $alipay.empty();
                $alipay.html(
                    `<div class="am-form-group">
                        <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require"> 提现金额 </label>
                        <div class=" am-u-sm-9 am-u-end">
                            <input type="text" class="tpl-form-input" name="user[money]"
                                   value="" required>
                        </div>
                    </div>
                    <div class="am-form-group">
                        <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require"> 真实姓名 </label>
                        <div class=" am-u-sm-9 am-u-end">
                            <input type="text" class="tpl-form-input" name="user[alipay_name]"
                                   value="" required>
                        </div>
                    </div>
                    <div class="am-form-group">
                        <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require"> 支付宝账号 </label>
                        <div class=" am-u-sm-9 am-u-end">
                            <input type="text" class="tpl-form-input" name="user[alipay_account]"
                                   value="" required>
                        </div>
                    </div>`
                );
            } else {
                $bank.empty();
                $alipay.empty();
                $bank.html(
                    `<div class="am-form-group">
                            <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require"> 提现金额 </label>
                            <div class=" am-u-sm-9 am-u-end">
                                <input type="text" class="tpl-form-input" name="user[money]"
                                       value="" required>
                            </div>
                        </div>
                        <div class="am-form-group">
                            <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require"> 开户行名称/地址:</label>
                            <div class=" am-u-sm-9 am-u-end">
                                <input type="text" class="tpl-form-input" name="user[bank_name]"
                                       value="" required>
                            </div>
                        </div>
                        <div class="am-form-group">
                            <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require"> 银行开户名:</label>
                            <div class=" am-u-sm-9 am-u-end">
                                <input type="text" class="tpl-form-input" name="user[bank_account]"
                                       value="" required>
                            </div>
                        </div>
                        <div class="am-form-group">
                            <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require"> 银行卡号:</label>
                            <div class=" am-u-sm-9 am-u-end">
                                <input type="text" class="tpl-form-input" name="user[bank_card]"
                                       value="" required>
                            </div>
                        </div>`
                );
            }
        });

        /**
         * 表单验证提交
         * @type {*}
         */
        $('#my-form').superForm();

    });
</script>
