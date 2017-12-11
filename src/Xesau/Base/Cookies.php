<?php

namespace Xesau\Base;

class Cookies {
    
    private static $prefix = '';
    private static $path = '';
    private static $domain = '';
    
    public static function init($prefix, $path = '', $domain = '') {
        self::$prefix = $prefix;
        self::$path = $path;
        self::$domain = $domain;
    }
    
    /**
     * Create or overwrite a cookie
     *
     * @param string $name The name of the cookie
     * @param mixed $value The value of the cookie
     * @param int $expires UNIX-timestamp of when the cookie will expire, or 0 if it is a session cookie
     * @param bool $asJson Whether the cookie should be saved in JSON
     */
    public static function set($name, $value, $expires = 0, $asJson = false) {
        if ($asJson)
            $value = json_encode($value);
        setcookie($n = self::$prefix . $name, $value, $expires, self::$path, self::$domain);
        $_COOKIE[$n] = $value;
    }

    /**
     * Checks whether a cookie exists
     *
     * @param string $name The name of the cookie
     * @return bool Whether the cookie exists
     */
    public static function has($name) {
        return isset($_COOKIE[self::$prefix . $name]);
    }

    /**
     * Gets the value of a cookie.
     *
     * @param string $name The name of the cookie
     * @param mixed $default The default value, for when the cookie has not been set
     * @param bool $asJson Whether the cookie value is encoded in json
     * @return mixed|null The value, or null if the cookie doesn't exist.
     */
    public static function get($name, $default = null, $asJson = false) {
        $cookieName = self::$prefix . $name;

        if (isset($_COOKIE[$cookieName]))
            return $asJson ? json_decode($_COOKIE[$cookieName]) : $_COOKIE[$cookieName];

        return $default;
    }

    /**
     * Removes a cookie
     *
     * @param string $name The name of the cookie
     */
    public static function remove($name) {
        setcookie(self::$prefix . $name, null, -1, self::$path, self::$domain);
    }
    
}