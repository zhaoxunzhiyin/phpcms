<?php if($url) {?>
<div class="alert">
    <a href="<?php echo $url;?>"><?php echo $msg;?></a>
    <p><?php echo $note;?></p>
</div>
<meta http-equiv="refresh" content="0; url=<?php echo $url;?>">
<?php } else {?>
<div class="alert">
    <?php echo $msg;?>
</div>
<?php }?>
<style>
@charset "utf-8";
::-webkit-scrollbar {
    width: 12px;
    height: 12px;
    -webkit-border-radius: 10px;
}
::-webkit-scrollbar-track {
    -webkit-border-radius: 10px;
    -webkit-box-shadow: inset 0px 0px 8px rgba(0,0,0,0.2);
    background-color: rgba(0, 0, 0, 0.05);
    width: 3px;
    height: 3px;
    border:1px solid rgba(255,255,255,0.05);
}
::-webkit-scrollbar-track-piece {
    -webkit-border-radius: 10px;
    background-color: rgba(234, 234, 234, 1);
    -webkit-box-shadow: inset 0px 0px 6px rgba(0,0,0,0.15);
    width: 3px;
    height: 3px;
}
::-webkit-scrollbar-thumb {
    -webkit-border-radius: 10px;
    background-color: rgba(244, 244, 244, 1);
    -webkit-box-shadow: inset 1px 1px 15px rgba(255,255,255,0.8),
    inset 1px 1px 0px rgba(255,255,255,0.8);
    border:1px solid rgba(0,0,0,0.1);
}
::-webkit-scrollbar-thumb:hover,
::-webkit-scrollbar-thumb:active {
    background-color: rgba(204, 204, 204, 1);
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
.alert p {
    margin-top: 10px;
    font-size: 12px;
    color: rgba(2, 1, 1, 0.41);
}
.alert a {
    text-shadow: none;
    color: #337ab7;
    text-decoration: none;
}
</style>