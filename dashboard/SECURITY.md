# Phantom.ai Dashboard Security Documentation

## Overview

The Phantom.ai Dashboard implements multiple layers of security hardening to protect against unauthorized access while maintaining zero external dependencies.

## Security Features

### 1. Encrypted Login (Local Only)

**Implementation:**
- Session-based authentication system
- Username + password required for access
- Passwords stored using PHP's `password_hash()` (bcrypt algorithm)
- Session cookies configured with `httponly` and `samesite` protection
- Session timeout: 1 hour (configurable)

**Configuration:**
- Credentials stored in `config/auth.php`
- Default username: `phantom_admin`
- Default password: `phantom_secure_2025` (CHANGE IMMEDIATELY)

**Generating New Password Hash:**
```bash
php -r "echo password_hash('your_secure_password', PASSWORD_BCRYPT) . PHP_EOL;"
```

Copy the output and update `config/auth.php`:
```php
'password_hash' => '$2y$10$...', // Your generated hash
```

### 2. Localhost / IP Lock

**Implementation:**
- All dashboard pages enforce localhost-only access
- Checks `REMOTE_ADDR` for `127.0.0.1` or `::1`
- Returns HTTP 403 for non-localhost requests
- Logs unauthorized access attempts

**Files Protected:**
- `login.php` - Login page
- `dashboard.php` - Main dashboard
- `api.php` - Data API endpoint

**Blocked Access Response:**
```
403 Forbidden

Access denied. Dashboard is only accessible from localhost.
```

### 3. Rate Limiting

**Implementation:**
- Tracks failed login attempts per IP address
- File-based storage in `logs/` directory
- Temporary lockout after threshold exceeded

**Configuration (in `config/auth.php`):**
- Maximum attempts: 5
- Time window: 10 minutes
- Lockout duration: 15 minutes

**How It Works:**
1. Failed login attempt recorded
2. After 5 failed attempts in 10 minutes, account is locked
3. Lockout lasts 15 minutes
4. Successful login clears the rate limit

### 4. Audit Logging

**Implementation:**
- All security events logged to `logs/audit.log`
- JSON format for easy parsing
- Includes timestamp, event type, IP address

**Logged Events:**
- `login_success` - Successful authentication
- `login_failed` - Failed login attempt
- `rate_limit_lockout` - Rate limit triggered
- `unauthorized_ip_access` - Non-localhost access attempt
- `logout` - User logout

**Log Format:**
```json
{"timestamp":"2025-12-25 05:00:00","event":"login_success","data":{"username":"phantom_admin","ip":"127.0.0.1"}}
{"timestamp":"2025-12-25 05:05:00","event":"login_failed","data":{"username":"admin","ip":"127.0.0.1"}}
{"timestamp":"2025-12-25 05:10:00","event":"logout","data":{"username":"phantom_admin","ip":"127.0.0.1"}}
```

**Viewing Logs:**
```bash
# View all logs
cat dashboard/logs/audit.log

# View recent login attempts
grep "login" dashboard/logs/audit.log

# View failed attempts
grep "login_failed" dashboard/logs/audit.log

# View unauthorized access
grep "unauthorized_ip_access" dashboard/logs/audit.log
```

### 5. Security Disclaimer Banner

**Implementation:**
- Persistent warning banner displayed on all authenticated pages
- Clearly visible at top of dashboard
- Reminder that activity is monitored

**Banner Text:**
```
⚠️ Internal system. Authorized access only. All activity is logged.
```

## File Structure

```
dashboard/
├── login.php              # Login gate
├── dashboard.php          # Main dashboard (authenticated)
├── api.php               # Data API (authenticated)
├── index.php             # Redirects to login
├── config/
│   └── auth.php          # Authentication configuration
├── includes/
│   └── security.php      # Security functions
└── logs/
    ├── audit.log         # Security audit log
    └── rate_limit_*.json # Rate limiting data
```

## What This DOES Protect Against

✅ Unauthorized localhost access
✅ Brute force login attacks (via rate limiting)
✅ Session hijacking (httponly cookies)
✅ Accidental exposure on wrong network
✅ Unauthorized API access

## What This DOES NOT Protect Against

❌ Physical access to the machine
❌ Compromised localhost environment
❌ Advanced attacks if dashboard exposed to internet
❌ Social engineering attacks
❌ Malicious code injection from other localhost services

## Developer Responsibilities

### CRITICAL: Change Default Credentials

**IMMEDIATELY** after setup:

1. Generate new password hash:
```bash
php -r "echo password_hash('YourStrongPassword123!', PASSWORD_BCRYPT) . PHP_EOL;"
```

2. Update `config/auth.php`:
```php
'username' => 'your_username',
'password_hash' => '$2y$10$...',  // Your generated hash
```

3. Optionally change username

### Localhost-Only Warning

**⚠️ NEVER expose this dashboard to the public internet**

This security implementation is designed for:
- Local development environments
- Internal developer workstations
- Localhost-only access

If you need to expose the dashboard remotely:
- Use SSH tunneling
- Use VPN access
- Add additional enterprise authentication (SSO, 2FA)
- Implement HTTPS with proper certificates
- Add CSRF protection
- Add Content Security Policy headers

### Regular Maintenance

1. **Review Audit Logs:**
```bash
# Weekly review
tail -n 100 dashboard/logs/audit.log

# Check for suspicious activity
grep "login_failed" dashboard/logs/audit.log | tail -20
```

2. **Rotate Passwords:**
- Change passwords every 90 days
- Use strong passwords (12+ characters, mixed case, numbers, symbols)

3. **Monitor Rate Limit Files:**
```bash
# Check for rate limit lockouts
ls -lah dashboard/logs/rate_limit_*.json
```

4. **Clean Old Logs:**
```bash
# Archive old logs monthly
mv dashboard/logs/audit.log dashboard/logs/audit_$(date +%Y%m).log
touch dashboard/logs/audit.log
```

### Backup Configuration

**Important files to backup:**
- `config/auth.php` - Contains credentials
- `logs/audit.log` - Security audit trail

**NEVER commit to version control:**
- `config/auth.php` with real credentials
- `logs/` directory contents
- Session files

Ensure `.gitignore` includes:
```
/dashboard/logs/
/dashboard/config/auth.php
```

## Security Best Practices

### Strong Passwords

Minimum requirements:
- At least 12 characters
- Mix of uppercase and lowercase
- Numbers and special characters
- Not based on dictionary words
- Unique to this system

### Session Security

The dashboard configures secure session handling:
```php
session_set_cookie_params([
    'lifetime' => 3600,      // 1 hour timeout
    'path' => '/',
    'secure' => false,       // localhost doesn't use HTTPS
    'httponly' => true,      // Prevent JavaScript access
    'samesite' => 'Strict'   // CSRF protection
]);
```

### Rate Limiting

Current settings are conservative:
- 5 attempts per 10 minutes
- 15 minute lockout

Adjust in `config/auth.php` if needed:
```php
'rate_limit_attempts' => 5,     // Max attempts
'rate_limit_window' => 600,     // Time window (seconds)
'rate_limit_lockout' => 900,    // Lockout duration (seconds)
```

## Troubleshooting

### Locked Out

If you're locked out due to rate limiting:

1. **Wait 15 minutes** for automatic unlock, OR
2. **Manually reset:**
```bash
rm dashboard/logs/rate_limit_*.json
```

### Forgot Password

1. Generate new password hash:
```bash
php -r "echo password_hash('NewPassword123!', PASSWORD_BCRYPT) . PHP_EOL;"
```

2. Update `config/auth.php` with new hash

### Session Issues

Clear session data:
```bash
# Linux/Mac
rm /tmp/sess_*

# Or restart PHP server
```

### Unauthorized IP Access

If getting 403 on localhost:

1. Check `$_SERVER['REMOTE_ADDR']`
2. Verify using `127.0.0.1` or `::1`
3. Check if running through proxy

## Security Audit Checklist

✅ Default password changed
✅ Username changed from default
✅ `.gitignore` includes sensitive files
✅ Audit logs reviewed regularly
✅ Rate limiting tested
✅ Session timeout appropriate
✅ Only accessible from localhost
✅ No external dependencies added

## Support

For security concerns or questions:
- Review this documentation
- Check audit logs for suspicious activity
- Contact: https://demewebsolutions.com/phantom-ai

## License

Proprietary software - all rights reserved.
© 2025 Deme Web Solutions / My Deme, LLC
