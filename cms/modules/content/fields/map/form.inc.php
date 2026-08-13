	function map($field, $value, $fieldinfo) {
		extract($fieldinfo);
		$setting = string2array($setting);
		// 表单宽度设置
		$width = is_mobile() ? '100%' : ($setting['width'] ? $setting['width'] : '100%');
		$height = $setting['height'] ? $setting['height'] : 200;
		// 地图默认值
		$value = ($value == '0,0' || $value == '0.000000,0.000000' || strlen($value) < 5) ? '' : $value;
		$input = pc_base::load_sys_class('input');
		$city = $input->ip2address($input->ip_address());
		$level = $setting['level'] ? $setting['level'] : 15;
		$errortips = $this->fields[$field]['errortips'];
		$modelid = $this->fields[$field]['modelid'];
		$str = load_js((strpos(FC_NOW_URL, 'https') === 0 ? 'https' : 'http').'://api.map.baidu.com/api?v=2.0&ak='.SYS_BDMAP_API);
		$str .= load_js(JS_PATH.'baidumap.js');
		// 获取当前坐标
		$default = '';
		if (!$value) {
			$default = 'var geolocation = new BMap.Geolocation();
		   geolocation.getCurrentPosition(function(r){
			  if(this.getStatus() == BMAP_STATUS_SUCCESS){
				  baiduSearchAddress(mapObj_'.$field.', \''.$field.'\', \''.$level.'\', \'\'+r.point.lng+\',\'+r.point.lat+\'\');
			  } else {
				 '.(CI_DEBUG ? 'dr_tips(0, \'定位失败：\'+this.getStatus());' : '').'
			  }
		   },{enableHighAccuracy: true});';
		}
		$str.= '
		<input type="hidden" name="info['.$field.']" value="'.$value.'" id="dr_'.$field.'" >
		<div style="width:'.$width.(is_numeric($width) ? 'px' : '').';height:50px">
			<div class="">
				<div class="pull-left" style="width:85%;padding-right:10px">
					<div class="input-group">
                        <input type="text" class="form-control" id="baidu_address_'.$field.'" value="'.$value.'" placeholder="'.L('输入地址，需要精确到街道号').'...">
                        <span class="input-group-btn">
                            <a title="'.L('输入地址，需要精确到街道号').'" class="btn blue" href="javascript:baiduSearchAddress(mapObj_'.$field.', \''.$field.'\', \''.$level.'\');">
                                <i class="fa fa-search"></i>
                            </a>
                        </span>
                    </div>
				</div>
				<div class="pull-left">
				<label>
					<a title="'.L('添加标注').'" href="javascript:addMarker(mapObj_'.$field.', \''.$field.'\');" class="btn btn-icon-only red">
						<i class="fa fa-map-marker"></i>
					</a></label>
				</div>
			</div>
		</div>
		<div style="width:'.$width.(is_numeric($width) ? 'px' : '').';height:'.$height.'px; clear:both;" id="baidumap_'.$field.'">
		
		</div>
		<script type="text/javascript">
        var assets_path = \''.IMG_PATH.'\';
		var mapObj_'.$field.' = new BMap.Map("baidumap_'.$field.'"); // 创建地图实例
		$(function(){
			dr_baidumap(mapObj_'.$field.', \''.$field.'\', \''.$city.'\', \''.$level.'\');
			'.$default.'
		});
		</script>';
		return $str;
	}
