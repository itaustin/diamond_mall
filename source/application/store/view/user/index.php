<div class="row-content am-cf">
    <div class="row">
        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
            <div class="widget am-cf">
                <div class="widget-head am-cf">
                    <div class="widget-title am-cf">用户列表</div>
                </div>
                <div class="widget-body am-fr">
                    <!-- 工具栏 -->
                    <div class="page_toolbar am-margin-bottom-xs am-cf">
                        <form class="toolbar-form" action="">
                            <input type="hidden" name="s" value="/<?= $request->pathinfo() ?>">
                            <div class="am-u-sm-12 am-u-md-9 am-u-sm-push-3">
                                <div class="am fr">
                                    <div class="am-form-group am-fl">
                                        <?php $grade = $request->get('grade'); ?>
<!--                                        <select name="grade"-->
<!--                                                data-am-selected="{btnSize: 'sm', placeholder: '请选择会员等级'}">-->
<!--                                            <option value=""></option>-->
<!--                                            --><?php //foreach ($gradeList as $item): ?>
<!--                                                <option value="--><?//= $item['grade_id'] ?><!--"-->
<!--                                                    --><?//= $grade == $item['grade_id'] ? 'selected' : '' ?><?//= $item['name'] ?>
<!--                                                </option>-->
<!--                                            --><?php //endforeach; ?>
<!--                                        </select>-->
                                    </div>
                                    <!--<div class="am-form-group am-fl">
                                        <?php $gender = $request->get('gender'); ?>
                                        <select name="gender"
                                                data-am-selected="{btnSize: 'sm', placeholder: '请选择性别'}">
                                            <option value=""></option>
                                            <option value="-1"
                                                <?= $gender === '-1' ? 'selected' : '' ?>>全部
                                            </option>
                                            <option value="1"
                                                <?= $gender === '1' ? 'selected' : '' ?>>男
                                            </option>
                                            <option value="2"
                                                <?= $gender === '2' ? 'selected' : '' ?>>女
                                            </option>
                                            <option value="0"
                                                <?= $gender === '0' ? 'selected' : '' ?>>未知
                                            </option>
                                        </select>
                                    </div>
                                    -->
                                    <div class="am-form-group am-fl">
                                        <div class="am-input-group am-input-group-sm tpl-form-border-form">
                                            <input type="text" class="am-form-field" name="real_name"
                                                   placeholder="请输入昵称"
                                                   value="<?= $request->get('real_name') ?>">
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
                    <div class="am-scrollable-horizontal am-u-sm-12">
                        <table width="100%" class="am-table am-table-compact am-table-striped
                         tpl-table-black am-text-nowrap">
                            <thead>
                            <tr>
                                <th>用户ID</th>
<!--                                <th>支付宝头像</th>-->
                                <th>昵称</th>
<!--                                <th>用户余额</th>-->
                                <th>购物积分</th>
                                <th>兑换积分</th>
                                <th>手续费积分</th>
                                <th>当前等级</th>
                                <th>推荐人</th>
                                <th>真实姓名</th>
                                <th>是否实名认证</th>
<!--                                <th>会员等级</th>-->
<!--                                <th>实际消费金额</th>-->
<!--                                <th>性别</th>-->
<!--                                <th>国家</th>-->
<!--                                <th>省份</th>-->
<!--                                <th>城市</th>-->
                                <th>注册时间</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (!$list->isEmpty()): foreach ($list as $item): ?>
                                <tr>
                                    <td class="am-text-middle"><?= $item['user_id'] ?></td>
<!--                                    <td class="am-text-middle">-->
<!--                                        <a href="--><?//= $item['avatarUrl'] ?><!--" title="点击查看大图" target="_blank">-->
<!--                                            <img src="--><?//= $item['avatarUrl'] ?><!--" width="72" height="72" alt="">-->
<!--                                        </a>-->
<!--                                    </td>-->
                                    <td class="am-text-middle"><?= $item['username'] ?></td>
<!--                                    <td class="am-text-middle">--><?//= $item['balance'] ?><!--</td>-->
                                    <td class="am-text-middle"><?= $item['points'] ?></td>
                                    <td class="am-text-middle"><?= $item['freeze_points'] ?></td>
                                    <td class="am-text-middle"><?= $item['handling_fee_points'] ?></td>
                                    <td class="am-text-middle"><?= $item['level'] ?></td>
                                    <td class="am-text-middle"><?= $item['parent']["username"] ?></td>
                                    <td class="am-text-middle"><?= $item['real_name'] ?></td>
                                    <td class="am-text-middle"><?= $item['is_certification'] == 0 ? "<span style='color:red;'>未认证</span>" : "已认证" ?></td>
<!--                                    <td class="am-text-middle">-->
<!--                                        --><?//= !empty($item['grade']) ? $item['grade']['name'] : '--' ?>
<!--                                    </td>-->
<!--                                    <td class="am-text-middle">--><?//= $item['expend_money'] ?><!--</td>-->
<!--                                    <td class="am-text-middle">--><?//= $item['gender'] ?><!--</td>-->
<!--                                    <td class="am-text-middle">--><?//= $item['country'] ?: '--' ?><!--</td>-->
<!--                                    <td class="am-text-middle">--><?//= $item['province'] ?: '--' ?><!--</td>-->
<!--                                    <td class="am-text-middle">--><?//= $item['city'] ?: '--' ?><!--</td>-->
                                    <td class="am-text-middle"><?= $item['create_time'] ?></td>
                                    <td class="am-text-middle">
                                        <div class="tpl-table-black-operation">
<!--                                            --><?php //if (checkPrivilege('user/recharge')): ?>
<!--                                                <a class="j-recharge tpl-table-black-operation-default"-->
<!--                                                   href="javascript:void(0);"-->
<!--                                                   title="用户充值"-->
<!--                                                   data-id="--><?//= $item['user_id'] ?><!--"-->
<!--                                                   data-balance="--><?//= $item['balance'] ?><!--"-->
<!--                                                   data-points="--><?//= $item['points'] ?><!--"-->
<!--                                                >-->
<!--                                                    <i class="iconfont icon-qiandai"></i>-->
<!--                                                    充值-->
<!--                                                </a>-->
<!--                                            --><?php //endif; ?>
                                            <a class="j-recharge tpl-table-black-operation-default"
                                                   href="javascript:void(0);"
                                                   title="用户充值"
                                                   data-id="<?= $item['user_id'] ?>"
                                                   data-balance="<?= $item['balance'] ?>"
                                                   data-points="<?= $item['points'] ?>"
                                                   data-handling_fee_points="<?= $item['handling_fee_points'] ?>"
                                            >
                                                    <i class="iconfont icon-qiandai"></i>
                                                    充值积分
                                            </a>
                                            <a class="j-change_level tpl-table-black-operation-default"
                                                   href="javascript:void(0);"
                                                   title="修改等级"
                                                   data-id="<?= $item['user_id'] ?>"
                                                   data-level="<?= $item['level'] ?>"
                                            >
                                                    <i class="iconfont"></i>
                                                修改等级
                                            </a>
<!--                                            --><?php //if (checkPrivilege('user/grade')): ?>
<!--                                                <a class="j-grade tpl-table-black-operation-default"-->
<!--                                                   href="javascript:void(0);"-->
<!--                                                   data-id="--><?//= $item['user_id'] ?><!--"-->
<!--                                                   title="修改会员等级">-->
<!--                                                    <i class="iconfont icon-grade-o"></i>-->
<!--                                                    会员等级-->
<!--                                                </a>-->
<!--                                            --><?php //endif; ?>
<!--                                            --><?php //if (checkPrivilege('user/delete')): ?>
<!--                                                <a class="j-delete tpl-table-black-operation-default"-->
<!--                                                   href="javascript:void(0);"-->
<!--                                                   data-id="--><?//= $item['user_id'] ?><!--" title="删除用户">-->
<!--                                                    <i class="am-icon-trash"></i> 删除-->
<!--                                                </a>-->
<!--                                            --><?php //endif; ?>
                                            <div class="j-opSelect operation-select am-dropdown">
                                                <button type="button"
                                                        class="am-dropdown-toggle am-btn am-btn-sm am-btn-secondary">
                                                    <span>更多</span>
                                                    <span class="am-icon-caret-down"></span>
                                                </button>
                                                <ul class="am-dropdown-content" data-id="<?= $item['user_id'] ?>">
                                                    <?php if (checkPrivilege('order/all_list')): ?>
                                                        <li>
                                                            <a class="am-dropdown-item" target="_blank"
                                                               href="<?= url('order/all_list', ['user_id' => $item['user_id']]) ?>">用户订单</a>
                                                        </li>
                                                    <?php endif; ?>
<!--                                                    --><?php //if (checkPrivilege('user.recharge/order')): ?>
<!--                                                        <li>-->
<!--                                                            <a class="am-dropdown-item" target="_blank"-->
<!--                                                               href="--= url('user.recharge/order', ['user_id' => $item['user_id']]) ?>">充值记录</a>-->
<!--                                                        </li>-->
<!--                                                    --><?php //endif; ?>
<!--                                                    --><?php //if (checkPrivilege('user.balance/log')): ?>
<!--                                                        <li>-->
<!--                                                            <a class="am-dropdown-item" target="_blank"-->
<!--                                                               href="--= url('user.balance/log', ['user_id' => $item['user_id']]) ?><!-">余额明细</a>-->
<!--                                                        </li>-->
<!--                                                    --><?php //endif; ?>
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; else: ?>
                                <tr>
                                    <td colspan="12" class="am-text-center">暂无记录</td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
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

<!-- 模板：修改会员等级 -->
<script id="tpl-grade" type="text/template">
    <div class="am-padding-xs am-padding-top">
        <form class="am-form tpl-form-line-form" method="post" action="">
            <div class="am-tab-panel am-padding-0 am-active">
                <div class="am-form-group">
                    <label class="am-u-sm-3 am-form-label form-require">
                        会员等级
                    </label>
                    <div class="am-u-sm-8 am-u-end">
                        <select name="grade[grade_id]"
                                data-am-selected="{btnSize: 'sm', placeholder: '请选择会员等级'}">
                            <option value="0">无等级</option>
                            <?php foreach ($gradeList as $item): ?>
                                <option value="<?= $item['grade_id'] ?>"
                                    <?= $grade == $item['grade_id'] ? 'selected' : '' ?>><?= $item['name'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="am-form-group">
                    <label class="am-u-sm-3 am-form-label"> 管理员备注 </label>
                    <div class="am-u-sm-8 am-u-end">
                                <textarea rows="2" name="grade[remark]" placeholder="请输入管理员备注"
                                          class="am-field-valid"></textarea>
                    </div>
                </div>
            </div>
        </form>
    </div>
</script>

<!-- 模板：用户充值 -->
<script id="tpl-recharge" type="text/template">
    <div class="am-padding-xs am-padding-top-sm">
        <form class="am-form tpl-form-line-form" method="post" action="">
            <div class="j-tabs am-tabs">

                <ul class="am-tabs-nav am-nav am-nav-tabs">
<!--                    <li class="am-active"><a href="#tab1">充值余额</a></li>-->
                    <li><a href="#tab2">充值积分</a></li>
                    <li><a href="#tab3">充值手续费积分</a></li>
                </ul>

                <div class="am-tabs-bd am-padding-xs">

<!--                    <div class="am-tab-panel am-padding-0 am-active" id="tab1">-->
<!--                        <div class="am-form-group">-->
<!--                            <label class="am-u-sm-3 am-form-label">-->
<!--                                当前余额-->
<!--                            </label>-->
<!--                            <div class="am-u-sm-8 am-u-end">-->
<!--                                <div class="am-form--static">{{ balance }}</div>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                        <div class="am-form-group">-->
<!--                            <label class="am-u-sm-3 am-form-label">-->
<!--                                充值方式-->
<!--                            </label>-->
<!--                            <div class="am-u-sm-8 am-u-end">-->
<!--                                <label class="am-radio-inline">-->
<!--                                    <input type="radio" name="recharge[balance][mode]"-->
<!--                                           value="inc" data-am-ucheck checked>-->
<!--                                    增加-->
<!--                                </label>-->
<!--                                <label class="am-radio-inline">-->
<!--                                    <input type="radio" name="recharge[balance][mode]" value="dec" data-am-ucheck>-->
<!--                                    减少-->
<!--                                </label>-->
<!--                                <label class="am-radio-inline">-->
<!--                                    <input type="radio" name="recharge[balance][mode]" value="final" data-am-ucheck>-->
<!--                                    最终金额-->
<!--                                </label>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                        <div class="am-form-group">-->
<!--                            <label class="am-u-sm-3 am-form-label">-->
<!--                                变更金额-->
<!--                            </label>-->
<!--                            <div class="am-u-sm-8 am-u-end">-->
<!--                                <input type="number" min="0" class="tpl-form-input"-->
<!--                                       placeholder="请输入要变更的金额" name="recharge[balance][money]" value="" required>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                        <div class="am-form-group">-->
<!--                            <label class="am-u-sm-3 am-form-label">-->
<!--                                管理员备注-->
<!--                            </label>-->
<!--                            <div class="am-u-sm-8 am-u-end">-->
<!--                                <textarea rows="2" name="recharge[balance][remark]" placeholder="请输入管理员备注"-->
<!--                                          class="am-field-valid"></textarea>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->

                    <div class="am-tab-panel am-padding-0" id="tab2">
                        <div class="am-form-group">
                            <label class="am-u-sm-3 am-form-label">
                                当前积分
                            </label>
                            <div class="am-u-sm-8 am-u-end">
                                <div class="am-form--static">{{ points }}</div>
                            </div>
                        </div>
                        <div class="am-form-group">
                            <label class="am-u-sm-3 am-form-label">
                                充值方式
                            </label>
                            <div class="am-u-sm-8 am-u-end">
                                <label class="am-radio-inline">
                                    <input type="radio" name="recharge[points][mode]"
                                           value="inc" data-am-ucheck checked>
                                    增加
                                </label>
                                <label class="am-radio-inline">
                                    <input type="radio" name="recharge[points][mode]" value="dec" data-am-ucheck>
                                    减少
                                </label>
                                <label class="am-radio-inline">
                                    <input type="radio" name="recharge[points][mode]" value="final" data-am-ucheck>
                                    最终积分
                                </label>
                            </div>
                        </div>
                        <div class="am-form-group">
                            <label class="am-u-sm-3 am-form-label">
                                变更数量
                            </label>
                            <div class="am-u-sm-8 am-u-end">
                                <input type="number" min="0" class="tpl-form-input"
                                       placeholder="请输入要变更的数量" name="recharge[points][value]" value="" required>
                            </div>
                        </div>
                        <div class="am-form-group">
                            <label class="am-u-sm-3 am-form-label">
                                管理员备注
                            </label>
                            <div class="am-u-sm-8 am-u-end">
                                <textarea rows="2" name="recharge[points][remark]" placeholder="请输入管理员备注"
                                          class="am-field-valid"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="am-tab-panel am-padding-0" id="tab3">
                        <div class="am-form-group">
                            <label class="am-u-sm-3 am-form-label">
                                手续费积分
                            </label>
                            <div class="am-u-sm-8 am-u-end">
                                <div class="am-form--static">{{ handling_fee_points }}</div>
                            </div>
                        </div>
                        <div class="am-form-group">
                            <label class="am-u-sm-3 am-form-label">
                                充值方式
                            </label>
                            <div class="am-u-sm-8 am-u-end">
                                <label class="am-radio-inline">
                                    <input type="radio" name="recharge[handling_fee_points][mode]"
                                           value="inc" data-am-ucheck checked>
                                    增加
                                </label>
                                <label class="am-radio-inline">
                                    <input type="radio" name="recharge[handling_fee_points][mode]" value="dec" data-am-ucheck>
                                    减少
                                </label>
                                <label class="am-radio-inline">
                                    <input type="radio" name="recharge[handling_fee_points][mode]" value="final" data-am-ucheck>
                                    最终手续费积分
                                </label>
                            </div>
                        </div>
                        <div class="am-form-group">
                            <label class="am-u-sm-3 am-form-label">
                                变更数量
                            </label>
                            <div class="am-u-sm-8 am-u-end">
                                <input type="number" min="0" class="tpl-form-input"
                                       placeholder="请输入要变更的数量" name="recharge[handling_fee_points][value]" value="" required>
                            </div>
                        </div>
                        <div class="am-form-group">
                            <label class="am-u-sm-3 am-form-label">
                                管理员备注
                            </label>
                            <div class="am-u-sm-8 am-u-end">
                                <textarea rows="2" name="recharge[handling_fee_points][remark]" placeholder="请输入管理员备注"
                                          class="am-field-valid"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </form>
    </div>
</script>

<script>
    $(function () {

        /**
         * 账户充值
         */
        $('.j-recharge').on('click', function () {
            var $tabs, data = $(this).data();
            $.showModal({
                title: '用户充值'
                , area: '460px'
                , content: template('tpl-recharge', data)
                , uCheck: true
                , success: function ($content) {
                    $tabs = $content.find('.j-tabs');
                    $tabs.tabs({noSwipe: 1});
                }
                , yes: function ($content) {
                    $content.find('form').myAjaxSubmit({
                        url: '<?= url('user/recharge') ?>',
                        data: {
                            user_id: data.id,
                            source: $tabs.data('amui.tabs').activeIndex
                        }
                    });
                    return true;
                }
            });
        });

        $(".j-change_level").on('click', function () {
            var user_id = $(this).data("id");
            $.showModal({
                title : "更改等级"
                , content : `
                    <div style="text-align: center;margin:0 auto;">
                        <select class="change_level  tpl-table-black-operation-default" name="level" style="width:40%;margin:0 auto;text-align:center;margin-top:10px;margin-bottom:10px;">
                          <option value="0">0</option>
                          <option value="1">1</option>
                          <option value="2">2</option>
                          <option value="3">3</option>
                          <option value="4">4</option>
                          <option value="5">5</option>
                        </select>
                    </div>

                `, uCheck: true
                , yes: function () {
                    var level = $(".change_level").val();
                    $.ajax({
                        type : "post",
                        url : "/api/user/changeLevel",
                        data : {
                            "level" : level,
                            "user_id" : user_id
                        },
                        success: function (data) {
                            layer.msg(data.msg);
                        }
                    });
                    return true;
                }
            })
        });

        /**
         * 修改会员等级
         */
        $('.j-grade').on('click', function () {
            var data = $(this).data();
            $.showModal({
                title: '修改会员等级'
                , area: '460px'
                , content: template('tpl-grade', data)
                , uCheck: true
                , success: function ($content) {
                }
                , yes: function ($content) {
                    $content.find('form').myAjaxSubmit({
                        url: '<?= url('user/grade') ?>',
                        data: {user_id: data.id}
                    });
                    return true;
                }
            });
        });

        /**
         * 注册操作事件
         * @type {jQuery|HTMLElement}
         */
        var $dropdown = $('.j-opSelect');
        $dropdown.dropdown();
        $dropdown.on('click', 'li a', function () {
            var $this = $(this);
            var id = $this.parent().parent().data('id');
            var type = $this.data('type');
            if (type === 'delete') {
                layer.confirm('删除后不可恢复，确定要删除吗？', function (index) {
                    $.post("index.php?s=/store/apps.dealer.user/delete", {dealer_id: id}, function (result) {
                        result.code === 1 ? $.show_success(result.msg, result.url)
                            : $.show_error(result.msg);
                    });
                    layer.close(index);
                });
            }
            $dropdown.dropdown('close');
        });

        // 删除元素
        var url = "<?= url('user/delete') ?>";
        $('.j-delete').delete('user_id', url, '删除后不可恢复，确定要删除吗？');

    });
</script>

