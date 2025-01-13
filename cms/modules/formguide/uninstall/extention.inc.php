<?php 
defined('IN_CMS') or exit('Access Denied');
defined('UNINSTALL') or exit('Access Denied');
$form_db = pc_base::load_model('sitemodel_model');
$form_field_db = pc_base::load_model('sitemodel_field_model');
$result = $form_db->select(array('type'=>3), 'modelid,tablename');
if (is_array($result)) {
	foreach ($result as $r) {
		$form_field_db->delete(array('modelid'=>$r['modelid']));
		if ($form_field_db->table_exists('form_'.$r['tablename'])) {
			$form_field_db->query('DROP TABLE IF EXISTS `'.$form_field_db->db_tablepre.'form_'.$r['tablename'].'`;');
		}
	}
}
$form_db->delete(array('type'=>3));
?>