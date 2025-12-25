<?php
/**
 * Dashboard Data API
 * Provides JSON endpoints for dashboard data
 *
 * @package PhantomAI\Dashboard
 */

// Set headers for JSON response
header( 'Content-Type: application/json' );
header( 'Access-Control-Allow-Origin: *' );

// Autoload Phantom.ai classes
require_once __DIR__ . '/../vendor/autoload.php';

use PhantomAI\Learning\MetadataTracker;

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
 * Get tier usage statistics
 *
 * @return array Tier usage counts
 */
function get_tier_usage(): array {
	$tracker = new MetadataTracker();
	$stats = $tracker->get_performance_stats();
	
	return [
		'cheap' => $stats['tier_distribution']['cheap'] ?? 0,
		'mid'   => $stats['tier_distribution']['mid'] ?? 0,
		'high'  => $stats['tier_distribution']['high'] ?? 0,
	];
}

/**
 * Get Copilot escalation statistics
 *
 * @return array Escalation stats
 */
function get_escalation_stats(): array {
	$tracker = new MetadataTracker();
	$stats = $tracker->get_performance_stats();
	
	$total = $stats['tier_distribution']['high'] ?? 0;
	$success_rate = $stats['success_rate'] ?? 0;
	
	return [
		'total'       => $total,
		'successRate' => $success_rate,
	];
}

/**
 * Get workflow pipeline statistics
 *
 * @return array Workflow stats
 */
function get_workflow_stats(): array {
	$tracker = new MetadataTracker();
	$all_metadata = $tracker->get_all_metadata();
	
	$stats = [
		'intake'        => count( $all_metadata ),
		'cheap'         => 0,
		'comprehension' => 0,
		'mid'           => 0,
		'high'          => 0,
		'artifacts'     => 0,
		'learning'      => 0,
	];
	
	foreach ( $all_metadata as $metadata ) {
		// Count by tier
		$tier = $metadata['tier_used'] ?? '';
		if ( $tier === 'cheap' ) {
			$stats['cheap']++;
		} elseif ( $tier === 'mid' ) {
			$stats['mid']++;
		} elseif ( $tier === 'high' ) {
			$stats['high']++;
		}
		
		// Count comprehension checks
		if ( isset( $metadata['comprehension'] ) && $metadata['comprehension'] === 'YES' ) {
			$stats['comprehension']++;
		}
		
		// Count artifacts and learning updates
		if ( isset( $metadata['review_result'] ) ) {
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
