# Phantom.ai Dashboard - Quick Start Guide

## Overview

The Phantom.ai Dashboard provides a visual interface for monitoring system status, review summaries, AI tier usage, and workflow execution pipeline.

## Access Methods

### Method 1: Standalone HTML (No Server Required)

Open directly in browser - uses mock data:

```bash
cd /home/runner/work/Phantom.ai/Phantom.ai/dashboard
open index.html  # macOS
xdg-open index.html  # Linux
start index.html  # Windows
```

### Method 2: PHP Built-in Server (Recommended)

Loads live data from artifacts and metadata:

```bash
cd /home/runner/work/Phantom.ai/Phantom.ai/dashboard
php -S localhost:8080

# Open browser to:
# http://localhost:8080
```

### Method 3: Production Web Server

Configure Apache, Nginx, or other web server to serve the dashboard directory.

## Dashboard Views

### System Overview

Displays 5 panels:

1. **System Status**: PHP version, PHPCS/Semgrep status, last run time
2. **Last Review Summary**: Error, warning, notice counts
3. **Artifacts**: Availability of JSON, SARIF, and raw outputs
4. **AI Tier Usage**: Execution counts by tier (Cheap/Mid/High)
5. **Copilot Escalations**: Total escalations and success rate

### Workflow Pipeline

Visual pipeline showing task flow:

- Task Intake → Cheap Tier → Comprehension Gate → Mid Tier → High Tier → Artifacts → Learning Engine

Each node displays:
- Icon and title
- Status indicator (idle/success/fail)
- Execution count

## Data Sources

The dashboard reads from:

1. **API Endpoint** (`api.php`):
   - Aggregates data from all sources
   - Returns JSON responses

2. **Artifacts Directory** (`../artifacts/`):
   - `phantom-report.json` - Main report
   - `phantom-report.sarif` - SARIF format
   - Raw tool outputs

3. **Metadata Directory** (`../phantom-metadata/`):
   - Task execution metadata files
   - Performance statistics

4. **Mock Data**:
   - Automatic fallback if no live data available
   - Demonstrates dashboard functionality

## Generating Live Data

To populate the dashboard with real data:

### Run Compliance Analysis

```bash
cd /home/runner/work/Phantom.ai/Phantom.ai
./scripts/wp-plugin-ai-review.sh /path/to/wordpress-plugin
```

This creates artifacts in `artifacts/` directory.

### Run Workflow CLI

```bash
cd /home/runner/work/Phantom.ai/Phantom.ai

# Process tasks
./phantom-ai/phantom-cli process "Create a product grid block"
./phantom-ai/phantom-cli process "Review security patterns"

# View statistics
./phantom-ai/phantom-cli stats
```

This creates metadata files in `phantom-metadata/` directory.

## Features

- ✅ Real-time data loading
- ✅ Automatic refresh capability
- ✅ Responsive design (desktop/mobile)
- ✅ Dark theme with accent colors
- ✅ Status indicators with color coding
- ✅ No external dependencies
- ✅ WordPress coding standards compliant

## Browser Support

- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+

Requires ES6+ JavaScript and CSS Grid support.

## Troubleshooting

### Dashboard shows all zeros

**Solution**: Generate data by running compliance analysis or workflow CLI commands.

### API returns 500 error

**Possible causes**:
1. PHP version too old (requires 7.4+)
2. File permissions on artifacts/metadata directories
3. Check PHP error logs for details

**Solution**: 
```bash
php --version  # Check PHP version
chmod 755 ../artifacts ../phantom-metadata  # Fix permissions
```

### Styles not loading

**Solution**: Verify file paths in browser dev tools. Ensure serving from correct directory.

## Security Notes

- Dashboard is read-only (no data modification)
- No authentication implemented (add if exposing publicly)
- Proprietary software - internal use only
- Do not expose publicly without access controls

## Support

For issues or questions:
- Review dashboard/README.md for detailed documentation
- Check PHANTOM-WORKFLOW.md for workflow details
- Contact: https://demewebsolutions.com/phantom-ai

## License

Proprietary software - all rights reserved.
© 2025 Deme Web Solutions / My Deme, LLC
