<?php
// Technician Dashboard Search and Filters Component
?>
<div class="tech-search-filter-container">
    <!-- Search Section -->
    <div class="tech-search-section">
        <div class="search-wrapper">
            <div class="search-input-container">
                <i class="fas fa-search search-icon"></i>
                <input type="text" class="tech-search-input" placeholder="Search by phone, name, or ID..." id="techSearchInput">
                <button type="button" class="search-clear-btn" id="techSearchClear" style="display: none;">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <button type="button" class="search-filter-toggle" id="filterToggle">
                <i class="fas fa-sliders-h"></i>
                <span>Filters</span>
            </button>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="tech-filter-section" id="filterSection">
        <div class="filter-tabs">
            <button class="filter-tab" data-filter="pending">
                <i class="fas fa-clock"></i>
                <span>Pending</span>
                <span class="filter-count" id="pendingCount">0</span>
            </button>
            <button class="filter-tab active" data-filter="active">
                <i class="fas fa-list"></i>
                <span>All Active</span>
                <span class="filter-count" id="activeCount">4</span>
            </button>
            <button class="filter-tab" data-filter="completed">
                <i class="fas fa-check-circle"></i>
                <span>Today's Completed</span>
                <span class="filter-count" id="completedCount">0</span>
            </button>
        </div>
        
        <!-- Advanced Filters (Hidden by default) -->
        <div class="advanced-filters" id="advancedFilters" style="display: none;">
            <div class="filter-row">
                <div class="filter-group">
                    <label>Service Type</label>
                    <select class="filter-select">
                        <option value="">All Services</option>
                        <option value="electrical">Electrical</option>
                        <option value="plumbing">Plumbing</option>
                        <option value="appliance">Appliance</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label>Priority</label>
                    <select class="filter-select">
                        <option value="">All Priority</option>
                        <option value="high">High</option>
                        <option value="medium">Medium</option>
                        <option value="low">Low</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label>Date Range</label>
                    <input type="date" class="filter-date" id="dateFrom">
                    <span class="date-separator">to</span>
                    <input type="date" class="filter-date" id="dateTo">
                </div>
            </div>
            <div class="filter-actions">
                <button type="button" class="btn-filter-apply">Apply Filters</button>
                <button type="button" class="btn-filter-clear">Clear All</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('techSearchInput');
    const searchClear = document.getElementById('techSearchClear');
    const filterToggle = document.getElementById('filterToggle');
    const filterSection = document.getElementById('filterSection');
    const advancedFilters = document.getElementById('advancedFilters');
    const filterTabs = document.querySelectorAll('.filter-tab');
    
    // Search functionality
    searchInput.addEventListener('input', function() {
        if (this.value.length > 0) {
            searchClear.style.display = 'block';
        } else {
            searchClear.style.display = 'none';
        }
        performSearch(this.value);
    });
    
    searchClear.addEventListener('click', function() {
        searchInput.value = '';
        searchClear.style.display = 'none';
        performSearch('');
    });
    
    // Filter toggle
    filterToggle.addEventListener('click', function() {
        const isVisible = advancedFilters.style.display !== 'none';
        advancedFilters.style.display = isVisible ? 'none' : 'block';
        this.classList.toggle('active', !isVisible);
    });
    
    // Filter tabs
    filterTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            filterTabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            const filter = this.getAttribute('data-filter');
            applyFilter(filter);
        });
    });
    
    function performSearch(query) {
        // Implement search logic
        console.log('Searching for:', query);
    }
    
    function applyFilter(filter) {
        // Implement filter logic
        console.log('Applying filter:', filter);
    }
});
</script>