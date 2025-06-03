document.addEventListener('DOMContentLoaded', function() {
    // Create overlay element if it doesn't exist
    if (!document.querySelector('.sidebar-overlay')) {
        const overlay = document.createElement('div');
        overlay.className = 'sidebar-overlay';
        document.body.appendChild(overlay);
    }

    const sidebarToggle = document.querySelector('[data-toggle="sidebar"]');
    const sidebar = document.querySelector('.main-sidebar');
    const overlay = document.querySelector('.sidebar-overlay');

    function toggleSidebar(e) {
        e.preventDefault();
        if (document.body.classList.contains('sidebar-open')) {
            // Closing sidebar
            document.body.classList.remove('sidebar-open');
            overlay.classList.remove('active');
            overlay.style.display = 'none';
        } else {
            // Opening sidebar
            document.body.classList.add('sidebar-open');
            overlay.style.display = 'block';
            // Force reflow to enable transition
            void overlay.offsetWidth;
            overlay.classList.add('active');
        }
    }

    // Toggle sidebar on button click
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', toggleSidebar);
    }

    // Close sidebar when clicking overlay
    if (overlay) {
        overlay.addEventListener('click', function() {
            if (document.body.classList.contains('sidebar-open')) {
                document.body.classList.remove('sidebar-open');
                document.body.classList.remove('sidebar-show');
                overlay.classList.remove('active');
                overlay.style.display = 'none';
            }
        });
    }

    // Close sidebar when pressing Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && document.body.classList.contains('sidebar-open')) {
            document.body.classList.remove('sidebar-open');
            document.body.classList.remove('sidebar-show');
            overlay.classList.remove('active');
            overlay.style.display = 'none';
        }
    });

    // Close sidebar when resizing to desktop view
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            if (window.innerWidth > 1024 && document.body.classList.contains('sidebar-open')) {
                document.body.classList.remove('sidebar-open');
                document.body.classList.remove('sidebar-show');
                overlay.classList.remove('active');
                overlay.style.display = 'none';
            }
        }, 250);
    });
});
