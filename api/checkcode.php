<?php
defined('IN_CMS') or exit('No permission resources.');

$cache = pc_base::load_sys_class('cache');
$checkcode = pc_base::load_sys_class('checkcode');
if(pc_base::load_sys_class('input')->get('width') && intval(pc_base::load_sys_class('input')->get('width'))) $checkcode->width = intval(pc_base::load_sys_class('input')->get('width'));
if(pc_base::load_sys_class('input')->get('height') && intval(pc_base::load_sys_class('input')->get('height'))) $checkcode->height = intval(pc_base::load_sys_class('input')->get('height'));
if(pc_base::load_sys_class('input')->get('code_len') && intval(pc_base::load_sys_class('input')->get('code_len'))) $checkcode->code_len = intval(pc_base::load_sys_class('input')->get('code_len'));
if(pc_base::load_sys_class('input')->get('font_size') && intval(pc_base::load_sys_class('input')->get('font_size'))) $checkcode->font_size = intval(pc_base::load_sys_class('input')->get('font_size'));
if (pc_base::load_sys_class('input')->get('font_color') && trim(urldecode(pc_base::load_sys_class('input')->get('font_color'))) && preg_match('/(^#[a-z0-9]{6}$)/im', trim(urldecode(pc_base::load_sys_class('input')->get('font_color'))))) $checkcode->font_color = trim(urldecode(pc_base::load_sys_class('input')->get('font_color')));
if (pc_base::load_sys_class('input')->get('background') && trim(urldecode(pc_base::load_sys_class('input')->get('background'))) && preg_match('/(^#[a-z0-9]{6}$)/im', trim(urldecode(pc_base::load_sys_class('input')->get('background'))))) $checkcode->background = trim(urldecode(pc_base::load_sys_class('input')->get('background')));
if($checkcode->width > 500 || $checkcode->width < 10) $checkcode->width = 100;
if($checkcode->height > 300 || $checkcode->height < 10) $checkcode->height = 35;
if($checkcode->code_len > 8 || $checkcode->code_len < 2) $checkcode->code_len = 4;
if($checkcode->font_size > 50 || $checkcode->font_size < 14) $checkcode->font_size = 20;
$checkcode->show_code();
$cache->set_auth_data('web-captcha-'.md5(pc_base::load_sys_class('input')->ip_address().pc_base::load_sys_class('input')->get_user_agent()), $checkcode->get_code(), 1);
exit;