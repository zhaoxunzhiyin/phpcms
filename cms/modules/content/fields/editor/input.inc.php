	function editor($field, $value) {
		$setting = string2array($this->fields[$field]['setting']);
		$enablesaveimage = $setting['enablesaveimage'];
		$watermark = $setting['watermark'];
		$attachment = $setting['attachment'];
		$image_reduce = $setting['image_reduce'];
		$local_img = $setting['local_img'];
		$local_watermark = $setting['local_watermark'];
		$local_attachment = $setting['local_attachment'];
		$local_image_reduce = $setting['local_image_reduce'];
		$watermark = dr_site_value('ueditor', $this->siteid) || $watermark ? 1 : 0;
		$local_watermark = dr_site_value('ueditor', $this->siteid) || $local_watermark ? 1 : 0;
		$value = str_replace(' style=""', '', $value);
		if(($enablesaveimage || ($this->input->post('is_auto_down_img_'.$field) && $this->input->post('is_auto_down_img_'.$field))) && !defined('IS_COLL')) {
			$value = $this->download->download($value, $watermark, $attachment, $image_reduce, $this->input->post('info')['catid'] ? $this->input->post('info')['catid'] : (param::get_cookie('catid') ? param::get_cookie('catid') : 0));
		}
		if(intval($local_img) && !defined('IS_COLL')) {
			$value = str_replace(' src="'.WEB_PATH.'statics/js/ueditor/themes/default/images/spacer.gif"', '', $value);
			$value = preg_replace(array('/(<img.*?)((style)=[\'"]background+(.*?)+[\'"]+)/'), array('$1'), $value);
			$value = str_replace('word_img=', 'src=', $value);
			$value = str_replace('img=', 'src=', $value);
			$value = $this->download->upload_local($value, $local_watermark, $local_attachment, $local_image_reduce, $this->input->post('info')['catid'] ? $this->input->post('info')['catid'] : (param::get_cookie('catid') ? param::get_cookie('catid') : 0));
		}
		// 去除站外链接
		if ($this->input->post('is_remove_a_'.$field) && $this->input->post('is_remove_a_'.$field) && preg_match_all("/<a (.*)href=(.+)>(.*)<\/a>/Ui", $value, $arrs)) {
			$this->sitedb = pc_base::load_model('site_model');
			$sitedb_data = $this->sitedb->select();
			$sites = array();
			foreach ($sitedb_data as $t) {
				$domain = parse_url($t['domain']);
				if ($domain['port']) {
					$sites[$domain['host'].':'.$domain['port']] = $t['siteid'];
				} else {
					$sites[$domain['host']] = $t['siteid'];
				}
				$mobile_domain = parse_url($t['mobile_domain']);
				if ($mobile_domain['port']) {
					$sites[$mobile_domain['host'].':'.$mobile_domain['port']] = $t['siteid'];
				} else {
					$sites[$mobile_domain['host']] = $t['siteid'];
				}
			}
			foreach ($arrs[2] as $i => $a) {
				if (strpos($a, ' ') !== false) {
					list($a) = explode(' ', $a);
				}
				$a = trim($a, '"');
				$a = trim($a, '\'');
				$arr = parse_url($a);
				if ($arr && $arr['host'] && !isset($sites[$arr['host']])) {
					// 去除a标签
					$value = str_replace($arrs[0][$i], $arrs[3][$i], $value);
				}
			}
		}
		return html2code($value);
	}
