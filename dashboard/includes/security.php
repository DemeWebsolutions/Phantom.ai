<?php
/**
 * Security Functions
 * Authentication, rate limiting, and audit logging
 *
 * @package PhantomAI\Dashboard\Security
 */

/**
 * Initialize security session
 */
function init_security_session(): void {
	$config = require __DIR__ . '/../config/auth.php';
	
	if ( session_status() === PHP_SESSION_NONE ) {
		session_name( $config['session_name'] );
		session_set_cookie_params([
			'lifetime' => $config['session_timeout'],
			'path' => '/',
			'secure' => false, // localhost doesn't need HTTPS
			'httponly' => true,
			'samesite' => 'Strict'
		]);
		session_start();
	}
	
	// Check session timeout
	if ( isset( $_SESSION['last_activity'] ) ) {
		if ( time() - $_SESSION['last_activity'] > $config['session_timeout'] ) {
			session_destroy();
			session_start();
		}
	}
	$_SESSION['last_activity'] = time();
}

/**
 * Check if request is from localhost
 *
 * @return bool True if localhost
 */
function is_localhost(): bool {
	$remote_addr = $_SERVER['REMOTE_ADDR'] ?? '';
	return in_array( $remote_addr, [ '127.0.0.1', '::1' ], true );
}

/**
 * Block non-localhost access
 */
function enforce_localhost(): void {
	if ( ! is_localhost() ) {
		$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
		audit_log( 'unauthorized_ip_access', [
			'ip' => $ip,
			'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
			'request_uri' => $_SERVER['REQUEST_URI'] ?? 'unknown',
		]);
		
		http_response_code( 403 );
		header( 'Content-Type: text/plain' );
		die( "403 Forbidden\n\nAccess denied. Dashboard is only accessible from localhost." );
	}
}

/**
 * Check if user is authenticated
 *
 * @return bool True if authenticated
 */
function is_authenticated(): bool {
	return isset( $_SESSION['authenticated'] ) && $_SESSION['authenticated'] === true;
}

/**
 * Authenticate user
 *
 * @param string $username Username
 * @param string $password Password
 * @return bool True if authenticated
 */
function authenticate_user( string $username, string $password ): bool {
	$config = require __DIR__ . '/../config/auth.php';
	
	// Check rate limiting
	if ( is_rate_limited() ) {
		audit_log( 'rate_limit_lockout', [
			'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
		]);
		return false;
	}
	
	// Verify credentials
	if ( $username === $config['username'] && password_verify( $password, $config['password_hash'] ) ) {
		$_SESSION['authenticated'] = true;
		$_SESSION['username'] = $username;
		$_SESSION['login_time'] = time();
		
		// Clear rate limit on successful login
		clear_rate_limit();
		
		audit_log( 'login_success', [
			'username' => $username,
			'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
		]);
		
		return true;
	}
	
	// Failed login attempt
	record_failed_attempt();
	audit_log( 'login_failed', [
		'username' => $username,
		'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
	]);
	
	return false;
}

/**
 * Logout user
 */
function logout_user(): void {
	audit_log( 'logout', [
		'username' => $_SESSION['username'] ?? 'unknown',
		'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
	]);
	
	session_destroy();
	session_start();
}

/**
 * Check if IP is rate limited
 *
 * @return bool True if rate limited
 */
function is_rate_limited(): bool {
	$config = require __DIR__ . '/../config/auth.php';
	$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
	$rate_file = __DIR__ . '/../logs/rate_limit_' . md5( $ip ) . '.json';
	
	if ( ! file_exists( $rate_file ) ) {
		return false;
	}
	
	$data = json_decode( file_get_contents( $rate_file ), true );
	
	// Check if locked out
	if ( isset( $data['locked_until'] ) && time() < $data['locked_until'] ) {
		return true;
	}
	
	// Check attempts within window
	if ( isset( $data['attempts'] ) && isset( $data['window_start'] ) ) {
		if ( time() - $data['window_start'] < $config['rate_limit_window'] ) {
			if ( $data['attempts'] >= $config['rate_limit_attempts'] ) {
				// Lock out
				$data['locked_until'] = time() + $config['rate_limit_lockout'];
				file_put_contents( $rate_file, json_encode( $data ) );
				return true;
			}
		}
	}
	
	return false;
}

/**
 * Record failed login attempt
 */
function record_failed_attempt(): void {
	$config = require __DIR__ . '/../config/auth.php';
	$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
	$rate_file = __DIR__ . '/../logs/rate_limit_' . md5( $ip ) . '.json';
	
	$data = [];
	if ( file_exists( $rate_file ) ) {
		$data = json_decode( file_get_contents( $rate_file ), true );
	}
	
	// Initialize or reset window
	if ( ! isset( $data['window_start'] ) || time() - $data['window_start'] >= $config['rate_limit_window'] ) {
		$data = [
			'window_start' => time(),
			'attempts' => 1,
		];
	} else {
		$data['attempts'] = ( $data['attempts'] ?? 0 ) + 1;
	}
	
	file_put_contents( $rate_file, json_encode( $data ) );
}

/**
 * Clear rate limit for current IP
 */
function clear_rate_limit(): void {
	$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
	$rate_file = __DIR__ . '/../logs/rate_limit_' . md5( $ip ) . '.json';
	
	if ( file_exists( $rate_file ) ) {
		unlink( $rate_file );
	}
}

/**
 * Audit log event
 *
 * @param string $event Event name
 * @param array  $data Event data
 */
function audit_log( string $event, array $data = [] ): void {
	$log_file = __DIR__ . '/../logs/audit.log';
	
	$entry = [
		'timestamp' => date( 'Y-m-d H:i:s' ),
		'event' => $event,
		'data' => $data,
	];
	
	$log_line = json_encode( $entry ) . "\n";
	file_put_contents( $log_file, $log_line, FILE_APPEND );
}
