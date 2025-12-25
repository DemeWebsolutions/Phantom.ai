<?php
/**
 * Dashboard Data API
 * Provides JSON endpoints for dashboard data
 *
 * @package PhantomAI\Dashboard
 */

require_once __DIR__ . '/includes/security.php';

// Enforce localhost access
enforce_localhost();

// Initialize session
init_security_session();

// Check authentication
if ( ! is_authenticated() ) {
	http_response_code( 401 );
	header( 'Content-Type: application/json' );
	echo json_encode([
		'success' => false,
		'error' => 'Authentication required',
	]);
	exit;
}

// Set headers for JSON response
header( 'Content-Type: application/json' );
header( 'Access-Control-Allow-Origin: *' );

/**
 * Get system status data
 *
 * @return array System status information
 */
function get_system_status(): array {
	$php_version = phpversion();
	
	// Check if PHPCS is available
	$phpcs_status = 'unknown';
	exec( 'which phpcs 2>/dev/null', $output, $return_code );
	if ( $return_code === 0 ) {
		$phpcs_status = 'operational';
	}
	
	// Check if Semgrep is available
	$semgrep_status = 'unknown';
	exec( 'which semgrep 2>/dev/null', $output, $return_code );
	if ( $return_code === 0 ) {
		$semgrep_status = 'operational';
	}
	
	// Get last run timestamp from artifacts
	$last_run = '--';
	$report_file = __DIR__ . '/../artifacts/phantom-report.json';
	if ( file_exists( $report_file ) ) {
		$report_data = json_decode( file_get_contents( $report_file ), true );
		$last_run = $report_data['timestamp'] ?? date( 'Y-m-d H:i:s', filemtime( $report_file ) );
	}
	
	return [
		'phpVersion'    => $php_version,
		'phpcsStatus'   => $phpcs_status,
		'semgrepStatus' => $semgrep_status,
		'lastRun'       => $last_run,
	];
}

/**
 * Get review summary from artifacts
 *
 * @return array Review summary counts
 */
function get_review_summary(): array {
	$report_file = __DIR__ . '/../artifacts/phantom-report.json';
	
	if ( ! file_exists( $report_file ) ) {
		return [
			'errors'   => 0,
			'warnings' => 0,
			'notices'  => 0,
		];
	}
	
	$report_data = json_decode( file_get_contents( $report_file ), true );
	
	return [
		'errors'   => $report_data['summary']['errors'] ?? 0,
		'warnings' => $report_data['summary']['warnings'] ?? 0,
		'notices'  => $report_data['summary']['notices'] ?? 0,
	];
}

/**
 * Get artifacts status
 *
 * @return array Artifacts availability
 */
function get_artifacts_status(): array {
	$artifacts_dir = __DIR__ . '/../artifacts/';
	
	return [
		'json'  => file_exists( $artifacts_dir . 'phantom-report.json' ) ? 'Available' : 'Not Generated',
		'sarif' => file_exists( $artifacts_dir . 'phantom-report.sarif' ) ? 'Available' : 'Not Generated',
		'raw'   => file_exists( $artifacts_dir . 'phpcs.json' ) ? 'Available' : 'Not Generated',
	];
}

/**
 * Get tier usage statistics from metadata files
 *
 * @return array Tier usage counts
 */
function get_tier_usage(): array {
	$metadata_dir = __DIR__ . '/../phantom-metadata/';
	
	$usage = [
		'cheap' => 0,
		'mid'   => 0,
		'high'  => 0,
	];
	
	if ( ! is_dir( $metadata_dir ) ) {
		return $usage;
	}
	
	$files = glob( $metadata_dir . '*.json' );
	foreach ( $files as $file ) {
		$data = json_decode( file_get_contents( $file ), true );
		$tier = $data['tier_used'] ?? '';
		if ( isset( $usage[ $tier ] ) ) {
			$usage[ $tier ]++;
		}
	}
	
	return $usage;
}

/**
 * Get Copilot escalation statistics
 *
 * @return array Escalation stats
 */
function get_escalation_stats(): array {
	$metadata_dir = __DIR__ . '/../phantom-metadata/';
	
	$total = 0;
	$successful = 0;
	
	if ( is_dir( $metadata_dir ) ) {
		$files = glob( $metadata_dir . '*.json' );
		foreach ( $files as $file ) {
			$data = json_decode( file_get_contents( $file ), true );
			if ( ( $data['tier_used'] ?? '' ) === 'high' ) {
				$total++;
				if ( ( $data['review_result'] ?? '' ) === 'PASS' ) {
					$successful++;
				}
			}
		}
	}
	
	$success_rate = $total > 0 ? ( $successful / $total ) * 100 : 0;
	
	return [
		'total'       => $total,
		'successRate' => round( $success_rate, 1 ),
	];
}

/**
 * Get workflow pipeline statistics
 *
 * @return array Workflow stats
 */
function get_workflow_stats(): array {
	$metadata_dir = __DIR__ . '/../phantom-metadata/';
	
	$stats = [
		'intake'        => 0,
		'cheap'         => 0,
		'comprehension' => 0,
		'mid'           => 0,
		'high'          => 0,
		'artifacts'     => 0,
		'learning'      => 0,
	];
	
	if ( ! is_dir( $metadata_dir ) ) {
		return $stats;
	}
	
	$files = glob( $metadata_dir . '*.json' );
	$stats['intake'] = count( $files );
	
	foreach ( $files as $file ) {
		$data = json_decode( file_get_contents( $file ), true );
		
		// Count by tier
		$tier = $data['tier_used'] ?? '';
		if ( $tier === 'cheap' ) {
			$stats['cheap']++;
		} elseif ( $tier === 'mid' ) {
			$stats['mid']++;
		} elseif ( $tier === 'high' ) {
			$stats['high']++;
		}
		
		// Count comprehension checks
		if ( isset( $data['comprehension'] ) && $data['comprehension'] === 'YES' ) {
			$stats['comprehension']++;
		}
		
		// Count artifacts and learning updates
		if ( isset( $data['review_result'] ) ) {
			$stats['artifacts']++;
			$stats['learning']++;
		}
	}
	
	return $stats;
}

/**
 * Main API router
 */
function handle_api_request(): void {
	$endpoint = $_GET['endpoint'] ?? 'all';
	
	try {
		switch ( $endpoint ) {
			case 'system-status':
				$data = get_system_status();
				break;
				
			case 'review-summary':
				$data = get_review_summary();
				break;
				
			case 'artifacts':
				$data = get_artifacts_status();
				break;
				
			case 'tier-usage':
				$data = get_tier_usage();
				break;
				
			case 'escalations':
				$data = get_escalation_stats();
				break;
				
			case 'workflow':
				$data = get_workflow_stats();
				break;
				
			case 'all':
			default:
				$data = [
					'systemStatus'  => get_system_status(),
					'reviewSummary' => get_review_summary(),
					'artifacts'     => get_artifacts_status(),
					'tierUsage'     => get_tier_usage(),
					'escalations'   => get_escalation_stats(),
					'workflowStats' => get_workflow_stats(),
				];
				break;
		}
		
		echo json_encode(
			[
				'success' => true,
				'data'    => $data,
			],
			JSON_PRETTY_PRINT
		);
		
	} catch ( Exception $e ) {
		http_response_code( 500 );
		echo json_encode(
			[
				'success' => false,
				'error'   => $e->getMessage(),
			],
			JSON_PRETTY_PRINT
		);
	}
}

// Handle the API request
handle_api_request();
