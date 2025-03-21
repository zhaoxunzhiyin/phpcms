<?php
class session_file {

	protected $lifetime = 1800;
	protected $id;
	protected $save_path;
	protected $sessionName = 'cms_session';
	protected $attributes = [];
	protected $serialization = 'php';
	protected $sessionTimeToUpdate = 300;

	public function __construct() {
		$this->save_path = rtrim(SESSION_SAVEPATH, '/\\');
		$this->sessionName = 'cms_'.md5(SYS_KEY);
		$this->lifetime = SESSION_TTL;
		$this->setId();
		if ((empty($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest')
			&& ($regenerateTime = $this->sessionTimeToUpdate) > 0
		) {
			if (!$this->get('cms_last_regenerate')) {
				$this->put('cms_last_regenerate', SYS_TIME);
			} elseif ($this->get('cms_last_regenerate') < (SYS_TIME - $regenerateTime)) {
				$this->put('cms_last_regenerate', SYS_TIME);
			}
		}
		$this->put('cms_previous_url', FC_NOW_URL);
	}

	public function __destruct() {
		$this->save();
		if ( ! is_dir($this->save_path) || ($directory = opendir($this->save_path)) === false) {
			log_message('debug', "会话：垃圾收集器无法列出目录下的文件 '".$this->save_path."'。");
			return false;
		}
		$collected = 0;
		while (($file = readdir($directory)) !== false) {
			if ( ! preg_match('/cms_/i', $file)
				|| ! is_file($this->save_path.DIRECTORY_SEPARATOR.$file)
				|| ($mtime = filemtime($this->save_path.DIRECTORY_SEPARATOR.$file)) === false
				|| $mtime > SYS_TIME - $this->lifetime) {
				continue;
			}

			unlink($this->save_path.DIRECTORY_SEPARATOR.$file);
			$collected++;
		}
		closedir($directory);
	}

	public function get($key, $default = null) {
		if (is_file($path = $this->save_path.'/'.$this->sessionName . $this->getId()) &&
			filemtime($path) >= SYS_TIME - $this->lifetime) {
			if ($this->serialization === 'json') {
				$data = json_decode($this->sharedGet($path), true);
			} else {
				$data = @unserialize($this->sharedGet($path));
			}
		} else {
			$this->delete($path = $this->save_path.'/'.$this->sessionName . $this->getId());
		}
		if (!empty($data)) {
			$this->attributes = array_merge($this->attributes, $data);
		}
		return $this->arrget($this->attributes, $key, $default);
	}

	public function arrget($array, $key, $default = null) {
		if (! $this->accessible($array)) {
			return $this->value($default);
		}

		if (is_null($key)) {
			return $array;
		}

		if ($this->exists($array, $key)) {
			return $array[$key];
		}

		if (strpos($key, '.') === false) {
			return $array[$key] ?? $this->value($default);
		}

		foreach (explode('.', $key) as $segment) {
			if ($this->accessible($array) && $this->exists($array, $segment)) {
				$array = $array[$segment];
			} else {
				return $this->value($default);
			}
		}

		return $array;
	}

	public function set($id, $data) {
		file_put_contents($this->save_path.'/'.$id, $data, LOCK_EX);
	}

	public function put($key, $value = null) {
		if (! is_array($key)) {
			$key = [$key => $value];
		}

		foreach ($key as $arrayKey => $arrayValue) {
			$this->arrset($this->attributes, $arrayKey, $arrayValue);
		}
	}

	public function arrset(&$array, $key, $value) {
		if (is_null($key)) {
			return $array = $value;
		}

		$keys = explode('.', $key);

		foreach ($keys as $i => $key) {
			if (count($keys) === 1) {
				break;
			}

			unset($keys[$i]);

			if (! isset($array[$key]) || ! is_array($array[$key])) {
				$array[$key] = [];
			}

			$array = &$array[$key];
		}

		$array[array_shift($keys)] = $value;

		return $array;
	}

	public function forget($keys) {
		$this->arrforget($this->attributes, $keys);
	}

	public function arrforget(&$array, $keys) {
		$original = &$array;

		$keys = (array) $keys;

		if (count($keys) === 0) {
			return;
		}

		foreach ($keys as $key) {
			if ($array && array_key_exists($key, $array)) {
				unset($array[$key]);

				continue;
			}

			$parts = explode('.', $key);

			$array = &$original;

			while (count($parts) > 1) {
				$part = array_shift($parts);

				if (isset($array[$part]) && is_array($array[$part]) || $array[$part]) {
					$array = &$array[$part];
				} else {
					continue 2;
				}
			}

			unset($array[array_shift($parts)]);
		}
	}

	public function accessible($value) {
		return is_array($value) || $value instanceof ArrayAccess;
	}

	public function exists($array, $key) {
		if ($array instanceof Enumerable) {
			return $array->has($key);
		}

		if ($array instanceof ArrayAccess) {
			return $array->offsetExists($key);
		}

		if (is_float($key)) {
			$key = (string) $key;
		}

		return array_key_exists($key, $array);
	}

	public function value($value, ...$args) {
		return $value instanceof Closure ? $value(...$args) : $value;
	}

	public function save() {
		if ($this->attributes) {
			$this->set($this->sessionName . $this->getId(), ($this->serialization === 'json' ? json_encode($this->attributes) : serialize($this->attributes)));
		} else {
			$this->delete($path = $this->save_path.'/'.$this->sessionName . $this->getId());
		}
	}

	public function sharedGet($path) {
		$contents = '';

		$handle = fopen($path, 'rb');

		if ($handle) {
			try {
				if (flock($handle, LOCK_SH)) {
					clearstatcache(true, $path);

					$contents = fread($handle, filesize($path) ?: 1);

					flock($handle, LOCK_UN);
				}
			} finally {
				fclose($handle);
			}
		}

		return $contents;
	}

	public function delete($paths) {
		$paths = is_array($paths) ? $paths : func_get_args();

		$success = true;

		foreach ($paths as $path) {
			try {
				if (@unlink($path)) {
					clearstatcache(false, $path);
				} else {
					$success = false;
				}
			} catch (ErrorException $e) {
				$success = false;
			}
		}

		return $success;
	}

	public function setId(string $id = ''): void {
		$this->id = is_string($id) && strlen($id) === 32 && ctype_alnum($id) ? $id : md5(SYS_TIME . build('alpha', 26));
	}

	public function getId(): string {
		return $this->id;
	}
}
?>