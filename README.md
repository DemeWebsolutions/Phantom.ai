# Phantom AI: WordPress Compliance Software

Phantom is a comprehensive compliance and review toolchain that unifies WordPress coding standards, PHP compatibility checks, security heuristics, accessibility patterns, and AI-assisted triage with a tiered workflow automation system for efficient development.

**Powered by Tru.Ai Core** - The mastermind orchestration layer that governs AI routing, self-maintenance, and policy enforcement.

## Features

- **Deep static checks**: PHPCS + WPCS, PHPCompatibilityWP, Semgrep (WordPress-focused rules)
- **Readme/i18n and basic accessibility checks** (static)
- **Unified JSON + SARIF output** for CI and GitHub code scanning
- **AI workflow automation**: Three-tier task routing system (Cheap/Mid/High)
- **TruAi Core**: Mastermind orchestration and governance layer
- **AI Screen Interface**: Cursor-style unified interaction surface
- **Self-maintenance capability**: Controlled, command-driven system modifications
- **Copilot integration**: Structured prompt generation for GitHub Copilot
- **Learning loop**: Continuous optimization based on historical performance
- **WordPress development**: Block-first templates and wp.org compliance
- **Comprehensive audit logging**: All AI interactions tracked and logged

## Quick start

1) Requirements
- PHP 8.2+, Composer
- Python 3.10+ (for report conversion and custom checks)
- jq, bash
- Semgrep (installed in CI step below)
- GitHub Actions (optional but recommended)

2) Install dev dependencies
```bash
composer install
```

3) Run compliance checks locally
```bash
bash ./scripts/wp-plugin-ai-review.sh /path/to/your-wordpress-plugin
```

Artifacts will be written to `./artifacts`:
- `phantom-report.json`: unified findings
- `phantom-report.sarif`: uploadable to GitHub code scanning
- `phpcs.json`, `phpcompat.json`, `semgrep.json`: raw tool outputs

4) Use the workflow automation system
```bash
# Classify a task
./phantom-ai/phantom-cli classify "Create a product grid block"

# Process a task through the workflow
./phantom-ai/phantom-cli process "Implement security validation in auth.php"

# View performance statistics
./phantom-ai/phantom-cli stats

# Generate Copilot-ready prompt
./phantom-ai/phantom-cli copilot task-12345
```

5) CI
A GitHub Actions workflow is included at `.github/workflows/phantom-audit.yml`. On each push/PR, it:
- Sets up PHP
- Installs PHPCS standards
- Installs jq and Semgrep
- Runs the Phantom review script
- Uploads SARIF to the Security tab (Code scanning alerts)

## Workflow Automation

Phantom.ai includes a sophisticated workflow automation system that routes tasks through a three-tier AI execution model:

- **Cheap/Fast Tier**: Planning, comprehension checking, and task classification
- **Mid-Tier**: Code review, validation, and automated testing  
- **High-Tier (Copilot)**: Production-ready code generation

This approach minimizes expensive high-tier API calls while maximizing ROI through intelligent task routing and continuous learning.

See [PHANTOM-WORKFLOW.md](PHANTOM-WORKFLOW.md) for complete workflow documentation.

## Tru.Ai Core

Phantom.ai is now powered by **Tru.Ai Core**, the mastermind orchestration layer that provides:

- **AI Arbitration**: Intelligent routing to GitHub, Copilot, ChatGPT, or Claude Sonnet
- **Self-Maintenance**: Controlled system upgrades and modifications
- **Policy Enforcement**: Immutable security and architectural constraints
- **Audit System**: Complete traceability of all AI operations
- **Governance**: Human-in-the-loop control for all changes

### AI Screen Interface

Access the unified AI execution interface at `http://localhost:8080/Phantom.ai-screen.html`:

- **Unified Input Panel**: Natural language commands for AI
- **Context Attachments**: Upload files, images, URLs, or select project files
- **AI Response Panel**: Syntax-highlighted code and explanations
- **Settings Control**: AI tier, escalation mode, and output format
- **Maintenance Mode**: Enable AI-based system modifications

See [TRUAI-CORE-DOCUMENTATION.md](TRUAI-CORE-DOCUMENTATION.md) for complete TruAi Core documentation.

## Configuration

Phantom reads optional settings from `.phantom.yml`:
- PHP versions for compatibility checks
- Include/exclude paths
- Toggle checks (i18n, readme, a11y)
- AI review mode and provider (optional)
- Workflow automation settings

## Exit codes
- Exit non-zero if any WARNING or ERROR is present in the aggregated report (configurable in future).

## Credits and Ownership

### Phantom.ai
- Credits: demewebsolutions.com / Kenneth "Demetrius" Weaver / My Deme, Llc.
- Proprietary software — all rights reserved. No open-source license is granted.

### Tru.Ai Core (TruAi)
- © 2013 – Present **My Deme, LLC**
- All Rights Reserved
- Developer: DemeWebsolutions.com
- See [TRUAI-LEGAL-NOTICE.md](TRUAI-LEGAL-NOTICE.md) for complete legal information

## Legal

This repository is proprietary. No permission is granted to use, copy, modify, merge, publish, distribute, sublicense, or sell copies of the software unless you obtain a commercial or written license from the copyright holder.

For licensing inquiries, contact: https://demewebsolutions.com/phantom-ai