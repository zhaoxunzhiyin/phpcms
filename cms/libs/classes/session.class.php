<?php
class session {
	
	private $session;

	public function __construct() {
		$this->session = pc_base::load_sys_class('session_'.SESSION_STORAGE);
		$cookieName = 'cms_'.md5(SYS_KEY);
		$sessionId = param::get_cookie($cookieName);
		if ($sessionId) {
			$this->session->setId($sessionId);
		}
		param::set_cookie($cookieName, $this->session->getId());
	}

	public function set($key, $value = null) {
		$this->session->put(SYS_KEY.$key, $value);
		$this->session->save();
	}

	public function setTempdata($key, $value, $time) {
		$this->set($key, (SYS_TIME+$time).'{cms}'.$value);
	}

	public function getTempdata($key = null) {
		$value = $this->get($key);
		if ($value) {
			list($time, $value) = explode('{cms}', $value);
			if (SYS_TIME > $time) {
				$this->remove($key);
				return NULL;
			}
		}
		return $value;
	}

	public function get($key = null) {
		return $this->session->get(SYS_KEY.$key);
	}

	public function remove($key) {
		$this->session->forget(SYS_KEY.$key);
		$this->session->save();
	}
}
?>