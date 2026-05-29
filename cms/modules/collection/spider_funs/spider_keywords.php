<?php
defined('IN_CMS') or exit('No permission resources.');
function spider_keywords($data) {
	if (!$data) {
		return '';
	}
	return dr_get_keywords($data);
}