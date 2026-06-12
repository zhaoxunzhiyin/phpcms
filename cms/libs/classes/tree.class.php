<?php
/**
* 通用的树型类，可以生成任何树型结构
*/
class tree {

    protected $data;
    protected $result_array;
    protected $icon;
    protected $nbsp = "{spacer}";
    protected $nbsp_str;
    protected $deep = 1;
    protected $ret;
    protected $result;
    protected $ismain = 0;

    // 初始化函数
    public function __construct() {
        $this->icon();
    }

    // 释放变量
    public function __destruct()
    {
        unset($this->data);
        unset($this->ret);
        unset($this->icon);
        unset($this->result_array);
        unset($this->nbsp_str);
        unset($this->nbsp);
        unset($this->result);
    }

    /**
     * 设置普通标签
     */
    public function icon() {
        $this->nbsp_str = '&nbsp;';
        $this->icon = [
            $this->nbsp_str,
            '├&nbsp;',
            '└&nbsp;'
        ];
        return $this;
    }

    /**
     * 初始化类
     */
    public function init($arr) {
        $this->ret = '';
        $this->data = $arr;
        $this->result = [];
        return $this;
    }

    // 创建数据
    public function get($data) {
        $this->data = $data;
        $this->result = [];
        $this->create(0);
        return $this->result_array;
    }

    /**
     * 得到子级数组
     * @param int
     * @return array
     */
    protected function get_child($k_id) {

        $arrays = [];

        if (is_array($this->data)) {
            foreach($this->data as $id => $a) {
                if ($a['parentid'] == $k_id) {
                    $arrays[$id] = $a;
                }
            }
        }

        $this->deep++;

        return $arrays;
    }

    /**
     * 得到树型数组
     */
    public function create($k_id = 0, $adds = '') {

        if ($this->deep > 5000) {
            return; // 防止死循环
        }

        $child = $this->get_child($k_id); // 获取子数据
        $number = 1;

        if (is_array($child)) {
            $total = dr_count($child);
            foreach($child as $id => $a) {
                $k = $adds ? $this->nbsp : '';
                $j = $number == $total ? $this->icon[2] : $this->icon[1];
                $a['spacer'] = $this->_get_spacer($adds ? $adds.$j : '');
                $this->result_array[] = $a;
                $this->create($a['id'], $adds.$k.$this->nbsp);
                $number++;
            }
        }

        $this->deep = 1;
    }

    // 替换空格填充符号
    protected function _get_spacer($str) {
        $num = substr_count($str, $this->nbsp) * 2;
        if ($num) {
            $str = str_replace($this->nbsp, '', $str);
            for ($i = 0; $i < $num; $i ++) {
                $str = $this->nbsp_str.$str;
            }

        }
        return $str;
    }

    /**
	* 得到父级数组
	* @param int
	* @return array
	*/
	public function get_parent($myid){
		$newarr = array();
		if(!isset($this->data[$myid])) return false;
		$pid = $this->data[$myid]['parentid'];
		$pid = $this->data[$pid]['parentid'];
		if(is_array($this->data)){
			foreach($this->data as $id => $a){
				if($a['parentid'] == $pid) $newarr[$id] = $a;
			}
		}
		return $newarr;
	}

    /**
	* 得到当前位置数组
	* @param int
	* @return array
	*/
	public function get_pos($myid,&$newarr){
		$a = array();
		if(!isset($this->data[$myid])) return false;
        $newarr[] = $this->data[$myid];
		$pid = $this->data[$myid]['parentid'];
		if(isset($this->data[$pid])){
		    $this->get_pos($pid,$newarr);
		}
		if(is_array($newarr)){
			krsort($newarr);
			foreach($newarr as $v){
				$a[$v['id']] = $v;
			}
		}
		return $a;
	}

    /**
     * 得到树型结构
     *
     * @param int ID，表示获得这个ID下的所有子级
     * @param string 生成树型结构的基本代码，例如："<option value=\$id \$selected>\$spacer\$name</option>"
     * @param int 被选中的ID，比如在做树型下拉框的时候需要用到
     * @return string
     */
    public function get_tree($myid, $str, $sid = 0, $adds = '', $str_group = '') {

        if ($this->deep > 5000) {
            return $this->ret; // 防止死循环
        }

        $parentid = 0;
        $nstr = '';
        $number = 1;
        $mychild = $this->get_child($myid);
        $mytotal = dr_count($mychild);

        if (is_array($mychild)) {
            foreach ($mychild as $id => $value) {
                $j = $k = '';
                if ($number == $mytotal) {
                    $j.= $this->icon[2];
                } else {
                    $j.= $this->icon[1];
                    $k = $adds ? $this->nbsp : '';
                }

                $spacer = $this->_get_spacer($adds ? $adds.$j : '');
                $selected = $id == $sid ? 'selected' : '';

                extract($value);

                $parentid == 0 && $str_group ? eval("\$nstr = \"$str_group\";") : eval("\$nstr = \"$str\";");
                $this->ret.= $nstr;
                $this->get_tree($id, $str, $sid, $adds.$k.$this->nbsp, $str_group);
                $number++;
            }
        }

        return $this->ret;
    }

    /**
     * 同上一方法类似,但允许多选
     */
    public function get_tree_multi($myid, $str, $sid = 0, $adds = '') {

        if ($this->deep > 5000) {
            return $this->ret; // 防止死循环
        }

        $nstr = '';
        $number = 1;
        $mychild = $this->get_child($myid);

        if (is_array($mychild)) {
            $mytotal = dr_count($mychild);
            foreach ($mychild as $id => $a) {

                $j = $k = '';
                if ($number == $mytotal) {
                    $j.= $this->icon[2];
                } else {
                    $j.= $this->icon[1];
                    $k = $adds ? $this->icon[0] : '';
                }

                $spacer = $this->_get_spacer($adds ? $adds.$j : '');
                $selected = $this->have($sid, $id) ? 'selected' : '';

                extract($a);

                eval("\$nstr = \"$str\";");
                $this->ret.= $nstr;
                $this->get_tree_multi($id, $str, $sid, $adds.$k.$this->nbsp);
                $number++;

            }
        }

        return $this->ret;
    }

    /**
     * 用于栏目选择框
     *
     * @param integer	$myid	要查询的ID
     * @param string	$str	HTML代码方式
     * @param integer	$sid	默认选中
     * @param integer	$adds	前缀
     */
    public function get_tree_category($myid, $str, $str2 = '', $sid = 0, $adds = '') {

        if ($this->deep > 5000) {
            return $this->ret; // 防止死循环
        }

        $number = 1;
        $mychild = $this->get_child($myid);

        if (is_array($mychild)) {

            $mytotal = dr_count($mychild);
            foreach ($mychild as $id => $a) {

                $j = $k = '';
                if ($number == $mytotal) {
                    $j.= $this->icon[2];
                } else {
                    $j.= $this->icon[1];
                    $k = $adds ? $this->icon[0] : '';
                }

                $spacer = $this->_get_spacer($adds ? $adds.$j : '');
                $selected = $this->have($sid, $id) ? 'selected' : '';
                $html_disabled = '';
                extract($a);

                //$now = $this->get_child($id);
                // 如果没有子栏目且当前禁用就不再显示
                //if (!$now && $html_disabled) continue;

                if ($html_disabled) {
                    $selected = ' disabled';
                }

                eval("\$this->ret.= \"$str\";");

                $number++;

                // 如果有下级菜单就递归
                if ($a['child']) {
                    $this->get_tree_category($id, $str, null, $sid, $adds.$k.$this->nbsp);
                }
            }
        }

        return $this->ret;
    }
	
	/**
	 * 同上一类方法，jquery treeview 风格，可伸缩样式（需要treeview插件支持）
	 * @param $myid 表示获得这个ID下的所有子级
	 * @param $effected_id 需要生成treeview目录数的id
	 * @param $str 末级样式
	 * @param $str2 目录级别样式
	 * @param $showlevel 直接显示层级数，其余为异步显示，0为全部限制
	 * @param $style 目录样式 默认 filetree 可增加其他样式如'filetree treeview-famfamfam'
	 * @param $currentlevel 计算当前层级，递归使用 适用改函数时不需要用该参数
	 * @param $recursion 递归使用 外部调用时为FALSE
	 */
    function get_treeview($myid,$effected_id='example',$str="<span class='file'>\$name</span>", $str2="<span class='folder'>\$name</span>" ,$showlevel = 0 ,$style='filetree ' , $currentlevel = 1,$recursion=FALSE) {
        $child = $this->get_child($myid);
        if(!defined('EFFECTED_INIT')){
           $effected = ' id="'.$effected_id.'"';
           define('EFFECTED_INIT', 1);
        } else {
           $effected = '';
        }
		$placeholder = '<ul><li><span class="placeholder"></span></li></ul>';
        if(!$recursion) $this->ret .='<ul'.$effected.'  class="'.$style.'">';
        foreach($child as $id=>$a) {

        	@extract($a);
			if($showlevel > 0 && $showlevel == $currentlevel && $this->get_child($id)) $folder = 'hasChildren'; //如设置显示层级模式@2011.07.01
        	$floder_status = isset($folder) ? ' class="'.$folder.'"' : '';		
            $this->ret .= $recursion ? '<ul><li'.$floder_status.' id=\''.$id.'\'>' : '<li'.$floder_status.' id=\''.$id.'\'>';
            $recursion = FALSE;
            if($this->get_child($id)){
            	eval("\$nstr = \"$str2\";");
            	$this->ret .= $nstr;
                if($showlevel == 0 || ($showlevel > 0 && $showlevel > $currentlevel)) {
					$this->get_treeview($id, $effected_id, $str, $str2, $showlevel, $style, $currentlevel+1, TRUE);
				} elseif($showlevel > 0 && $showlevel == $currentlevel) {
					$this->ret .= $placeholder;
				}
            } else {
                eval("\$nstr = \"$str\";");
                $this->ret .= $nstr;
            }
            $this->ret .=$recursion ? '</li></ul>': '</li>';
        }
        if(!$recursion)  $this->ret .='</ul>';
        return $this->ret;
    }
	
	/**
	 * 获取子栏目json
	 * Enter description here ...
	 * @param unknown_type $myid
	 */
	public function creat_sub_json($myid, $str='') {
		$sub_cats = $this->get_child($myid);
		$n = 0;
		if(is_array($sub_cats)) foreach($sub_cats as $c) {			
			$data[$n]['id'] = iconv(CHARSET,'utf-8',$c['catid']);
			if($this->get_child($c['catid'])) {
				$data[$n]['liclass'] = 'hasChildren';
				$data[$n]['children'] = array(array('text'=>'&nbsp;','classes'=>'placeholder'));
				$data[$n]['classes'] = 'folder';
				$data[$n]['text'] = iconv(CHARSET,'utf-8',$c['catname']);
			} else {				
				if($str) {
					@extract(array_iconv($c,CHARSET,'utf-8'));
					eval("\$data[$n]['text'] = \"$str\";");
				} else {
					$data[$n]['text'] = iconv(CHARSET,'utf-8',$c['catname']);
				}
			}
			$n++;
		}
		return json_encode($data);		
	}

	// 替换逗号
	private function have($list,$item){
		return(strpos(',,'.$list.',',','.$item.','));
	}
}
?>