<?php
/**
 * 安全过滤
 */
class security {

	public $filename_bad_chars = array(
		'../', '<!--', '-->', '<', '>',
		"'", '"', '&', '$', '#',
		'{', '}', '[', ']', '=',
		';', '?', '%20', '%22',
		'%3c',		// <
		'%253c',	// <
		'%3e',		// >
		'%0e',		// >
		'%28',		// (
		'%29',		// )
		'%2528',	// (
		'%26',		// &
		'%24',		// $
		'%3f',		// ?
		'%3b',		// ;
		'%3d'		// =
    );

    protected $naughty_tags  = array();

    protected $evil_attributes = array();

	public $charset = 'UTF-8';

	protected $_xss_hash;

	protected $_never_allowed_str = array(
		'document.cookie' => '[xss_clean]',
		'(document).cookie' => '[xss_clean]',
		'document.write'  => '[xss_clean]',
		'(document).write'  => '[xss_clean]',
		'.parentNode'     => '[xss_clean]',
		'.innerHTML'      => '[xss_clean]',
		'-moz-binding'    => '[xss_clean]',
		'<!--'            => '&lt;!--',
		'-->'             => '--&gt;',
		'<![CDATA['       => '&lt;![CDATA[',
		'<comment>'	  => '&lt;comment&gt;',
		'<%'              => '&lt;&#37;'
    );

	// 替换前的处理
	protected $_never_call_str = array(
        '&quot;javascript:'    => '&quot;javascript_cms:',
    );

	protected $_never_allowed_regex = array(
		'javascript\s*:',
		'(\(?document\)?|\(?window\)?(\.document)?)\.(location|on\w*)',
		'expression\s*(\(|&\#40;)', // CSS and IE
		'vbscript\s*:', // IE, surprise!
		'wscript\s*:', // IE
		'jscript\s*:', // IE
		'vbs\s*:', // IE
		'Redirect\s+30\d',
		"([\"'])+data\s*:[^\\1]*?base64[^\\1]*?,[^\\1]*?\\1?"
    );

	public function xss_clean($str, $is_image = FALSE) {

		if (is_numeric($str)) {
			return $str;
		} elseif (!$str) {
	        return '';
        }

		if (is_array($str)) {
			foreach ($str as $key => &$value) {
				$str[$key] = $this->xss_clean($value, $is_image);
			}

			return $str;
		}

        if (json_encode( $str) === false) {
            return '[xss_clean]'; // 判断含有乱码直接过滤为空
        }

        $this->naughty_tags = array(
            'alert', 'area', 'prompt', 'confirm', 'applet', 'basefont', 'base', 'behavior', 'bgsound',
            'blink', 'body',  'expression', 'form', 'frameset', 'frame', 'head', 'html', 'ilayer',
            'input', 'button', 'select', 'isindex', 'layer', 'link', 'meta', 'keygen', 'object',
            'plaintext', 'script', 'textarea', 'title', 'math',  'svg', 'xml', 'xss',
            //'iframe', 'video', 'audio', 'embed', 'style'  //排除过滤

        );
        $this->evil_attributes = array(
            'on\w+', 'xmlns', 'formaction', 'form', 'xlink:href', 'FSCommand', 'seekSegmentTime'
            //  ,'style' 排除过滤

        );

        if ($is_image) {
            // 严格的过滤
            $this->naughty_tags = dr_array2array($this->naughty_tags, array('iframe', 'video', 'audio', 'embed', 'style'));
            $this->evil_attributes = dr_array2array($this->evil_attributes, array('style'));

            if (stripos($str, '%') !== false) {
                do {
                    $oldstr = $str;
                    $str = rawurldecode($str);
                    $str = preg_replace_callback('#%(?:\s*[0-9a-f]){2,}#i', array($this, '_urldecodespaces'), $str);
                }
                while ($oldstr !== $str);
                unset($oldstr);
            }

            // 不进行二次编码的xss过滤
            $str = preg_replace_callback("/[^a-z0-9>]+[a-z0-9]+=([\'\"]).*?\\1/si", array($this, '_convert_attribute'), $str);
            $str = preg_replace_callback('/<\w+.*/si', array($this, '_decode_entity'), $str);
        }


		$str = $this->_remove_invisible_characters($str);

		$str = str_replace("\t", ' ', $str);

		$converted_string = $str;

		if ($is_image) {
			$str = preg_replace('/<\?(php)/i', '&lt;?\\1', $str);
		} else {
			$str = str_replace(array('<?', '?'.'>'), array('&lt;?', '?&gt;'), $str);
		}

		$words = array(
            'javascript', 'expression', 'vbscript', 'jscript', 'wscript',
            'vbs', 'script', 'base64', 'applet', 'alert', 'document',
            'write', 'cookie', 'window', 'confirm', 'prompt', 'eval'
        );

		foreach ($words as $word) {
			$word = implode('\s*', str_split($word)).'\s*';

			$str = preg_replace_callback('#('.substr($word, 0, -3).')(\W)#is', array($this, '_compact_exploded_words'), $str);
		}

		do {
			$original = $str;

			if ($str && preg_match('/<a/i', $str)) {
				$str = preg_replace_callback('#<a(?:rea)?[^a-z0-9>]+([^>]*?)(?:>|$)#si', array($this, '_js_link_removal'), $str);
			}

            /* 会影响编辑器base64格式图片本地化
			if ($str && preg_match('/<img/i', $str)) {
				$str = preg_replace_callback('#<img[^a-z0-9]+([^>]*?)(?:\s?/?>|$)#si', array($this, '_js_img_removal'), $str);
			}*/

			if ($str && preg_match('/script|xss/i', $str)) {
				$str = preg_replace('#</*(?:script|xss).*?>#si', '[xss_clean]', $str);
			}
		}
		while ($original !== $str);
		unset($original);

		$pattern = '#'
			.'<((?<slash>/*\s*)((?<tagName>[a-z0-9]+)(?=[^a-z0-9]|$)|.+)'
			.'[^\s\042\047a-z0-9>/=]*'
			.'(?<attributes>(?:[\s\042\047/=]*'
			.'[^\s\042\047>/=]+'
				.'(?:\s*='
					.'(?:[^\s\042\047=><`]+|\s*\042[^\042]*\042|\s*\047[^\047]*\047|\s*(?U:[^\s\042\047=><`]*))'
				.')?'
			.')*)'
			.'[^>]*)(?<closeTag>\>)?#isS';

		do {
			$old_str = $str;
			$str = preg_replace_callback($pattern, array($this, '_sanitize_naughty_html'), $str);
		}
		while ($old_str !== $str);
		unset($old_str);

		$str = preg_replace(
			'#(alert|prompt|confirm|cmd|passthru|eval|exec|expression|system|fopen|fsockopen|file|file_get_contents|readfile|unlink)(\s*)\((.*?)\)#si',
			'\\1\\2&#40;\\3&#41;',
			$str
		);

		$str = preg_replace(
			'#(alert|prompt|confirm|cmd|passthru|eval|exec|expression|system|fopen|fsockopen|file|file_get_contents|readfile|unlink)(\s*)`(.*?)`#si',
			'\\1\\2&#96;\\3&#96;',
			$str
		);

		$str = $this->_do_never_allowed($str);

        $ra = array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
        foreach ($ra as $t) {
            $str = str_replace(' '.$t.'="', ' '.$t.'=', $str);
        }

		return $str;
	}

	protected function _remove_invisible_characters(string $str, bool $urlEncoded = true): string {
        $nonDisplayables = [];

        if ($urlEncoded) {
            $nonDisplayables[] = '/%0[0-8bcef]/';
            $nonDisplayables[] = '/%1[0-9a-f]/';
        }

        $nonDisplayables[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S';

        do {
            $str = preg_replace($nonDisplayables, '', $str, -1, $count);
        } while ($count);

        return $str;
    }

	public function xss_hash() {
		if ($this->_xss_hash === NULL) {
			$rand = $this->get_random_bytes(16);
			$this->_xss_hash = ($rand === FALSE)
				? md5(uniqid(mt_rand(), TRUE))
				: bin2hex($rand);
		}

		return $this->_xss_hash;
	}

	public function get_random_bytes($length) {
		if (empty($length) OR ! ctype_digit((string) $length)) {
			return FALSE;
		}

		if (function_exists('random_bytes')) {
			try {
				return random_bytes((int) $length);
			} catch (Exception $e) {
				log_message('error', $e->getMessage());
				return FALSE;
			}
		}

		if (defined('MCRYPT_DEV_URANDOM') && ($output = mcrypt_create_iv($length, MCRYPT_DEV_URANDOM)) !== FALSE) {
			return $output;
		}


		if (is_readable('/dev/urandom') && ($fp = fopen('/dev/urandom', 'rb')) !== FALSE) {
			is_php('5.4') && stream_set_chunk_size($fp, $length);
			$output = fread($fp, $length);
			fclose($fp);
			if ($output !== FALSE) {
				return $output;
			}
		}

		if (function_exists('openssl_random_pseudo_bytes')) {
			return openssl_random_pseudo_bytes($length);
		}

		return FALSE;
	}

	public function entity_decode($str, $charset = NULL) {
		if (strpos($str, '&') === FALSE) {
			return $str;
		}

		static $_entities;

		isset($charset) OR $charset = $this->charset;
		$flag = is_php('5.4')
			? ENT_COMPAT | ENT_HTML5
			: ENT_COMPAT;

		if ( ! isset($_entities)) {
			$_entities = array_map('strtolower', get_html_translation_table(HTML_ENTITIES, $flag, $charset));

			if ($flag === ENT_COMPAT) {
				$_entities[':'] = '&colon;';
				$_entities['('] = '&lpar;';
				$_entities[')'] = '&rpar;';
				$_entities["\n"] = '&NewLine;';
				$_entities["\t"] = '&Tab;';
			}
		}

		do {
			$str_compare = $str;

			if (preg_match_all('/&[a-z]{2,}(?![a-z;])/i', $str, $matches)) {
				$replace = array();
				$matches = array_unique(array_map('strtolower', $matches[0]));
				foreach ($matches as &$match) {
					if (($char = array_search($match.';', $_entities, TRUE)) !== FALSE) {
						$replace[$match] = $char;
					}
				}

				$str = str_replace(array_keys($replace), array_values($replace), $str);
			}

			$str = html_entity_decode(
				preg_replace('/(&#(?:x0*[0-9a-f]{2,5}(?![0-9a-f;])|(?:0*\d{2,4}(?![0-9;]))))/iS', '$1;', $str),
				$flag,
				$charset
			);

			if ($flag === ENT_COMPAT) {
				$str = str_replace(array_values($_entities), array_keys($_entities), $str);
			}
		}
		while ($str_compare !== $str);
		return $str;
	}

	public function sanitize_filename($str, $relative_path = FALSE) {
		$bad = $this->filename_bad_chars;

		if ( ! $relative_path) {
			$bad[] = './';
			$bad[] = '/';
		}

		$str = $this->_remove_invisible_characters($str, FALSE);

		do {
			$old = $str;
			$str = str_replace($bad, '', $str);
		}
		while ($old !== $str);

		return stripslashes($str);
	}

	public function strip_image_tags($str) {
		return preg_replace(
			array(
				'#<img[\s/]+.*?src\s*=\s*(["\'])([^\\1]+?)\\1.*?\>#i',
				'#<img[\s/]+.*?src\s*=\s*?(([^\s"\'=<>`]+)).*?\>#i'
			),
			'\\2',
			$str
		);
	}

	protected function _urldecodespaces($matches) {
		$input    = $matches[0];
		$nospaces = preg_replace('#\s+#', '[xss_clean_space]', $input);
		return ($nospaces === $input)
			? $input
			: rawurldecode($nospaces);
	}

	protected function _compact_exploded_words($matches) {
		return preg_replace('/\s+/s', '', $matches[1]).$matches[2];
	}

	protected function _sanitize_naughty_html($matches) {

		if (empty($matches['closeTag'])) {
			return '&lt;'.$matches[1];
		} elseif (in_array(strtolower($matches['tagName']), $this->naughty_tags, TRUE)) {
			return '&lt;'.$matches[1].'&gt;';
		} elseif (isset($matches['attributes'])) {
			$attributes = array();

			$attributes_pattern = '#'
				.'(?<name>[^\s\042\047>/=]+)'
				.'(?:\s*=(?<value>[^\s\042\047=><`]+|\s*\042[^\042]*\042|\s*\047[^\047]*\047|\s*(?U:[^\s\042\047=><`]*)))'
				.'#i';

			$is_evil_pattern = '#^('.implode('|', $this->evil_attributes).')$#i';

			do {
				$matches['attributes'] = preg_replace('#^[^a-z]+#i', '', $matches['attributes']);

				if ( ! preg_match($attributes_pattern, $matches['attributes'], $attribute, PREG_OFFSET_CAPTURE)) {
					break;
				}

				if (preg_match($is_evil_pattern, $attribute['name'][0])
					OR (trim($attribute['value'][0]) === '')) {
                    if (CI_DEBUG) {
                        $attributes[] = 'xss_clean_'.$attribute[0][0];
                    } else {
                        $attributes[] = 'xss=clean';
                    }
				} else {
					$attributes[] = $attribute[0][0];
				}

				$matches['attributes'] = substr($matches['attributes'], $attribute[0][1] + strlen($attribute[0][0]));
			}
			while ($matches['attributes'] !== '');

			$attributes = empty($attributes)
				? ''
				: ' '.implode(' ', $attributes);
			return '<'.$matches['slash'].$matches['tagName'].$attributes.'>';
		}

		return $matches[0];
	}

	protected function _js_link_removal($match) {
		return str_replace(
			$match[1],
			preg_replace(
				'#href=.*?(?:(?:alert|prompt|confirm)(?:\(|&\#40;)|javascript:|livescript:|mocha:|charset=|window\.|document\.|\.cookie|<script|<xss|d\s*a\s*t\s*a\s*:)#si',
				'',
				$this->_filter_attributes($match[1])
			),
			$match[0]
		);
	}

	protected function _js_img_removal($match) {
		return str_replace(
			$match[1],
			preg_replace(
				'#src=.*?(?:(?:alert|prompt|confirm|eval)(?:\(|&\#40;|`|&\#96;)|javascript:|livescript:|mocha:|charset=|window\.|\(?document\)?\.|\.cookie|<script|<xss|base64\s*,)#si',
				'',
				$this->_filter_attributes($match[1])
			),
			$match[0]
		);
	}

	protected function _convert_attribute($match) {
		return str_replace(array('>', '<', '\\'), array('&gt;', '&lt;', '\\\\'), $match[0]);
	}

	protected function _filter_attributes($str) {
		$out = '';
		if (preg_match_all('#\s*[a-z\-]+\s*=\s*(\042|\047)([^\\1]*?)\\1#is', $str, $matches))
		{
			foreach ($matches[0] as $match)
			{
				$out .= preg_replace('#/\*.*?\*/#s', '', $match);
			}
		}

		return $out;
	}

	protected function _decode_entity($match) {
		$match = preg_replace('|\&([a-z\_0-9\-]+)\=([a-z\_0-9\-/]+)|i', $this->xss_hash().'\\1=\\2', $match[0]);

		return str_replace(
			$this->xss_hash(),
			'&',
			$this->entity_decode($match, $this->charset)
		);
	}

	protected function _do_never_allowed($str) {

        $str = str_replace(array_keys($this->_never_call_str), $this->_never_call_str, $str);
		$str = str_replace(array_keys($this->_never_allowed_str), $this->_never_allowed_str, $str);

        $old = preg_replace_callback('#<pre(.+)</pre>#Us', function ($match) {
            return '';
        }, $str);

		foreach ($this->_never_allowed_regex as $regex) {
            if (preg_match('#'.$regex.'#is', $old, $mt)) {
                $str = preg_replace('#'.$regex.'#is', '_\\0', $str);
            }
		}

		$str = str_replace($this->_never_call_str, array_keys($this->_never_call_str), $str);

		return $str;
	}
}