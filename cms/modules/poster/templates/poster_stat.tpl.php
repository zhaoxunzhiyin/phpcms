<?php 
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header','admin');
?>
<div class="subnav">
    <?php if(is_mobile()) {?>
    <div class="content-menu btn-group dropdown-btn-group"> <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true"><i class="fa fa-th-large"></i> <?php echo L('菜单')?> <i class="fa fa-angle-down"></i></a>
        <ul class="dropdown-menu">
            <li><a class="tooltips add fb" href="?m=poster&c=poster&a=init&spaceid=<?php echo $info['spaceid'];?>" data-container="body" data-placement="bottom" data-original-title="<?php echo L('ad_list');?>"><i class="fa fa-plus"></i> <?php echo L('ad_list')?></a></li>
            <div class="dropdown-line"></div>
            <li><a class="tooltips on" href="?m=poster&c=space" data-container="body" data-placement="bottom" data-original-title="<?php echo L('space_list');?>"><i class="fa fa-reorder"></i> <?php echo L('space_list')?></a></li>
        </ul>
    </div>
    <?php } else {?>
    <div class="content-menu ib-a"><a class="tooltips add fb" href="?m=poster&c=poster&a=init&spaceid=<?php echo $info['spaceid'];?>" data-container="body" data-placement="bottom" data-original-title="<?php echo L('ad_list');?>"><i class="fa fa-plus"></i> <?php echo L('ad_list')?></a><i class="fa fa-circle"></i><a class="tooltips on" href="?m=poster&c=space" data-container="body" data-placement="bottom" data-original-title="<?php echo L('space_list');?>"><i class="fa fa-reorder"></i> <?php echo L('space_list')?></a></div>
    <?php }?>
</div>
<div class="content-header"></div>
<div class="pad-lr-10">
<div class="col-tab">
        <ul class="tabBut cu-li">
            
            <li<?php if($this->input->get('click')) {?> class="on"<?php }?>><a href="?m=poster&c=poster&a=stat&id=<?php echo $this->input->get('id')?>&click=1"><?php echo L('hits_stat')?></a></li>
            <li<?php if($this->input->get('click')==0){?> class="on"<?php }?>><a href="?m=poster&c=poster&a=stat&id=<?php echo $this->input->get('id')?>&click=0"><?php echo L('show_stat')?></a></li><li style="background:none; border:none;"><?php if(is_numeric($this->input->get('click'))) {?><strong><a href="?m=poster&c=poster&a=stat&id=<?php echo $this->input->get('id')?>&click=<?php echo $this->input->get('click')?>&group=area"><?php echo L('listorder_f_area')?></a></strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong><a href="?m=poster&c=poster&a=stat&id=<?php echo $this->input->get('id')?>&click=<?php echo $this->input->get('click')?>&group=ip"><?php echo L('listorder_f_ip')?></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong><?php }?>
<label class="mt-radio mt-radio-outline"><input name='range' type='radio' value='' onclick="redirect('?m=poster&c=poster&a=stat&id=<?php echo $this->input->get('id')?>&click=<?php echo $this->input->get('click')?>&pc_hash=<?php echo $this->input->get('pc_hash')?>&group=<?php echo $this->input->get('group')?>')" <?php if(!$this->input->get('range')) {?>checked<?php }?>> <?php echo L('all')?><span></span></label>
<label class="mt-radio mt-radio-outline"><input name='range' type='radio' value='1' onclick="redirect('?m=poster&c=poster&a=stat&id=<?php echo $this->input->get('id')?>&click=<?php echo $this->input->get('click')?>&pc_hash=<?php echo $this->input->get('pc_hash')?>&group=<?php echo $this->input->get('group')?>&range='+this.value)" <?php if($this->input->get('range')==1) {?>checked<?php }?>> <?php echo L('today')?><span></span></label>
<label class="mt-radio mt-radio-outline"><input name='range' type='radio' value='2' onclick="redirect('?m=poster&c=poster&a=stat&id=<?php echo $this->input->get('id')?>&click=<?php echo $this->input->get('click')?>&pc_hash=<?php echo $this->input->get('pc_hash')?>&group=<?php echo $this->input->get('group')?>&range='+this.value)" <?php if($this->input->get('range')==2) {?>checked<?php }?>> <?php echo L('yesterday')?><span></span></label>
<label class="mt-radio mt-radio-outline"><input name='range' type='radio' value='7' onclick="redirect('?m=poster&c=poster&a=stat&id=<?php echo $this->input->get('id')?>&click=<?php echo $this->input->get('click')?>&pc_hash=<?php echo $this->input->get('pc_hash')?>&group=<?php echo $this->input->get('group')?>&range='+this.value)" <?php if($this->input->get('range')==7) {?>checked<?php }?>> <?php echo L('one_week')?><span></span></label>
<label class="mt-radio mt-radio-outline"><input name='range' type='radio' value='14' onclick="redirect('?m=poster&c=poster&a=stat&id=<?php echo $this->input->get('id')?>&click=<?php echo $this->input->get('click')?>&pc_hash=<?php echo $this->input->get('pc_hash')?>&group=<?php echo $this->input->get('group')?>&range='+this.value)" <?php if($this->input->get('range')==14) {?>checked<?php }?>> <?php echo L('two_week')?><span></span></label>
<label class="mt-radio mt-radio-outline"><input name='range' type='radio' value='30' onclick="redirect('?m=poster&c=poster&a=stat&id=<?php echo $this->input->get('id')?>&click=<?php echo $this->input->get('click')?>&pc_hash=<?php echo $this->input->get('pc_hash')?>&group=<?php echo $this->input->get('group')?>&range='+this.value)" <?php if($this->input->get('range')==30) {?>checked<?php }?>> <?php echo L('one_month')?> <span></span></label><font color="red"><?php echo L('history_select')?>：</font><select name="year" onchange="if(this.value!=''){location='?m=poster&c=poster&a=stat&id=<?php echo $this->input->get('id')?>&click=<?php echo $this->input->get('click')?>&pc_hash=<?php echo $this->input->get('pc_hash')?>&group=<?php echo $this->input->get('group')?>&year='+this.value;}">
<?php echo $selectstr;?></select></li>
        </ul>
            <div class="content pad-10">
                <?php if(is_numeric($this->input->get('click')) && $this->input->get('group')) {?>
<div class="table-list">
    <table width="100%" cellspacing="0">
        <thead>
            <tr>
            <?php if($this->input->get('group')=='ip') {?>
            <th align="center"><?php echo L('browse_ip')?></th><?php }?>
            <th align="center"><?php echo L('for_area')?></th>
            <th align="center"><?php echo L('show_times')?></th>
            </tr>
        </thead>
    </table>
    <table width="100%" class="contentWrap">
 <?php 
if(is_array($data)){
    foreach($data as $info){
?>   
    <tr>
    <?php if($this->input->get('group')=='ip') {?>
    <td align="center"><?php echo $info['ip']?></td><?php }?>
    <td align="center">
    <?php echo $info['area']?>
    </td>
    <td align="center"><?php echo $info['num']?></td>
    </tr>
<?php 
    }
}
?>
    </table>  </div>
 <div><?php echo $pages?></div>
<?php } else {?>
<table width="100%" border="0" cellspacing="0" cellpadding="0" style="table-layout:fixed;">
      <tr>
       <td style="padding-top:2px;padding-left:6px;padding-right:6px;padding-bottom:8px;">
        <table width="100%" border="1" class="contentWrap" bordercolor="#dddddd" cellpadding="0" cellspacing="0">
          <?php if(is_array($data)) { foreach($data as $k => $v) {?>
          <tr>
           <td width="24" align="center"><?php echo intval($k+1);?></td>
           <td style="padding:5px;"><div><span>
           <b>IP：<?php echo $v['ip']?></b> ( <b><?php echo $v['area']?></b> )</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo L('come_from')?>： <a href="<?php echo $v['referer']?>" target="_blank">
          <?php echo $v['referer']?></a></div>
         <div><span class="item"><?php echo L('visit_time')?>：<em><?php echo format::date($v['clicktime'], 1);?></em></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo L('operate')?>：<?php if($v['type']) { echo L('click'); } else { echo L('show'); }?></div></td>
         </tr>
        <?php } }?>
       </table>
      </td>
    </tr>
    <tr>
     <td><div><?php echo $pages;?></div></td>
    </tr>
</table>
<?php } ?>
            </div>
</div>
</div>
</body>
</html>