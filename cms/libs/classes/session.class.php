<?php
class session {

	function __construct() {
		pc_base::load_sys_class('session_'.SESSION_STORAGE);
	}

	public function set($data, $value = null) {
		if (is_array($data)) {
			foreach ($data as $key => &$value) {
				if (is_int($key)) {
					$_SESSION[$value] = null;
				} else {
					$_SESSION[$key] = $value;
				}
			}

			return;
		}

		$_SESSION[$data] = $value;
	}

	public function get(?string $key = null) {
		if (! empty($key) && (null !== ($value = $_SESSION[$key] ?? null) || null !== ($value = $this->dot_array_search($key, $_SESSION ?? [])))) {
			return $value;
		}

		if (empty($_SESSION)) {
			return $key === null ? [] : null;
		}

		if (! empty($key)) {
			return null;
		}

		$userdata = [];
		$_exclude = array_merge(['cms_vars'], $this->getFlashKeys(), $this->getTempKeys());

		$keys = array_keys($_SESSION);

		foreach ($keys as $key) {
			if (! in_array($key, $_exclude, true)) {
				$userdata[$key] = $_SESSION[$key];
			}
		}

		return $userdata;
	}

	public function has(string $key): bool {
		return isset($_SESSION[$key]);
	}

	public function push(string $key, array $data) {
		if ($this->has($key) && is_array($value = $this->get($key))) {
			$this->set($key, array_merge($value, $data));
		}
	}

	public function remove($key) {
		if (is_array($key)) {
			foreach ($key as $k) {
				unset($_SESSION[$k]);
			}

			return;
		}

		unset($_SESSION[$key]);
	}

	public function dot_array_search(string $index, array $array) {

		$segments = explode('.', rtrim(rtrim($index, '* '), '.'));

		return $this->_array_search_dot($segments, $array);
	}

	public function _array_search_dot(array $indexes, array $array) {
		$currentIndex = $indexes
			? array_shift($indexes)
			: null;

		if ((empty($currentIndex) && (int) $currentIndex !== 0) || (! isset($array[$currentIndex]) && $currentIndex !== '*'))
		{
			return null;
		}

		if ($currentIndex === '*')
		{
			foreach ($array as $value)
			{
				$answer = $this->_array_search_dot($indexes, $value);

				if ($answer !== null)
				{
					return $answer;
				}
			}

			return null;
		}

		if (empty($indexes))
		{
			return $array[$currentIndex];
		}

		if (is_array($array[$currentIndex]) && $array[$currentIndex])
		{
			return $this->_array_search_dot($indexes, $array[$currentIndex]);
		}

		return $array[$currentIndex];
	}

	public function markAsTempdata($key, int $ttl = 300): bool {
		$ttl += SYS_TIME;

		if (is_array($key)) {
			$temp = [];

			foreach ($key as $k => $v) {
				if (is_int($k)) {
					$k = $v;
					$v = $ttl;
				} elseif (is_string($v)) {
					$v = SYS_TIME + $ttl;
				} else {
					$v += SYS_TIME;
				}

				if (! array_key_exists($k, $_SESSION)) {
					return false;
				}

				$temp[$k] = $v;
			}

			$_SESSION['cms_vars'] = isset($_SESSION['cms_vars']) ? array_merge($_SESSION['cms_vars'], $temp) : $temp;

			return true;
		}

		if (! isset($_SESSION[$key])) {
			return false;
		}

		$_SESSION['cms_vars'][$key] = $ttl;

		return true;
	}

	public function getFlashKeys(): array {
		if (! isset($_SESSION['cms_vars'])) {
			return [];
		}

		$keys = [];

		foreach (array_keys($_SESSION['cms_vars']) as $key) {
			if (! is_int($_SESSION['cms_vars'][$key])) {
				$keys[] = $key;
			}
		}

		return $keys;
	}

	public function getTempKeys(): array {
		if (! isset($_SESSION['cms_vars'])) {
			return [];
		}

		$keys = [];

		foreach (array_keys($_SESSION['cms_vars']) as $key) {
			if (is_int($_SESSION['cms_vars'][$key])) {
				$keys[] = $key;
			}
		}

		return $keys;
	}

	public function setTempdata($data, $value = null, int $ttl = 300) {
		$this->set($data, $value);
		$this->markAsTempdata($data, $ttl);
	}

	public function getTempdata(?string $key = null) {
		if (isset($key)) {
			return (isset($_SESSION['cms_vars'], $_SESSION['cms_vars'][$key], $_SESSION[$key])
					&& is_int($_SESSION['cms_vars'][$key])) ? $_SESSION[$key] : null;
		}

		$tempdata = [];

		if (! empty($_SESSION['cms_vars'])) {
			foreach ($_SESSION['cms_vars'] as $key => &$value) {
				if (is_int($value)) {
					$tempdata[$key] = $_SESSION[$key];
				}
			}
		}

		return $tempdata;
	}
}
?>