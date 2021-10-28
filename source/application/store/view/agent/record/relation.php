<div class="row-content am-cf">
    <div class="row">
        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
            <div class="widget am-cf">
                <div class="widget-head am-cf">
                    <div class="widget-title am-cf">伙伴分润记录</div>
                </div>
                <div class="widget-body am-fr">
                    <!-- 工具栏 -->
                    <div class="page_toolbar am-margin-bottom-xs am-cf">
                        <form class="toolbar-form" action="">
                            <input type="hidden" name="s" value="/<?= $request->pathinfo() ?>">
                            <div class="am-u-sm-12 am-u-md-9 am-u-sm-push-3">
                                <div class="am fr">
                                    <div class="am-form-group am-fl">
                                        <div class="am-input-group am-input-group-sm tpl-form-border-form">
                                            <input type="text" class="am-form-field" name="search"
                                                   placeholder="请输入昵称/姓名/手机号"
                                                   value="<?= $request->get('search') ?>" />
                                            <div class="am-input-group-btn">
                                                <button class="am-btn am-btn-default am-icon-search"
                                                        type="submit"></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="am-scrollable-horizontal am-u-sm-12 am-padding-bottom-lg">
                        <table width="100%" class="am-table am-table-compact am-table-striped
                         tpl-table-black am-text-nowrap">
                            <thead>
                            <tr>
                                <th>分润ID</th>
                                <th>
                                    <p>姓名</p>
                                    <p>手机号</p>
                                </th>
                                <th>订单ID</th>
                                <th>订单号</th>
                                <th>支付金额</th>
                                <th>分润金额</th>
                                <th>代理类型</th>
                                <th>订单支付状态</th>
                                <th>分润时间</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (!$list->isEmpty()): foreach ($list as $item): ?>
                                <tr>
                                    <td class="am-text-middle"><?= $item['bonus_id'] ?></td>
                                    <td class="am-text-middle">
                                        <?php if (!empty($item['real_name']) || !empty($item['user_name'])): ?>
                                            <p><?= $item['real_name'] ?: '--' ?></p>
                                            <p><?= $item['user_name'] ?: '--' ?></p>
                                        <?php else: ?>
                                            <p>--</p>
                                        <?php endif; ?>
                                    </td>
                                    <td class="am-text-middle">
                                        <?= $item['with_order']['order_id'] ?>
                                    </td>
                                    <td class="am-text-middle">
                                        <?= $item['with_order']['order_no'] ?>
                                    </td>
                                    <td class="am-text-middle">
                                        <?= $item['with_order']['pay_price'] ?>
                                    </td>
                                    <td class="am-text-middle">
                                        <?= $item['bonus_money'] ?>
                                    </td>
                                    <td class="am-text-middle">
                                        <?php
                                            if($item['agent_type'] == 2){
                                                echo "省代理";
                                            } else if($item['agent_type'] == 3){
                                                echo "市代理";
                                            } else if($item['agent_type'] == 4){
                                                echo "区域代理";
                                            } else if($item['agent_type'] == 5){
                                                echo "小区代理";
                                            }
                                        ?>
                                    </td>
                                    <td class="am-text-middle">
                                        <?php if($item['with_order']['pay_status'] == 10): ?>
                                            <span class="am-badge">未支付</span>
                                        <?php else : ?>
                                            <span class="am-badge am-badge-secondary">已支付</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="am-text-middle"><?= $item['create_time'] ?></td>
                                </tr>
                            <?php endforeach; else: ?>
                                <tr>
                                    <td colspan="9" class="am-text-center">暂无记录</td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                        <div class="am-u-lg-12 am-cf">
                            <div class="am-fr"><?= $list->render() ?> </div>
                            <div class="am-fr pagination-total am-margin-right">
                                <div class="am-vertical-align-middle">总记录：<?= $list->total() ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 分销商审核 -->
<script id="tpl-dealer-apply" type="text/template">
    <div class="am-padding-top-sm">
        <form class="form-dealer-apply am-form tpl-form-line-form" method="post"
              action="<?= url('agent.agent/submit') ?>">
            <input type="hidden" name="apply_id" value="{{ id }}">
            <div class="am-form-group">
                <label class="am-u-sm-3 am-form-label"> 审核状态 </label>
                <div class="am-u-sm-9">
                    <label class="am-radio-inline">
                        <input type="radio" name="apply[apply_status]" value="20" data-am-ucheck
                               checked> 审核通过
                    </label>
                    <label class="am-radio-inline">
                        <input type="radio" name="apply[apply_status]" value="30" data-am-ucheck> 驳回
                    </label>
                </div>
            </div>
            <div class="am-form-group">
                <label class="am-u-sm-3 am-form-label"> 驳回原因 </label>
                <div class="am-u-sm-9">
                    <input type="text" class="tpl-form-input" name="apply[reject_reason]" placeholder="仅在驳回时填写"
                           value="">
                </div>
            </div>
        </form>
    </div>
</script>

<script>
    $(function () {

        /**
         * 审核操作
         */
        $('.j-audit').click(function () {
            var $this = $(this);
            layer.open({
                type: 1
                , title: '分销商审核'
                , area: '340px'
                , offset: 'auto'
                , anim: 1
                , closeBtn: 1
                , shade: 0.3
                , btn: ['确定', '取消']
                , content: template('tpl-dealer-apply', $this.data())
                , success: function (layero) {
                    // 注册radio组件
                    layero.find('input[type=radio]').uCheck();
                }
                , yes: function (index, layero) {
                    // 表单提交
                    layero.find('.form-dealer-apply').ajaxSubmit({
                        type: 'post',
                        dataType: 'json',
                        success: function (result) {
                            result.code === 1 ? $.show_success(result.msg, result.url)
                                : $.show_error(result.msg);
                        }
                    });
                    layer.close(index);
                }
            });
        });

        /**
         * 确认支付成功
         */
        $('.j-money').click(function () {
            var id = $(this).data('id');
            var url = "<?= url('agent.agent/confirm_transfer') ?>";
            layer.confirm('确定用户转账成功吗？', {title: '友情提示'}, function (index) {
                $.post(url, {apply_id: id}, function (result) {
                    result.code === 1 ? $.show_success(result.msg, result.url)
                        : $.show_error(result.msg);
                });
                layer.close(index);
            });
        });

        /**
         * 显示驳回原因
         */
        $('.j-show-reason').click(function () {
            var $this = $(this);
            layer.alert($this.data('reason'), {title: '驳回原因'});
        });

    });
</script>

