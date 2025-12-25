# Phantom.ai Dashboard

Visual dashboard for the Phantom.ai WordPress compliance and AI orchestration system.

## Features

### System Overview Dashboard
- **System Status**: PHP version, PHPCS status, Semgrep status, Last run timestamp
- **Last Review Summary**: Error, Warning, and Notice counts from latest analysis
- **Artifacts**: Status of phantom-report.json, phantom-report.sarif, and raw tool outputs
- **AI Tier Usage**: Execution counts for Cheap, Mid, and High (Copilot) tiers
- **Copilot Escalations**: Total escalations and success rate metrics

### Workflow Pipeline View
Visual representation of the task execution pipeline:
- Task Intake → Cheap Tier → Comprehension Gate → Mid Tier → High Tier → Artifacts → Learning Engine
- Each node shows status (idle/success/fail) and execution counts
- Color-coded status indicators

## Usage

### Standalone HTML Dashboard

Open the dashboard directly in a browser:

```bash
# Navigate to dashboard directory
cd dashboard

# Open in browser
open index.html  # macOS
xdg-open index.html  # Linux
start index.html  # Windows
```

### With PHP Server

For live data integration, run with PHP's built-in server:

```bash
# From repository root
php -S localhost:8080 -t dashboard

# Open in browser
open http://localhost:8080
```

### With Apache/Nginx

Configure your web server to serve the dashboard directory.

Example Apache configuration:

```apache
<VirtualHost *:80>
    ServerName phantom.local
    DocumentRoot /path/to/Phantom.ai/dashboard
    
    <Directory /path/to/Phantom.ai/dashboard>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

## Data Sources

The dashboard reads data from:

1. **Artifacts** (`../artifacts/`):
   - `phantom-report.json` - Unified findings and summary
   - `phantom-report.sarif` - SARIF format for GitHub
   - Raw tool outputs (phpcs.json, semgrep.json, etc.)

2. **Metadata** (`../phantom-metadata/`):
   - Task metadata files from CLI operations
   - Performance statistics
   - Tier usage tracking

3. **API Endpoint** (`api.php`):
   - PHP backend that aggregates data from artifacts and metadata
   - Provides JSON responses for dashboard components

## Mock Data

If live data is not available, the dashboard automatically falls back to mock data for demonstration purposes.

## Dashboard Components

### HTML (`index.html`)
Main dashboard structure with:
- Header with logo and refresh button
- Navigation tabs for view switching
- Overview panels grid
- Workflow pipeline visualization
- Footer with copyright and legal notice

### CSS (`assets/css/dashboard.css`)
Styling based on SVG design references:
- Dark theme with accent colors
- Responsive grid layout
- Status indicators and badges
- Hover effects and transitions
- Mobile-responsive design

### JavaScript (`assets/js/dashboard.js`)
Dashboard logic:
- Data loading from API or artifacts
- Mock data fallback
- View switching
- Real-time UI updates
- Automatic refresh capability

### PHP API (`api.php`)
Backend data provider:
- System status detection
- Artifact parsing
- Metadata aggregation
- Tier usage statistics
- Workflow pipeline stats

## Customization

### Updating Colors

Edit CSS variables in `assets/css/dashboard.css`:

```css
:root {
    --primary-color: #0a0a0a;
    --accent-color: #4a90e2;
    --success-color: #27ae60;
    --warning-color: #f39c12;
    --error-color: #e74c3c;
}
```

### Adding New Panels

1. Add HTML structure to `index.html`
2. Add styles to `dashboard.css`
3. Add data loading logic to `dashboard.js`
4. Update API endpoint in `api.php` if needed

## Browser Support

- Modern browsers (Chrome, Firefox, Safari, Edge)
- ES6+ JavaScript support required
- CSS Grid and Flexbox support required

## Security

- Dashboard is read-only (no data modification)
- API uses proper headers and error handling
- No authentication required (add if exposing publicly)
- Proprietary software - internal use only

## SVG Design References

The dashboard layout is based on SVG design files:
- `1.svg` / `phantom-ai-01.svg` - Primary logo and design elements
- `2.svg` / `phantom-ai-02.svg` - Neural network visualization

Design elements are used as-is without modification per project guidelines.

## Troubleshooting

### No Data Displayed

1. Check if artifacts exist: `ls ../artifacts/`
2. Check if metadata exists: `ls ../phantom-metadata/`
3. Run analysis to generate data: `./scripts/wp-plugin-ai-review.sh`
4. Run workflow CLI: `./phantom-ai/phantom-cli process "test task"`

### API Errors

1. Verify Composer autoload: `composer install`
2. Check PHP version: `php --version` (requires 7.4+)
3. Check PHP error logs
4. Verify file permissions

### Styling Issues

1. Clear browser cache
2. Check CSS file path in HTML
3. Verify CSS file is loaded in browser dev tools

## License

Proprietary software - all rights reserved.

For licensing inquiries, contact: https://demewebsolutions.com/phantom-ai

## Credits

- **Author**: Kenneth "Demetrius" Weaver / My Deme, LLC
- **Company**: Deme Web Solutions
- **Website**: https://demewebsolutions.com
