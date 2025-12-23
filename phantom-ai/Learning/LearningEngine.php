<?php
/**
 * Learning Engine - Optimizes future task routing based on historical performance
 *
 * @package PhantomAI\Learning
 */

namespace PhantomAI\Learning;

/**
 * LearningEngine handles optimization and learning from task execution
 */
class LearningEngine {
    /**
     * MetadataTracker instance
     *
     * @var MetadataTracker
     */
    private $tracker;

    /**
     * Constructor
     *
     * @param MetadataTracker $tracker Metadata tracker instance
     */
    public function __construct( MetadataTracker $tracker ) {
        $this->tracker = $tracker;
    }

    /**
     * Learn from task execution and update heuristics
     *
     * @param array $task_metadata Task metadata
     * @return array Lessons learned
     */
    public function learn_from_task( array $task_metadata ): array {
        $lessons = [];

        // Analyze iteration count
        if ( isset( $task_metadata['iterations'] ) && $task_metadata['iterations'] > 1 ) {
            $lessons[] = [
                'type' => 'high_iterations',
                'message' => 'Task required multiple iterations. Consider improving prompt clarity.',
                'task_id' => $task_metadata['task_id'],
            ];
        }

        // Analyze tier usage vs success
        if ( isset( $task_metadata['tier_used'], $task_metadata['review_result'] ) ) {
            if ( $task_metadata['tier_used'] === 'high' && $task_metadata['review_result'] === 'FAIL' ) {
                $lessons[] = [
                    'type' => 'high_tier_failure',
                    'message' => 'High-tier execution failed. Review prompt structure and constraints.',
                    'task_id' => $task_metadata['task_id'],
                ];
            }
        }

        // Analyze comprehension issues
        if ( isset( $task_metadata['comprehension'] ) && $task_metadata['comprehension'] === 'NO' ) {
            $lessons[] = [
                'type' => 'comprehension_failure',
                'message' => 'Task failed comprehension gate. Improve initial task description.',
                'task_id' => $task_metadata['task_id'],
            ];
        }

        return $lessons;
    }

    /**
     * Get optimization recommendations
     *
     * @return array Recommendations for improving workflow
     */
    public function get_optimization_recommendations(): array {
        $stats = $this->tracker->get_performance_stats();
        $recommendations = [];

        // High tier usage recommendation
        $high_tier_percentage = $stats['total_tasks'] > 0 
            ? ( $stats['tier_distribution']['high'] / $stats['total_tasks'] ) * 100 
            : 0;

        if ( $high_tier_percentage > 50 ) {
            $recommendations[] = [
                'priority' => 'high',
                'category' => 'cost_optimization',
                'message' => "High-tier usage is {$high_tier_percentage}%. Consider better task classification to reduce costs.",
            ];
        }

        // Success rate recommendation
        if ( $stats['success_rate'] < 80 ) {
            $recommendations[] = [
                'priority' => 'high',
                'category' => 'quality',
                'message' => "Success rate is {$stats['success_rate']}%. Review failed tasks and improve prompt templates.",
            ];
        }

        // Iteration recommendation
        if ( $stats['avg_iterations'] > 2 ) {
            $recommendations[] = [
                'priority' => 'medium',
                'category' => 'efficiency',
                'message' => "Average iterations is {$stats['avg_iterations']}. Improve comprehension gates and prompt clarity.",
            ];
        }

        return $recommendations;
    }

    /**
     * Suggest tier for task type based on historical success
     *
     * @param string $task_type Task type
     * @return string Suggested tier
     */
    public function suggest_tier( string $task_type ): string {
        $all_metadata = $this->tracker->get_all_metadata();
        $task_type_data = array_filter( $all_metadata, function( $task ) use ( $task_type ) {
            return isset( $task['task_type'] ) && $task['task_type'] === $task_type;
        } );

        if ( empty( $task_type_data ) ) {
            // No historical data, return default
            return 'cheap';
        }

        // Find most successful tier for this task type
        $tier_success = [];
        foreach ( $task_type_data as $task ) {
            if ( ! isset( $task['tier_used'], $task['review_result'] ) ) {
                continue;
            }

            $tier = $task['tier_used'];
            if ( ! isset( $tier_success[ $tier ] ) ) {
                $tier_success[ $tier ] = [ 'success' => 0, 'total' => 0 ];
            }

            $tier_success[ $tier ]['total']++;
            if ( $task['review_result'] === 'PASS' ) {
                $tier_success[ $tier ]['success']++;
            }
        }

        // Calculate success rates
        $best_tier = 'cheap';
        $best_rate = 0;
        foreach ( $tier_success as $tier => $data ) {
            $rate = $data['total'] > 0 ? $data['success'] / $data['total'] : 0;
            if ( $rate > $best_rate ) {
                $best_rate = $rate;
                $best_tier = $tier;
            }
        }

        return $best_tier;
    }
}
