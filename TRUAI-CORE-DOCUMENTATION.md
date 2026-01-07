# Tru.Ai Core — Implementation Documentation

## Overview

**Tru.Ai Core (TruAi)** is the mastermind orchestration and governance layer for Phantom.ai. It manages AI routing, self-maintenance authorization, policy enforcement, and system-wide intelligence.

## System Architecture

```
User
  ↓
Tru.Ai Core (Mastermind)
  ↓
Phantom.ai (Execution / Workflow Engine)
  ↓
Tools / AI Sources / Artifacts
```

No lateral shortcuts permitted. All operations flow through TruAi Core.

## Core Components

### 1. TruAiCore Class (`phantom-ai/Core/TruAiCore.php`)

The main orchestration engine responsible for:
- AI source arbitration
- Self-maintenance authorization
- Policy enforcement
- Maintenance mode management
- System-wide governance

#### Key Methods

**arbitrateAISource(string $taskType, array $context = [])**
- Determines which AI source to use for a given task
- Returns: `['source' => string, 'reason' => string]`

**authorizeMaintenanceOperation(array $maintenanceRequest)**
- Evaluates and authorizes maintenance operations
- Checks policy violations
- Generates maintenance plan and rollback strategy
- Returns authorization result with plan

**enforcePolicy(string $policy, array $context = [])**
- Enforces system policies
- Supported policies: PHANTOM-UI-001, localhost_only, security_immutability, audit_completeness, deterministic_behavior

**setMaintenanceMode(bool $enabled)**
- Enable/disable maintenance mode
- Logs mode changes

### 2. AuditLogger Class (`phantom-ai/Core/AuditLogger.php`)

Comprehensive audit logging for all system operations:
- AI interactions
- Maintenance operations
- Setting proposals
- Policy checks
- Copilot escalations

#### Key Methods

**logAIInteraction(array $interaction)**
- Logs AI interaction with full context
- Stores in both audit log and AI history

**logMaintenanceOperation(array $operation)**
- Logs maintenance operations with authorization status
- Tracks files affected, risk level, and outcomes

**getAuditEntries(array $filters = [])**
- Retrieves filtered audit log entries

**getStatistics()**
- Returns comprehensive audit statistics

### 3. MaintenanceController Class (`phantom-ai/Core/MaintenanceController.php`)

Handles AI-based self-maintenance operations:
- Command parsing
- Intent generation
- Authorization requests
- Plan execution
- Risk assessment

#### Key Methods

**processMaintenanceCommand(string $command, array $context = [])**
- Processes maintenance commands
- Generates maintenance intent
- Requests TruAi authorization
- Returns plan for approval

**executeMaintenancePlan(array $plan, array $context = [])**
- Executes approved maintenance plans
- Logs execution progress

**setMaintenanceMode(bool $enabled)**
- Controls maintenance mode

## Approved AI Sources

TruAi manages routing to four approved AI sources:

| Source | Role | Use Cases |
|--------|------|-----------|
| **GitHub** | Reference code, issues, PRs | Research, references |
| **GitHub Copilot** | High-tier production code | Production code, fixes |
| **ChatGPT** | Planning, refactors, documentation | Planning, design, documentation |
| **Claude Sonnet** | Long-form reasoning, audits | Code review, refactoring, audits |

### Routing Rules

```php
'planning' => 'chatgpt',
'design' => 'chatgpt',
'code_review' => 'claude',
'refactor' => 'claude',
'production_code' => 'copilot',
'research' => 'github',
'references' => 'github'
```

## AI Screen Interface

The AI execution screen (`Phantom.ai-screen.html`) provides a Cursor-style unified interaction surface with:

### 1. Unified Input Panel
- Multiline text input for commands
- Keyboard shortcuts (Enter/Shift+Enter)
- Command history preservation

### 2. Context Attachment Panel
- File upload (PHP, JS, CSS, JSON, MD)
- Image upload (SVG, PNG, JPG)
- Web URL input
- Project file selector
- Include/exclude toggles per item

### 3. AI Response Panel
- Text explanation display
- Code blocks with syntax highlighting
- Action controls (Copy, Save, Review, Escalate)

### 4. AI-Accessible Settings Panel
- AI Tier selection (Cheap/Mid/Copilot)
- Escalation mode (Auto/Manual)
- Output format (Text/Code/Diff)
- Workflow mode (Review/Generate/Document)
- AI suggestion proposal system

### 5. Maintenance Mode Toggle
- Enable/disable self-maintenance operations
- Visual indicators when active
- Authorization workflow

## Self-Maintenance Capability

### Supported Commands

- `Upgrade [component]` - Upgrade system components
- `Refactor [component]` - Refactor existing code
- `Fix [issue]` - Fix bugs or issues
- `Improve [aspect]` - Performance improvements
- `Align [component] with [target]` - Alignment operations
- `Update [component]` - Update documentation or code
- `Prepare [task]` - Preparation tasks

### Command Flow

```
User Command
     ↓
Command Parsing
     ↓
Intent Generation
     ↓
TruAi Authorization
     ↓
User Approval
     ↓
Plan Execution
     ↓
Output Validation
     ↓
Audit Log
```

### Risk Levels

**High Risk:**
- Authentication system modifications
- Security-related changes
- Audit system changes
- Core system modifications

**Medium Risk:**
- Workflow modifications
- API changes
- Database changes
- Configuration updates

**Low Risk:**
- Documentation updates
- Template modifications
- CSS/styling changes

## Policy Enforcement

### Immutable Policies

1. **PHANTOM-UI-001** - UI framework constraints
2. **localhost_only** - Localhost execution only
3. **security_immutability** - Security boundaries
4. **audit_completeness** - Complete audit trails
5. **deterministic_behavior** - Predictable execution

### Enforcement Rules

- No AI acts independently
- All changes require human confirmation
- All actions logged
- No silent modifications
- No auto-merge or auto-deploy

## Audit & Security

### Logged Information

Every AI interaction logs:
- User ID
- Timestamp
- Input text
- Context items
- AI tier used
- Suggested setting changes
- User approvals/rejections

### Storage Locations

```
logs/audit.log - Main audit log
logs/truai-core.log - TruAi specific operations
logs/maintenance.log - Maintenance operations
artifacts/ai-history/ - Historical AI interactions (dated)
```

### Audit Statistics

Access via `AuditLogger::getStatistics()`:
- Total interactions by type
- Operations by user
- Recent entries (last 10)
- Success/failure rates

## Usage Examples

### Example 1: AI Arbitration

```php
$truAi = new TruAiCore();
$result = $truAi->arbitrateAISource('code_review', [
    'files' => ['auth.php'],
    'complexity' => 'high'
]);

// Returns: ['source' => 'claude', 'reason' => 'Long-form reasoning, audits']
```

### Example 2: Maintenance Authorization

```php
$truAi = new TruAiCore();
$truAi->setMaintenanceMode(true);

$request = [
    'command' => 'Upgrade dashboard layout',
    'summary' => 'Modernize dashboard UI',
    'files_affected' => ['phantom-ai/Templates/dashboard.html'],
    'ai_source' => 'chatgpt',
    'risk_level' => 'low'
];

$authorization = $truAi->authorizeMaintenanceOperation($request);

if ($authorization['authorized']) {
    echo "Plan: " . json_encode($authorization['plan']);
    echo "Rollback: " . json_encode($authorization['rollback_strategy']);
}
```

### Example 3: Maintenance Command Processing

```php
$controller = new MaintenanceController();
$controller->setMaintenanceMode(true);

$result = $controller->processMaintenanceCommand(
    'Upgrade Phantom.ai dashboard',
    ['user_id' => 'admin']
);

if ($result['success']) {
    echo "Plan generated. Requires approval.";
    echo "Risk Level: " . $result['intent']['risk_level'];
}
```

### Example 4: Audit Logging

```php
AuditLogger::logAIInteraction([
    'user_id' => 'admin',
    'input_text' => 'Create a product grid block',
    'context_items' => ['block-template.php'],
    'ai_tier' => 'high',
    'ai_source' => 'copilot',
    'outcome' => 'success'
]);

$stats = AuditLogger::getStatistics();
echo "Total interactions: " . $stats['total_interactions'];
```

## Integration with Existing Workflow

TruAi Core integrates seamlessly with existing Phantom.ai components:

1. **Task Classification** (`TierManager`) → Routes to TruAi for AI source selection
2. **Task Processing** (`TaskRouter`) → Uses TruAi arbitration
3. **Maintenance Operations** → Governed by TruAi authorization
4. **Setting Changes** → Proposed through TruAi approval system
5. **All Operations** → Audited via TruAi logging

## Security Boundaries

### What AI CANNOT Do

❌ No auto-merge  
❌ No silent file writes  
❌ No dependency changes without approval  
❌ No new tooling (Node, Vite, etc.)  
❌ No schema changes without migration plan  
❌ No modification of auth system  
❌ No modification of audit system  

### What AI CAN Propose

✅ Code improvements (with approval)  
✅ Documentation updates (with approval)  
✅ UI enhancements (with approval)  
✅ Workflow optimizations (with approval)  
✅ Setting adjustments (with approval)  

## Configuration

### Enable TruAi in `.phantom.yml`

```yaml
truai_core:
  enabled: true
  maintenance_mode: false
  audit_logging: true
  ai_sources:
    github: true
    copilot: true
    chatgpt: true
    claude: true
```

### Composer Autoloading

```json
{
  "autoload": {
    "psr-4": {
      "PhantomAI\\": "phantom-ai/"
    }
  }
}
```

Run `composer dump-autoload` after adding TruAi Core.

## UI Access

Access the AI Screen interface at:
```
http://localhost:8080/Phantom.ai-screen.html
```

## Legal & Ownership

**Tru.Ai Core (TruAi)**  
© 2013 – Present My Deme, LLC  
All Rights Reserved

See `TRUAI-LEGAL-NOTICE.md` for complete legal information.

## Status

✅ TruAi Core implemented  
✅ AI Screen interface complete  
✅ Self-maintenance capability active  
✅ Audit system operational  
✅ Policy enforcement enabled  
✅ Integration with Phantom.ai complete  

**System Status: OPERATIONAL**

---

*For licensing inquiries: https://demewebsolutions.com/phantom-ai*
