<?php
class session_mysqli {

	protected $lifetime = 1800;
	protected $id;
	protected $db;
	protected $match_ip = false;
	protected $_row_exists = false;
	protected $_lock = false;
	protected $_fingerprint;
	protected $_session_id;
	protected $attributes = [];
	protected $serialization = 'php';
	protected $sessionTimeToUpdate = 300;
	protected $idPrefix;

	public function __construct() {
		$this->db = pc_base::load_model('session_model');
		$this->lifetime = SESSION_TTL;
		$this->idPrefix = 'cms_session:';
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
		$expiretime = SYS_TIME - $this->lifetime;
		$this->db->delete("`timestamp`<$expiretime");
	}

	public function get($key, $default = null) {
		if ($this->_get_lock($this->getId()) === false) {
			return false;
		}

		$this->_session_id = $this->getId();

		$where = array('id'=>$this->idPrefix . $this->getId());
		if ($this->match_ip) {
			$where2 = array('ip_address'=>ip());
		}
		$where = dr_array22array($where, $where2);

		$result = $this->db->get_one($where, 'data');

		if ($result === null) {
			$this->_row_exists = false;
			$this->_fingerprint = md5('');
			return '';
		}

		$result = is_bool($result) ? '' : $result['data'];

		$this->_fingerprint = md5($result);
		$this->_row_exists = true;
		if ($this->serialization === 'json') {
			$data = json_decode($result, true);
		} else {
			$data = @unserialize($result);
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
		if (isset($this->_session_id) && $id !== $this->_session_id) {
			if ( ! $this->_release_lock() || ! $this->_get_lock($id)) {
				return false;
			}

			$this->_row_exists = false;
			$this->_session_id = $id;
		} elseif ($this->_lock === false) {
			return false;
		}

		if ($this->_row_exists === false) {
			$insert_data = array(
				'id' => $this->idPrefix . $id,
				'ip_address' => ip(),
				'timestamp' => SYS_TIME,
				'data' => $data
			);

			if ($this->db->insert($insert_data)) {
				$this->_fingerprint = md5($data);
				$this->_row_exists = true;
				return true;
			}

			return false;
		}

		$where = array('id'=>$this->idPrefix . $id);
		if ($this->match_ip) {
			$where2 = array('ip_address'=>ip());
		}
		$where = dr_array22array($where, $where2);

		$update_data = array('timestamp' => SYS_TIME);
		if ($this->_fingerprint !== md5($data)) {
			$update_data['data'] = $data;
		}

		if ($this->db->update($update_data, $where)) {
			$this->_fingerprint = md5($data);
			return true;
		}

		return false;
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
			$this->set($this->getId(), ($this->serialization === 'json' ? json_encode($this->attributes) : serialize($this->attributes)));
		} else {
			$this->delete();
		}
	}

	public function delete() {
		if ($this->_lock) {

			$where = array('id'=>$this->idPrefix . $this->getId());
			if ($this->match_ip) {
				$where2 = array('ip_address'=>ip());
			}
			$where = dr_array22array($where, $where2);

			if (!$this->db->delete($where)) {
				return false;
			}
		}

		return ($this->_lock && ! $this->_release_lock()) ? false : true;
	}

	protected function _get_lock(string $session_id): bool {

		$arg = md5($session_id.($this->match_ip ? '_'.ip() : ''));
		if ($this->db->query("SELECT GET_LOCK('".$arg."', 300) AS cms_session_lock")) {
			$this->_lock = $arg;
			return true;
		}

		return false;
	}

	protected function _release_lock(): bool {
		if ( ! $this->_lock) {
			return true;
		}

		if ($this->db->query("SELECT RELEASE_LOCK('".$this->_lock."') AS cms_session_lock")) {
			$this->_lock = false;
			return true;
		}

		return false;
	}

	public function setId(string $id = ''): void {
		$this->id = is_string($id) && strlen($id) === 32 && ctype_alnum($id) ? $id : md5(SYS_TIME . build('alpha', 26));
	}

	public function getId(): string {
		return $this->id;
	}
}
?>