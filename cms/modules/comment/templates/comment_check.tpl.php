<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.'); 
include $this->admin_tpl('header', 'admin');
?>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="note note-danger">
    <p><?php echo L('comment_check')?> (<?php echo L('for_audit_several')?>：<span id="wait" style="color:red"><?php echo $total?></span>)</p>
</div>
<div class="right-card-box">
<div class="comment">
<?php if(is_array($comment_check_data)) foreach($comment_check_data as $v) :
$this->comment_data_db->table_name($v['tableid']);
$data = $this->comment_data_db->get_one(array('id'=>$v['comment_data_id'], 'siteid'=>$this->get_siteid()));
?>
<div id="tbody_<?php echo $data['id']?>">
<h5 class="title fn"><span class="rt"><input type="button" value="<?php echo L('pass')?>" class="button" onclick="check(<?php echo $data['id']?>, 1, '<?php echo $data['commentid']?>')" /> <input  class="button"  type="button" value="<?php echo L('delete')?>" onclick="check(<?php echo $data['id']?>, -1, '<?php echo new_html_special_chars($data['commentid'])?>')" />
</span><?php echo $data['username']?> (<?php echo $data['ip']?>) <?php echo L('chez')?> <?php echo format::date($data['creat_at'], 1)?> <?php echo L('release')?> </h5>
    <div class="content">
        <pre><?php echo $data['content']?></pre>
    </div>
    <div class="bk20 hr mb8"></div>
</div>
<?php endforeach;?>
</div>
</div>
</div>
</div>
</div>
<script type="text/javascript">
function check(id, type, commentid) {
    if(type == -1) {
        Dialog.confirm('<?php echo L('are_you_sure_you_want_to_delete')?>',function(){$.get('?m=comment&c=check&a=ajax_checks&id='+id+'&type='+type+'&commentid='+commentid+'&pc_hash='+pc_hash+'&'+Math.random(), function(data){if(data!=1){if(data==0){Dialog.alert('<?php echo L('illegal_parameters')?>')}else{Dialog.alert(data)}}else{$('#tbody_'+id).remove();
        $.getJSON('?m=comment&c=check&a=public_get_one'+'&pc_hash='+pc_hash+Math.random(), function(data){
            if (data) {
                $('#wait').html(data.total);
                val = data.data;
                if (val.content) {
                html = '<div id="tbody_'+val.id+'"><h5 class="title fn" ><span class="rt"><input type="button" value="<?php echo L('pass')?>" class="button" onclick="check('+val.id+', 1, \''+val.commentid+'\')" /> <input  class="button"  type="button" value="<?php echo L('delete')?>" onclick="check('+val.id+', -1, \''+val.commentid+'\')" /></span>'+val.username+' ('+val.ip+') <?php echo L('chez')?> '+val.creat_at+' <?php echo L('release')?> </h5><div class="content"><pre>'+val.content+'</pre></div><div class="bk20 hr mb8"></div></div>';
                $('.comment').append(html);
                }
            }
            });
        }});});
    } else {
        $.get('?m=comment&c=check&a=ajax_checks&id='+id+'&type='+type+'&commentid='+commentid+'&pc_hash='+pc_hash+'&'+Math.random(), function(data){if(data!=1){if(data==0){Dialog.alert('<?php echo L('illegal_parameters')?>')}else{Dialog.alert(data)}}else{$('#tbody_'+id).remove();
        $.getJSON('?m=comment&c=check&a=public_get_one'+'&pc_hash='+pc_hash+Math.random(), function(data){
            if (data) {
                $('#wait').html(data.total);
                val = data.data;
                if (val.content) {
                html = '<div id="tbody_'+val.id+'"><h5 class="title fn" ><span class="rt"><input type="button" value="<?php echo L('pass')?>" class="button" onclick="check('+val.id+', 1, \''+val.commentid+'\')" /> <input  class="button"  type="button" value="<?php echo L('delete')?>" onclick="check('+val.id+', -1, \''+val.commentid+'\')" /></span>'+val.username+' ('+val.ip+') <?php echo L('chez')?> '+val.creat_at+' <?php echo L('release')?> </h5><div class="content"><pre>'+val.content+'</pre></div><div class="bk20 hr mb8"></div></div>';
                $('.comment').append(html);
                }
            }
            });

        }});
    }
}
</script>
</body>
</html>