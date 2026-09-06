<?php
/**
 * 通用列表视图业务字段提供器
 *
 * scene 格式：module:scope
 * 例如：member:list、wms_order:list、wms_order:export
 *
 * 约定：
 *   module/classes/{module}_list_view.class.php
 *   class {module}_list_view
 *   public static function candidates($scope = 'list')
 */

defined('IN_CMS') or exit('No permission resources.');

class list_view_provider {

    /**
     * 根据 scene 加载业务字段定义。
     *
     * @param string $scene
     * @return array|false
     */
    public static function candidates($scene) {
        $parsed = self::parse_scene($scene);
        if (!$parsed) {
            return false;
        }
        $module = $parsed['module'];
        $scope = $parsed['scope'];
        $class = $module . '_list_view';
		
		//module_exists内置函数global.func.php
        if (!module_exists($module)) {
            return false;
        }
		
		
        pc_base::load_app_class($class, $module, 0);
        if (!class_exists($class) || !method_exists($class, 'candidates')) {
            return false;
        }
		
        $candidates = call_user_func(array($class, 'candidates'), $scope);
        return is_array($candidates) ? $candidates : false;
    }

    /**
     * scene 拆分。
     */
    public static function parse_scene($scene) {
        $scene = trim((string)$scene);
        if (!preg_match('/^([a-z][a-z0-9_]*):([a-z][a-z0-9_]*)$/i', $scene, $matches)) {
            return false;
        }

        return array(
            'module' => strtolower($matches[1]),
            'scope' => strtolower($matches[2]),
        );
    }
}
?>
