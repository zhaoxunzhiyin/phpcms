<?php 
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
$show_header = true;
include $this->admin_tpl('header', 'admin');
?>
<div class="pad-10">
<div class="table-list">
<table width="100%" cellspacing="0">
    <thead>
        <tr>
            <th width="15%" align="right"><?php echo L('selects')?></th>
            <th align="left"><?php echo L('values')?></th>
        </tr>
    </thead>
<tbody>
 <?php if(is_array($forminfos_data)){
    foreach($forminfos_data as $key => $form){?>
        <tr>
            <td><?php echo $fields[$key]['name']?>:</td>
            <td><?php if ($fields[$key]['formtype']=='image' && $form) {
                echo '<a class="thumbnail" style="display: inherit;" href="javascript:dr_preview_image(\''.$form.'\');"><img style="width:30px" src="'.$form.'"></a>';
            } elseif ($fields[$key]['formtype']=='images') {
                foreach ($form as $t) {
                    echo '<a class="thumbnail" style="display: inherit;" href="javascript:dr_preview_image(\''.$t['url'].'\');"><img style="width:30px" src="'.$t['url'].'"></a>';
                }
            } elseif ($fields[$key]['formtype']=='downfiles') {
                foreach ($form as $t) {
                    $ext = trim(strtolower(strrchr((string)$t['url'], '.')), '.');
                    $file = WEB_PATH.'api.php?op=icon&fileext='.$ext;
                    if (dr_is_image($ext)) {
                        $url = 'javascript:dr_preview_image(\''.$t['url'].'\');';
                        echo '<a href="'.$url.'"><img src="'.$t['url'].'" width="25"></a>';
                    } elseif ($ext == 'mp4') {
                        $url = 'javascript:dr_preview_video(\''.dr_file($t['url']).'\');';
                        echo '<a href="'.$url.'"><img src="'.$file.'" width="25"></a>';
                    } elseif ($ext == 'mp3') {
                        $url = 'javascript:dr_preview_audio(\''.dr_file($t['url']).'\');';
                        echo '<a href="'.$url.'"><img src="'.$file.'" width="25"></a>';
                    } elseif (strpos((string)$t['url'], 'http://') === 0) {
                        $url = 'javascript:dr_preview_url(\''.$t['url'].'\');';
                        echo '<a href="'.$url.'"><img src="'.$file.'" width="25"></a>';
                    } else {
                        echo $t['url'];
                    }
                }
            } elseif ($fields[$key]['formtype']=='box') {
                $setting = dr_string2array($fields[$key]['setting']);
                $arr = dr_string2array($form);
                if (!is_array($arr)) {
                    $arr = explode(',',$arr);
                }
                $str = array();
                if (is_array($arr)) {
                    $options = dr_format_option_array($setting['options']);
                    if ($options) {
                        foreach ($options as $boxi => $boxv) {
                            if (dr_in_array($boxi, $arr)) {
                                $str[] = $boxv;
                            }
                        }
                    }
                }
                echo implode('、', $str);
            } elseif ($fields[$key]['formtype']=='editor') {
                echo code2html($form);
            } else {?><?php echo $form;?><?php }?></td>
        </tr>
    <?php }}?>
    <?php if($info['userid']){?>
    <tr>
      <th><?php echo L('账号Id');?></th>
      <td><?php echo $info['userid'];?></td>
    </tr>
    <?php }?>
    <tr>
      <th><?php echo L('作者');?></th>
      <td><?php echo $info['username'] ? $info['username'] : '游客';?></td>
    </tr>
    </tbody>
</table>
</div>
</div>
</body>
</html>