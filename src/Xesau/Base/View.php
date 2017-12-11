<?php

namespace Xesau\Base;

abstract class View extends Controller {

    /**
     * @var callable $handler
     */
    private static $handler = false;
    private static $redirectBaseUrl = '';

    public static function setRedirectBaseUrl($url) {
        if ($url !== '' && $url[strlen($url) - 1] != '/')
            $url .= '/';

        self::$redirectBaseUrl = $url;
    }

    public static function setHandler(callable $handler) {
        self::$handler = $handler;
    }

    public static function redirect($url, $statusCode = 200, $useBaseUrl = true) {
        if ($url !== '' && $url[0] == '/') {
            $url = substr($url, 1);
        }

        header('Location: '. ($useBaseUrl ? self::$redirectBaseUrl : '') . $url);
    }
    
    /**
     * Outputs or returns a parsed template
     *
     * @param string $templateName The name of the template
     * @param mixed[string] $variables The variables to pass to the template
     * @param bool $print Whether or not to print.
     * @return void|string
     */
    public static function template($templateName, array $variables = [], $print = true) {
		$handler = self::$handler;

        if ($handler === false) {
            throw new \UnexpectedValueException('Template handler not set');
        }
		if ($print === true) {
			echo $handler($templateName, $variables);
        } else {
            return $handler($templateName, $variables);
        }
    }

}
