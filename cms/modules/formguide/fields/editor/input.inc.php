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
		$site_setting = string2array($this->site_config['setting']);
		$watermark = $site_setting['ueditor'] || $watermark ? 1 : 0;
		$local_watermark = $site_setting['ueditor'] || $local_watermark ? 1 : 0;
		if($enablesaveimage) {
			$value = $this->download->download($value, $watermark, $attachment, $image_reduce, 0);
		}
		if(intval($local_img)) {
			$value = str_replace(' src="'.WEB_PATH.'statics/js/ueditor/themes/default/images/spacer.gif"', '', $value);
			$value = preg_replace(array('/(<img.*?)((style)=[\'"]+(.*?)+[\'"]+)/'), array('$1'), $value);
			$value = str_replace('word_img=', 'src=', $value);
			$value = $this->download->upload_local($value, $local_watermark, $local_attachment, $local_image_reduce, 0);
		}
		$value = str_replace(array('&lt;iframe', '&gt;&lt;/iframe&gt;'), array('<iframe', '></iframe>'), $value);
		return htmlspecialchars($value);
	}
