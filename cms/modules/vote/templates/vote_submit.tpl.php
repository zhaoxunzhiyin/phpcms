<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
?>
<form style="border: medium none;" id="voteform<?php echo $subjectid;?>" method="post" action="<?php echo APP_PATH;?>index.php?m=vote&c=index&a=post&subjectid=<?php echo $subjectid;?>">
 <dl>
      <dt><?php echo $subject;?></dt>
      </dl>
<dl>
<?php if(is_array($options)){?>
<div class="mt-radio-inline">
<?php
$i=0;
foreach($options as $optionid=>$option){
$i++;
?>
<label class="mt-radio mt-radio-outline"><input type="radio" value="<?php echo $option['optionid']?>" name="radio[]" id="radio"> <?php echo $option['option'];?> <span></span></label>
<?php }?>
</div>
<?php }?>
<input type="hidden" name="voteid" value="<?php echo $subjectid;?>">
</dl> 
<p> &nbsp;&nbsp; <input type="submit" value="<?php echo L('submit')?>" name="dosubmit" />    &nbsp;&nbsp; <a href="<?php echo trim(FC_NOW_HOST, '/').WEB_PATH?>index.php?m=vote&c=index&a=result&id=<?php echo $subjectid;?>"><?php echo L('vote_showresult')?></a> </p>
</form>