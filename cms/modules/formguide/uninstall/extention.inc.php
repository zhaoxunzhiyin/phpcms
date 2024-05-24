<?php 
defined('IN_CMS') or exit('Access Denied');
defined('UNINSTALL') or exit('Access Denied');
$form_db = pc_base::load_model('sitemodel_model');
$form_field_db = pc_base::load_model('sitemodel_field_model');
$result = $form_db->select(array(), 'tablename');
if (is_array($result)) {
	foreach ($result as $r) {
		$tablename = 'form_'.$r['tablename'];
		$form_field_db->query('DROP TABLE IF EXISTS `cms_'.$tablename.'`;');
	}
}
?>