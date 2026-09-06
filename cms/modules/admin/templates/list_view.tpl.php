<?php defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');?>
<?php include $this->admin_tpl('header', 'admin');?>
<link rel="stylesheet" href="<?php echo JS_PATH;?>jquery-ui/jquery-ui.min.css">
<script type="text/javascript" src="<?php echo JS_PATH;?>jquery-ui/jquery-ui.min.js"></script>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content">
            <div class="right-card-box">
                <div class="note note-info">
                    <p>带“必选”标记的字段默认显示，且不能取消。</p>
                </div>
                <form action="?m=admin&c=list_view_config&a=public_list_view&scene=<?php echo rawurlencode($scene);?>" method="post" id="list-view-form">
                    <input type="hidden" name="action" value="save">
                    <div class="table-list">
                        <table class="table table-striped table-bordered table-hover">
                            <thead>
                            <tr class="heading">
                                <th width="80">显示</th>
                                <th width="180">字段</th>
                                <th width="100">宽度</th>
                                <th width="120">对齐</th>
                                <th>状态</th>
                            </tr>
                            </thead>
                            <tbody class="field-sort-items">
                            <?php foreach ($field as $name => $item) { ?>
                            <tr data-field="<?php echo htmlspecialchars($name, ENT_QUOTES, CHARSET); ?>">
                                <td>
                                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                        <input type="hidden" name="config[<?php echo htmlspecialchars($name, ENT_QUOTES, CHARSET); ?>][order]" value="<?php echo intval($item['order']); ?>" class="field-order">
                                        <input type="checkbox" name="config[<?php echo htmlspecialchars($name, ENT_QUOTES, CHARSET); ?>][use]" value="1" <?php if (!empty($item['use'])) { ?>checked<?php } ?> <?php if (!empty($item['required'])) { ?>disabled<?php } ?> />
                                        <span></span>
                                    </label>
                                </td>
                                <td><?php echo L($item['name']); ?> (<?php echo htmlspecialchars($name, ENT_QUOTES, CHARSET); ?>)</td>
                                <td>
                                    <input class="form-control" type="text" name="config[<?php echo htmlspecialchars($name, ENT_QUOTES, CHARSET); ?>][width]" value="<?php echo isset($item['width']) ? htmlspecialchars((string)$item['width'], ENT_QUOTES, CHARSET) : ''; ?>">
                                </td>
                                <td>
                                    <input type="checkbox" name="config[<?php echo htmlspecialchars($name, ENT_QUOTES, CHARSET); ?>][center]" value="1" <?php if (!empty($item['center'])) { ?>checked<?php } ?>> 居中
                                </td>
                                <td>
                                    <?php if (!empty($item['required'])) { ?>
                                        <span class="label label-success">必选</span>
                                    <?php } else { ?>
                                        <span class="label label-default">可选</span>
                                    <?php } ?>
                                </td>
                            </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="form-actions text-center">
                        <button type="button" class="btn green" id="list-view-save"><i class="fa fa-save"></i> 保存</button>
                        <button type="button" class="btn default" id="list-view-reset">恢复默认</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
$(function() {
    $('.field-sort-items').sortable({
        update: function() {
            $('.field-sort-items tr').each(function(index) {
                $(this).find('.field-order').val(index);
            });
        }
    });

    $('#list-view-save').on('click', function() {
        var form = $('#list-view-form');
        var data = form.serializeArray();
        var button = $(this);
        button.prop('disabled', true);

        $.post(form.attr('action'), data, function(result) {
            if (typeof result === 'string') {
                try { result = JSON.parse(result); } catch (e) {}
            }
            if (result && result.code == 1) {
                if (window.parent && window.parent.location) {
                    window.parent.location.reload();
                } else {
                    location.reload();
                }
                return;
            }
            button.prop('disabled', false);
            Dialog.alert(result && result.msg ? result.msg : '保存失败');
        });
    });

    $('#list-view-reset').on('click', function() {
        var button = $(this);
        if (!confirm('确定恢复默认列表字段吗？')) {
            return;
        }
        button.prop('disabled', true);

        $.post('?m=admin&c=list_view_config&a=public_list_view&scene=<?php echo rawurlencode($scene);?>', {
            action: 'reset'
        }, function(result) {
            if (typeof result === 'string') {
                try { result = JSON.parse(result); } catch (e) {}
            }
            if (result && result.code == 1) {
                if (window.parent && window.parent.location) {
                    window.parent.location.reload();
                } else {
                    location.reload();
                }
                return;
            }
            button.prop('disabled', false);
            Dialog.alert(result && result.msg ? result.msg : '恢复失败');
        });
    });
});
</script>
</body>
</html>