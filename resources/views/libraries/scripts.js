// DOM Elements
const refreshBtn = document.getElementById('refreshBtn');
const lastUpdateElement = document.getElementById('lastUpdate');
const refreshIcon = refreshBtn.querySelector('.fa-sync-alt');

// Update timestamp function
function updateTimestamp() {
    const now = new Date();
    lastUpdateElement.textContent = now.toLocaleString();
}

// Function to update metrics
async function updateMetrics() {
    try {
        // Add rotating class to refresh icon
        refreshIcon.classList.add('rotating');
        
        // Simulate API call - Replace this with your actual API endpoint
        const response = await fetch('/api/metrics');
        const metrics = await response.json();
        
        // Update CPU Load
        document.querySelector('.cpu-load').textContent = `${metrics.cpu_load}%`;
        document.querySelector('.cpu-progress').style.width = `${metrics.cpu_load}%`;
        
        // Update RAM Usage
        document.querySelector('.ram-usage').textContent = `${metrics.ram_usage} MB`;
        document.querySelector('.ram-progress').style.width = 
            `${(metrics.ram_usage / metrics.memory_limit) * 100}%`;
        
        // Update Disk Usage
        document.querySelector('.disk-free').textContent = `${metrics.disk_free_space} GB`;
        document.querySelector('.disk-progress').style.width = 
            `${metrics.disk_usage_percentage}%`;
        
        // Update timestamp
        updateTimestamp();
        
    } catch (error) {
        console.error('Error updating metrics:', error);
        // You could add error handling UI here
    } finally {
        // Remove rotating class from refresh icon
        refreshIcon.classList.remove('rotating');
    }
}

// Event Listeners
refreshBtn.addEventListener('click', function(e) {
    e.preventDefault();
    updateMetrics();
});

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    updateTimestamp();
    // Optional: Start periodic updates
    // setInterval(updateMetrics, 60000); // Update every minute
});