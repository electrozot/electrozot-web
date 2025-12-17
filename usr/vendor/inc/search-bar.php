<?php
// Search bar component - separate from navbar
?>
<div class="search-bar-container">
    <div class="search-bar">
        <div class="search-input-wrapper">
            <i class="fas fa-search search-icon"></i>
            <input type="text" class="search-input" placeholder="Search by phone, name, or ID..." id="searchInput">
            <button type="button" class="search-clear" id="searchClear" style="display: none;">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
</div>

<script>
// Search functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const searchClear = document.getElementById('searchClear');
    
    searchInput.addEventListener('input', function() {
        if (this.value.length > 0) {
            searchClear.style.display = 'block';
        } else {
            searchClear.style.display = 'none';
        }
        
        // Add your search logic here
        performSearch(this.value);
    });
    
    searchClear.addEventListener('click', function() {
        searchInput.value = '';
        searchClear.style.display = 'none';
        performSearch('');
    });
    
    function performSearch(query) {
        // Implement search functionality based on your needs
        console.log('Searching for:', query);
    }
});
</script>