<?php
/**
 * Task Router - Routes tasks through the workflow pipeline
 *
 * @package PhantomAI\Workflow
 */

namespace PhantomAI\Workflow;

use PhantomAI\Core\TierManager;

/**
 * TaskRouter handles task routing and escalation
 */
class TaskRouter {
    /**
     * TierManager instance
     *
     * @var TierManager
     */
    private $tier_manager;

    /**
     * Constructor
     */
    public function __construct() {
        $this->tier_manager = new TierManager();
    }

    /**
     * Process a task through the workflow
     *
     * @param string $task_id Unique task identifier
     * @param string $task_description Task description
     * @param array  $context Optional context
     * @return array Processing result
     */
    public function process_task( string $task_id, string $task_description, array $context = [] ): array {
        // Step 1: Compress and verify comprehension
        $compressed = $this->compress_prompt( $task_description );
        
        // Step 2: Comprehension gate
        $comprehension = $this->check_comprehension( $compressed );
        
        if ( ! $comprehension['understood'] ) {
            return [
                'status' => 'clarification_needed',
                'task_id' => $task_id,
                'questions' => $comprehension['questions'],
                'compressed_prompt' => $compressed,
            ];
        }

        // Step 3: Classify and determine tier
        $classification = $this->tier_manager->classify_task( $task_description, $context );

        // Step 4: Route based on tier
        return [
            'status' => 'ready_for_execution',
            'task_id' => $task_id,
            'tier' => $classification['tier'],
            'task_type' => $classification['task_type'],
            'reason' => $classification['reason'],
            'compressed_prompt' => $compressed,
            'copilot_ready' => $classification['tier'] === TierManager::TIER_HIGH,
        ];
    }

    /**
     * Compress prompt to minimal unambiguous instruction
     *
     * @param string $task_description Original task description
     * @return string Compressed prompt
     */
    private function compress_prompt( string $task_description ): string {
        // Remove redundant phrases
        $compressed = $task_description;
        
        // Remove filler words
        $filler_words = [ 'please', 'kindly', 'could you', 'would you' ];
        foreach ( $filler_words as $filler ) {
            $compressed = str_ireplace( $filler, '', $compressed );
        }

        // Trim whitespace
        $compressed = preg_replace( '/\s+/', ' ', $compressed );
        $compressed = trim( $compressed );

        return $compressed;
    }

    /**
     * Check comprehension of task
     *
     * @param string $task_description Task description
     * @return array Comprehension result
     */
    private function check_comprehension( string $task_description ): array {
        $questions = [];
        $understood = true;

        // Check for vague terms
        $vague_terms = [ 'something', 'somehow', 'maybe', 'probably', 'might' ];
        foreach ( $vague_terms as $term ) {
            if ( stripos( $task_description, $term ) !== false ) {
                $questions[] = "Task contains vague term '$term'. Please specify exactly what is needed.";
                $understood = false;
            }
        }

        // Check for missing file specifications (if code task)
        if ( preg_match( '/\b(create|modify|update|change|edit)\b/i', $task_description ) ) {
            if ( ! preg_match( '/\b[a-zA-Z0-9_\-]+\.(php|js|css|json)\b/', $task_description ) ) {
                $questions[] = 'Which files need to be created or modified?';
                $understood = false;
            }
        }

        return [
            'understood' => $understood,
            'questions' => $questions,
        ];
    }

    /**
     * Generate Copilot-ready structured prompt
     *
     * @param array $task_info Task information from process_task
     * @param array $files_to_modify List of files to modify
     * @param array $constraints Additional constraints
     * @param array $svg_assets SVG assets to reference
     * @return string Structured prompt for Copilot
     */
    public function generate_copilot_prompt( array $task_info, array $files_to_modify = [], array $constraints = [], array $svg_assets = [] ): string {
        $prompt = "ROLE:\n";
        $prompt .= "You are a WordPress plugin developer working on a block-first hybrid theme.\n\n";

        $prompt .= "PROJECT CONTEXT:\n";
        $prompt .= "- Phantom.ai manages task routing and verification\n";
        $prompt .= "- High-tier code execution is your responsibility\n";
        $prompt .= "- Ensure WooCommerce compatibility, wp.org compliance, and clean code\n\n";

        $prompt .= "TASK:\n";
        $prompt .= $task_info['compressed_prompt'] . "\n\n";

        if ( ! empty( $svg_assets ) ) {
            $prompt .= "DESIGN ASSETS:\n";
            foreach ( $svg_assets as $svg ) {
                $prompt .= "- $svg\n";
            }
            $prompt .= "Use these SVGs without modification unless explicitly instructed.\n\n";
        }

        if ( ! empty( $files_to_modify ) ) {
            $prompt .= "FILES TO MODIFY:\n";
            foreach ( $files_to_modify as $file ) {
                $prompt .= "- $file\n";
            }
            $prompt .= "\n";
        }

        $prompt .= "CONSTRAINTS:\n";
        $prompt .= "- Only modify designated files\n";
        if ( ! empty( $svg_assets ) ) {
            $prompt .= "- Do not alter SVG internals\n";
        }
        $prompt .= "- Maintain WordPress coding standards\n";
        $prompt .= "- Do not refactor unrelated code\n";
        foreach ( $constraints as $constraint ) {
            $prompt .= "- $constraint\n";
        }
        $prompt .= "\n";

        $prompt .= "OUTPUT:\n";
        $prompt .= "- Full code / diffs for modified files\n";
        $prompt .= "- Include comments for any assumptions made\n";
        $prompt .= "- Minimal prose, only what's necessary for Phantom.ai to verify\n";

        return $prompt;
    }
}
