<?php
defined('IS_ADMIN') or exit('No permission resources.');
?>
<div class="alert">
    <?php if($mark) {?>
    <h5><?php echo $msg;?></h5>
    <?php } else {?>
    <h5 style="color: red"><?php echo $msg;?></h5>
    <?php }?>
    <?php if($url) {?>
    <a href="<?php echo $url;?>"><?php echo L('如果您的浏览器没有自动跳转，请点击这里');?></a>
    <meta http-equiv="refresh" content="0; url=<?php echo $url;?>">
    <?php }?>
    <?php if($note) {?><p><?php echo $note;?></p><?php }?>
</div>
<style>
@charset "utf-8";
::-webkit-scrollbar {
    width: 6px;
    height: 6px;
    -webkit-border-radius: 4px;
}
::-webkit-scrollbar-track {
    -webkit-border-radius: 4px;
    -webkit-box-shadow: inset 0px 0px 8px rgba(0,0,0,0.2);
    background-color: rgba(0,0,0,0.05);
    width: 3px;
    height: 3px;
    border:1px solid rgba(255,255,255,0.05);
}
::-webkit-scrollbar-track-piece {
    -webkit-border-radius: 4px;
    background-color: rgba(234,234,234,1);
    -webkit-box-shadow: inset 0px 0px 6px rgba(0,0,0,0.15);
    width: 3px;
    height: 3px;
}
::-webkit-scrollbar-thumb {
    -webkit-border-radius: 4px;
    background-color: rgba(244,244,244,1);
    -webkit-box-shadow: inset 1px 1px 15px rgba(255,255,255,0.8),
    inset 1px 1px 0px rgba(255,255,255,0.8);
    border:1px solid rgba(0,0,0,0.1);
}
::-webkit-scrollbar-thumb:hover,
::-webkit-scrollbar-thumb:active {
    background-color: rgba(204,204,204,1);
    -webkit-box-shadow: none;
    border:1px solid rgba(0,0,0,0.1);
}
::-webkit-scrollbar-track:window-inactive,
::-webkit-scrollbar-track-piece:window-inactive,
::-webkit-scrollbar-thumb:window-inactive {
    -webkit-box-shadow:none;
}
::-webkit-scrollbar-button {
    width: 0;
    height: 0;
    display: none;
}
::-webkit-scrollbar-corner {
    background-color: transparent;
}
::-webkit-resizer{
    background-color: transparent;
}
.alert {
    background: #ffffff!important;
    text-align: center;
    margin-top: 70px;
}
.alert h5 {
    margin-bottom: 10px;
    font-size: 16px;
}
.alert p {
    margin-top: 6px;
    font-size: 12px;
    color: rgba(2, 1, 1, 0.41);
}
.alert a {
    text-shadow: none;
    color: #337ab7;
    text-decoration: none;
    font-size: 12px;
}
</style>