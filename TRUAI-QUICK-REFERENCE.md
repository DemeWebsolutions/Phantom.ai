# TruAi Core Quick Reference Guide

## Quick Start

### 1. Basic Setup

```php
require_once __DIR__ . '/vendor/autoload.php';

use PhantomAI\Core\TruAiCore;
use PhantomAI\Core\AuditLogger;
use PhantomAI\Core\MaintenanceController;
```

### 2. AI Source Arbitration

```php
$truAi = new TruAiCore();

// Get AI source recommendation for a task
$result = $truAi->arbitrateAISource('code_review', [
    'files' => ['auth.php'],
    'complexity' => 'high'
]);

echo "Recommended AI: " . $result['source']; // Output: claude
```

### 3. Maintenance Operations

```php
$controller = new MaintenanceController();
$controller->setMaintenanceMode(true);

// Process a maintenance command
$result = $controller->processMaintenanceCommand(
    'Upgrade Phantom.ai dashboard',
    ['user_id' => 'admin']
);

if ($result['success']) {
    // Execute the approved plan
    $execution = $controller->executeMaintenancePlan(
        $result['plan'],
        ['user_id' => 'admin']
    );
}
```

### 4. Audit Logging

```php
// Log an AI interaction
AuditLogger::logAIInteraction([
    'user_id' => 'admin',
    'input_text' => 'Create a product block',
    'context_items' => ['block-template.php'],
    'ai_tier' => 'high',
    'ai_source' => 'copilot',
    'outcome' => 'success'
]);

// Get statistics
$stats = AuditLogger::getStatistics();
echo "Total interactions: " . $stats['total_interactions'];
```

## AI Source Routing Rules

| Task Type | AI Source | Use Case |
|-----------|-----------|----------|
| `planning` | ChatGPT | Architecture, design, planning |
| `design` | ChatGPT | UI/UX design, layouts |
| `code_review` | Claude | Security audits, code review |
| `refactor` | Claude | Code refactoring, optimization |
| `production_code` | Copilot | Production code generation |
| `research` | GitHub | Code references, examples |
| `references` | GitHub | Documentation, APIs |

## Maintenance Commands

Supported command patterns:

- `Upgrade [component]` - System upgrades
- `Refactor [component]` - Code refactoring
- `Fix [issue]` - Bug fixes
- `Improve [aspect]` - Performance improvements
- `Align [component] with [target]` - Alignment tasks
- `Update [component]` - Updates
- `Prepare [task]` - Preparation tasks

## Risk Levels

### High Risk (Requires Special Approval)
- Authentication system modifications
- Security-related changes
- Audit logging modifications
- Core system changes

### Medium Risk
- Workflow modifications
- API changes
- Database modifications
- Configuration updates

### Low Risk
- Documentation updates
- Template modifications
- CSS/styling changes

## Policy Enforcement

Immutable policies:
- `PHANTOM-UI-001` - UI framework constraints
- `localhost_only` - Localhost execution only
- `security_immutability` - Security boundaries
- `audit_completeness` - Complete audit trails
- `deterministic_behavior` - Predictable execution

```php
$truAi = new TruAiCore();

// Check policy enforcement
$enforced = $truAi->enforcePolicy('security_immutability');
```

## UI Access Points

### AI Screen Interface
Access at: `http://localhost:8080/Phantom.ai-screen.html`

Features:
- Unified input panel for commands
- Context attachment (files, images, URLs)
- AI response display
- Settings control
- Maintenance mode toggle

## Security Boundaries

### What AI CANNOT Do
❌ Auto-merge code  
❌ Silent file modifications  
❌ Add dependencies without approval  
❌ Modify authentication system  
❌ Modify audit logging  
❌ Change security policies  

### What AI CAN Propose
✅ Code improvements (with approval)  
✅ Documentation updates (with approval)  
✅ UI enhancements (with approval)  
✅ Workflow optimizations (with approval)  

## Common Integration Patterns

### Pattern 1: Task Processing with TruAi

```php
// 1. Classify task
$taskType = 'production_code';

// 2. Get AI source from TruAi
$aiSource = $truAi->arbitrateAISource($taskType);

// 3. Process task with recommended AI
// ... (route to appropriate AI)

// 4. Log the interaction
AuditLogger::logAIInteraction([
    'user_id' => 'system',
    'input_text' => $taskDescription,
    'ai_tier' => $tier,
    'ai_source' => $aiSource['source'],
    'outcome' => 'success'
]);
```

### Pattern 2: Setting Changes with Approval

```php
// AI proposes a setting change
AuditLogger::logSettingProposal([
    'user_id' => 'admin',
    'setting_name' => 'ai_tier',
    'current_value' => 'mid',
    'proposed_value' => 'high',
    'reason' => 'Complex task requires high-tier',
    'ai_source' => 'chatgpt',
    'user_decision' => 'pending'
]);

// User reviews and accepts/rejects
// ... update with user_decision = 'accepted' or 'rejected'
```

### Pattern 3: Copilot Escalation

```php
// Log escalation to Copilot
AuditLogger::logCopilotEscalation([
    'user_id' => 'admin',
    'task_id' => 'task-123',
    'reason' => 'Production code required',
    'task_description' => 'Create WooCommerce block',
    'context_provided' => ['spec.md', 'examples/'],
    'outcome' => 'pending'
]);
```

## Audit Log Queries

```php
// Get all maintenance operations
$entries = AuditLogger::getAuditEntries([
    'type' => 'maintenance_operation'
]);

// Get entries for specific user
$userEntries = AuditLogger::getAuditEntries([
    'user_id' => 'admin'
]);

// Export audit log
AuditLogger::exportAuditLog(
    'audit-export.json',
    ['type' => 'ai_interaction']
);
```

## File Locations

```
phantom-ai/Core/
  ├── TruAiCore.php              # Main orchestration engine
  ├── AuditLogger.php            # Audit logging system
  └── MaintenanceController.php  # Maintenance handler

logs/
  ├── audit.log                  # Main audit log
  ├── truai-core.log            # TruAi operations
  └── maintenance.log           # Maintenance operations

artifacts/
  └── ai-history/               # Historical AI interactions
      └── ai-history-YYYY-MM-DD.json
```

## CLI Commands (Future)

```bash
# View audit statistics
./phantom-ai/phantom-cli audit stats

# Export audit log
./phantom-ai/phantom-cli audit export --output audit.json

# Enable maintenance mode
./phantom-ai/phantom-cli maintenance enable

# Process maintenance command
./phantom-ai/phantom-cli maintenance run "Upgrade dashboard"
```

## Configuration (.phantom.yml)

```yaml
truai_core:
  enabled: true
  maintenance_mode: false
  audit_logging: true
  log_path: logs/truai-core.log
  ai_sources:
    github: true
    copilot: true
    chatgpt: true
    claude: true
```

## Troubleshooting

### Issue: Logs not being created
**Solution:** Ensure `logs/` directory has write permissions

### Issue: Maintenance commands rejected
**Solution:** Enable maintenance mode first:
```php
$controller->setMaintenanceMode(true);
```

### Issue: High-risk operations denied
**Solution:** High-risk operations (auth, security) require special approval through TruAi authorization

## Support & Documentation

- Full Documentation: `TRUAI-CORE-DOCUMENTATION.md`
- Legal Notice: `TRUAI-LEGAL-NOTICE.md`
- Examples: `examples/truai-integration-example.php`
- Workflow Guide: `PHANTOM-WORKFLOW.md`

## Legal

**Tru.Ai Core (TruAi)**  
© 2013 – Present My Deme, LLC  
All Rights Reserved

For licensing: https://demewebsolutions.com/phantom-ai
