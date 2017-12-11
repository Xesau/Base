<?php

namespace Xesau\Base;

abstract class Controller {

    /**
     * Returns JSON output with correct header
     */
    public static function json($data, $options = 0, $charset = 'utf-8') {
        $serialized = json_encode($data, $options);
        if ($serialized === false)
            return false;
        else {
            header('Content-type: application/json; charset='. $charset);
            echo $serialized;
            return true;
        }
    }

    /**
     * Handle the current request by its 
     */
    public static function handleAcceptType(array $callbacks) {
        if (!isset($callbacks['*/*']))
            throw new InvalidArgumentException('handleByAccept callbacks parameter requires a */* catch-all handler');
        $callback = $callbacks['*/*'];
        
        $explicitAcceptType = self::getParam('accept_type');
        if ($explicitAcceptType !== null) {
            if (isset($callbacks[$explicitAcceptType])) {
                $callback = $callbacks[$explicitAcceptType];
            } else {
                $callback = $callbacks['*/*'];
            }
            $callback();
            return;
        }
        
        // Assume the browser sends the types in a sensible order, don't bother with q=
        $types = explode(',', isset($_SERVER['HTTP_ACCEPT']) ? $_SERVER['HTTP_ACCEPT'] : 'text/html,*/*');
        foreach($types as $type) {
            $index = strpos($type, ';');
            if ($index !== false)
                $type = substr($type, 0, $index);
            $type = trim($type);
            if (!isset($callbacks[$type]))
                continue;
            $callback = $callbacks[$type];
            break;
        }
        
        $callback();
    }

    /**
     * Gets a request POST/GET parameter
     *
     * @param string $name The name of the parameter.
     * @param mixed $default The default value for when the parameter is not set.
     * @param array $allowed Allowed values
     * @param callable[] $filters Filters, applied to the value, before checking if the value is allwoed.
     * @return mixed|mixed[] Returns the value of the parameter, or an array of values.
     */
    public static function getParam($name, $default = null, array $allowed = null, array $filters = []) {
        if (isset($_POST[$name])) {
            $value = $_POST[$name];
            foreach($filters as $filter)
                $value = self::callFilter($filter, $value);
            if ($allowed === null)
                return $value;
            if (in_array($value, $allowed))
                return $value;
            return $default;
        }
		if (isset($_GET[$name])) {
            $value = $_GET[$name];
            foreach($filters as $filter)
                $value = self::callFilter($filter, $value);
            if ($allowed === null)
                return $value;
            if (in_array($value, $allowed))
                return $value;
            return $default;
		}
        return $default;
    }

    private static function callFilter($filter, $value) {
        if (is_string($filter)) {
            if (strpos($filter, '::') !== false) {
                $filter = explode('::', $filter, 2);
            }
        }
        return $filter($value);
    }

    public static function hasParam($name) {
        return isset($_POST[$name]) || isset($_GET[$name]);
    }

	/**
	 * Verifies the CSRF token
     *
     * @return bool
	 */
	public static function verifyCsrf($csrfToken) {
		if (!Session::verifyCsrf(self::getSession(), self::getParam($csrfToken))) {
			self::json([
				'status' => 'invalid_csrf',
			]);
			exit();
		}
	}
    
    /**
     * Gets the currently active session
     *
     * @return Session
     */
    public static function getSession() {
        return Session::getCurrentSession();
    }
    
}
