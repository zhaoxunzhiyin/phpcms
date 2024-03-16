<?php
class session_file {

	protected $lifetime = 1800;
	protected $save_path;
	protected $sessionName = 'cms_session';
	protected $match_ip = false;
	protected $sessionIDRegex = '';
	protected $_file_path;
	protected $_file_handle;
	protected $_file_new;
	protected $_fingerprint;
	protected $_session_id;
	protected $sessionTimeToUpdate = 300;

	function __construct() {
		$this->save_path = SESSION_SAVEPATH;
		$this->configureSessionIDRegex();
		$this->sessionName = 'cms_'.md5(SYS_KEY);
		$this->lifetime = SESSION_TTL;
		if (empty($this->sessionName)) {
			$this->sessionName = ini_get('session.name');
		} else {
			ini_set('session.name', $this->sessionName);
		}
		if (isset($this->save_path)) {
			$this->save_path = rtrim($this->save_path, '/\\');
			ini_set('session.save_path', $this->save_path);
		} else {
			$this->save_path = rtrim(ini_get('session.save_path'), '/\\');
		}
		ini_set('session.use_trans_sid', 0);
		ini_set('session.use_strict_mode', 1);
		ini_set('session.use_cookies', 1);
		ini_set('session.use_only_cookies', 1);
		session_set_save_handler(array(&$this,'open'), array(&$this,'close'), array(&$this,'read'), array(&$this,'write'), array(&$this,'destroy'), array(&$this,'gc'));
		register_shutdown_function('session_write_close');
		session_start();
		if ((empty($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest')
			&& ($regenerateTime = $this->sessionTimeToUpdate) > 0
		) {
			if (! isset($_SESSION['cms_last_regenerate'])) {
				$_SESSION['cms_last_regenerate'] = SYS_TIME;
			} elseif ($_SESSION['cms_last_regenerate'] < (SYS_TIME - $regenerateTime)) {
				$_SESSION['cms_last_regenerate'] = SYS_TIME;
			}
		}
		if (isset($_SESSION)) {
			$_SESSION['cms_previous_url'] = FC_NOW_URL;
		}
	}

	public function open($path, $name): bool {
		if ( ! is_dir($path)) {
			if ( ! mkdir($path, 0700, true)) {
				log_message('error', "会话：配置的存储路径 '".$this->savePath."' 不是目录、不存在或无法创建。");
				return false;
			}
		} elseif ( ! is_writable($path)) {
			log_message('error', "会话：配置的保存路径 '".$this->save_path."' 不可由PHP进程写入。");
			return false;
		}

		$this->save_path = $path;
		$this->_file_path = $this->save_path.DIRECTORY_SEPARATOR.$name.($this->match_ip ? md5(ip()) : '');

		return true;
	}


	public function read($id) {
		if ($this->_file_handle === NULL) {
			$this->_file_new = ! is_file($this->_file_path.$id);

			if (($this->_file_handle = fopen($this->_file_path.$id, 'c+b')) === false) {
				log_message('error', "会话：无法打开文件'".$this->_file_path.$id."'。");
				return false;
			}

			if (flock($this->_file_handle, LOCK_EX) === false) {
				log_message('error', "会话：无法获取文件的锁定'".$this->_file_path.$id."'。");
				fclose($this->_file_handle);
				$this->_file_handle = NULL;
				return false;
			}

			if (! isset($this->_session_id)) {
				$this->_session_id = $id;
			}

			if ($this->_file_new) {
				chmod($this->_file_path.$id, 0600);
				$this->_fingerprint = md5('');
				return '';
			}

			clearstatcache(true, $this->_file_path.$id);
		} elseif ($this->_file_handle === false) {
			return false;
		} else {
			rewind($this->_file_handle);
		}

		$data = '';
		$buffer = 0;
		for ($read = 0, $length = filesize($this->_file_path.$id); $read < $length; $read += strlen($buffer)) {
			if (($buffer = fread($this->_file_handle, $length - $read)) === false) {
				break;
			}

			$data .= $buffer;
		}

		$this->_fingerprint = md5($data);
		return $data;
	}

	public function write($id, $data): bool {
		if ($id !== $this->_session_id && ($this->close() === false || $this->read($id) === false)) {
			return false;
		}

		if ( ! is_resource($this->_file_handle)) {
			return false;
		} elseif ($this->_fingerprint === md5($data)) {
			return ( ! $this->_file_new && ! touch($this->_file_path.$id)) ? false : true;
		}

		if ( ! $this->_file_new) {
			ftruncate($this->_file_handle, 0);
			rewind($this->_file_handle);
		}

		if (($length = strlen($data)) > 0) {
			$result = null;

			for ($written = 0; $written < $length; $written += $result) {
				if (($result = fwrite($this->_file_handle, substr($data, $written))) === false) {
					break;
				}
			}

			if ( ! is_int($result)) {
				$this->_fingerprint = md5(substr($data, 0, $written));
				log_message('error', '会话：无法写入数据。');
				return false;
			}
		}

		$this->_fingerprint = md5($data);
		return true;
	}

	public function close(): bool {
		if (is_resource($this->_file_handle)) {
			flock($this->_file_handle, LOCK_UN);
			fclose($this->_file_handle);

			$this->_file_handle = $this->_file_new = $this->_session_id = NULL;
		}
		return $this->gc($this->lifetime);
	}

	public function destroy($id): bool {
		if ($this->close() === true) {
			if (is_file($this->_file_path.$id)) {
				return unlink($this->_file_path.$id) ? true : false;
			}

			return true;
		} elseif ($this->_file_path !== NULL) {
			clearstatcache();
			if (is_file($this->_file_path.$id)) {
				return unlink($this->_file_path.$id) ? true : false;
			}

			return true;
		}

		return false;
	}

	public function gc($maxlifetime) {
		if ( ! is_dir($this->save_path) || ($directory = opendir($this->save_path)) === false) {
			log_message('debug', "会话：垃圾收集器无法列出目录下的文件 '".$this->save_path."'。");
			return false;
		}

		$ts = SYS_TIME - $maxlifetime;

		$pattern = ($this->match_ip === true) ? '[0-9a-f]{32}' : '';

		$pattern = sprintf(
			'#\A%s' . $pattern . $this->sessionIDRegex . '\z#',
			preg_quote($this->sessionName, '#')
		);

		$collected = 0;

		while (($file = readdir($directory)) !== false) {
			if ( ! preg_match($pattern, $file)
				|| ! is_file($this->save_path.DIRECTORY_SEPARATOR.$file)
				|| ($mtime = filemtime($this->save_path.DIRECTORY_SEPARATOR.$file)) === false
				|| $mtime > $ts) {
				continue;
			}

			unlink($this->save_path.DIRECTORY_SEPARATOR.$file);
			$collected++;
		}

		closedir($directory);

		return $collected;
	}
	
	protected function configureSessionIDRegex() {
		$bitsPerCharacter = (int) ini_get('session.sid_bits_per_character');
		$SIDLength = (int) ini_get('session.sid_length');

		if (($bits = $SIDLength * $bitsPerCharacter) < 160) {
			$SIDLength += (int) ceil((160 % $bits) / $bitsPerCharacter);
			ini_set('session.sid_length', (string) $SIDLength);
		}

		switch ($bitsPerCharacter) {
			case 4:
				$this->sessionIDRegex = '[0-9a-f]';
				break;

			case 5:
				$this->sessionIDRegex = '[0-9a-v]';
				break;

			case 6:
				$this->sessionIDRegex = '[0-9a-zA-Z,-]';
				break;
		}

		$this->sessionIDRegex .= '{' . $SIDLength . '}';
	}
}
?>