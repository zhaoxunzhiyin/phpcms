<?php
defined('IN_CMS') or exit('No permission resources.');
if(!defined('CACHE_MODEL_PATH')) define('CACHE_MODEL_PATH',CACHE_PATH.'caches_model'.DIRECTORY_SEPARATOR.'caches_data'.DIRECTORY_SEPARATOR);

/**
 * 内容模型数据库操作类
 */
pc_base::load_sys_class('model', '', 0);
class content_model extends model {
	private $grouplist,$isadmin,$groupid;
	public $input,$cache,$url,$siteid,$userid,$rid,$model,$modelid,$search_db,$fields,$sitedb,$hits_db,$sitemodel_db,$category_db,$attachment_db,$form,$sitemodel,$form_cache,$content_check_db,$message_db,$upload;
	public $table_name = '';
	public $model_tablename = '';
	public $category = '';
	public function __construct() {
		pc_base::load_sys_class('upload','',0);
		$this->input = pc_base::load_sys_class('input');
		$this->cache = pc_base::load_sys_class('cache');
		$this->db_config = pc_base::load_config('database');
		$this->db_setting = 'default';
		parent::__construct();
		$this->url = pc_base::load_app_class('url', 'content');
		$this->siteid = get_siteid();
		$this->grouplist = getcache('grouplist', 'member');
		$this->isadmin = IS_ADMIN && param::get_session('roleid') ? 1 : 0;
        $this->userid = $this->isadmin ? (param::get_session('userid') ? param::get_session('userid') : param::get_cookie('userid')) : param::get_cookie('_userid');
        $this->rid = md5(FC_NOW_URL.$this->input->get_user_agent().$this->input->ip_address().intval($this->userid));
		$this->groupid = param::get_cookie('_groupid') ? param::get_cookie('_groupid') : 8;
	}
	public function set_model($modelid) {
		$this->model = getcache('model', 'commons');
		$this->modelid = $modelid;
		$this->table_name = $this->db_tablepre.$this->model[$modelid]['tablename'];
		$this->model_tablename = $this->model[$modelid]['tablename'];
	}
	/**
	 * 添加内容
	 * 
	 * @param $datas
	 * @param $isimport 是否为外部接口导入
	 */
	public function add_content($data,$isimport = 0) {
		// 挂钩点 模块内容发布之前
		$rt = pc_base::load_sys_class('hooks')::trigger_callback('module_content_before', $data);
		if ($rt && isset($rt['code'])) {
			// 钩子中存在特殊返回值
			if ($rt['code'] == 0) {
				return dr_return_data(0, $rt['msg']);
			}
			$data = $rt['data'];
		}
		$this->search_db = pc_base::load_model('search_model');
		$modelid = $this->modelid;
		require_once CACHE_MODEL_PATH.'content_input.class.php';
		require_once CACHE_MODEL_PATH.'content_update.class.php';
		$content_input = new content_input($this->modelid);
		$inputinfo = $content_input->get($data,$isimport);

		$systeminfo = $inputinfo['system'];
		$modelinfo = $inputinfo['model'];

		if($data['inputtime'] && !is_numeric($data['inputtime'])) {
			$systeminfo['inputtime'] = strtotime($data['inputtime']);
		} elseif(!$data['inputtime']) {
			$systeminfo['inputtime'] = SYS_TIME;
		} else {
			$systeminfo['inputtime'] = $data['inputtime'];
		}
		
		//读取模型字段配置中，关于日期配置格式，来组合日期数据
		$this->fields = getcache('model_field_'.$modelid,'model');
		$setting = string2array($this->fields['inputtime']['setting']);
		if($setting['fieldtype']=='date') {
			$systeminfo['inputtime'] = date('Y-m-d');
		}elseif($setting['fieldtype']=='datetime'){
 			$systeminfo['inputtime'] = date('Y-m-d H:i:s');
		}

		if($data['updatetime'] && !is_numeric($data['updatetime'])) {
			$systeminfo['updatetime'] = strtotime($data['updatetime']);
		} elseif(!$data['updatetime']) {
			$systeminfo['updatetime'] = SYS_TIME;
		} else {
			$systeminfo['updatetime'] = $data['updatetime'];
		}
		$inputinfo['system']['username'] = $systeminfo['username'] = $data['username'] ? $data['username'] : param::get_cookie('admin_username');
		$systeminfo['sysadd'] = IS_ADMIN || IS_COLLAPI ? 1 : 0;
		
		foreach($this->fields as $field=>$t) {
			if ($t['formtype']=='editor') {
				// 提取缩略图
				$is_auto_thumb = $this->input->post('is_auto_thumb_'.$field);
				if(isset($systeminfo['thumb']) && isset($is_auto_thumb) && $is_auto_thumb && !$systeminfo['thumb']) {
					$downloadfiles = pc_base::load_sys_class('cache')->get_data('downloadfiles-'.$this->siteid);
					$auto_thumb_length = intval($this->input->post('auto_thumb_'.$field))-1;
					if (isset($downloadfiles) && $downloadfiles) {
						$systeminfo['thumb'] = $downloadfiles[$auto_thumb_length];
					} else {
						$setting = string2array($t['setting']);
						$watermark = $setting['watermark'];
						$attachment = $setting['attachment'];
						$image_reduce = $setting['image_reduce'];
						$watermark = dr_site_value('ueditor', $this->siteid) || $watermark ? 1 : 0;
						if(preg_match_all("/(src)=([\"|']?)([^ \"'>]+)\\2/i", str_replace('_"data:image', '"data:image', code2html($modelinfo[$field])), $matches)) {
							$this->upload = new upload('content',$systeminfo['catid'],$this->siteid);
							$images = [];
							foreach ($matches[3] as $img) {
								if (preg_match('/^(data:\s*image\/(\w+);base64,)/i', $img, $result)) {
									// 处理图片
									$ext = strtolower($result[2]);
									if (!in_array($ext, ['png', 'jpg', 'jpeg', 'gif', 'webp'])) {
										continue;
									}
									$content = base64_decode(str_replace($result[1], '', $img));
									if (strlen($content) > 30000000) {
										continue;
									}
									$rt = $this->upload->base64_image([
										'ext' => $ext,
										'content' => $content,
										'watermark' => $watermark,
										'attachment' => $this->upload->get_attach_info(intval($attachment), intval($image_reduce)),
									]);
									$attachments = array();
									if (defined('SYS_ATTACHMENT_CF') && SYS_ATTACHMENT_CF && $rt['data']['md5']) {
										$attachmentdb = pc_base::load_model('attachment_model');
										$att = $attachmentdb->get_one(array('userid'=>intval($this->userid),'filemd5'=>$rt['data']['md5'],'fileext'=>$rt['data']['ext'],'filesize'=>$rt['data']['size']));
										if ($att) {
											$attachments = dr_return_data($att['aid'], 'ok');
											$images[] = $att['aid'];
											// 删除现有附件
											// 开始删除文件
											$storage = new storage($this->module,$catid,$this->siteid);
											$storage->delete($this->upload->get_attach_info((int)$attachment), $rt['data']['file']);
											$rt['data'] = get_attachment($att['aid']);
										}
									}
									if (!$attachments) {
										$rt['data']['isadmin'] = $this->isadmin;
										$attachments = $this->upload->save_data($rt['data'], 'ueditor:'.$this->rid);
										if ($attachments['code']) {
											// 附件归档
											$images[] = $attachments['code'];
											// 标记附件
											upload_json($attachments['code'],$rt['data']['url'],$rt['data']['name'],format_file_size($rt['data']['size']));
										}
									}
								} else {
									$ext = get_image_ext($img);
									if (!$ext) {
										continue;
									}
									if (!$this->isadmin && $this->check_upload($this->userid)) {
										//用户存储空间已满
										log_message('debug', '用户存储空间已满');
									} else {
										// 下载缩略图
										// 判断域名白名单
										$arr = parse_url($img);
										$domain = $arr['host'];
										if ($domain) {
											$this->sitedb = pc_base::load_model('site_model');
											$site_data = $this->sitedb->select();
											$sites = array();
											foreach ($site_data as $t) {
												$site_domain = parse_url($t['domain']);
												if ($site_domain['port']) {
													$sites[$site_domain['host'].':'.$site_domain['port']] = $t['siteid'];
												} else {
													$sites[$site_domain['host']] = $t['siteid'];
												}
												if ($t['mobile_domain']) {
													$site_mobile_domain = parse_url($t['mobile_domain']);
													if ($site_mobile_domain['port']) {
														$sites[$site_mobile_domain['host'].':'.$site_mobile_domain['port']] = $t['siteid'];
													} else {
														$sites[$site_mobile_domain['host']] = $t['siteid'];
													}
												}
											}
											if (isset($sites[$domain])) {
												// 过滤站点域名
												$images[] = $img;
											} elseif (strpos(SYS_UPLOAD_URL, $domain) !== false) {
												// 过滤附件白名单
												$images[] = $img;
											} else {
												if(strpos($img, '://') === false) continue;
												$zj = 0;
												$remote = get_cache('attachment');
												if ($remote) {
													foreach ($remote as $t) {
														if (strpos($t['url'], $domain) !== false) {
															$zj = 1;
															break;
														}
													}
												}
												if ($zj == 0) {
													// 可以下载文件
													// 下载远程文件
													$file = dr_catcher_data($img, 8);
													if (!$file) {
														CI_DEBUG && log_message('debug', '服务器无法下载图片：'.$img);
													} else {
														// 尝试找一找附件库
														$attachmentdb = pc_base::load_model('attachment_model');
														$att = $attachmentdb->get_one(array('related'=>'%ueditor%', 'filemd5'=>md5($file)));
														if ($att) {
															$images[] = $att['aid'];
															// 标记附件
															upload_json($att['aid'],dr_get_file($att['aid']),$att['name'],format_file_size($att['size']));
														} else {
															// 下载归档
															$rt = $this->upload->down_file([
																'url' => $img,
																'timeout' => 5,
																'watermark' => $watermark,
																'attachment' => $this->upload->get_attach_info(intval($attachment), intval($image_reduce)),
																'file_ext' => $ext,
																'file_content' => $file,
															]);
															if ($rt['code']) {
																$rt['data']['isadmin'] = $this->isadmin;
																$att = $this->upload->save_data($rt['data'], 'ueditor:'.$this->rid);
																if ($att['code']) {
																	// 归档成功
																	$images[] = $att['code'];
																	// 标记附件
																	upload_json($att['code'],$rt['data']['url'],$rt['data']['name'],format_file_size($rt['data']['size']));
																}
															}
														}
													}
												} else {
													$images[] = $img;
												}
											}
										}
									}
								}
							}
							if ($images) {
								$systeminfo['thumb'] = $images[$auto_thumb_length];
							}
						}
					}
				}
				// 提取描述信息
				$is_auto_description = $this->input->post('is_auto_description_'.$field);
				if(isset($systeminfo['description']) && isset($is_auto_description) && !$systeminfo['description']) {
					$this->form = getcache('model', 'commons');
					$this->sitemodel = $this->cache->get('sitemodel');
					$this->form_cache = $this->sitemodel[$this->form[$this->modelid]['tablename']];
					$auto_description_length = intval($this->input->post('auto_description_'.$field));
					if (isset($this->form_cache['setting']['desc_clear']) && $this->form_cache['setting']['desc_clear']) {
						$systeminfo['description'] = dr_get_description(str_replace(array("'","\r\n","\t",'[page]','[/page]'), '', str_replace(' ', '', code2html($modelinfo[$field]))), $auto_description_length);
					} else {
						$systeminfo['description'] = dr_get_description(str_replace(array("'","\r\n","\t",'[page]','[/page]'), '', code2html($modelinfo[$field])), $auto_description_length);
					}
				}
			}
		}
		
		// 提取描述信息
		if (isset($systeminfo['description']) && !$systeminfo['description']) {
			$this->form = getcache('model', 'commons');
			$this->sitemodel = $this->cache->get('sitemodel');
			$this->form_cache = $this->sitemodel[$this->form[$this->modelid]['tablename']];
			if (isset($this->form_cache['setting']['desc_auto']) && $this->form_cache['setting']['desc_auto']) {
				// 手动填充描述
			} else {
				// 自动填充描述
				if (isset($modelinfo['content']) && code2html($modelinfo['content'])) {
					if (isset($this->form_cache['setting']['desc_limit']) && $this->form_cache['setting']['desc_limit']) {
						if (isset($this->form_cache['setting']['desc_clear']) && $this->form_cache['setting']['desc_clear']) {
							$systeminfo['description'] = dr_get_description(str_replace(array("'","\r\n","\t",'[page]','[/page]'), '', str_replace(' ', '', code2html($modelinfo['content']))), $this->form_cache['setting']['desc_limit']);
						} else {
							$systeminfo['description'] = dr_get_description(str_replace(array("'","\r\n","\t",'[page]','[/page]'), '', code2html($modelinfo['content'])), $this->form_cache['setting']['desc_limit']);
						}
					} else {
						if (isset($this->form_cache['setting']['desc_clear']) && $this->form_cache['setting']['desc_clear']) {
							$systeminfo['description'] = dr_get_description(str_replace(array("'","\r\n","\t",'[page]','[/page]'), '', str_replace(' ', '', code2html($modelinfo['content']))));
						} else {
							$systeminfo['description'] = dr_get_description(str_replace(array("'","\r\n","\t",'[page]','[/page]'), '', code2html($modelinfo['content'])));
						}
					}
				}
			}
		}
		$systeminfo['keywords'] && $systeminfo['keywords'] = str_replace(array('/','\\','#','.',"'"),' ',$systeminfo['keywords']);
		$systeminfo['tableid'] = 0;
		
		//主表
		$tablename = $this->table_name = $this->db_tablepre.$this->model_tablename;
		$id = $modelinfo['id'] = $this->insert($systeminfo,true);
		$this->update($systeminfo,array('id'=>$id));
		//更新URL地址
		if($data['islink']==1) {
			$urls[0] = trim_script($this->input->post('linkurl'));
			$urls[0] = remove_xss($urls[0]);
			$urls[0] = str_replace(array('select ',')','\\','#',"'"),' ',$urls[0]);
		} else {
			list($urls) = $this->url->show($id, 0, $systeminfo['catid'], $systeminfo['inputtime'], $data['prefix'],$inputinfo,'add');
			// 站长工具
			if (module_exists('bdts')) {
				if ($urls['content_ishtml']) {
					$data['url'] = siteurl($this->siteid).$urls[0];
				} else {
					$data['url'] = $urls[0];
				}
				$this->sitemodel_db = pc_base::load_model('sitemodel_model');
				$sitemodel = $this->sitemodel_db->get_one(array('modelid'=>$this->modelid));
				$data['tablename'] = $sitemodel['tablename'];
			}
		}
		$this->table_name = $tablename;
		$tid = $this->get_table_id($id);
		$this->is_data_table($this->table_name.'_data_', $tid);
		$this->update(array('url'=>$urls[0],'tableid'=>$tid),array('id'=>$id));
		//附属表
		$this->table_name = $this->table_name.'_data_'.$tid;
		$this->insert($modelinfo);
		//添加统计
		$this->hits_db = pc_base::load_model('hits_model');
		$hitsid = 'c-'.$modelid.'-'.$id;
		$this->hits_db->insert(array('hitsid'=>$hitsid,'catid'=>$systeminfo['catid'],'updatetime'=>SYS_TIME));
		if($data['status']==99) {
			//更新到全站搜索
			$this->search_api($id,$inputinfo);
		}
		//更新栏目统计数据
		$this->update_category_items($systeminfo['catid'],'add',1);
		//调用 update
		$content_update = new content_update($this->modelid,$id);
		//合并后，调用update
		$merge_data = array_merge($systeminfo,$modelinfo);
		$merge_data['posids'] = $data['posids'];
		$content_update->update($merge_data);
		
		//发布到审核列表中
		if((!IS_ADMIN && !IS_COLLAPI) || $data['status']!=99) {
			$this->content_check_db = pc_base::load_model('content_check_model');
			$check_data = array(
				'checkid'=>'c-'.$id.'-'.$modelid,
				'catid'=>$systeminfo['catid'],
				'siteid'=>$this->siteid,
				'title'=>$systeminfo['title'],
				'username'=>$systeminfo['username'],
				'inputtime'=>$systeminfo['inputtime'],
				'status'=>$data['status'],
				);
			$this->content_check_db->insert($check_data);
		}
		$relation_content = array();
		//END发布到审核列表中
		if(!$isimport) {
			$html = pc_base::load_app_class('html', 'content');
			$urls['data']['system']['id'] = $id;
			$catid = $systeminfo['catid'];
			if($urls['content_ishtml'] && $data['status']==99) {
				$html->show($urls[1],$urls['data']);
				$relation_content[] = array($id, $catid);
			}
		}
		//发布到其他栏目
		if($id && $this->input->post('othor_catid') && is_array($this->input->post('othor_catid'))) {
			$linkurl = $urls[0];
			$r = $this->get_one(array('id'=>$id));
			foreach ($this->input->post('othor_catid') as $cid=>$_v) {
				$othor_catid[] = $cid;
				$this->set_catid($cid);
				$mid = $this->category[$cid]['modelid'];
				if($modelid==$mid) {
					//相同模型的栏目插入新的数据
					$inputinfo['system']['catid'] = $systeminfo['catid'] = $cid;
					$newid = $modelinfo['id'] = $this->insert($systeminfo,true);
					$tnewid = $this->get_table_id($newid);
					$this->is_data_table($this->table_name.'_data_', $tnewid);
					$this->table_name = $tablename.'_data_'.$tnewid;
					$this->insert($modelinfo);
					if($data['islink']==1) {
						$urls = $this->input->post('linkurl');
						$urls = str_replace(array('select ',')','\\','#',"'"),' ',$urls);
					} else {
						list($urls) = $this->url->show($newid, 0, $cid, $systeminfo['inputtime'], $data['prefix'],$inputinfo,'add');
					}
					$this->table_name = $tablename;
					$this->update(array('url'=>$urls[0],'tableid'=>$tnewid),array('id'=>$newid));
					//发布到审核列表中
					if($data['status']!=99) {
						$check_data = array(
							'checkid'=>'c-'.$newid.'-'.$mid,
							'catid'=>$cid,
							'siteid'=>$this->siteid,
							'title'=>$systeminfo['title'],
							'username'=>$systeminfo['username'],
							'inputtime'=>$systeminfo['inputtime'],
							'status'=>1,
							);
						$this->content_check_db->insert($check_data);
					}
					if($urls['content_ishtml'] && $data['status']==99) {
						$html->show($urls[1],$urls['data']);
						$relation_content[] = array($newid, $cid);
					}
				} else {
					//不同模型插入转向链接地址
					$newid = $this->insert(
					array('title'=>$systeminfo['title'],
						'style'=>$systeminfo['style'],
						'thumb'=>$systeminfo['thumb'],
						'keywords'=>$systeminfo['keywords'],
						'description'=>$systeminfo['description'],
						'status'=>$systeminfo['status'],
						'catid'=>$cid,
						'url'=>$linkurl,
						'sysadd'=>1,
						'username'=>$systeminfo['username'],
						'inputtime'=>$systeminfo['inputtime'],
						'updatetime'=>$systeminfo['updatetime'],
						'islink'=>1,
						'tableid'=>0
					),true);
					$tnewid = $this->get_table_id($newid);
					$this->is_data_table($this->table_name.'_data_', $tnewid);
					$this->update(array('tableid'=>$tnewid),array('id'=>$newid));
					$this->table_name = $this->table_name.'_data_'.$tnewid;
					$this->insert(array('id'=>$newid));
					//发布到审核列表中
					if($data['status']!=99) {
						$check_data = array(
							'checkid'=>'c-'.$newid.'-'.$mid,
							'catid'=>$systeminfo['catid'],
							'siteid'=>$this->siteid,
							'title'=>$systeminfo['title'],
							'username'=>$systeminfo['username'],
							'inputtime'=>$systeminfo['inputtime'],
							'status'=>1,
							);
						$this->content_check_db->insert($check_data);
					}
				}
				$hitsid = 'c-'.$mid.'-'.$newid;
				$this->hits_db->insert(array('hitsid'=>$hitsid,'catid'=>$cid,'updatetime'=>SYS_TIME));
				$this->update_category_items($cid,'add',1);
			}
		}
		//END 发布到其他栏目
		//更新附件状态
		if(SYS_ATTACHMENT_STAT) {
			$this->attachment_db = pc_base::load_model('attachment_model');
			$this->attachment_db->api_update('','c-'.$systeminfo['catid'].'-'.$id,2);
		}
		//生成静态
		if(!$isimport && $data['status']==99) {
			//在添加和修改内容处定义了 INDEX_HTML
			if(defined('INDEX_HTML')) $html->index();
			if(defined('RELATION_HTML')) {
				$relation_catids = array($catid);
				if($othor_catid && is_array($othor_catid)) {
					$relation_catids = array_merge($relation_catids, $othor_catid);
				}
				$html->create_relation_html($relation_catids, $relation_content);
			}
		}
		// 挂钩点 模块内容发布完成之后
		pc_base::load_sys_class('hooks')::trigger('module_content_after', $data, []);
		return $id;
	}
	/**
	 * 修改内容
	 * 
	 * @param $datas
	 */
	public function edit_content($data,$id) {
		// 挂钩点 模块内容修改之前
		$rt = pc_base::load_sys_class('hooks')::trigger_callback('module_content_before', $data);
		if ($rt && isset($rt['code'])) {
			// 钩子中存在特殊返回值
			if ($rt['code'] == 0) {
				return dr_return_data(0, $rt['msg']);
			}
			$data = $rt['data'];
		}
		if (SYS_CACHE) {
			for($i=1; $i<=dr_count(array_filter(explode('[page]', $data['content']))); $i++) {
				$this->cache->clear('module_'.$this->modelid.'_show_id_'.$id.($i > 1 ? '_p'.$i : ''));
				$this->cache->clear('rs_module_'.$this->modelid.'_show_id_'.$id.($i > 1 ? '_p'.$i : ''));
				$this->cache->clear('module_'.$this->modelid.'_show_html_id_'.$id);
			}
		}
		$model_tablename = $this->model_tablename;
		$old = $this->get_content($data['catid'],$id);
		//前台权限判断
		if(!IS_ADMIN && !IS_COLLAPI) {
			$_username = param::get_cookie('_username');
			$us = $this->get_one(array('id'=>$id,'username'=>$_username));
			if(!$us) return false;
		}
		
		$this->search_db = pc_base::load_model('search_model');
		
		require_once CACHE_MODEL_PATH.'content_input.class.php';
		require_once CACHE_MODEL_PATH.'content_update.class.php';
		$content_input = new content_input($this->modelid);
		$inputinfo = $content_input->get($data);

		$systeminfo = $inputinfo['system'];
		$modelinfo = $inputinfo['model'];
		if($data['inputtime'] && !is_numeric($data['inputtime'])) {
			$systeminfo['inputtime'] = strtotime($data['inputtime']);
		} elseif(!$data['inputtime']) {
			$systeminfo['inputtime'] = SYS_TIME;
		} else {
			$systeminfo['inputtime'] = $data['inputtime'];
		}

		// 不更新时间
		if ($id && $this->input->post('no_time')) {
			if (!$old['updatetime']) {
				$systeminfo['updatetime'] = SYS_TIME;
			} else {
				$systeminfo['updatetime'] = $old['updatetime'];
			}
		} else {
			if($data['updatetime'] && !is_numeric($data['updatetime'])) {
				$systeminfo['updatetime'] = strtotime($data['updatetime']);
			} elseif(!$data['updatetime']) {
				$systeminfo['updatetime'] = SYS_TIME;
			} else {
				$systeminfo['updatetime'] = $data['updatetime'];
			}
		}
		
		$this->fields = getcache('model_field_'.$this->modelid,'model');
		foreach($this->fields as $field=>$t) {
			if ($t['formtype']=='editor') {
				// 提取缩略图
				$is_auto_thumb = $this->input->post('is_auto_thumb_'.$field);
				if(isset($systeminfo['thumb']) && isset($is_auto_thumb) && $is_auto_thumb && !$systeminfo['thumb']) {
					$downloadfiles = pc_base::load_sys_class('cache')->get_data('downloadfiles-'.$this->siteid);
					$auto_thumb_length = intval($this->input->post('auto_thumb_'.$field))-1;
					if (isset($downloadfiles) && $downloadfiles) {
						$systeminfo['thumb'] = $downloadfiles[$auto_thumb_length];
					} else {
						$setting = string2array($t['setting']);
						$watermark = $setting['watermark'];
						$attachment = $setting['attachment'];
						$image_reduce = $setting['image_reduce'];
						$watermark = dr_site_value('ueditor', $this->siteid) || $watermark ? 1 : 0;
						if(preg_match_all("/(src)=([\"|']?)([^ \"'>]+)\\2/i", str_replace('_"data:image', '"data:image', code2html($modelinfo[$field])), $matches)) {
							$this->upload = new upload('content',$systeminfo['catid'],$this->siteid);
							$images = [];
							foreach ($matches[3] as $img) {
								if (preg_match('/^(data:\s*image\/(\w+);base64,)/i', $img, $result)) {
									// 处理图片
									$ext = strtolower($result[2]);
									if (!in_array($ext, ['png', 'jpg', 'jpeg', 'gif', 'webp'])) {
										continue;
									}
									$content = base64_decode(str_replace($result[1], '', $img));
									if (strlen($content) > 30000000) {
										continue;
									}
									$rt = $this->upload->base64_image([
										'ext' => $ext,
										'content' => $content,
										'watermark' => $watermark,
										'attachment' => $this->upload->get_attach_info(intval($attachment), intval($image_reduce)),
									]);
									$attachments = array();
									if (defined('SYS_ATTACHMENT_CF') && SYS_ATTACHMENT_CF && $rt['data']['md5']) {
										$attachmentdb = pc_base::load_model('attachment_model');
										$att = $attachmentdb->get_one(array('userid'=>intval($this->userid),'filemd5'=>$rt['data']['md5'],'fileext'=>$rt['data']['ext'],'filesize'=>$rt['data']['size']));
										if ($att) {
											$attachments = dr_return_data($att['aid'], 'ok');
											$images[] = $att['aid'];
											// 删除现有附件
											// 开始删除文件
											$storage = new storage($this->module,$catid,$this->siteid);
											$storage->delete($this->upload->get_attach_info((int)$attachment), $rt['data']['file']);
											$rt['data'] = get_attachment($att['aid']);
										}
									}
									if (!$attachments) {
										$rt['data']['isadmin'] = $this->isadmin;
										$attachments = $this->upload->save_data($rt['data'], 'ueditor:'.$this->rid);
										if ($attachments['code']) {
											// 附件归档
											$images[] = $attachments['code'];
											// 标记附件
											upload_json($attachments['code'],$rt['data']['url'],$rt['data']['name'],format_file_size($rt['data']['size']));
										}
									}
								} else {
									$ext = get_image_ext($img);
									if (!$ext) {
										continue;
									}
									if (!$this->isadmin && $this->check_upload($this->userid)) {
										//用户存储空间已满
										log_message('debug', '用户存储空间已满');
									} else {
										// 下载缩略图
										// 判断域名白名单
										$arr = parse_url($img);
										$domain = $arr['host'];
										if ($domain) {
											$this->sitedb = pc_base::load_model('site_model');
											$site_data = $this->sitedb->select();
											$sites = array();
											foreach ($site_data as $t) {
												$site_domain = parse_url($t['domain']);
												if ($site_domain['port']) {
													$sites[$site_domain['host'].':'.$site_domain['port']] = $t['siteid'];
												} else {
													$sites[$site_domain['host']] = $t['siteid'];
												}
												if ($t['mobile_domain']) {
													$site_mobile_domain = parse_url($t['mobile_domain']);
													if ($site_mobile_domain['port']) {
														$sites[$site_mobile_domain['host'].':'.$site_mobile_domain['port']] = $t['siteid'];
													} else {
														$sites[$site_mobile_domain['host']] = $t['siteid'];
													}
												}
											}
											if (isset($sites[$domain])) {
												// 过滤站点域名
												$images[] = $img;
											} elseif (strpos(SYS_UPLOAD_URL, $domain) !== false) {
												// 过滤附件白名单
												$images[] = $img;
											} else {
												if(strpos($img, '://') === false) continue;
												$zj = 0;
												$remote = get_cache('attachment');
												if ($remote) {
													foreach ($remote as $t) {
														if (strpos($t['url'], $domain) !== false) {
															$zj = 1;
															break;
														}
													}
												}
												if ($zj == 0) {
													// 可以下载文件
													// 下载远程文件
													$file = dr_catcher_data($img, 8);
													if (!$file) {
														CI_DEBUG && log_message('debug', '服务器无法下载图片：'.$img);
													} else {
														// 尝试找一找附件库
														$attachmentdb = pc_base::load_model('attachment_model');
														$att = $attachmentdb->get_one(array('related'=>'%ueditor%', 'filemd5'=>md5($file)));
														if ($att) {
															$images[] = $att['aid'];
															// 标记附件
															upload_json($att['aid'],dr_get_file($att['aid']),$att['name'],format_file_size($att['size']));
														} else {
															// 下载归档
															$rt = $this->upload->down_file([
																'url' => $img,
																'timeout' => 5,
																'watermark' => $watermark,
																'attachment' => $this->upload->get_attach_info(intval($attachment), intval($image_reduce)),
																'file_ext' => $ext,
																'file_content' => $file,
															]);
															if ($rt['code']) {
																$rt['data']['isadmin'] = $this->isadmin;
																$att = $this->upload->save_data($rt['data'], 'ueditor:'.$this->rid);
																if ($att['code']) {
																	// 归档成功
																	$images[] = $att['code'];
																	// 标记附件
																	upload_json($att['code'],$rt['data']['url'],$rt['data']['name'],format_file_size($rt['data']['size']));
																}
															}
														}
													}
												} else {
													$images[] = $img;
												}
											}
										}
									}
								}
							}
							if ($images) {
								$systeminfo['thumb'] = $images[$auto_thumb_length];
							}
						}
					}
				}
				// 提取描述信息
				$is_auto_description = $this->input->post('is_auto_description_'.$field);
				if(isset($systeminfo['description']) && isset($is_auto_description) && !$systeminfo['description']) {
					$this->form = getcache('model', 'commons');
					$this->sitemodel = $this->cache->get('sitemodel');
					$this->form_cache = $this->sitemodel[$this->form[$this->modelid]['tablename']];
					$auto_description_length = intval($this->input->post('auto_description_'.$field));
					if (isset($this->form_cache['setting']['desc_clear']) && $this->form_cache['setting']['desc_clear']) {
						$systeminfo['description'] = dr_get_description(str_replace(array("'","\r\n","\t",'[page]','[/page]'), '', str_replace(' ', '', code2html($modelinfo[$field]))), $auto_description_length);
					} else {
						$systeminfo['description'] = dr_get_description(str_replace(array("'","\r\n","\t",'[page]','[/page]'), '', code2html($modelinfo[$field])), $auto_description_length);
					}
				}
			}
		}
		
		// 提取描述信息
		if (isset($systeminfo['description']) && !$systeminfo['description']) {
			$this->form = getcache('model', 'commons');
			$this->sitemodel = $this->cache->get('sitemodel');
			$this->form_cache = $this->sitemodel[$this->form[$this->modelid]['tablename']];
			if (isset($this->form_cache['setting']['desc_auto']) && $this->form_cache['setting']['desc_auto']) {
				// 手动填充描述
			} else {
				// 自动填充描述
				if (isset($modelinfo['content']) && code2html($modelinfo['content'])) {
					if (isset($this->form_cache['setting']['desc_limit']) && $this->form_cache['setting']['desc_limit']) {
						if (isset($this->form_cache['setting']['desc_clear']) && $this->form_cache['setting']['desc_clear']) {
							$systeminfo['description'] = dr_get_description(str_replace(array("'","\r\n","\t",'[page]','[/page]'), '', str_replace(' ', '', code2html($modelinfo['content']))), $this->form_cache['setting']['desc_limit']);
						} else {
							$systeminfo['description'] = dr_get_description(str_replace(array("'","\r\n","\t",'[page]','[/page]'), '', code2html($modelinfo['content'])), $this->form_cache['setting']['desc_limit']);
						}
					} else {
						if (isset($this->form_cache['setting']['desc_clear']) && $this->form_cache['setting']['desc_clear']) {
							$systeminfo['description'] = dr_get_description(str_replace(array("'","\r\n","\t",'[page]','[/page]'), '', str_replace(' ', '', code2html($modelinfo['content']))));
						} else {
							$systeminfo['description'] = dr_get_description(str_replace(array("'","\r\n","\t",'[page]','[/page]'), '', code2html($modelinfo['content'])));
						}
					}
				}
			}
		}
		if($data['islink']==1) {
			$systeminfo['url'] = $this->input->post('linkurl');
			$systeminfo['url'] = str_replace(array('select ',')','\\','#',"'"),' ',$systeminfo['url']);
		} else {
			//更新URL地址
			list($urls) = $this->url->show($id, 0, $systeminfo['catid'], $systeminfo['inputtime'], $data['prefix'],$inputinfo,'edit');
			$systeminfo['url'] = $urls[0];
			// 站长工具
			if (module_exists('bdts')) {
				if ($urls['content_ishtml']) {
					$data['url'] = siteurl($this->siteid).$urls[0];
				} else {
					$data['url'] = $urls[0];
				}
				$this->sitemodel_db = pc_base::load_model('sitemodel_model');
				$sitemodel = $this->sitemodel_db->get_one(array('modelid'=>$this->modelid));
				$data['status'] = $old['status'];
				$data['tablename'] = $sitemodel['tablename'];
			}
		}
		$systeminfo['keywords'] && $systeminfo['keywords'] = str_replace(array('/','\\','#','.',"'"),' ',$systeminfo['keywords']);
		//主表
		$this->table_name = $this->db_tablepre.$model_tablename;
		$this->update($systeminfo,array('id'=>$id));

		//附属表
		$this->table_name = $this->table_name.'_data_'.$old['tableid'];
		$this->update($modelinfo,array('id'=>$id));
		$this->search_api($id,$inputinfo);
		//调用 update
		$content_update = new content_update($this->modelid,$id);
		$content_update->update($data);
		//更新附件状态
		if(SYS_ATTACHMENT_STAT) {
			$this->attachment_db = pc_base::load_model('attachment_model');
			$this->attachment_db->api_update('','c-'.$systeminfo['catid'].'-'.$id,2);
		}
		//更新审核列表
		$this->content_check_db = pc_base::load_model('content_check_model');
		$check_data = array(
			'catid'=>$systeminfo['catid'],
			'siteid'=>$this->siteid,
			'title'=>$systeminfo['title'],
			'status'=>$systeminfo['status'],
			);
		if(!isset($systeminfo['status'])) unset($check_data['status']);
		$this->content_check_db->update($check_data,array('checkid'=>'c-'.$id.'-'.$this->modelid));
		//生成静态
		$html = pc_base::load_app_class('html', 'content');
		if($urls['content_ishtml']) {
			$html->show($urls[1],$urls['data']);
		}
		//在添加和修改内容处定义了 INDEX_HTML
		if(defined('INDEX_HTML')) $html->index();
		if(defined('RELATION_HTML')) {
			$html->create_relation_html($systeminfo['catid'], array(array($id, $systeminfo['catid'])));
		}
		// 挂钩点 模块内容修改完成之后
		pc_base::load_sys_class('hooks')::trigger('module_content_after', $data, $old);
		$this->cache->clear('module_'.$this->modelid.'_show_id_'.$id);
		return true;
	}
	
	public function status($ids = array(), $status = 99) {
		$this->content_check_db = pc_base::load_model('content_check_model');
		$this->message_db = pc_base::load_model('message_model');
		$this->set_model($this->modelid);
		if(is_array($ids) && !empty($ids)) {
			foreach($ids as $id) {
				$this->update(array('status'=>$status),array('id'=>$id));
				$del = false;
				$r = $this->get_one(array('id'=>$id));
				if($status==0) {
					//退稿发送短消息、邮件
					$message = L('reject_message_tips').$r['title']."<br><a href='index.php?m=member&c=content&a=edit&catid={$r['catid']}&id={$r['id']}'><font color=red>".L('click_edit')."</font></a><br>";
					if($this->input->post('reject_c') && $this->input->post('reject_c') != L('reject_msg')) {
						$message .= $this->input->post('reject_c');
					} elseif($this->input->get('reject_c') && $this->input->get('reject_c') != L('reject_msg')) {
						$message .= $this->input->get('reject_c');
					}
					if (module_exists('message')) {
						$this->message_db->add_message($r['username'],'SYSTEM',L('reject_message'),$message);
					}
				} elseif($status==99 && $r['sysadd']) {
					$this->content_check_db->delete(array('checkid'=>'c-'.$id.'-'.$this->modelid));
					$del = true;
				}
				if(!$del) $this->content_check_db->update(array('status'=>$status),array('checkid'=>'c-'.$id.'-'.$this->modelid));
			}
		} else {
			$this->update(array('status'=>$status),array('id'=>$ids));
			$del = false;
			$r = $this->get_one(array('id'=>$ids));
			if($status==0) {
				//退稿发送短消息、邮件
				$message = L('reject_message_tips').$r['title']."<br><a href='index.php?m=member&c=content&a=edit&catid={$r['catid']}&id={$r['id']}'><font color=red>".L('click_edit')."</font></a><br>";
				if($this->input->post('reject_c') && $this->input->post('reject_c') != L('reject_msg')) {
					$message .= $this->input->post('reject_c');
				} elseif($this->input->get('reject_c') && $this->input->get('reject_c') != L('reject_msg')) {
					$message .= $this->input->get('reject_c');
				}
				if (module_exists('message')) {
					$this->message_db->add_message($r['username'],'SYSTEM',L('reject_message'),$message);
				}
			} elseif($status==99 && $r['sysadd']) {
				$this->content_check_db->delete(array('checkid'=>'c-'.$ids.'-'.$this->modelid));
				$del = true;
			}
			if(!$del) $this->content_check_db->update(array('status'=>$status),array('checkid'=>'c-'.$ids.'-'.$this->modelid));
		}
		return true;
	}
	/**
	 * 删除内容
	 * @param $id 内容id
	 * @param $catid 栏目id
	 */
	public function delete_content($id,$catid = 0) {
		$r = $this->get_one(array('id'=>$id));
		//删除主表数据
		$this->delete(array('id'=>$id));
		//删除从表数据
		$this->table_name = $this->table_name.'_data_'.$r['tableid'];
		$this->delete(array('id'=>$id));
		//重置默认表
		$this->table_name = $this->db_tablepre.$this->model_tablename;
		//更新栏目统计
		$this->update_category_items($catid,'delete',1);
		//更新相关
		$html = pc_base::load_app_class('html', 'content');
		$html->create_relation_html($catid, array(array($id, $catid)));
	}
	
	
	public function search_api($id = 0, $data = array(), $action = 'update') {
		$type_arr = getcache('search_model_'.$this->siteid,'search');
		$typeid = $type_arr[$this->modelid]['typeid'];
		if($action == 'update') {
			$fulltext_array = getcache('model_field_'.$this->modelid,'model');
			foreach($fulltext_array AS $key=>$value){
				if($value['isfulltext']) {
					$fulltextcontent .= $data['system'][$key] ? $data['system'][$key] : $data['model'][$key];
				}
			}
			$this->search_db->update_search($typeid ,$id, $fulltextcontent,$data['system']['title'].' '.$data['system']['keywords'],$data['system']['inputtime']);
		} elseif($action == 'delete') {
			$this->search_db->delete_search($typeid ,$id);
		}
	}
	/**
	 * 获取单篇信息
	 * 
	 * @param $catid
	 * @param $id
	 */
	public function get_content($catid,$id) {
		$catid = intval($catid);
		$id = intval($id);
		if(!$catid || !$id) return false;
		$siteids = getcache('category_content','commons');
		$siteid = $siteids[$catid];
		$this->category = get_category($siteid);
		if(isset($this->category[$catid]) && $this->category[$catid]['type'] == 0) {
			$modelid = $this->category[$catid]['modelid'];
			$this->set_model($modelid);
			$r = $this->get_one(array('id'=>$id));
			if(!$r) {
				return false;
			}
			//附属表
			$this->table_name = $this->table_name.'_data_'.$r['tableid'];
			$r2 = $this->get_one(array('id'=>$id));
			$this->set_model($modelid);
			if($r2) {
				return array_merge($r,$r2);
			} else {
				return $r;
			}
		}
		return true;
	}
	/**
	 * 设置catid 所在的模型数据库
	 * 
	 * @param $catid
	 */
	public function set_catid($catid) {
		$catid = intval($catid);
		if(!$catid) return false;
		if(empty($this->category) || empty($this->category[$catid])) {
			$siteids = getcache('category_content','commons');
			$siteid = $siteids[$catid];
			$this->category = get_category($siteid);
		}
		if(isset($this->category[$catid]) && $this->category[$catid]['type'] == 0) {
			$modelid = $this->category[$catid]['modelid'];
			$this->set_model($modelid);
		}
	}
	
	// 验证附件上传权限，直接返回1 表示空间不够
	public function check_upload($uid) {
		if ($this->isadmin) {
			return;
		}
		// 获取用户总空间
		$total = abs((int)$this->grouplist[$this->groupid]['filesize']) * 1024 * 1024;
		if ($total) {
			// 判断空间是否满了
			$filesize = $this->get_member_filesize($uid);
			if ($filesize >= $total) {
				return 1;
			}
		}
		return;
	}
	
	// 用户已经使用附件空间
	public function get_member_filesize($uid) {
		$db = pc_base::load_model('attachment_model');
		$db->query('SELECT sum(filesize) as filesize FROM `'.$db->dbprefix('attachment').'` where userid='.intval($uid).' and isadmin='.intval($this->isadmin));
		$row = $db->fetch_array();
		return intval($row[0]['filesize']);
	}
	
	private function update_category_items($catid,$action = 'add',$cache = 0) {
		$this->sitemodel_db = pc_base::load_model('sitemodel_model');
		$this->category_db = pc_base::load_model('category_model');
		if($action=='add') {
			$this->category_db->update(array('items'=>'+=1'),array('catid'=>$catid));
			$this->sitemodel_db->update(array('items'=>'+=1'),array('modelid'=>$this->modelid));
		} else {
			$this->category_db->update(array('items'=>'-=1'),array('catid'=>$catid));
			$this->sitemodel_db->update(array('items'=>'-=1'),array('modelid'=>$this->modelid));
		}
		if($cache) $this->cache_items();
	}
	
	public function cache_items() {
		$datas = $this->category_db->select(array('modelid'=>$this->modelid),'catid,type,items',10000);
		$array = array();
		foreach ($datas as $r) {
			if($r['type']==0) $array[$r['catid']] = $r['items'];
		}
		setcache('category_items_'.$this->modelid, $array,'commons');
		$this->set_model($this->modelid);
		$total = $this->count();
		pc_base::load_model('sitemodel_model')->update(array('items'=>$total),array('modelid'=>$this->modelid));
	}
}
?>