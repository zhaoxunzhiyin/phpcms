<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header');?>
<?php echo load_js(JS_PATH.'content_addtop.js');?>
<?php echo load_js(JS_PATH.'cookie.js');?>
<script type="text/javascript">var catid=<?php echo intval($catid);?></script>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<form action="?m=admin&c=category&a=add" class="form-horizontal" method="post" name="myform" id="myform">
<input name="catid" type="hidden" value="<?php echo intval($catid);?>">
<input name="type" type="hidden" value="<?php echo $type;?>">
<input name="page" id="dr_page" type="hidden" value="<?php echo $page;?>">
<div class="portlet light bordered">
    <div class="portlet-title tabbable-line">
        <ul class="nav nav-tabs" style="float:left;">
            <li<?php if ($page==0) {?> class="active"<?php }?>>
                <a data-toggle="tab_0" onclick="$('#dr_page').val('0')"<?php if (is_mobile()) {echo ' onmouseover="layer.tips(\''.L('catgory_basic').'\',this,{tips: [1, \'#fff\']});" onmouseout="layer.closeAll();"';}?>> <i class="fa fa-cog"></i> <?php if (is_pc()) {echo L('catgory_basic');}?> </a>
            </li>
            <li<?php if ($page==1) {?> class="active"<?php }?>>
                <a data-toggle="tab_1" onclick="$('#dr_page').val('1')"<?php if (is_mobile()) {echo ' onmouseover="layer.tips(\''.L('catgory_createhtml').'\',this,{tips: [1, \'#fff\']});" onmouseout="layer.closeAll();"';}?>> <i class="fa fa-file-code-o"></i> <?php if (is_pc()) {echo L('catgory_createhtml');}?> </a>
            </li>
            <li<?php if ($page==2) {?> class="active"<?php }?>>
                <a data-toggle="tab_2" onclick="$('#dr_page').val('2')"<?php if (is_mobile()) {echo ' onmouseover="layer.tips(\''.L('catgory_template').'\',this,{tips: [1, \'#fff\']});" onmouseout="layer.closeAll();"';}?>> <i class="fa fa-html5"></i> <?php if (is_pc()) {echo L('catgory_template');}?> </a>
            </li>
            <li<?php if ($page==3) {?> class="active"<?php }?>>
                <a data-toggle="tab_3" onclick="$('#dr_page').val('3')"<?php if (is_mobile()) {echo ' onmouseover="layer.tips(\''.L('catgory_seo').'\',this,{tips: [1, \'#fff\']});" onmouseout="layer.closeAll();"';}?>> <i class="fa fa-internet-explorer"></i> <?php if (is_pc()) {echo L('catgory_seo');}?> </a>
            </li>
            <li<?php if ($page==4) {?> class="active"<?php }?>>
                <a data-toggle="tab_4" onclick="$('#dr_page').val('4')"<?php if (is_mobile()) {echo ' onmouseover="layer.tips(\''.L('catgory_private').'\',this,{tips: [1, \'#fff\']});" onmouseout="layer.closeAll();"';}?>> <i class="fa fa-user-circle"></i> <?php if (is_pc()) {echo L('catgory_private');}?> </a>
            </li>
            <?php if($forminfos && is_array($forminfos['base'])) {?>
            <li<?php if ($page==5) {?> class="active"<?php }?>>
                <a data-toggle="tab_5" onclick="$('#dr_page').val('5')"<?php if (is_mobile()) {echo ' onmouseover="layer.tips(\''.L('extention_field').'\',this,{tips: [1, \'#fff\']});" onmouseout="layer.closeAll();"';}?>> <i class="fa fa-code"></i> <?php if (is_pc()) {echo L('extention_field');}?> </a>
            </li>
            <?php }?>
        </ul>
    </div>
    <div class="portlet-body form">
        <div class="tab-content">
            <div class="tab-pane<?php if ($page==0) {?> active<?php }?>" id="tab_0">

                <div class="form-body">

                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('add_category_types')?></label>
                        <div class="col-md-9">
                            <div class="mt-radio-inline">
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="addtype" value="0" checked onclick="$('#catdir_tr').show();$('#normal_add').show();$('#normal_add').show();$('#batch_add').hide();"> <?php echo L('normal_add');?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="addtype" value="1" onclick="$('#catdir_tr').hide();$('#normal_add').hide();$('#normal_add').hide();$('#batch_add').show();"> <?php echo L('batch_add');?> <span></span></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('parent_category')?></label>
                        <div class="col-md-9">
                            <?php echo form::select_category('module/category-'.$this->siteid.'-data',$parentid,'name="info[parentid]" id="parentid"',L('please_select_parent_category'),0,-1);?>
                        </div>
                    </div>
                    <div class="form-group" id="dr_row_catname">
                        <label class="col-md-2 control-label"><?php echo L('catname')?></label>
                        <div class="col-md-9">
                            <label id="normal_add"><input class="form-control input-large" type="text" name="info[catname]" id="catname" value="" onblur="topinyin('<?php echo WEB_PATH;?>api.php?op=pinyin','catname','catdir',12);"></label>
                            <span id="batch_add" style="display:none"><textarea class="form-control" name="batch_add" id="batch" style="height:190px;"></textarea>
                            <span class="help-block" id="dr_catname_tips"><?php echo L('batch_add_tips')?></span>
                            </span>
                        </div>
                    </div>
                    <div class="form-group" id="dr_row_catdir">
                        <label class="col-md-2 control-label"><?php echo L('catdir')?></label>
                        <div class="col-md-9">
                            <label><input class="form-control input-large" type="text" id="catdir" name="info[catdir]" value=""></label>
                            <span class="help-block" id="dr_catdir_tips"><?php echo L('栏目目录确保唯一，用于url填充或者生成目录')?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('catgory_img')?></label>
                        <div class="col-md-9">
                            <?php echo form::images('info[image]', 'image', $image, 'content');?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('description')?></label>
                        <div class="col-md-9">
                            <textarea class="form-control" name="info[description]" style="height:90px;"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('ismenu')?></label>
                        <div class="col-md-9">
                            <div class="mt-radio-inline">
                                <label class="mt-radio mt-radio-outline"><input type='radio' name='info[ismenu]' value='1' checked> <?php echo L('yes');?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input type='radio' name='info[ismenu]' value='0'  > <?php echo L('no');?> <span></span></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('继承下级')?></label>
                        <div class="col-md-9">
                            <div class="mt-radio-inline">
                                <label class="mt-radio mt-radio-outline"><input type='radio' name='setting[getchild]' value='1'  > <?php echo L('open');?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input type='radio' name='setting[getchild]' value='0' checked> <?php echo L('close');?> <span></span></label>
                            </div>
                            <span class="help-block"><?php echo L('将下级第一个栏目数据作为当前的栏目，不对外链类型的栏目有效')?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('可用')?></label>
                        <div class="col-md-9">
                            <div class="mt-radio-inline">
                                <label class="mt-radio mt-radio-outline"><input type='radio' name='info[disabled]' value='0' checked> <?php echo L('可用');?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input type='radio' name='info[disabled]' value='1'  > <?php echo L('禁用');?> <span></span></label>
                            </div>
                            <span class="help-block"><?php echo L('禁用状态下此栏目不能正常访问')?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('您现在的位置')?></label>
                        <div class="col-md-9">
                            <div class="mt-radio-inline">
                                <label class="mt-radio mt-radio-outline"><input type='radio' name='setting[iscatpos]' value='1' checked> <?php echo L('display');?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input type='radio' name='setting[iscatpos]' value='0'  > <?php echo L('hidden');?> <span></span></label>
                            </div>
                            <span class="help-block"><?php echo L('前端栏目面包屑导航调用不会显示，但可以正常访问，您现在的位置不显示')?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('左侧')?></label>
                        <div class="col-md-9">
                            <div class="mt-radio-inline">
                                <label class="mt-radio mt-radio-outline"><input type='radio' name='setting[isleft]' value='1' checked> <?php echo L('display');?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input type='radio' name='setting[isleft]' value='0'> <?php echo L('hidden');?> <span></span></label>
                            </div>
                            <span class="help-block"><?php echo L('前端栏目调用左侧不会显示，但可以正常访问')?></span>
                        </div>
                    </div>

                </div>
            </div>
            <div class="tab-pane<?php if ($page==1) {?> active<?php }?>" id="tab_1">
                <div class="form-body">

                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('html_category')?></label>
                        <div class="col-md-9">
                            <div class="mt-radio-inline">
                                <label class="mt-radio mt-radio-outline"><input type='radio' name='setting[ishtml]' value='1' <?php if($setting['ishtml']) echo 'checked';?> onClick="$('#category_php_ruleid').css('display','none');$('#category_html_ruleid').css('display','');$('#tr_domain').css('display','');"> <?php echo L('yes');?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input type='radio' name='setting[ishtml]' value='0' <?php if(!$setting['ishtml']) echo 'checked';?>  onClick="$('#category_php_ruleid').css('display','');$('#category_html_ruleid').css('display','none');$('#tr_domain').css('display','none');"> <?php echo L('no');?> <span></span></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('category_urlrules')?></label>
                        <div class="col-md-9">
                            <label id="category_php_ruleid" style="display:<?php if($setting['ishtml']) echo 'none';?>">
                            <?php echo form::urlrule('content','category',0,$setting['category_ruleid'],'name="category_php_ruleid"');?>
                            </label>
                            <label id="category_html_ruleid" style="display:<?php if(!$setting['ishtml']) echo 'none';?>">
                            <?php echo form::urlrule('content','category',1,$setting['category_ruleid'],'name="category_html_ruleid"');?>
                            </label>
                        </div>
                    </div>

                </div>
            </div>
            <div class="tab-pane<?php if ($page==2) {?> active<?php }?>" id="tab_2">
                <div class="form-body">

                    <div class="form-group" id="dr_row_template_list">
                        <label class="col-md-2 control-label"><?php echo L('available_styles')?></label>
                        <div class="col-md-9">
                            <?php echo form::select($template_list, $setting['template_list'], 'name="setting[template_list]" id="template_list" onchange="load_file_list(this.value)"', L('please_select'))?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('page_templates')?></label>
                        <div class="col-md-9">
                            <label id="page_template"></label>
                        </div>
                    </div>

                </div>
            </div>
            <div class="tab-pane<?php if ($page==3) {?> active<?php }?>" id="tab_3">
                <div class="form-body">

                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('SEO标题')?></label>
                        <div class="col-md-9">
                            <textarea class="form-control" name="setting[meta_title]" style="height:90px;"></textarea>
                            <span class="help-block"><?php echo L('针对搜索引擎设置的标题')?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('SEO关键字')?></label>
                        <div class="col-md-9">
                            <textarea class="form-control" name="setting[meta_keywords]" style="height:90px;"></textarea>
                            <span class="help-block"><?php echo L('关键字中间用半角逗号隔开')?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('SEO描述信息')?></label>
                        <div class="col-md-9">
                            <textarea class="form-control" name="setting[meta_description]" style="height:90px;"></textarea>
                            <span class="help-block"><?php echo L('针对搜索引擎设置的网页描述')?></span>
                        </div>
                    </div>

                </div>
            </div>
            <div class="tab-pane<?php if ($page==4) {?> active<?php }?>" id="tab_4">
                <div class="form-body">

                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('role_private')?></label>
                        <div class="col-md-9">
                            <div class="table-list"><table width="100%">
              <thead>
                <tr>
                  <th align="left" width="200"><?php echo L('role_name');?></th><th><?php echo L('edit');?></th>
              </tr>
                </thead>
                 <tbody>
                <?php
                $roles = getcache('role','commons');
                foreach($roles as $roleid=> $rolrname) {
                $disableds = $roleid==1 ? 'disabled' : '';
                ?>
                  <tr>
                  <td><?php echo $rolrname?></td>
                  <td align="center"><label class="mt-checkbox mt-checkbox-outline"><input type="checkbox" name="priv_roleid[]" <?php echo $disableds;?> value="init,<?php echo $roleid;?>" ><span></span></label></td>
              </tr>
              <?php }?>
    
             </tbody>
            </table></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('group_private')?></label>
                        <div class="col-md-9">
                            <div class="table-list"><table width="100%">
              <thead>
                <tr>
                  <th align="left" width="200"><?php echo L('group_name');?></th><th><?php echo L('allow_vistor');?></th>
              </tr>
                </thead>
                 <tbody>
            <?php
            $group_cache = getcache('grouplist','member');
            foreach($group_cache as $_key=>$_value) {
            if($_value['groupid']==1) continue;
            ?>
                  <tr>
                  <td><?php echo $_value['name'];?></td>
                  <td align="center"><label class="mt-checkbox mt-checkbox-outline"><input type="checkbox" name="priv_groupid[]" value="visit,<?php echo $_value['groupid'];?>" ><span></span></label></td>
              </tr>
            <?php }?>
             </tbody>
            </table></div>
                        </div>
                    </div>

                </div>
            </div>
            <?php if($forminfos && is_array($forminfos['base'])) {?>
            <div class="tab-pane<?php if ($page==5) {?> active<?php }?>" id="tab_5">
                <div class="form-body">

<?php foreach($forminfos['base'] as $field=>$info) {
     if($info['isomnipotent']) continue;
     if($info['formtype']=='omnipotent') {
        foreach($forminfos['base'] as $_fm=>$_fm_value) {
            if($_fm_value['isomnipotent']) {
                $info['form'] = str_replace('{'.$_fm.'}',$_fm_value['form'],$info['form']);
            }
        }
        foreach($forminfos['senior'] as $_fm=>$_fm_value) {
            if($_fm_value['isomnipotent']) {
                $info['form'] = str_replace('{'.$_fm.'}',$_fm_value['form'],$info['form']);
            }
        }
    }
?>
                    <div class="form-group" id="dr_row_<?php echo $field?>">
                        <label class="col-md-2 control-label"><?php if($info['star']){ ?> <font color="red">*</font><?php } ?> <?php echo $info['name']?></label>
                        <div class="col-md-9">
                            <?php echo $info['form']?>
                            <span class="help-block" id="dr_<?php echo $field?>_tips"><?php echo $info['tips']?></span>
                        </div>
                    </div>
<?php }?>

                </div>
            </div>
            <?php }?>
        </div>
    </div>
</div>
</form>
</div>
<script type="text/javascript">
function load_file_list(id) {
    if(id=='') return false;
    $.getJSON('?m=admin&c=category&a=public_tpl_file_list&style='+id+'&catid=<?php echo $parentid?>&type=1', function(data){$('#page_template').html(data.page_template);});
}
<?php if(isset($setting['template_list']) && !empty($setting['template_list'])) echo "load_file_list('".$setting['template_list']."')";?>
</script>
</div>
</div>
</body>
</html>