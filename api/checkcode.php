<?php
defined('IN_CMS') or exit('No permission resources.'); 

$session_storage = 'session_'.pc_base::load_config('system','session_storage');
pc_base::load_sys_class($session_storage);
$checkcode = pc_base::load_sys_class('checkcode');
if($input->get('width') && intval($input->get('width'))) $checkcode->width = intval($input->get('width'));
if($input->get('height') && intval($input->get('height'))) $checkcode->height = intval($input->get('height'));
if($input->get('code_len') && intval($input->get('code_len'))) $checkcode->code_len = intval($input->get('code_len'));
if($input->get('font_size') && intval($input->get('font_size'))) $checkcode->font_size = intval($input->get('font_size'));
if($checkcode->width > 500 || $checkcode->width < 10)  $checkcode->width = 100;
if($checkcode->height > 300 || $checkcode->height < 10)  $checkcode->height = 35;
if($checkcode->code_len > 8 || $checkcode->code_len < 2)  $checkcode->code_len = 4;
$checkcode->show_code();
$_SESSION['code'] = $checkcode->get_code();