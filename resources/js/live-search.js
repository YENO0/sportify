/**
 * Live Search Implementation
 * Provides real-time search functionality for tables with debouncing
 */

class LiveSearch {
    constructor(options) {
        this.searchInput = options.searchInput;
        this.tableContainer = options.tableContainer;
        this.form = options.form;
        this.route = options.route;
        this.debounceDelay = options.debounceDelay || 0; // No delay for instant results
        this.timeoutId = null;
        this.isLoading = false;
        this.abortController = null; // For canceling previous requests
        this.requestSequence = 0; // Track request order
        
        this.init();
    }

    init() {
        if (!this.searchInput || !this.tableContainer) {
            return;
        }

        // Add loading indicator
        this.addLoadingIndicator();

        // Prevent form submission (everything is automatic now)
        this.form.addEventListener('submit', (e) => {
            e.preventDefault();
            this.handleSearch(this.searchInput.value);
        });

        // Listen for input events (search field)
        this.searchInput.addEventListener('input', (e) => {
            this.handleSearch(e.target.value);
        });

        // Listen for filter changes (automatic search on change)
        const filterInputs = this.form.querySelectorAll('select, input[type="checkbox"]');
        filterInputs.forEach(input => {
            input.addEventListener('change', () => {
                // Automatically trigger search when filter changes
                this.handleSearch(this.searchInput.value);
            });
        });
    }

    handleSearch(searchValue) {
        // Clear existing timeout
        if (this.timeoutId) {
            clearTimeout(this.timeoutId);
        }

        // Debounce the search
        this.timeoutId = setTimeout(() => {
            this.performSearch(searchValue);
        }, this.debounceDelay);
    }

    async performSearch(searchValue) {
        // Cancel any previous request
        if (this.abortController) {
            this.abortController.abort();
        }

        // Create new abort controller for this request
        this.abortController = new AbortController();
        const currentSequence = ++this.requestSequence;

        this.isLoading = true;
        this.showLoading();

        try {
            // Get all form data
            const formData = new FormData(this.form);
            const params = new URLSearchParams();
            
            // Add all form fields to params
            for (const [key, value] of formData.entries()) {
                if (value) {
                    params.append(key, value);
                }
            }

            // Add search value
            if (searchValue) {
                params.set('search', searchValue);
            } else {
                params.delete('search');
            }

            // Make AJAX request with abort signal
            const url = `${this.route}?${params.toString()}`;
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html',
                },
                signal: this.abortController.signal // Allow cancellation
            });

            // Check if this is still the latest request
            if (currentSequence !== this.requestSequence) {
                return; // Ignore stale response
            }

            if (!response.ok) {
                throw new Error('Search request failed');
            }

            const html = await response.text();
            
            // Double-check sequence again after async operation
            if (currentSequence !== this.requestSequence) {
                return; // Ignore stale response
            }

            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            
            // Extract table content - find the container by ID or class
            const containerId = this.tableContainer.id;
            const containerClass = this.tableContainer.className;
            const containerTag = this.tableContainer.tagName.toLowerCase();
            
            let newTableContainer = null;
            if (containerId) {
                newTableContainer = doc.getElementById(containerId);
            } else if (containerClass) {
                // Find by class - get the first matching element
                const classSelector = containerTag + '.' + containerClass.split(' ').filter(c => c).join('.');
                newTableContainer = doc.querySelector(classSelector);
            } else {
                // Fallback: find by tag name or table container
                if (containerTag === 'main') {
                    // For main content, get the main element
                    newTableContainer = doc.querySelector('main');
                } else {
                    newTableContainer = doc.querySelector('.overflow-x-auto');
                }
            }
            
            if (newTableContainer) {
                // Update the table container
                this.tableContainer.innerHTML = newTableContainer.innerHTML;
                
                // Re-initialize any JavaScript that might be needed (like status update buttons)
                this.reinitializeScripts();
                
                // Update URL without page reload
                const newUrl = new URL(url, window.location.origin);
                window.history.pushState({}, '', newUrl.pathname + newUrl.search);
            }

        } catch (error) {
            // Ignore abort errors (they're expected)
            if (error.name === 'AbortError') {
                return;
            }
            
            console.error('Live search error:', error);
            // Fallback to form submission on error
            this.form.submit();
        } finally {
            // Only hide loading if this was the latest request
            if (currentSequence === this.requestSequence) {
                this.hideLoading();
                this.isLoading = false;
            }
        }
    }

    addLoadingIndicator() {
        const loadingDiv = document.createElement('div');
        loadingDiv.id = 'live-search-loading';
        loadingDiv.className = 'hidden fixed top-20 right-4 bg-blue-600 text-white px-4 py-2 rounded-md shadow-lg z-50';
        loadingDiv.innerHTML = '<span class="inline-block animate-spin mr-2">⟳</span> Searching...';
        document.body.appendChild(loadingDiv);
    }

    showLoading() {
        const loadingDiv = document.getElementById('live-search-loading');
        if (loadingDiv) {
            loadingDiv.classList.remove('hidden');
        }
    }

    hideLoading() {
        const loadingDiv = document.getElementById('live-search-loading');
        if (loadingDiv) {
            loadingDiv.classList.add('hidden');
        }
    }

    reinitializeScripts() {
        // Re-initialize any scripts that might be needed after content update
        // For example, if there are onclick handlers that need to be reattached
        // This is a placeholder for any additional initialization needed
    }
}

// Initialize live search when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Equipment List
    const inventorySearch = document.querySelector('#inventory-search');
    const inventoryTable = document.querySelector('#inventory-table-container');
    const inventoryForm = document.querySelector('#inventory-search-form');
    
    if (inventorySearch && inventoryTable && inventoryForm) {
        new LiveSearch({
            searchInput: inventorySearch,
            tableContainer: inventoryTable,
            form: inventoryForm,
            route: inventoryForm.action || window.location.pathname,
            debounceDelay: 0
        });
    }

    // Brands List
    const brandsSearch = document.querySelector('#brands-search');
    const brandsTable = document.querySelector('#brands-table-container');
    const brandsForm = document.querySelector('#brands-search-form');
    
    if (brandsSearch && brandsTable && brandsForm) {
        new LiveSearch({
            searchInput: brandsSearch,
            tableContainer: brandsTable,
            form: brandsForm,
            route: brandsForm.action || window.location.pathname,
            debounceDelay: 0
        });
    }

    // Sport Types List - use content container to include empty states
    const sportTypesSearch = document.querySelector('#sport-types-search');
    const sportTypesContent = document.querySelector('#sport-types-content-container');
    const sportTypesForm = document.querySelector('#sport-types-search-form');
    
    if (sportTypesSearch && sportTypesContent && sportTypesForm) {
        new LiveSearch({
            searchInput: sportTypesSearch,
            tableContainer: sportTypesContent,
            form: sportTypesForm,
            route: sportTypesForm.action || window.location.pathname,
            debounceDelay: 0
        });
    }

    // Maintenance List - update the entire main content area since there are multiple tables
    const maintenanceSearch = document.querySelector('#maintenance-search');
    const maintenanceForm = document.querySelector('#maintenance-search-form');
    const maintenanceContent = document.querySelector('main');
    
    if (maintenanceSearch && maintenanceForm && maintenanceContent) {
        // For maintenance, we'll update the entire main content area
        // Find the first table container as a reference
        const maintenanceTable = document.querySelector('#maintenance-table-container') || 
                                 document.querySelector('#maintenance-upcoming-container') ||
                                 document.querySelector('.overflow-x-auto');
        
        if (maintenanceTable) {
            new LiveSearch({
                searchInput: maintenanceSearch,
                tableContainer: maintenanceContent, // Update entire main content
                form: maintenanceForm,
                route: maintenanceForm.action || window.location.pathname,
                debounceDelay: 0
            });
        }
    }
});

