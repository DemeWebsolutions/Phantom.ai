<?php
/**
 * Metadata Tracker - Tracks task execution metadata and performance
 *
 * @package PhantomAI\Learning
 */

namespace PhantomAI\Learning;

/**
 * MetadataTracker handles task metadata storage and tracking
 */
class MetadataTracker {
    /**
     * Storage directory for metadata
     *
     * @var string
     */
    private $storage_dir;

    /**
     * Constructor
     *
     * @param string $storage_dir Directory to store metadata files
     */
    public function __construct( string $storage_dir = './phantom-metadata' ) {
        $this->storage_dir = $storage_dir;
        
        if ( ! is_dir( $this->storage_dir ) ) {
            mkdir( $this->storage_dir, 0755, true );
        }
    }

    /**
     * Store task metadata
     *
     * @param array $metadata Task metadata
     * @return bool Success status
     */
    public function store_task_metadata( array $metadata ): bool {
        $required_fields = [ 'task_id', 'tier_used', 'comprehension' ];
        foreach ( $required_fields as $field ) {
            if ( ! isset( $metadata[ $field ] ) ) {
                return false;
            }
        }

        $task_id = $metadata['task_id'];
        $file_path = $this->storage_dir . "/{$task_id}.json";

        // Add timestamp if not present
        if ( ! isset( $metadata['timestamp'] ) ) {
            $metadata['timestamp'] = date( 'Y-m-d H:i:s' );
        }

        $json = json_encode( $metadata, JSON_PRETTY_PRINT );
        return file_put_contents( $file_path, $json ) !== false;
    }

    /**
     * Retrieve task metadata
     *
     * @param string $task_id Task identifier
     * @return array|null Metadata or null if not found
     */
    public function get_task_metadata( string $task_id ): ?array {
        $file_path = $this->storage_dir . "/{$task_id}.json";
        
        if ( ! file_exists( $file_path ) ) {
            return null;
        }

        $json = file_get_contents( $file_path );
        return json_decode( $json, true );
    }

    /**
     * Get all task metadata
     *
     * @return array Array of all metadata entries
     */
    public function get_all_metadata(): array {
        $metadata = [];
        $files = glob( $this->storage_dir . '/*.json' );

        foreach ( $files as $file ) {
            $json = file_get_contents( $file );
            $data = json_decode( $json, true );
            if ( $data ) {
                $metadata[] = $data;
            }
        }

        return $metadata;
    }

    /**
     * Get performance statistics
     *
     * @return array Performance metrics
     */
    public function get_performance_stats(): array {
        $all_metadata = $this->get_all_metadata();
        
        if ( empty( $all_metadata ) ) {
            return [
                'total_tasks' => 0,
                'tier_distribution' => [],
                'success_rate' => 0,
                'avg_iterations' => 0,
            ];
        }

        $stats = [
            'total_tasks' => count( $all_metadata ),
            'tier_distribution' => [
                'cheap' => 0,
                'mid' => 0,
                'high' => 0,
            ],
            'successful_tasks' => 0,
            'failed_tasks' => 0,
            'total_iterations' => 0,
            'total_tokens' => [
                'input' => 0,
                'output' => 0,
            ],
        ];

        foreach ( $all_metadata as $task ) {
            // Tier distribution
            $tier = $task['tier_used'] ?? 'unknown';
            if ( isset( $stats['tier_distribution'][ $tier ] ) ) {
                $stats['tier_distribution'][ $tier ]++;
            }

            // Success/failure
            if ( isset( $task['review_result'] ) ) {
                if ( $task['review_result'] === 'PASS' ) {
                    $stats['successful_tasks']++;
                } else {
                    $stats['failed_tasks']++;
                }
            }

            // Iterations
            if ( isset( $task['iterations'] ) ) {
                $stats['total_iterations'] += $task['iterations'];
            }

            // Token usage
            if ( isset( $task['token_usage'] ) ) {
                $stats['total_tokens']['input'] += $task['token_usage']['input'] ?? 0;
                $stats['total_tokens']['output'] += $task['token_usage']['output'] ?? 0;
            }
        }

        // Calculate averages
        $stats['success_rate'] = $stats['total_tasks'] > 0 
            ? ( $stats['successful_tasks'] / $stats['total_tasks'] ) * 100 
            : 0;
        
        $stats['avg_iterations'] = $stats['total_tasks'] > 0 
            ? $stats['total_iterations'] / $stats['total_tasks'] 
            : 0;

        return $stats;
    }

    /**
     * Generate performance report
     *
     * @return string Formatted report
     */
    public function generate_report(): string {
        $stats = $this->get_performance_stats();
        
        $report = "=== Phantom.ai Performance Report ===\n\n";
        $report .= "Total Tasks: {$stats['total_tasks']}\n";
        $report .= "Success Rate: " . number_format( $stats['success_rate'], 2 ) . "%\n";
        $report .= "Average Iterations: " . number_format( $stats['avg_iterations'], 2 ) . "\n\n";
        
        $report .= "Tier Distribution:\n";
        foreach ( $stats['tier_distribution'] as $tier => $count ) {
            $percentage = $stats['total_tasks'] > 0 
                ? ( $count / $stats['total_tasks'] ) * 100 
                : 0;
            $report .= sprintf( "  - %s: %d (%.1f%%)\n", ucfirst( $tier ), $count, $percentage );
        }
        
        $report .= "\nToken Usage:\n";
        $report .= "  - Input: {$stats['total_tokens']['input']}\n";
        $report .= "  - Output: {$stats['total_tokens']['output']}\n";
        $report .= "  - Total: " . ( $stats['total_tokens']['input'] + $stats['total_tokens']['output'] ) . "\n";

        return $report;
    }
}
