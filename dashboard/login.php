<?php
/**
 * Login Page
 * Authentication gate for Phantom.ai Dashboard
 *
 * @package PhantomAI\Dashboard
 */

require_once __DIR__ . '/includes/security.php';

// Enforce localhost access
enforce_localhost();

// Initialize session
init_security_session();

// Handle login form submission
$error_message = '';
$rate_limited = false;

if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
	$username = $_POST['username'] ?? '';
	$password = $_POST['password'] ?? '';
	
	if ( is_rate_limited() ) {
		$error_message = 'Too many failed attempts. Please try again in 15 minutes.';
		$rate_limited = true;
	} elseif ( authenticate_user( $username, $password ) ) {
		header( 'Location: dashboard.php' );
		exit;
	} else {
		$error_message = 'Invalid username or password.';
	}
}

// Check if already authenticated
if ( is_authenticated() ) {
	header( 'Location: dashboard.php' );
	exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Phantom.ai Dashboard</title>
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <style>
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--bg-dark);
            padding: 2rem;
        }
        
        .login-box {
            background-color: var(--bg-panel);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 3rem;
            max-width: 400px;
            width: 100%;
        }
        
        .login-logo {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .login-logo svg {
            width: 80px;
            height: 80px;
        }
        
        .login-title {
            text-align: center;
            font-size: 1.5rem;
            margin-bottom: 2rem;
            color: var(--text-primary);
        }
        
        .security-warning {
            background-color: rgba(243, 156, 18, 0.1);
            border: 1px solid var(--warning-color);
            border-radius: 4px;
            padding: 1rem;
            margin-bottom: 2rem;
            font-size: 0.875rem;
            color: var(--warning-color);
            text-align: center;
        }
        
        .security-warning::before {
            content: "⚠️ ";
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-secondary);
            font-size: 0.875rem;
        }
        
        .form-input {
            width: 100%;
            padding: 0.75rem;
            background-color: var(--bg-dark);
            border: 1px solid var(--border-color);
            border-radius: 4px;
            color: var(--text-primary);
            font-size: 1rem;
        }
        
        .form-input:focus {
            outline: none;
            border-color: var(--accent-color);
        }
        
        .btn-login {
            width: 100%;
            padding: 0.75rem;
            background-color: var(--accent-color);
            color: var(--text-primary);
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .btn-login:hover {
            background-color: #357abd;
        }
        
        .btn-login:disabled {
            background-color: var(--border-color);
            cursor: not-allowed;
        }
        
        .error-message {
            background-color: rgba(231, 76, 60, 0.1);
            border: 1px solid var(--error-color);
            border-radius: 4px;
            padding: 0.75rem;
            margin-bottom: 1.5rem;
            color: var(--error-color);
            font-size: 0.875rem;
        }
        
        .login-footer {
            text-align: center;
            margin-top: 2rem;
            font-size: 0.75rem;
            color: var(--text-secondary);
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <div class="login-logo">
                <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="100" cy="100" r="80" fill="#4a90e2" opacity="0.2"/>
                    <path d="M 100 40 L 120 80 L 160 80 L 130 105 L 145 145 L 100 120 L 55 145 L 70 105 L 40 80 L 80 80 Z" fill="#4a90e2"/>
                </svg>
            </div>
            
            <h1 class="login-title">Phantom.ai Dashboard</h1>
            
            <div class="security-warning">
                Internal system. Authorized access only. All activity is logged.
            </div>
            
            <?php if ( $error_message ): ?>
                <div class="error-message">
                    <?php echo htmlspecialchars( $error_message ); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="username" class="form-label">Username</label>
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                        class="form-input" 
                        required 
                        autocomplete="username"
                        <?php echo $rate_limited ? 'disabled' : ''; ?>
                    >
                </div>
                
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="form-input" 
                        required 
                        autocomplete="current-password"
                        <?php echo $rate_limited ? 'disabled' : ''; ?>
                    >
                </div>
                
                <button type="submit" class="btn-login" <?php echo $rate_limited ? 'disabled' : ''; ?>>
                    Login
                </button>
            </form>
            
            <div class="login-footer">
                <p>&copy; 2025 Deme Web Solutions / My Deme, LLC</p>
                <p>Proprietary Software - All Rights Reserved</p>
            </div>
        </div>
    </div>
</body>
</html>
