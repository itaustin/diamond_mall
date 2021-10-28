<div class="row-content am-cf">
    <div class="row">
        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
            <div class="widget am-cf">
                <form id="my-form" class="am-form tpl-form-line-form" method="post">
                    <div class="widget-body">
                        <fieldset>
                            <div class="widget-head am-cf">
                                <div class="widget-title am-fl">添加自定义区域</div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">区域名称 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="text" class="tpl-form-input" name="custom_area[name]"
                                           value="" placeholder="请输入区域名称" required>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">选择父层地区 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <select name="custom_area[province_id]" id="province" data-am-selected="{btnSize: 'sm'}" required>
                                        <option value="0">点击选择</option>
                                        <?php if (isset($list)): foreach ($list as $area): ?>
                                            <option value="<?= $area['id'] ?>"> <?= $area['name'] ?></option>
                                        <?php endforeach; endif; ?>
                                    </select>
                                    <select name="custom_area[city_id]" id="city" data-am-selected="{btnSize: 'sm'}" required>

                                    </select>
                                    <select name="custom_area[region_id]" id="region" data-am-selected="{btnSize: 'sm'}" required>

                                    </select>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">详细地址 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="text" class="tpl-form-input" name="custom_area[address]"
                                           value="" required>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <div class="am-u-sm-9 am-u-sm-push-3 am-margin-top-lg">
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

        /**
         * 表单验证提交
         * @type {*}
         */
        $('#my-form').superForm();

        $("#province").change(function () {
            $.ajax({
                type : "post",
                dataType : "json",
                url : "/?s=/store/passport/get_region",
                data : {
                    type : "2",
                    id : $(this).val()
                },
                success: function (data) {
                    var html = ``;
                    data.data.forEach(function (value, key) {
                        html += `<option value="`+value.id+`">`+value.name+`</option>`;
                    });
                    $("#city").empty();
                    $("#city").append(html);
                    $("#region").empty();
                }
            });
        });
        $("#city").change(function () {
            $.ajax({
                type : "post",
                dataType : "json",
                url : "/?s=/store/passport/get_region",
                data : {
                    type : "2",
                    id : $(this).val()
                },
                success: function (data) {
                    var html = ``;
                    data.data.forEach(function (value, key) {
                        html += `<option value="`+value.id+`">`+value.name+`</option>`;
                    });
                    $("#region").empty();
                    $("#region").append(html);
                }
            });
        });
    });
</script>
