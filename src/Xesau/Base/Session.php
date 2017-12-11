<?php

namespace Xesau\Base;

abstract class Session {
	
    // REGARDING SESSION USED IN THE CURRENT REQUEST
    
    /**
     * @var Session|null The session for the current request
     */
	private static $currentSession = null;
	private static $csrfVerifier = null;
    
    /**
     * Sets the session for the current request
     *
     * @param Session $session The session
     * @return void
     */
	public static function setCurrentSession(Session $session) {
		self::$currentSession = $session;
	}
	
    /**
     * Sets the CSRF verifier
     *
     * @param callable $verifier the verifier
     * @return void
     */
	public static function setCsrfVerifier(callable $verifier) {
		self::$csrfVerifier = $verifier;
	}
	
    /**
     * Gets the session set for this request
     * 
     * @return Session|null Null when none has been set
     */
	public static function getCurrentSession() {
		return self::$currentSession;
	}
	
    /**
     * Returns whether a current session has been set for this request
     * 
     * @return bool Whether a session has been set
     */
	public static function hasSession() {
		return self::$currentSession !== null;
	}
    
    // FUNCTIONS TO BE IMPLEMENTED BY Session CLASSES
	
	/**
	 * Gets the ID of the session
     *
     * @return mixed The ID
	 */
	public abstract function getSessionID();
	
    /**
     * Returns whether this session has a user attached
     *
     * @return bool
     */
    public abstract function isLoggedIn();
    
    /**
     * Gets the user attached to this session
     *
     * @return User The user
     */
    public abstract function getUser();
	
    /**
     * Gets the CSRF token of this session
     */
    public abstract function getCsrfToken();
    
	/**
	 * Finds a session
	 *
     * @param string $id The session ID as stored in a cookie/url
     * @param string $ip The IP address from $_SERVER['REMOTE_ADDR']
     * @param string $userAgent The agent header from $_SERVER['HTTP_USER_AGENT']
	 * @return Session|null Null if the session was not found.
	 */
	public abstract static function findSession($id, $ip, $userAgent);
	
	/**
	 * Finds a session
	 *
	 * @return object|null Null if the session was not found.
	 */
	public abstract static function createSession($ip, $userAgent);
    
    public static function verifyCsrf(Session $session = null, $csrf) {
        if (self::$csrfVerifier == null) {
            if ($session == null)
                return true;
            return $session->getCsrfToken() == $csrf;
        }
        
        $verifier = self::$csrfVerifier;
        return $verifier($session, $csrf);
    }
    
}