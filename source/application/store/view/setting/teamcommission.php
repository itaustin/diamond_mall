<div class="row-content am-cf">
    <div class="row">
        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
            <div class="widget am-cf">
                <form id="my-form" class="am-form tpl-form-line-form" enctype="multipart/form-data" method="post">
                    <div class="widget-body">
                        <fieldset>
                            <div class="widget-head am-cf">
                                <div class="widget-title am-fl">团队奖金设置</div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-form-label">
                                    团队奖金设置
                                </label>
                                <div class="am-u-sm-9">
                                    <label class="am-radio-inline">
                                        <input type="radio" name="team" value="first" data-am-ucheck checked> 报单商城
                                    </label>
                                    <label class="am-radio-inline">
                                        <input type="radio" name="team" value="second" data-am-ucheck> 会员商城
                                    </label>
                                </div>
                            </div>
                            <div id="first" class="form-tab-group active">
                                <div class="am-form-group am-g">
                                    <label class="am-u-sm-3 am-form-label">
                                        直推满足 <span class="tpl-form-line-small-title"></span>
                                    </label>
                                    <div class="am-u-sm-1">
                                        <input type="text" class="tpl-form-input"
                                               name="teamcommission[1][first_team][level_one][first]"
                                               value="<?= $values[1]['first_team']['level_one']['first'] ?>">
                                    </div>
                                    <label class="am-u-sm-3 am-form-label">
                                        直推+二层满足 <span class="tpl-form-line-small-title"></span>
                                    </label>
                                    <div class="am-u-sm-1">
                                        <input type="text" class="tpl-form-input"
                                               name="teamcommission[1][first_team][level_one][second]"
                                               value="<?= $values[1]['first_team']['level_one']['second'] ?>">
                                    </div>
                                    <label class="am-u-sm-3 am-form-label am-u-end">
                                        奖金 <span class="tpl-form-line-small-title"></span>
                                    </label>
                                    <div class="am-u-sm-1 am-u-end">
                                        <input type="text" class="tpl-form-input"
                                               name="teamcommission[1][first_team][level_one][money]"
                                               value="<?= $values[1]['first_team']['level_one']['money'] ?>">
                                    </div>
                                </div>
                                <div class="am-form-group am-g">
                                    <label class="am-u-sm-3 am-form-label">
                                        直推满足 <span class="tpl-form-line-small-title"></span>
                                    </label>
                                    <div class="am-u-sm-1">
                                        <input type="text" class="tpl-form-input"
                                               name="teamcommission[1][first_team][level_two][first]"
                                               value="<?= $values[1]['first_team']['level_two']['first'] ?>">
                                    </div>
                                    <label class="am-u-sm-3 am-form-label">
                                        直推+二层满足 <span class="tpl-form-line-small-title"></span>
                                    </label>
                                    <div class="am-u-sm-1">
                                        <input type="text" class="tpl-form-input"
                                               name="teamcommission[1][first_team][level_two][second]"
                                               value="<?= $values[1]['first_team']['level_two']['second'] ?>">
                                    </div>
                                    <label class="am-u-sm-3 am-form-label am-u-end">
                                        奖金 <span class="tpl-form-line-small-title"></span>
                                    </label>
                                    <div class="am-u-sm-1 am-u-end">
                                        <input type="text" class="tpl-form-input"
                                               name="teamcommission[1][first_team][level_two][money]"
                                               value="<?= $values[1]['first_team']['level_two']['money'] ?>">
                                    </div>
                                </div>
                                <div class="am-form-group am-g">
                                    <label class="am-u-sm-3 am-form-label">
                                        直推满足 <span class="tpl-form-line-small-title"></span>
                                    </label>
                                    <div class="am-u-sm-1">
                                        <input type="text" class="tpl-form-input"
                                               name="teamcommission[1][first_team][level_three][first]"
                                               value="<?= $values[1]['first_team']['level_three']['first'] ?>">
                                    </div>
                                    <label class="am-u-sm-3 am-form-label">
                                        直推+二层满足 <span class="tpl-form-line-small-title"></span>
                                    </label>
                                    <div class="am-u-sm-1">
                                        <input type="text" class="tpl-form-input"
                                               name="teamcommission[1][first_team][level_three][second]"
                                               value="<?= $values[1]['first_team']['level_three']['second'] ?>">
                                    </div>
                                    <label class="am-u-sm-3 am-form-label am-u-end">
                                        奖金 <span class="tpl-form-line-small-title"></span>
                                    </label>
                                    <div class="am-u-sm-1 am-u-end">
                                        <input type="text" class="tpl-form-input"
                                               name="teamcommission[1][first_team][level_three][money]"
                                               value="<?= $values[1]['first_team']['level_three']['money'] ?>">
                                    </div>
                                </div>
                                <div class="am-form-group am-g">
                                    <label class="am-u-sm-3 am-form-label">
                                        直推满足 <span class="tpl-form-line-small-title"></span>
                                    </label>
                                    <div class="am-u-sm-1">
                                        <input type="text" class="tpl-form-input"
                                               name="teamcommission[1][first_team][level_four][first]"
                                               value="<?= $values[1]['first_team']['level_four']['first'] ?>">
                                    </div>
                                    <label class="am-u-sm-3 am-form-label">
                                        直推+二层满足 <span class="tpl-form-line-small-title"></span>
                                    </label>
                                    <div class="am-u-sm-1">
                                        <input type="text" class="tpl-form-input"
                                               name="teamcommission[1][first_team][level_four][second]"
                                               value="<?= $values[1]['first_team']['level_four']['second'] ?>">
                                    </div>
                                    <label class="am-u-sm-3 am-form-label am-u-end">
                                        奖金 <span class="tpl-form-line-small-title"></span>
                                    </label>
                                    <div class="am-u-sm-1 am-u-end">
                                        <input type="text" class="tpl-form-input"
                                               name="teamcommission[1][first_team][level_four][money]"
                                               value="<?= $values[1]['first_team']['level_four']['money'] ?>">
                                    </div>
                                </div>
                            </div>
                            <div id="second"
                                 class="form-tab-group ">
                                <div class="am-form-group am-g">
                                    <label class="am-u-sm-3 am-form-label">
                                        直推满足 <span class="tpl-form-line-small-title"></span>
                                    </label>
                                    <div class="am-u-sm-1">
                                        <input type="text" class="tpl-form-input"
                                               name="teamcommission[2][first_team][level_one][first]"
                                               value="<?= $values[2]['first_team']['level_one']['first'] ?>">
                                    </div>
                                    <label class="am-u-sm-3 am-form-label">
                                        直推+二层满足 <span class="tpl-form-line-small-title"></span>
                                    </label>
                                    <div class="am-u-sm-1">
                                        <input type="text" class="tpl-form-input"
                                               name="teamcommission[2][first_team][level_one][second]"
                                               value="<?= $values[2]['first_team']['level_one']['second'] ?>">
                                    </div>
                                    <label class="am-u-sm-3 am-form-label am-u-end">
                                        奖金 <span class="tpl-form-line-small-title"></span>
                                    </label>
                                    <div class="am-u-sm-1 am-u-end">
                                        <input type="text" class="tpl-form-input"
                                               name="teamcommission[2][first_team][level_one][money]"
                                               value="<?= $values[2]['first_team']['level_one']['money'] ?>">
                                    </div>
                                </div>
                                <div class="am-form-group am-g">
                                    <label class="am-u-sm-3 am-form-label">
                                        直推满足 <span class="tpl-form-line-small-title"></span>
                                    </label>
                                    <div class="am-u-sm-1">
                                        <input type="text" class="tpl-form-input"
                                               name="teamcommission[2][first_team][level_two][first]"
                                               value="<?= $values[2]['first_team']['level_two']['first'] ?>">
                                    </div>
                                    <label class="am-u-sm-3 am-form-label">
                                        直推+二层满足 <span class="tpl-form-line-small-title"></span>
                                    </label>
                                    <div class="am-u-sm-1">
                                        <input type="text" class="tpl-form-input"
                                               name="teamcommission[2][first_team][level_two][second]"
                                               value="<?= $values[2]['first_team']['level_two']['second'] ?>">
                                    </div>
                                    <label class="am-u-sm-3 am-form-label am-u-end">
                                        奖金 <span class="tpl-form-line-small-title"></span>
                                    </label>
                                    <div class="am-u-sm-1 am-u-end">
                                        <input type="text" class="tpl-form-input"
                                               name="teamcommission[2][first_team][level_two][money]"
                                               value="<?= $values[2]['first_team']['level_two']['money'] ?>">
                                    </div>
                                </div>
                                <div class="am-form-group am-g">
                                    <label class="am-u-sm-3 am-form-label">
                                        直推满足 <span class="tpl-form-line-small-title"></span>
                                    </label>
                                    <div class="am-u-sm-1">
                                        <input type="text" class="tpl-form-input"
                                               name="teamcommission[2][first_team][level_three][first]"
                                               value="<?= $values[2]['first_team']['level_three']['first'] ?>">
                                    </div>
                                    <label class="am-u-sm-3 am-form-label">
                                        直推+二层满足 <span class="tpl-form-line-small-title"></span>
                                    </label>
                                    <div class="am-u-sm-1">
                                        <input type="text" class="tpl-form-input"
                                               name="teamcommission[2][first_team][level_three][second]"
                                               value="<?= $values[2]['first_team']['level_three']['second'] ?>">
                                    </div>
                                    <label class="am-u-sm-3 am-form-label am-u-end">
                                        奖金 <span class="tpl-form-line-small-title"></span>
                                    </label>
                                    <div class="am-u-sm-1 am-u-end">
                                        <input type="text" class="tpl-form-input"
                                               name="teamcommission[2][first_team][level_three][money]"
                                               value="<?= $values[2]['first_team']['level_three']['money'] ?>">
                                    </div>
                                </div>
                                <div class="am-form-group am-g">
                                    <label class="am-u-sm-3 am-form-label">
                                        直推满足 <span class="tpl-form-line-small-title"></span>
                                    </label>
                                    <div class="am-u-sm-1">
                                        <input type="text" class="tpl-form-input"
                                               name="teamcommission[2][first_team][level_four][first]"
                                               value="<?= $values[2]['first_team']['level_four']['first'] ?>">
                                    </div>
                                    <label class="am-u-sm-3 am-form-label">
                                        直推+二层满足 <span class="tpl-form-line-small-title"></span>
                                    </label>
                                    <div class="am-u-sm-1">
                                        <input type="text" class="tpl-form-input"
                                               name="teamcommission[2][first_team][level_four][second]"
                                               value="<?= $values[2]['first_team']['level_four']['second'] ?>">
                                    </div>
                                    <label class="am-u-sm-3 am-form-label am-u-end">
                                        奖金 <span class="tpl-form-line-small-title"></span>
                                    </label>
                                    <div class="am-u-sm-1 am-u-end">
                                        <input type="text" class="tpl-form-input"
                                               name="teamcommission[2][first_team][level_four][money]"
                                               value="<?= $values[2]['first_team']['level_four']['money'] ?>">
                                    </div>
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

        // 切换默认上传方式
        $("input:radio[name='team']").change(function (e) {
            $('.form-tab-group').removeClass('active');
            $('#' + e.currentTarget.value).addClass('active');
        });

        /**
         * 表单验证提交
         * @type {*}
         */
        $('#my-form').superForm();

    });
</script>
