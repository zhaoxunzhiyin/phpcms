	function readpoint($field, $value, $fieldinfo) {
		$paytype = isset($this->data['paytype']) && $this->data['paytype'] ? $this->data['paytype'] : '';
		if($paytype) {
			$checked1 = '';
			$checked2 = 'checked';
		} else {
			$checked1 = 'checked';
			$checked2 = '';
		}
		return '<input type="text" name="info['.$field.']" value="'.$value.'"><div class="mt-radio-inline"><label class="mt-radio mt-radio-outline"><input type="radio" name="info[paytype]" value="0" '.$checked1.'> '.L('point').' <span></span></label><label class="mt-radio mt-radio-outline"><input type="radio" name="info[paytype]" value="1" '.$checked2.'>'.L('money').'<span></span></label></div>';
	}
