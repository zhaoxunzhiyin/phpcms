<?php
class session_mysqli {

	protected $lifetime = 1800;
	protected $db;
	protected $sessionName = 'cms_session';
	protected $match_ip = false;
	protected $_row_exists = false;
	protected $_lock = false;
	protected $_fingerprint;
	protected $_session_id;
	protected $sessionTimeToUpdate = 300;
	protected $idPrefix;

	function __construct() {
		$this->db = pc_base::load_model('session_model');
		$this->sessionName = 'cms_'.md5(SYS_KEY);
		$this->lifetime = SESSION_TTL;
		if (empty($this->sessionName)) {
			$this->sessionName = ini_get('session.name');
		} else {
			ini_set('session.name', $this->sessionName);
		}
		$this->idPrefix = 'cms_session:';
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
		return true;
	}

	public function read($id) {
		if ($this->_get_lock($id) === false) {
			return false;
		}

		$this->_session_id = $id;

		$where = array('id'=>$this->idPrefix . $id);
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
		return $result;
	}

	public function write($id, $data): bool {

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

	public function close(): bool {
		$this->gc($this->lifetime);
		return ($this->_lock && ! $this->_release_lock()) ? false : true;
	}

	public function destroy($id): bool {
		if ($this->_lock) {

			$where = array('id'=>$this->idPrefix . $id);
			if ($this->match_ip) {
				$where2 = array('ip_address'=>ip());
			}
			$where = dr_array22array($where, $where2);

			if (!$this->db->delete($where)) {
				return false;
			}
		}

		if ($this->close() === true) {
			return true;
		}

		return false;
	}

	public function gc($maxlifetime) {
		$expiretime = SYS_TIME - $maxlifetime;
		return $this->db->delete("`timestamp`<$expiretime");
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
}
?>