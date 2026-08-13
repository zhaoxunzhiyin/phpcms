<?php defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');?>
<?php include $this->admin_tpl('header', 'admin');?>
<div class="pad-lr-10">
<div class="common-form">
    <input type="hidden" name="info[userid]" value="<?php echo $memberinfo['userid']?>"></input>
    <input type="hidden" name="info[username]" value="<?php echo $memberinfo['username']?>"></input>
<fieldset>
    <legend><?php echo L('basic_configuration')?></legend>
<div class="table-list">
    <table width="100%" class="table_form">
        <tr>
            <td width="120"><?php echo L('username')?></td> 
            <td><?php echo $memberinfo['username']?><?php if($memberinfo['islock']) {?><img title="<?php echo L('lock')?>" src="<?php echo IMG_PATH?>icon/icon_padlock.gif"><?php }?><?php if($memberinfo['vip']) {?><img title="<?php echo L('vip')?>" src="<?php echo IMG_PATH?>icon/vip.gif"><?php }?></td>
        </tr>
        <tr>
            <td><?php echo L('avatar')?></td> 
            <td><img class="img-circle" src="<?php echo $memberinfo['avatar']?>" height="90" width="90"></td>
        </tr>
        <tr>
            <td><?php echo L('nickname')?></td> 
            <td><?php echo $memberinfo['nickname']?></td>
        </tr>
        <tr>
            <td><?php echo L('email')?></td>
            <td>
            <?php echo $memberinfo['email']?>
            </td>
        </tr>
        <tr>
            <td><?php echo L('mp')?></td>
            <td>
            <?php echo $memberinfo['mobile'];?>
            </td>
        </tr>
        <tr>
            <td><?php echo L('member_group')?></td>
            <td>
            <?php echo $grouplist[$memberinfo['groupid']]['name'];?>
            </td>
        </tr>
        <tr>
            <td><?php echo L('member_model')?></td>
            <td>
            <?php echo $modellist[$modelid]['name'];?>
            </td>
        </tr>
        <tr>
            <td><?php echo L('in_site_name')?></td>
            <td>
            <?php echo $sitelist[$memberinfo['siteid']]['name'];?>
            </td>
        </tr>
        
        <?php if($memberinfo['vip']) {?>
        <tr>
            <td><?php echo L('vip').L('overduedate')?></td>
            <td>
             <?php echo date('Y-m-d H:i:s',$memberinfo['overduedate']);?>
            </td>
        </tr>
        <?php }?>
        <tr>
            <td><?php echo L('注册时间')?></td>
            <td>
            <?php echo dr_date($memberinfo['regdate'], null, 'red');?></a></p>
            </td>
        </tr>
        <tr>
            <td><?php echo L('上次登录')?></td>
            <td>
            <?php echo dr_date($memberinfo['lastdate'], null, 'red');?></a></p>
            </td>
        </tr>
        <tr>
            <td><?php echo L('登录次数')?></td>
            <td>
            <?php echo $memberinfo['loginnum'];?></a></p>
            </td>
        </tr>
        <tr>
            <td><?php echo L('regip')?></td>
            <td>
            <?php echo $memberinfo['regip'];?><a class="btn btn-xs green" href="javascript:dr_show_ip('<?php echo WEB_PATH;?>api.php?op=ip_address', '<?php echo $memberinfo['regip'];?>');"><i class="fa fa-eye" /></i> <?php echo L('查看')?></a></p>
            </td>
        </tr>
        <tr>
            <td><?php echo L('lastip')?></td>
            <td>
            <?php echo $memberinfo['lastip'];?><a class="btn btn-xs green" href="javascript:dr_show_ip('<?php echo WEB_PATH;?>api.php?op=ip_address', '<?php echo $memberinfo['lastip'];?>');"><i class="fa fa-eye" /></i> <?php echo L('查看')?></a></p>
            </td>
        </tr>
    </table>
</div>
</fieldset>
<div class="bk15"></div>
<fieldset>
    <legend><?php echo L('more_configuration')?></legend>
<div class="table-list">
    <table width="100%" class="table_form">
    <?php foreach($member_modelinfo as $k=>$v) {?>
        <tr>
            <td width="120"><?php echo $k?></td> 
            <td><?php echo $v?></td>
        </tr>
    <?php }?>
    </table>
</div>
</fieldset>
</div>
</div>
</body>
</html>