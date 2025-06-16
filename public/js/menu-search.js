// Initialize menu search functionality
function initMenuSearch() {
    const searchInput = document.getElementById('sidebar-menu-search');
    if (!searchInput) return;

    searchInput.addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase().trim();
        const menuItems = document.querySelectorAll('.sidebar-menu li:not(.menu-header)');
        const menuHeaders = document.querySelectorAll('.sidebar-menu li.menu-header');
        
        // Reset all items to visible
        menuItems.forEach(item => {
            item.classList.remove('d-none');
        });
        menuHeaders.forEach(header => {
            header.classList.remove('d-none', 'has-visible-items');
        });

        if (searchTerm === '') return;

        // Filter menu items
        menuItems.forEach(item => {
            const link = item.querySelector('a');
            if (!link) return;

            const text = link.textContent.toLowerCase();
            const icon = link.querySelector('i');
            const iconClass = icon ? icon.className.toLowerCase() : '';
            
            if (!text.includes(searchTerm) && !iconClass.includes(searchTerm)) {
                item.classList.add('d-none');
            }
        });

        // Handle section headers visibility
        menuHeaders.forEach(header => {
            const nextSibling = header.nextElementSibling;
            let hasVisibleItems = false;
            let current = nextSibling;

            // Check all items until next header or end
            while (current && !current.classList.contains('menu-header')) {
                if (!current.classList.contains('d-none')) {
                    hasVisibleItems = true;
                    break;
                }
                current = current.nextElementSibling;
            }

            if (!hasVisibleItems) {
                header.classList.add('d-none');
            } else {
                header.classList.add('has-visible-items');
            }
        });
    });

    // Clear search on escape key
    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            this.value = '';
            this.dispatchEvent(new Event('input'));
            this.blur();
        }
    });
}
