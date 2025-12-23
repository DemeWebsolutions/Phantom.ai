# Phantom.ai Core Components

This directory contains the core PHP classes for the Phantom.ai WordPress development workflow automation system.

## Directory Structure

```
phantom-ai/
├── Core/               # Core system components
│   └── TierManager.php # Three-tier AI execution system
├── Workflow/           # Task routing and processing
│   └── TaskRouter.php  # Task router and escalation logic
├── Learning/           # Learning and optimization
│   ├── MetadataTracker.php  # Task metadata storage
│   └── LearningEngine.php   # Learning and optimization
├── Templates/          # Templates and documentation
│   ├── copilot-checklist.md  # Copilot submission checklist
│   ├── execution-plan.md     # Workflow execution plan
│   └── WordPress/            # WordPress templates
│       ├── block-template/   # Block template files
│       └── plugin-template.php
├── Assets/             # Design assets
│   ├── phantom-ai-01.svg  # Primary logo
│   └── phantom-ai-02.svg  # Neural network variant
└── phantom-cli         # Command-line interface
```

## Core Components

### TierManager (`Core/TierManager.php`)

Manages the three-tier AI execution system:
- **Cheap/Fast Tier**: Planning and comprehension
- **Mid-Tier**: Review and validation
- **High-Tier**: Code generation (Copilot)

**Key Methods:**
- `classify_task()` - Classify task and determine tier
- `get_tier_cost_multiplier()` - Get cost multiplier for ROI tracking

### TaskRouter (`Workflow/TaskRouter.php`)

Routes tasks through the workflow pipeline:
- Compress prompts
- Check comprehension
- Generate Copilot-ready prompts
- Handle clarification loops

**Key Methods:**
- `process_task()` - Process task through workflow
- `generate_copilot_prompt()` - Generate structured Copilot prompt

### MetadataTracker (`Learning/MetadataTracker.php`)

Tracks task execution metadata:
- Store task metadata
- Retrieve task history
- Generate performance statistics
- Create performance reports

**Key Methods:**
- `store_task_metadata()` - Store task metadata
- `get_task_metadata()` - Retrieve task metadata
- `get_performance_stats()` - Get performance statistics
- `generate_report()` - Generate formatted report

### LearningEngine (`Learning/LearningEngine.php`)

Optimizes future task routing:
- Learn from task execution
- Generate optimization recommendations
- Suggest tiers based on historical success

**Key Methods:**
- `learn_from_task()` - Learn from task execution
- `get_optimization_recommendations()` - Get optimization recommendations
- `suggest_tier()` - Suggest tier for task type

## CLI Usage

The `phantom-cli` script provides command-line access to the workflow system.

### Available Commands

```bash
# Process a new task
./phantom-cli process "Create a product grid block"

# Classify a task
./phantom-cli classify "Review code for security"

# View statistics
./phantom-cli stats

# Generate performance report
./phantom-cli report

# Generate Copilot prompt
./phantom-cli copilot task-12345

# Display help
./phantom-cli help
```

## Templates

### Copilot Checklist (`Templates/copilot-checklist.md`)

A comprehensive checklist template for submitting tasks to GitHub Copilot. Ensures:
- Full project context
- Clear file boundaries
- SVG asset usage rules
- Non-negotiable constraints
- Structured output requirements

### Execution Plan (`Templates/execution-plan.md`)

Documents the complete workflow execution plan with:
- Tier definitions
- Step-by-step execution flow
- Output structure
- ROI and efficiency principles

### WordPress Templates (`Templates/WordPress/`)

Provides templates for:
- WordPress blocks (block.json, index.js, editor.css, style.css)
- WordPress plugins (plugin-template.php)

## Assets

### SVG Design Files (`Assets/`)

Two SVG design files are provided:
- `phantom-ai-01.svg` - Primary ghost/phantom logo with AI circuit elements
- `phantom-ai-02.svg` - Neural network design with nodes and connections

**Usage Rules:**
- Use as-is, do not modify
- Maintain original viewBox and aspect ratio
- Do not rasterize
- Do not inject inline styles

## Autoloading

Classes follow PSR-4 autoloading standard:

```php
use PhantomAI\Core\TierManager;
use PhantomAI\Workflow\TaskRouter;
use PhantomAI\Learning\MetadataTracker;
use PhantomAI\Learning\LearningEngine;
```

Autoload configuration is in `composer.json`:

```json
{
  "autoload": {
    "psr-4": {
      "PhantomAI\\": "phantom-ai/"
    }
  }
}
```

## Integration with Existing System

The workflow automation system integrates with the existing Phantom compliance tools:

1. **Compliance checks** run first (PHPCS, PHPCompatibilityWP, Semgrep, etc.)
2. **Workflow system** routes remediation tasks
3. **Copilot** generates fixes for identified issues
4. **Learning system** optimizes future task routing

## Development

To extend the system:

1. Add new tier classification patterns in `TierManager.php`
2. Enhance comprehension gates in `TaskRouter.php`
3. Add new learning heuristics in `LearningEngine.php`
4. Create new templates in `Templates/`

## Testing

Test the system with:

```bash
# Test task classification
./phantom-cli classify "Your task description"

# Test full workflow
./phantom-cli process "Your task description"

# View results
./phantom-cli stats
```

## Metadata Storage

Task metadata is stored in `../phantom-metadata/` (excluded from git).

Each task creates a JSON file with:
- Task ID and description
- Tier used
- Comprehension result
- Copilot prompt and output
- Review result
- Iteration count
- Token usage
- Timestamp

## License

Proprietary software — all rights reserved.

For licensing inquiries, contact: https://demewebsolutions.com/phantom-ai
