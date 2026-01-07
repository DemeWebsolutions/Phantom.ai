/**
 * Phantom.ai Dashboard JavaScript
 * Handles data loading, view switching, and UI updates
 */

// Dashboard state
const Dashboard = {
    currentView: 'overview',
    data: {
        systemStatus: {},
        reviewSummary: {},
        artifacts: {},
        tierUsage: {},
        escalations: {},
        workflowStats: {}
    },

    // Initialize dashboard
    init() {
        this.setupEventListeners();
        this.loadData();
    },

    // Setup event listeners
    setupEventListeners() {
        // Navigation buttons
        document.querySelectorAll('.nav-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                this.switchView(e.target.dataset.view);
            });
        });

        // Refresh button
        document.getElementById('refresh-btn').addEventListener('click', () => {
            this.loadData();
        });
    },

    // Switch between views
    switchView(view) {
        // Update active nav button
        document.querySelectorAll('.nav-btn').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.view === view);
        });

        // Update active view
        document.querySelectorAll('.dashboard-view').forEach(viewEl => {
            viewEl.classList.toggle('active', viewEl.id === `${view}-view`);
        });

        this.currentView = view;
    },

    // Load dashboard data
    async loadData() {
        try {
            // Try to load from PHP API first
            const apiData = await this.loadFromAPI();
            if (apiData) {
                this.data = apiData;
                this.updateUI();
                return;
            }
        } catch (error) {
            console.warn('Could not load from API, trying direct files:', error);
        }

        try {
            // Try to load from artifacts
            const artifactsData = await this.loadFromArtifacts();
            const metadataData = await this.loadFromMetadata();

            // Merge data
            this.data = {
                ...this.data,
                ...artifactsData,
                ...metadataData
            };

            // Update UI
            this.updateUI();
        } catch (error) {
            console.warn('Could not load live data, using mock data:', error);
            this.loadMockData();
        }
    },

    // Load data from PHP API
    async loadFromAPI() {
        try {
            const response = await fetch('api.php?endpoint=all');
            if (!response.ok) throw new Error('API request failed');
            
            const result = await response.json();
            if (!result.success) throw new Error(result.error || 'API error');
            
            return result.data;
        } catch (error) {
            console.warn('Could not load from API:', error);
            return null;
        }
    },

    // Load data from phantom-report.json
    async loadFromArtifacts() {
        try {
            const response = await fetch('../artifacts/phantom-report.json');
            if (!response.ok) throw new Error('Artifacts not found');
            
            const reportData = await response.json();
            
            return {
                reviewSummary: {
                    errors: reportData.summary?.errors || 0,
                    warnings: reportData.summary?.warnings || 0,
                    notices: reportData.summary?.notices || 0
                },
                systemStatus: {
                    phpVersion: reportData.environment?.php_version || 'Unknown',
                    phpcsStatus: reportData.tools?.phpcs?.status || 'unknown',
                    semgrepStatus: reportData.tools?.semgrep?.status || 'unknown',
                    lastRun: reportData.timestamp || new Date().toISOString()
                },
                artifacts: {
                    json: 'Available',
                    sarif: reportData.sarif_available ? 'Available' : 'Not Generated',
                    raw: 'Available'
                }
            };
        } catch (error) {
            console.warn('Could not load artifacts:', error);
            return {};
        }
    },

    // Load data from phantom-metadata
    async loadFromMetadata() {
        try {
            // Try to load stats from a stats.json file if it exists
            const response = await fetch('../phantom-metadata/stats.json');
            if (!response.ok) throw new Error('Metadata not found');
            
            const statsData = await response.json();
            
            return {
                tierUsage: {
                    cheap: statsData.tier_usage?.cheap || 0,
                    mid: statsData.tier_usage?.mid || 0,
                    high: statsData.tier_usage?.high || 0
                },
                escalations: {
                    total: statsData.escalations?.total || 0,
                    successRate: statsData.escalations?.success_rate || 0
                },
                workflowStats: {
                    intake: statsData.workflow?.intake || 0,
                    cheap: statsData.workflow?.cheap || 0,
                    comprehension: statsData.workflow?.comprehension || 0,
                    mid: statsData.workflow?.mid || 0,
                    high: statsData.workflow?.high || 0,
                    artifacts: statsData.workflow?.artifacts || 0,
                    learning: statsData.workflow?.learning || 0
                }
            };
        } catch (error) {
            console.warn('Could not load metadata:', error);
            return {};
        }
    },

    // Load mock data for demonstration
    loadMockData() {
        this.data = {
            systemStatus: {
                phpVersion: '8.2.0',
                phpcsStatus: 'operational',
                semgrepStatus: 'operational',
                lastRun: new Date().toLocaleString()
            },
            reviewSummary: {
                errors: 3,
                warnings: 12,
                notices: 8
            },
            artifacts: {
                json: 'Available',
                sarif: 'Available',
                raw: 'Available'
            },
            tierUsage: {
                cheap: 45,
                mid: 18,
                high: 7
            },
            escalations: {
                total: 7,
                successRate: 85.7
            },
            workflowStats: {
                intake: 70,
                cheap: 45,
                comprehension: 45,
                mid: 18,
                high: 7,
                artifacts: 70,
                learning: 70
            }
        };

        this.updateUI();
    },

    // Update UI with current data
    updateUI() {
        this.updateSystemStatus();
        this.updateReviewSummary();
        this.updateArtifacts();
        this.updateTierUsage();
        this.updateEscalations();
        this.updateWorkflowPipeline();
    },

    // Update System Status panel
    updateSystemStatus() {
        const status = this.data.systemStatus;
        
        document.getElementById('php-version').textContent = status.phpVersion || '--';
        
        const phpcsStatus = document.getElementById('phpcs-status');
        phpcsStatus.textContent = (status.phpcsStatus || 'unknown').toUpperCase();
        phpcsStatus.className = `status-value status-badge ${status.phpcsStatus || 'unknown'}`;
        
        const semgrepStatus = document.getElementById('semgrep-status');
        semgrepStatus.textContent = (status.semgrepStatus || 'unknown').toUpperCase();
        semgrepStatus.className = `status-value status-badge ${status.semgrepStatus || 'unknown'}`;
        
        document.getElementById('last-run').textContent = status.lastRun || '--';
    },

    // Update Review Summary panel
    updateReviewSummary() {
        const summary = this.data.reviewSummary;
        
        document.getElementById('error-count').textContent = summary.errors || 0;
        document.getElementById('warning-count').textContent = summary.warnings || 0;
        document.getElementById('notice-count').textContent = summary.notices || 0;
    },

    // Update Artifacts panel
    updateArtifacts() {
        const artifacts = this.data.artifacts;
        
        document.getElementById('artifact-json').textContent = artifacts.json || '--';
        document.getElementById('artifact-sarif').textContent = artifacts.sarif || '--';
        document.getElementById('artifact-raw').textContent = artifacts.raw || '--';
    },

    // Update AI Tier Usage panel
    updateTierUsage() {
        const usage = this.data.tierUsage;
        
        document.getElementById('tier-cheap-count').textContent = usage.cheap || 0;
        document.getElementById('tier-mid-count').textContent = usage.mid || 0;
        document.getElementById('tier-high-count').textContent = usage.high || 0;
    },

    // Update Copilot Escalations panel
    updateEscalations() {
        const escalations = this.data.escalations;
        
        document.getElementById('total-escalations').textContent = escalations.total || 0;
        document.getElementById('success-rate').textContent = 
            (escalations.successRate || 0).toFixed(1) + '%';
    },

    // Update Workflow Pipeline
    updateWorkflowPipeline() {
        const stats = this.data.workflowStats;
        
        // Update counts
        document.getElementById('node-intake-count').textContent = stats.intake || 0;
        document.getElementById('node-cheap-count').textContent = stats.cheap || 0;
        document.getElementById('node-comprehension-count').textContent = stats.comprehension || 0;
        document.getElementById('node-mid-count').textContent = stats.mid || 0;
        document.getElementById('node-high-count').textContent = stats.high || 0;
        document.getElementById('node-artifacts-count').textContent = stats.artifacts || 0;
        document.getElementById('node-learning-count').textContent = stats.learning || 0;

        // Update statuses based on counts
        this.updateNodeStatus('intake', stats.intake);
        this.updateNodeStatus('cheap', stats.cheap);
        this.updateNodeStatus('comprehension', stats.comprehension);
        this.updateNodeStatus('mid', stats.mid);
        this.updateNodeStatus('high', stats.high);
        this.updateNodeStatus('artifacts', stats.artifacts);
        this.updateNodeStatus('learning', stats.learning);
    },

    // Update individual node status
    updateNodeStatus(nodeId, count) {
        const statusEl = document.getElementById(`node-${nodeId}-status`);
        const nodeEl = document.querySelector(`[data-node="${nodeId}"]`);
        
        if (count > 0) {
            statusEl.textContent = 'success';
            statusEl.className = 'node-status success';
            nodeEl.classList.add('status-success');
        } else {
            statusEl.textContent = 'idle';
            statusEl.className = 'node-status';
            nodeEl.classList.remove('status-success');
        }
    }
};

// Initialize dashboard when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    Dashboard.init();
});
