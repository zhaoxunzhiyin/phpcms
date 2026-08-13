<?php 
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header', 'admin');
?>
<form method="post" action="?m=poster&c=space&a=edit&spaceid=<?php echo $this->input->get('spaceid')?>" name="myform" id="myform">
<input type="hidden" name="old_type" value="<?php echo $info['type']?>">
<table class="table_form" width="100%" cellspacing="0">
<tbody>
    <tr>
        <th width="120"><strong><?php echo L('boardtype')?>：</strong></th>
        <td><input name="space[name]" class="input-text" id="name" type="text" value="<?php echo new_html_special_chars($info['name'])?>" size="25"></td>
    </tr>
    <tr>
        <th><strong><?php echo L('ads_type')?>：</strong></th>
        <td><?php echo form::select($TYPES, $info['type'], 'name="space[type]" id="type" onchange="AdsType(this.value)"')?>&nbsp;&nbsp;<span id="ScrollSpan" style="padding-left:30px;display:none;"><label class="mt-checkbox mt-checkbox-outline"><input type="checkbox" id="ScrollBox" name="setting[scroll]"<?php if($setting['scroll']) {?> checked<?php }?> value='1'/> <?php echo L('rolling')?><span></span></label></span>
      <span id="AlignSpan" style="padding-left:30px;display:none;"><label class="mt-checkbox mt-checkbox-outline"><input type="checkbox" <?php if($setting['align']) {?> checked<?php }?> id="AlignBox" name="setting[align]" value='1'/> <?php echo L('lightbox')?><span></span></label></span></td>
    </tr>
    <tr id="trPosition" style="display:none;">
        <th align="right"  valign="top"><strong><?php echo L('position')?>：</strong></th>
        <td valign="top" colspan="2">
        <?php echo L('left_margin')?>：<label><input name='setting[paddleft]' id='PaddingLeft' type='text' size='5' value='<?php echo $setting['paddleft']?>' class="input-text"></label> px&nbsp;&nbsp;
        <?php echo L('top_margin')?>：<label><input name='setting[paddtop]' id='PaddingTop' type='text' size='5' value='<?php echo $setting['paddtop']?>' class="input-text" /></label> px</div>
        </td>
    </tr>
    <tr id="SizeFormat">
        <th><strong><?php echo L('size_format')?>：</strong></th>
        <td><?php echo L('plate_width')?><label><input name="space[width]" id="width" class="input-text" type="text" value="<?php echo $info['width']?>" size="10"></label> px &nbsp;&nbsp;&nbsp;&nbsp; <?php echo L('plate_height')?><label><input name="space[height]" id="height" type="text" class="input-text" value="<?php echo $info['height']?>" size="10"></label> px<div id="w_hTip"></div></td>
    </tr>
    <tr>
        <th><strong><?php echo L('description')?>：</strong></th>
        <td><textarea name="space[description]" id="description" class="input-textarea" cols="45" rows="4"><?php echo $info['description']?></textarea></td>
    </tr></tbody>
    </table>
</form>
</body>
</html>
<script language="javascript" type="text/javascript">
function AdsType(adstype) {
    $('#ScrollSpan').css('display', 'none');
    $('#AlignSpan').css('display', 'none');
    $('#trPosition').css('display', 'none');
    $('#SizeFormat').css('display', '');
    $('#PaddingLeft').attr('disabled', false);
    $('#PaddingTop').attr('disabled', false);
    <?php 
        if (is_array($poster_template) && !empty($poster_template)) {
            $n = 0;
            foreach ($poster_template as $key => $p) {
                if ($n==0) {
                    echo 'if (adstype==\''.$key.'\') {';
                } else {
                    echo '} else if (adstype==\''.$key.'\') {';
                }
                if ($p['align']) {
                    if ($p['align']=='align') {
                        echo '$(\'#AlignSpan\').css(\'display\', \'\');';
                        if ($setting['align']) {
                            echo '$(\'#AlignBox\').prop(\'checked\', \'true\');';
                            echo '$(\'#PaddingLeft\').attr(\'disabled\', true);';
                            echo '$(\'#PaddingTop\').attr(\'disabled\', true);';
                        }
                    } elseif ($p['align']=='scroll') {
                        echo '$(\'#ScrollSpan\').css(\'display\', \'\');';
                        if ($setting['scroll']) {
                            echo '$(\'#ScrollBox\').prop(\'checked\', \'true\');';
                        }
                    }
                }
                if ($p['padding']) {
                    echo '$(\'#trPosition\').css(\'display\', \'\');';
                }
                if (!isset($p['size']) || !$p['size']) {
                    echo '$(\'#SizeFormat\').css(\'display\', \'none\');';
                }
                $n++;
            }
        }
        echo '}';
    ?>
}
$('#AlignBox').click( function (){
    if($('#AlignBox').is(':checked')) {
        $('#PaddingLeft').attr('disabled', true);
        $('#PaddingTop').attr('disabled', true);
    } else {
        $('#PaddingLeft').attr('disabled', false);
        $('#PaddingTop').attr('disabled', false);
    }
});
AdsType($('#type').val());
</script>