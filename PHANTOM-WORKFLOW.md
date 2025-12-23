# Phantom.ai WordPress Development Workflow Automation

A comprehensive AI-managed project pipeline for WordPress development that routes, compresses, verifies, and escalates tasks efficiently using a tiered AI execution system.

## Overview

Phantom.ai automates WordPress development workflows by intelligently routing tasks through a three-tier AI system:

- **Cheap/Fast Tier**: Planning, comprehension checking, and task classification
- **Mid-Tier**: Code review, validation, and automated testing
- **High-Tier (Copilot)**: Production-ready code generation and complex logic implementation

This approach minimizes expensive high-tier API calls while maximizing ROI through intelligent task routing and continuous learning.

## Features

### Core Workflow Automation
- ✅ Automated task classification and tier assignment
- ✅ Prompt compression and comprehension gates
- ✅ Clarification loops for ambiguous tasks
- ✅ Structured Copilot prompt generation
- ✅ Metadata tracking and learning loops

### WordPress Development
- ✅ Block-first hybrid architecture templates
- ✅ WooCommerce compatibility checks
- ✅ wp.org compliance validation
- ✅ WordPress coding standards enforcement

### Learning & Optimization
- ✅ Task performance tracking
- ✅ Token usage and ROI metrics
- ✅ Tier optimization based on historical data
- ✅ Success rate monitoring

### Asset Management
- ✅ SVG design asset handling
- ✅ Asset validation rules
- ✅ Integration guidelines for Copilot

## Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/DemeWebsolutions/Phantom.ai.git
   cd Phantom.ai
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Verify installation**
   ```bash
   ./phantom-ai/phantom-cli help
   ```

## Quick Start

### 1. Process a Task

```bash
./phantom-ai/phantom-cli process "Create a product grid block with category filtering"
```

This will:
- Compress the prompt
- Check comprehension
- Classify the task
- Determine appropriate tier
- Generate a task ID

### 2. Generate Copilot Prompt

For high-tier tasks:

```bash
./phantom-ai/phantom-cli copilot task-12345
```

This generates a structured, copy-paste ready prompt for GitHub Copilot.

### 3. View Performance Statistics

```bash
./phantom-ai/phantom-cli stats
```

### 4. Generate Full Report

```bash
./phantom-ai/phantom-cli report
```

## Workflow Steps

### Step 0: User Intent Submission
- User submits task description
- System assigns unique task ID

### Step 1: Prompt Compression & Comprehension Gate
- **Tier**: Cheap/Fast
- Compress prompt to minimal instruction
- Verify comprehension (YES/NO)
- Enter clarification loop if needed

### Step 2: Clarification Loop (if needed)
- **Tier**: Cheap/Fast
- Identify missing details
- Generate questions
- Repeat until comprehension = YES

### Step 3: Escalation & Delegation
- **Tier**: Cheap/Fast
- Classify task type:
  - Basic response → Cheap/Fast
  - Review → Mid-Tier
  - Code generation → High-Tier
  - Testing → Mid-Tier
- Generate Copilot-ready prompt if needed

### Step 4: High-Tier Execution (Copilot)
- Paste structured prompt into Copilot
- Copilot generates code/diffs
- Save output with metadata

### Step 5: Review & Verification
- **Tier**: Mid-Tier
- Review output against constraints
- Validate correctness and compliance
- Run automated tests
- Determine PASS/FAIL

### Step 6: Learning & Knowledge Update
- **Tier**: Cheap/Fast + Mid-Tier
- Record task performance
- Update heuristics
- Optimize future routing

### Step 7: Automated Project Continuation
- Trigger next dependent tasks
- Use learned heuristics
- Repeat until completion

## Copilot Integration

### Using the Checklist Template

1. **Load the template**
   ```bash
   cat phantom-ai/Templates/copilot-checklist.md
   ```

2. **Fill in task details**
   - Task ID and description
   - Files to modify
   - Constraints
   - SVG assets

3. **Copy the generated prompt**
   - Use the structured prompt in section 9
   - Paste directly into GitHub Copilot

4. **Submit to Copilot**
   - Copilot executes the task
   - Returns structured output

### SVG Asset Usage Rules

When working with Phantom.ai SVG assets:

- ✅ Use SVGs as-is (no modifications)
- ✅ Maintain original viewBox and aspect ratio
- ✅ Keep paths, gradients, and stroke widths intact
- ❌ Do NOT redraw or reinterpret
- ❌ Do NOT rasterize (no PNG/JPG conversion)
- ❌ Do NOT inject inline styles into SVG paths

**Available Assets:**
- `phantom-ai/Assets/phantom-ai-01.svg` - Primary logo
- `phantom-ai/Assets/phantom-ai-02.svg` - Neural network variant

## WordPress Development

### Block Templates

Create WordPress blocks using the provided templates:

```bash
cp -r phantom-ai/Templates/WordPress/block-template blocks/my-custom-block
```

Edit the files:
- `block.json` - Block metadata
- `index.js` - React components
- `editor.css` - Editor styles
- `style.css` - Frontend styles

### Plugin Template

Use the plugin template as a starting point:

```bash
cp phantom-ai/Templates/WordPress/plugin-template.php my-plugin.php
```

### WooCommerce Compatibility

Ensure WooCommerce compatibility by:
1. Using WooCommerce hooks and filters
2. Testing with WooCommerce active
3. Following WooCommerce coding standards
4. Validating cart and checkout integrations

### wp.org Compliance

All code must meet wp.org standards:
- Proper escaping and sanitization
- Internationalization (i18n)
- Accessibility (a11y)
- Security best practices
- No external dependencies that violate wp.org guidelines

## Metadata Structure

Every task stores:

```json
{
  "task_id": "task-abc123",
  "tier_used": "high",
  "comprehension": "YES",
  "copilot_prompt": "...",
  "copilot_output": "...",
  "review_result": "PASS",
  "iterations": 1,
  "lessons_learned": "...",
  "token_usage": {
    "input": 1500,
    "output": 3000
  },
  "timestamp": "2025-12-22 12:00:00"
}
```

## ROI & Efficiency Principles

1. **Cheap tier absorbs ambiguity** - Prevents wasted high-tier calls
2. **Deterministic prompts** - Maximize first-pass success in Copilot
3. **High-tier only when necessary** - Reduces cost per task
4. **Review + testing** - Ensures compliance, limits rework
5. **Learning loop** - Optimizes future tier usage

## API Reference

### TierManager

```php
use PhantomAI\Core\TierManager;

$tier_manager = new TierManager();
$classification = $tier_manager->classify_task( "Create a block" );
// Returns: ['tier' => 'high', 'task_type' => 'code_generation', 'reason' => '...']
```

### TaskRouter

```php
use PhantomAI\Workflow\TaskRouter;

$router = new TaskRouter();
$result = $router->process_task( 'task-123', 'Build a form' );
// Returns: ['status' => 'ready_for_execution', 'tier' => 'high', ...]
```

### MetadataTracker

```php
use PhantomAI\Learning\MetadataTracker;

$tracker = new MetadataTracker();
$tracker->store_task_metadata( $metadata );
$stats = $tracker->get_performance_stats();
```

### LearningEngine

```php
use PhantomAI\Learning\LearningEngine;

$engine = new LearningEngine( $tracker );
$lessons = $engine->learn_from_task( $task_metadata );
$recommendations = $engine->get_optimization_recommendations();
```

## Configuration

Edit `.phantom.yml` to configure:

```yaml
php_versions: ["7.4", "8.0", "8.1", "8.2", "8.3"]
include:
  - "**/*.php"
  - "**/*.js"
  - "**/*.css"
exclude:
  - "vendor/**"
  - "node_modules/**"
checks:
  readme: true
  i18n: true
  security: true
  accessibility: true
ai:
  enabled: true
  mode: deep
  provider: "phantom"
```

## Troubleshooting

### Task Classification Issues

If tasks are being misclassified:

1. Review the classification patterns in `TierManager.php`
2. Check comprehension gate results
3. Provide more specific task descriptions

### High Iteration Counts

If tasks require multiple iterations:

1. Review the generated Copilot prompts
2. Check comprehension gate effectiveness
3. Add more context to task descriptions
4. Review failed task metadata

### Low Success Rates

If success rate is below 80%:

1. Run `phantom-cli report` to see recommendations
2. Review failed tasks
3. Improve prompt templates
4. Add more constraints to Copilot prompts

## Contributing

This is proprietary software. We are not accepting external contributions at this time.

For business or partnership inquiries, visit: https://demewebsolutions.com

## License

Proprietary software — all rights reserved. No open-source license is granted.

For licensing inquiries, contact: https://demewebsolutions.com/phantom-ai

## Credits

- **Author**: Kenneth "Demetrius" Weaver / My Deme, LLC
- **Company**: Deme Web Solutions
- **Website**: https://demewebsolutions.com
