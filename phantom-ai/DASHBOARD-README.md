# Phantom.ai Dashboard

A comprehensive real-time analytics dashboard for monitoring Phantom.ai workflow automation system performance, task metrics, and AI learning insights.

## Overview

The Phantom.ai Dashboard provides a centralized interface for tracking:

- **Workflow Statistics**: Real-time metrics on task processing and performance
- **Tier Distribution**: Visual breakdown of Cheap/Mid/High tier task routing
- **Performance Trends**: Historical data and week-over-week comparisons
- **AI Learning Insights**: Continuous optimization and pattern recognition updates
- **Cost Analytics**: ROI tracking and cost savings from intelligent tier routing

## Features

### Real-Time Metrics
- **Total Tasks**: Aggregate count of all processed tasks
- **Success Rate**: Percentage of successfully completed tasks
- **Average Response Time**: Mean task processing duration
- **Cost Savings**: Financial impact of tier optimization

### Tier Distribution Analysis
- Visual bar chart showing distribution across tiers:
  - Cheap/Fast Tier (planning, classification)
  - Mid-Tier (review, validation)
  - High-Tier (code generation via Copilot)
- Detailed breakdown of API calls per tier

### Task Monitoring
- Recent task list with real-time status updates
- Task metadata including:
  - Unique task ID
  - Status (Completed, In Progress, Failed)
  - Tier assignment
  - Processing duration
  - Timestamp

### Performance Analytics
- Weekly performance chart
- Peak performance identification
- Week-over-week growth tracking
- Daily task volume statistics

### Learning Engine Insights
- Task classification accuracy improvements
- Response time optimization metrics
- New pattern recognition discoveries
- Continuous learning updates

### Comprehensive Statistics
- Total processing time
- Task complexity analysis
- Token usage breakdown (input/output)
- System uptime monitoring
- ROI improvement tracking

## Usage

### Opening the Dashboard

1. **Local Access**:
   ```bash
   # Open in default browser (macOS)
   open phantom-ai/dashboard.html
   
   # Open in default browser (Linux)
   xdg-open phantom-ai/dashboard.html
   
   # Open in default browser (Windows)
   start phantom-ai/dashboard.html
   ```

2. **From Repository Root**:
   ```bash
   cd /path/to/Phantom.ai
   open phantom-ai/dashboard.html
   ```

3. **Web Server** (for development):
   ```bash
   # Using Python 3
   cd phantom-ai
   python3 -m http.server 8000
   # Open browser to http://localhost:8000/dashboard.html
   
   # Using PHP
   cd phantom-ai
   php -S localhost:8000
   # Open browser to http://localhost:8000/dashboard.html
   ```

### Dashboard Sections

#### Header
- System status indicator (Active/Inactive)
- Phantom.ai branding with logo
- Quick status badge

#### Metrics Grid
Four primary KPI cards displaying:
- Total Tasks with month-over-month comparison
- Success Rate percentage with trend
- Average Response Time with performance delta
- Cost Savings with ROI tracking

#### Tier Distribution Card
- Visual bar chart showing tier usage percentages
- Detailed statistics for each tier
- Total API call count
- Distribution breakdown

#### Recent Tasks Card
- Live feed of recent task executions
- Task ID with status badge
- Brief description
- Metadata (tier, duration, timestamp)

#### Performance Trends Card
- 7-day performance chart
- Peak performance identification
- Daily average calculations
- Growth metrics

#### AI Learning Insights Card
- Classification accuracy improvements
- Response time optimizations
- Pattern recognition discoveries
- Learning loop updates

#### Workflow Statistics Panel
Full-width comprehensive statistics including:
- Processing time metrics
- Task complexity analysis
- Token usage statistics
- ROI and uptime monitoring

### Interactive Features

#### Refresh Button
- Manual data refresh trigger
- Located in Tier Distribution card header
- Updates all dashboard metrics

#### Auto-Refresh
- Automatic data updates every 30 seconds
- Visibility-aware refresh (pauses when tab is hidden)
- Seamless real-time updates

#### Animations
- Smooth card entrance animations on page load
- Chart bar growth animations
- Hover effects on interactive elements
- Visual feedback on data refresh

## Integration

### With Phantom CLI

The dashboard data can be populated from Phantom CLI commands:

```bash
# Get current statistics
./phantom-ai/phantom-cli stats

# Generate full report
./phantom-ai/phantom-cli report

# View specific task details
./phantom-ai/phantom-cli task task-abc123
```

### With Metadata Tracker

Data flows from the MetadataTracker PHP class:

```php
use PhantomAI\Learning\MetadataTracker;

$tracker = new MetadataTracker();
$stats = $tracker->get_performance_stats();

// Export to JSON for dashboard consumption
file_put_contents('dashboard-data.json', json_encode($stats));
```

### API Integration (Future)

The dashboard is designed to support REST API integration:

```javascript
// Example API call structure
async function fetchDashboardData() {
    const response = await fetch('/api/phantom/dashboard');
    const data = await response.json();
    updateDashboard(data);
}
```

## Customization

### Styling

The dashboard uses CSS custom properties for easy theming:

```css
:root {
    --primary-color: #6366f1;     /* Primary brand color */
    --secondary-color: #8b5cf6;   /* Secondary accent */
    --success-color: #10b981;     /* Success states */
    --warning-color: #f59e0b;     /* Warning states */
    --danger-color: #ef4444;      /* Error states */
    --dark-bg: #0f172a;           /* Background */
    --card-bg: #1e293b;           /* Card backgrounds */
}
```

### Metrics

Modify the metrics display by editing the HTML:

```html
<div class="metric-card">
    <div class="metric-header">
        <span class="metric-title">Your Custom Metric</span>
        <div class="metric-icon">📈</div>
    </div>
    <div class="metric-value" id="custom-metric">Value</div>
</div>
```

### Adding New Sections

Add new cards to the content grid:

```html
<div class="card">
    <div class="card-header">
        <h2 class="card-title">New Section</h2>
    </div>
    <!-- Your content here -->
</div>
```

## Browser Compatibility

- Chrome/Edge: ✅ Full support
- Firefox: ✅ Full support
- Safari: ✅ Full support
- Mobile browsers: ✅ Responsive design

## Performance

- Lightweight: ~50KB total (HTML + CSS + JS)
- No external dependencies
- Fast initial load time
- Smooth animations (60fps)
- Efficient DOM updates

## Security Considerations

- No sensitive data exposed in client-side code
- All data should be served via authenticated endpoints
- CORS policies should be configured appropriately
- CSP headers recommended for production deployments

## Future Enhancements

- [ ] Real-time WebSocket integration
- [ ] Advanced filtering and search capabilities
- [ ] Exportable reports (PDF, CSV)
- [ ] Customizable date ranges
- [ ] User-specific dashboards
- [ ] Dark/light theme toggle
- [ ] Mobile app version
- [ ] Alert notifications
- [ ] Historical data comparison
- [ ] Advanced charting with Chart.js/D3.js

## Troubleshooting

### Dashboard Not Loading
- Check file permissions
- Verify file path is correct
- Open browser console for errors

### Data Not Updating
- Verify API endpoints are accessible
- Check browser console for CORS errors
- Ensure data files are being generated

### Style Issues
- Clear browser cache
- Check for CSS conflicts
- Verify viewport meta tag

## Support

For issues or questions:
- Review documentation in `/phantom-ai/README.md`
- Check workflow documentation in `PHANTOM-WORKFLOW.md`
- Contact: https://demewebsolutions.com

## License

Proprietary software — all rights reserved. See LICENSE file for details.

## Credits

- **Author**: Kenneth "Demetrius" Weaver / My Deme, LLC
- **Company**: Deme Web Solutions
- **Website**: https://demewebsolutions.com
