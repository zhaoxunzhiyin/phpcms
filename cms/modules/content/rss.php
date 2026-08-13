<?php
defined('IN_CMS') or exit('No permission resources.');

class rss {
	private $input,$db,$siteid,$rssid;
	function __construct() {
		$this->input = pc_base::load_sys_class('input');
		$this->db = pc_base::load_model('content_model');
		pc_base::load_app_class('rssbuilder','','','0');
		$this->siteid = intval($this->input->get('siteid')) ? intval(trim($this->input->get('siteid'))) : (defined('SITE_ID') && SITE_ID!=1 ? SITE_ID : get_siteid());
		$this->rssid = intval($this->input->get('rssid'));
		define('SITEID', $this->siteid);
	}

	public function init() {
		if (IS_POST) {
			dr_json(0, L('禁止提交，请检查提交地址是否有误'));
		}
		$default_style = dr_site_info('default_style', SITEID);
		if(!$default_style) $default_style = 'default';
		$siteurl = siteurl(SITEID);
		if(empty($this->rssid)) {
			$catid = $this->input->get('catid') ? intval($this->input->get('catid')) : '0';
			$siteids = getcache('category_content','commons');
			$siteid = $siteids[$catid] ? $siteids[$catid] : $this->siteid;
			$CATEGORYS = get_category($siteid);
			$subcats = subcat($catid,0,1,$siteid);
			foreach ($CATEGORYS as $r) if($r['parentid'] == 0) $channel[] = $r;
			pc_base::load_sys_class('service')->assign([
				'siteurl' => $siteurl,
				'siteid' => $siteid,
				'catid' => $catid,
				'CATEGORYS' => $CATEGORYS,
				'subcats' => $subcats,
				'channel' => $channel,
			]);
			pc_base::load_sys_class('service')->display('content','rss');
		} else {
			$CAT = dr_cat_value($this->rssid);
			$CAT['setting'] = dr_string2array($CAT['setting']);
			if(dr_count($CAT) == 0) showmessage(L('missing_part_parameters'),'blank');
			$siteid = $CAT['siteid'];
			$sitedomain = dr_site_info('domain', $siteid);  //获取站点域名
			$MODEL = getcache('model','commons');
			$modelid = $CAT['modelid'];		
			$encoding   =  CHARSET;
			$about      =  trim(FC_NOW_HOST, '/');
			$title      =  $CAT['catname'];
			$description = $CAT['description'];
			$content_html = $CAT['setting']['content_ishtml'];
			$image_link =  "<![CDATA[".$CAT['image']."]]> ";
			$category   =  '';
			$cache      =  60;
			$rssfile    = new RSSBuilder($encoding, $about, $title, $description, $image_link, $category, $cache);
			$publisher  =  '';
			$creator    =  trim(FC_NOW_HOST, '/');
			$date       =  date('r');
			$rssfile->addDCdata($publisher, $creator, $date);
			$ids = explode(",",$CAT['arrchildid']);
			if(dr_count($ids) == 1 && in_array($this->rssid, $ids)) {
				$sql .= "`catid` = '$this->rssid' AND `status` = '99'";
			} else {
				$sql .= get_sql_catid('module/category-'.$siteid.'-data',$this->rssid)." AND `status` = '99'";
			}
			if(empty($MODEL[$modelid]['tablename'])) showmessage(L('missing_part_parameters'),'blank');
			$this->db->table_name = $this->db->db_tablepre.$MODEL[$modelid]['tablename'];
			$info = $this->db->select($sql,'`title`, `description`, `url`, `inputtime`, `thumb`, `keywords`','0,20','id DESC');
		
			foreach ($info as $r) {
				//添加项目
				if(!empty($r['thumb'])) $img = "<img src=".thumb($r['thumb'], 150, 150)." border='0' /><br />";else $img = '';
				$about          =  $link = (strpos($r['url'], 'http://') !== FALSE || strpos($r['url'], 'https://') !== FALSE) ? "<![CDATA[".$r['url']."]]> " : (($content_html == 1) ? "<![CDATA[".substr($sitedomain,0,-1).$r['url']."]]> " : "<![CDATA[".substr(APP_PATH,0,-1).$r['url']."]]> ");
				$title          =   "<![CDATA[".$r['title']."]]> ";
				$description    =  "<![CDATA[".$img.$r['description']."]]> ";
				$subject        =  '';
				$date           =  date('Y-m-d H:i:s' , $r['inputtime']);
				$author         =  $CMS['sitename'].' '.trim(FC_NOW_HOST, '/');
				$comments       =  '';//注释;

				$rssfile->addItem($about, $title, $link, $description, $subject, $date,	$author, $comments, $image);
			}	
			$version = '2.00';
			$rssfile->outputRSS($version);
			exit();
		}
	}
}
?>