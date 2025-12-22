<?php
/**
 * Tier Manager - Manages the three-tier AI execution system
 *
 * @package PhantomAI\Core
 */

namespace PhantomAI\Core;

/**
 * TierManager handles tier classification and routing
 */
class TierManager {
    /**
     * Tier constants
     */
    const TIER_CHEAP = 'cheap';
    const TIER_MID = 'mid';
    const TIER_HIGH = 'high';

    /**
     * Task type constants
     */
    const TASK_BASIC_RESPONSE = 'basic_response';
    const TASK_REVIEW = 'review';
    const TASK_CODE_GENERATION = 'code_generation';
    const TASK_TESTING = 'testing';

    /**
     * Classify task and determine appropriate tier
     *
     * @param string $task_description Task description
     * @param array  $context Optional context
     * @return array Classification result with tier and task_type
     */
    public function classify_task( string $task_description, array $context = [] ): array {
        $task_description_lower = strtolower( $task_description );
        
        // Code generation patterns
        if ( $this->is_code_generation_task( $task_description_lower ) ) {
            return [
                'tier' => self::TIER_HIGH,
                'task_type' => self::TASK_CODE_GENERATION,
                'reason' => 'Requires code implementation',
            ];
        }

        // Review patterns
        if ( $this->is_review_task( $task_description_lower ) ) {
            return [
                'tier' => self::TIER_MID,
                'task_type' => self::TASK_REVIEW,
                'reason' => 'Requires code review or validation',
            ];
        }

        // Testing patterns
        if ( $this->is_testing_task( $task_description_lower ) ) {
            return [
                'tier' => self::TIER_MID,
                'task_type' => self::TASK_TESTING,
                'reason' => 'Requires testing or verification',
            ];
        }

        // Default to cheap tier for basic responses
        return [
            'tier' => self::TIER_CHEAP,
            'task_type' => self::TASK_BASIC_RESPONSE,
            'reason' => 'Basic response or planning task',
        ];
    }

    /**
     * Check if task requires code generation
     *
     * @param string $task Task description (lowercase)
     * @return bool
     */
    private function is_code_generation_task( string $task ): bool {
        $patterns = [
            'implement',
            'create',
            'build',
            'generate',
            'add function',
            'write code',
            'develop',
            'code',
        ];

        foreach ( $patterns as $pattern ) {
            if ( strpos( $task, $pattern ) !== false ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if task requires review
     *
     * @param string $task Task description (lowercase)
     * @return bool
     */
    private function is_review_task( string $task ): bool {
        $patterns = [
            'review',
            'check',
            'validate',
            'verify',
            'analyze',
            'inspect',
        ];

        foreach ( $patterns as $pattern ) {
            if ( strpos( $task, $pattern ) !== false ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if task requires testing
     *
     * @param string $task Task description (lowercase)
     * @return bool
     */
    private function is_testing_task( string $task ): bool {
        $patterns = [
            'test',
            'run tests',
            'unit test',
            'integration test',
        ];

        foreach ( $patterns as $pattern ) {
            if ( strpos( $task, $pattern ) !== false ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get tier cost multiplier (for ROI tracking)
     *
     * @param string $tier Tier constant
     * @return float Cost multiplier
     */
    public function get_tier_cost_multiplier( string $tier ): float {
        $multipliers = [
            self::TIER_CHEAP => 1.0,
            self::TIER_MID => 5.0,
            self::TIER_HIGH => 20.0,
        ];

        return $multipliers[ $tier ] ?? 1.0;
    }
}
