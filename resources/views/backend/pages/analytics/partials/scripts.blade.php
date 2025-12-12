<script>
// Global variables
let currentPeriod = 30;
let charts = {};
let autoRefreshInterval;

// Initialize on page load
document.addEventListener('DOMContentLoaded', function () {
    initializeCharts();
    loadAllData();
    
    // Auto-refresh real-time every 30 seconds
    autoRefreshInterval = setInterval(loadRealTimeData, 30000);
    
    // Period selector
    document.querySelectorAll('.btn-period').forEach(btn => {
        btn.addEventListener('click', function() {
            // Update active state
            document.querySelectorAll('.btn-period').forEach(b => {
                b.classList.remove('active', 'text-blue-600', 'dark:text-blue-400');
                b.classList.add('text-gray-900', 'dark:text-white');
            });
            this.classList.add('active', 'text-blue-600', 'dark:text-blue-400');
            this.classList.remove('text-gray-900', 'dark:text-white');
            
            // Update period and reload data
            currentPeriod = parseInt(this.dataset.days);
            loadTimeBasedData();
        });
    });
});

// Initialize all ApexCharts
function initializeCharts() {
    // Get theme colors
    const isDark = document.documentElement.classList.contains('dark');
    const textColor = isDark ? '#f3f4f6' : '#1f2937';
    const gridColor = isDark ? '#374151' : '#e5e7eb';

    // Users Trend Line Chart (with multiple series)
    charts.usersTrend = new ApexCharts(document.querySelector("#users-trend-chart"), {
        series: [
            { name: 'Users', data: [] },
            { name: 'Sessions', data: [] },
            { name: 'Page Views', data: [] }
        ],
        chart: {
            type: 'area',
            height: 350,
            toolbar: { show: true },
            zoom: { enabled: true }
        },
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: 2 },
        xaxis: {
            type: 'datetime',
            labels: {
                format: 'MMM dd',
                style: { colors: textColor }
            }
        },
        yaxis: {
            title: { text: 'Count', style: { color: textColor } },
            labels: { style: { colors: textColor } }
        },
        colors: ['#3B82F6', '#10B981', '#F59E0B'],
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.5,
                opacityTo: 0.1,
            }
        },
        legend: {
            position: 'top',
            labels: { colors: textColor }
        },
        grid: {
            borderColor: gridColor
        },
        tooltip: {
            theme: isDark ? 'dark' : 'light',
            x: { format: 'dd MMM yyyy' }
        }
    });
    charts.usersTrend.render();
    
    // Top Events Bar Chart
    charts.topEvents = new ApexCharts(document.querySelector("#top-events-chart"), {
        series: [{ name: 'Events', data: [] }],
        chart: {
            type: 'bar',
            height: 350
        },
        plotOptions: {
            bar: {
                horizontal: true,
                borderRadius: 4,
                dataLabels: { position: 'top' }
            }
        },
        dataLabels: {
            enabled: true,
            style: { colors: [textColor] }
        },
        xaxis: {
            categories: [],
            labels: { style: { colors: textColor } }
        },
        yaxis: {
            labels: { style: { colors: textColor } }
        },
        colors: ['#10B981'],
        grid: { borderColor: gridColor },
        tooltip: { theme: isDark ? 'dark' : 'light' }
    });
    charts.topEvents.render();
    
    // Traffic Sources Donut Chart
    charts.trafficSources = new ApexCharts(document.querySelector("#traffic-sources-chart"), {
        series: [],
        chart: {
            type: 'donut',
            height: 300
        },
        labels: [],
        colors: ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899', '#14B8A6'],
        legend: {
            position: 'bottom',
            labels: { colors: textColor }
        },
        dataLabels: {
            enabled: true,
            formatter: function (val) {
                return val.toFixed(1) + "%"
            }
        },
        tooltip: {
            theme: isDark ? 'dark' : 'light',
            y: {
                formatter: function(val) {
                    return val + " users"
                }
            }
        }
    });
    charts.trafficSources.render();
    
    // Devices Pie Chart
    charts.devices = new ApexCharts(document.querySelector("#devices-chart"), {
        series: [],
        chart: {
            type: 'pie',
            height: 300
        },
        labels: [],
        colors: ['#3B82F6', '#10B981', '#F59E0B'],
        legend: {
            position: 'bottom',
            labels: { colors: textColor }
        },
        dataLabels: {
            enabled: true,
            formatter: function (val) {
                return val.toFixed(1) + "%"
            }
        },
        tooltip: {
            theme: isDark ? 'dark' : 'light',
            y: {
                formatter: function(val) {
                    return val + " users"
                }
            }
        }
    });
    charts.devices.render();
}

// Load all dashboard data
function loadAllData() {
    loadRealTimeData();
    loadTimeBasedData();
    loadBrowsers();
    loadOperatingSystems();
    loadLandingPages();
}

// Load time-based data (affected by period selector)
function loadTimeBasedData() {
    loadOverviewData();
    loadUsersTrend();
    loadTopPages();
    loadTopEvents();
    loadTrafficSources();
    loadGeography();
    loadDevices();
    updateLastUpdated();
}

// Real-time users
function loadRealTimeData() {
    fetch('{{ route("admin.analytics.realtime") }}')
        .then(response => response.json())
        .then(data => {
            document.getElementById('realtime-users').textContent = formatNumber(data.active_users || 0);
        })
        .catch(error => {
            console.error('Error loading real-time data:', error);
            document.getElementById('realtime-users').textContent = '0';
        });
}

// Overview stats
function loadOverviewData() {
    fetch(`{{ route("admin.analytics.overview") }}?days=${currentPeriod}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('total-users').textContent = formatNumber(data.total_users || 0);
            document.getElementById('new-users').textContent = formatNumber(data.new_users || 0);
            document.getElementById('sessions').textContent = formatNumber(data.sessions || 0);
            document.getElementById('page-views').textContent = formatNumber(data.page_views || 0);
            document.getElementById('avg-session').textContent = formatDuration(data.avg_session_duration || 0);
            document.getElementById('bounce-rate').textContent = (data.bounce_rate || 0).toFixed(1) + '%';
            document.getElementById('engagement-rate').textContent = (data.engagement_rate || 0).toFixed(1) + '%';
            document.getElementById('sessions-per-user').textContent = (data.sessions_per_user || 0).toFixed(2);
        })
        .catch(error => {
            console.error('Error loading overview:', error);
            showErrorInStats();
        });
}

// Users trend chart
function loadUsersTrend() {
    fetch(`{{ route("admin.analytics.users-trend") }}?days=${currentPeriod}`)
        .then(response => response.json())
        .then(data => {
            const usersData = [];
            const sessionsData = [];
            const pageViewsData = [];

            data.forEach(item => {
                const timestamp = new Date(item.date).getTime();
                usersData.push({ x: timestamp, y: item.users });
                sessionsData.push({ x: timestamp, y: item.sessions });
                pageViewsData.push({ x: timestamp, y: item.page_views });
            });
            
            charts.usersTrend.updateSeries([
                { name: 'Users', data: usersData },
                { name: 'Sessions', data: sessionsData },
                { name: 'Page Views', data: pageViewsData }
            ]);
        })
        .catch(error => {
            console.error('Error loading users trend:', error);
        });
}

// Top pages table
function loadTopPages() {
    fetch('{{ route("admin.analytics.top-pages") }}')
        .then(response => response.json())
        .then(data => {
            if (data.length === 0) {
                document.getElementById('top-pages-container').innerHTML = '<p class="text-center text-gray-500 py-4">{{ __("No data available") }}</p>';
                return;
            }

            let html = '<div class="overflow-x-auto"><table class="w-full"><thead class="bg-gray-50 dark:bg-gray-900/50"><tr>';
            html += '<th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">#</th>';
            html += '<th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __("Page") }}</th>';
            html += '<th class="px-3 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __("Views") }}</th>';
            html += '<th class="px-3 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __("Users") }}</th>';
            html += '</tr></thead><tbody class="divide-y divide-gray-200 dark:divide-gray-800">';
            
            data.forEach((page, index) => {
                html += `
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/50">
                        <td class="px-3 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">${index + 1}</td>
                        <td class="px-3 py-3">
                            <div class="text-sm font-medium text-gray-900 dark:text-white truncate" style="max-width: 300px;" title="${page.path}">${page.path}</div>
                            ${page.title ? `<div class="text-xs text-gray-500 dark:text-gray-400 truncate" style="max-width: 300px;">${page.title}</div>` : ''}
                        </td>
                        <td class="px-3 py-3 text-sm text-gray-900 dark:text-white text-right font-semibold">${formatNumber(page.views)}</td>
                        <td class="px-3 py-3 text-sm text-gray-600 dark:text-gray-400 text-right">${formatNumber(page.users)}</td>
                    </tr>
                `;
            });
            html += '</tbody></table></div>';
            document.getElementById('top-pages-container').innerHTML = html;
        })
        .catch(error => {
            console.error('Error loading top pages:', error);
            document.getElementById('top-pages-container').innerHTML = '<p class="text-center text-red-500 py-4">{{ __("Error loading data") }}</p>';
        });
}

// Top events chart
function loadTopEvents() {
    fetch('{{ route("admin.analytics.top-events") }}')
        .then(response => response.json())
        .then(data => {
            if (data.length === 0) {
                charts.topEvents.updateOptions({
                    xaxis: { categories: ['No events'] }
                });
                charts.topEvents.updateSeries([{ name: 'Events', data: [0] }]);
                return;
            }

            const categories = data.map(item => item.event_name);
            const seriesData = data.map(item => item.count);
            
            charts.topEvents.updateOptions({
                xaxis: { categories: categories }
            });
            charts.topEvents.updateSeries([{
                name: 'Events',
                data: seriesData
            }]);
        })
        .catch(error => {
            console.error('Error loading top events:', error);
        });
}

// Traffic sources chart
function loadTrafficSources() {
    fetch('{{ route("admin.analytics.traffic-sources") }}')
        .then(response => response.json())
        .then(data => {
            if (data.length === 0) {
                charts.trafficSources.updateOptions({ labels: ['No data'] });
                charts.trafficSources.updateSeries([1]);
                return;
            }

            const labels = data.map(item => item.source);
            const series = data.map(item => item.users);
            
            charts.trafficSources.updateOptions({ labels: labels });
            charts.trafficSources.updateSeries(series);
        })
        .catch(error => {
            console.error('Error loading traffic sources:', error);
        });
}

// Geography list
function loadGeography() {
    fetch('{{ route("admin.analytics.geography") }}')
        .then(response => response.json())
        .then(data => {
            if (data.length === 0) {
                document.getElementById('geography-container').innerHTML = '<p class="text-center text-gray-500 py-4">{{ __("No data available") }}</p>';
                return;
            }

            let html = '<div class="space-y-3">';
            data.forEach(country => {
                html += `
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-sm font-medium text-gray-900 dark:text-white">${country.country}</span>
                            <span class="text-sm font-semibold text-gray-600 dark:text-gray-400">${formatNumber(country.users)}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2 dark:bg-gray-700">
                            <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: ${country.percentage}%"></div>
                        </div>
                    </div>
                `;
            });
            html += '</div>';
            document.getElementById('geography-container').innerHTML = html;
        })
        .catch(error => {
            console.error('Error loading geography:', error);
            document.getElementById('geography-container').innerHTML = '<p class="text-center text-red-500 py-4">{{ __("Error loading data") }}</p>';
        });
}

// Devices chart
function loadDevices() {
    fetch('{{ route("admin.analytics.devices") }}')
        .then(response => response.json())
        .then(data => {
            if (data.length === 0) {
                charts.devices.updateOptions({ labels: ['No data'] });
                charts.devices.updateSeries([1]);
                return;
            }

            const labels = data.map(item => item.device);
            const series = data.map(item => item.users);
            
            charts.devices.updateOptions({ labels: labels });
            charts.devices.updateSeries(series);
        })
        .catch(error => {
            console.error('Error loading devices:', error);
        });
}

// Browsers list
function loadBrowsers() {
    fetch('{{ route("admin.analytics.browsers") }}')
        .then(response => response.json())
        .then(data => {
            if (data.length === 0) {
                document.getElementById('browsers-container').innerHTML = '<p class="text-center text-gray-500 py-4">{{ __("No data available") }}</p>';
                return;
            }

            let html = '<div class="space-y-2">';
            data.forEach((browser, index) => {
                const percentage = index === 0 ? 100 : (browser.users / data[0].users) * 100;
                html += `
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-900 dark:text-white">${browser.browser}</span>
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400">${formatNumber(browser.users)}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-1.5 dark:bg-gray-700 mb-3">
                        <div class="bg-indigo-600 h-1.5 rounded-full transition-all duration-300" style="width: ${percentage}%"></div>
                    </div>
                `;
            });
            html += '</div>';
            document.getElementById('browsers-container').innerHTML = html;
        })
        .catch(error => {
            console.error('Error loading browsers:', error);
            document.getElementById('browsers-container').innerHTML = '<p class="text-center text-red-500 py-4">{{ __("Error loading data") }}</p>';
        });
}

// Operating systems list
function loadOperatingSystems() {
    fetch('{{ route("admin.analytics.operating-systems") }}')
        .then(response => response.json())
        .then(data => {
            if (data.length === 0) {
                document.getElementById('os-container').innerHTML = '<p class="text-center text-gray-500 py-4">{{ __("No data available") }}</p>';
                return;
            }

            let html = '<div class="space-y-2">';
            data.forEach((os, index) => {
                const percentage = index === 0 ? 100 : (os.users / data[0].users) * 100;
                html += `
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-900 dark:text-white">${os.os}</span>
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400">${formatNumber(os.users)}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-1.5 dark:bg-gray-700 mb-3">
                        <div class="bg-purple-600 h-1.5 rounded-full transition-all duration-300" style="width: ${percentage}%"></div>
                    </div>
                `;
            });
            html += '</div>';
            document.getElementById('os-container').innerHTML = html;
        })
        .catch(error => {
            console.error('Error loading operating systems:', error);
            document.getElementById('os-container').innerHTML = '<p class="text-center text-red-500 py-4">{{ __("Error loading data") }}</p>';
        });
}

// Landing pages table
function loadLandingPages() {
    fetch('{{ route("admin.analytics.landing-pages") }}')
        .then(response => response.json())
        .then(data => {
            if (data.length === 0) {
                document.getElementById('landing-pages-container').innerHTML = '<p class="text-center text-gray-500 py-4">{{ __("No data available") }}</p>';
                return;
            }

            let html = '<div class="overflow-x-auto"><table class="w-full"><thead class="bg-gray-50 dark:bg-gray-900/50"><tr>';
            html += '<th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">#</th>';
            html += '<th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __("Landing Page") }}</th>';
            html += '<th class="px-3 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __("Sessions") }}</th>';
            html += '<th class="px-3 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __("Bounce Rate") }}</th>';
            html += '</tr></thead><tbody class="divide-y divide-gray-200 dark:divide-gray-800">';
            
            data.forEach((page, index) => {
                const bounceRateClass = page.bounce_rate > 70 ? 'text-red-600 dark:text-red-400' : 
                                       page.bounce_rate > 50 ? 'text-orange-600 dark:text-orange-400' : 
                                       'text-green-600 dark:text-green-400';
                html += `
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/50">
                        <td class="px-3 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">${index + 1}</td>
                        <td class="px-3 py-3 text-sm text-gray-900 dark:text-white truncate" style="max-width: 400px;" title="${page.page}">${page.page}</td>
                        <td class="px-3 py-3 text-sm text-gray-900 dark:text-white text-right font-semibold">${formatNumber(page.sessions)}</td>
                        <td class="px-3 py-3 text-sm ${bounceRateClass} text-right font-semibold">${page.bounce_rate.toFixed(1)}%</td>
                    </tr>
                `;
            });
            html += '</tbody></table></div>';
            document.getElementById('landing-pages-container').innerHTML = html;
        })
        .catch(error => {
            console.error('Error loading landing pages:', error);
            document.getElementById('landing-pages-container').innerHTML = '<p class="text-center text-red-500 py-4">{{ __("Error loading data") }}</p>';
        });
}

// Update last updated timestamp
function updateLastUpdated() {
    const now = new Date();
    const timeString = now.toLocaleTimeString();
    document.getElementById('last-updated').textContent = timeString;
}

// Helper functions
function formatNumber(num) {
    if (num === null || num === undefined) return '0';
    return new Intl.NumberFormat().format(num);
}

function formatDuration(seconds) {
    if (!seconds) return '0:00';
    const minutes = Math.floor(seconds / 60);
    const secs = Math.floor(seconds % 60);
    return `${minutes}:${secs.toString().padStart(2, '0')}`;
}

function showErrorInStats() {
    const statIds = ['total-users', 'new-users', 'sessions', 'page-views', 'avg-session', 'bounce-rate', 'engagement-rate', 'sessions-per-user'];
    statIds.forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            element.textContent = 'N/A';
            element.classList.add('text-red-500');
        }
    });
}

// Cleanup on page unload
window.addEventListener('beforeunload', function() {
    if (autoRefreshInterval) {
        clearInterval(autoRefreshInterval);
    }
});
</script>
