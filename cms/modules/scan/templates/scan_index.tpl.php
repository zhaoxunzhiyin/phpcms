<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.'); 
include $this->admin_tpl('header','admin');
?>
<div class="pad-10">
<div class="common-form">
<form name="myform" action="?m=scan&c=index&a=public_update_config" method="post" id="myform">
<input name="menuid" type="hidden" value="<?php echo $this->input->get('menuid');?>">
    <table width="100%" class="table_form">
        <tr>
            <td width="120"><?php echo L('ravsingle')?>:</td> 
            <td><ul id="file" style="list-style:none; height:200px;overflow:auto;width:300px;">
            <?php $dir = $file= ''; foreach ($list as $v){
                $filename = basename($v);
                if (is_dir($v)) {
                    $dir .= '<li><label class="mt-checkbox mt-checkbox-outline"><input type="checkbox" name="dir[]" value="'.$v.'" '.(isset($scan['dir']) && is_array($scan['dir']) && !empty($scan['dir']) && in_array($v, $scan['dir']) ? 'checked' :'').'><span></span><img src="'.IMG_PATH.'folder.png"> '.$filename.'</label></li>';
                } elseif (substr(strtolower($v), -3, 3)=='php') {
                    $file .= '<li><label class="mt-checkbox mt-checkbox-outline"><input type="checkbox" name="dir[]" value="'.$v.'" '.(isset($scan['dir']) && is_array($scan['dir']) && !empty($scan['dir']) && in_array($v, $scan['dir']) ? 'checked' :'').'><span></span><img src="'.IMG_PATH.'file.png">'.$filename.'</label></li>';
                } else {
                    continue;
                }
            }
            echo $dir,$file;
            ?>
</ul></td>
        </tr>
        <tr>
            <td><?php echo L('file_type')?>:</td> 
            <td><input type="text" name="info[file_type]" size="100"  class="input-text" value="<?php echo $scan['file_type']?>"></input></td>
        </tr>
        <tr>
            <td><?php echo L('characteristic_function')?>:</td> 
            <td><input type="text" name="info[func]" size="100" class="input-text" value="<?php echo $scan['func']?>"></input></td>
        </tr>
        <tr>
            <td><?php echo L('characteristic_key')?>:</td> 
            <td><input type="text" name="info[code]" size="100" class="input-text" value="<?php echo $scan['code']?>"></input></td>
        </tr>
        
        <tr>
            <td><?php echo L('md5_the_mirror')?>:</td>
            <td>
            <?php echo form::select($md5_file_list, $scan['md5_file'], 'name="info[md5_file]"')?>
            </td>
        </tr>
    </table>
<div class="portlet-body form myfooter">
    <div class="form-actions text-center">
        <button type="button" onclick="dr_ajax_submit('?m=scan&c=index&a=public_update_config', 'myform', '2000')" class="btn green"> <i class="fa fa-save"></i> <?php echo L('submit')?></button>
    </div>
</div>
</form>
</div>
</div>
</body>
</html>