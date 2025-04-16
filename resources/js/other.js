// public/js/admin.js
document.addEventListener('DOMContentLoaded', function() {

    const sidebar = document.getElementById('adminSidebar');
    const toggleBtn = document.getElementById('sidebarToggleBtn');
    // Select body or layout wrapper if needed for global class
    // const body = document.body;

    // Function to toggle sidebar state
    function toggleSidebar() {
        if (sidebar) {
            const isCollapsed = sidebar.classList.toggle('collapsed');

            // --- Option 1: Send state to server via AJAX to store in session ---
            // Requires a backend route to handle this
            /*
            fetch('/admin/settings/sidebar-toggle', { // Example route
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') // Get CSRF token
                },
                body: JSON.stringify({ collapsed: isCollapsed })
            })
            .then(response => response.json())
            .then(data => console.log('Sidebar state saved:', data))
            .catch(error => console.error('Error saving sidebar state:', error));
            */

            // --- Option 2: Use localStorage (Simpler, client-side only persistence) ---
            localStorage.setItem('sidebarCollapsed', isCollapsed);
        }
    }

    // Add event listener to the toggle button
    if (toggleBtn) {
        toggleBtn.addEventListener('click', toggleSidebar);
    }

    // --- Option 2 Initialization: Check localStorage on page load ---
    const savedState = localStorage.getItem('sidebarCollapsed');
    if (sidebar && savedState !== null) { // Check if a saved state exists
        if (savedState === 'true') {
            sidebar.classList.add('collapsed');
        } else {
             sidebar.classList.remove('collapsed'); // Ensure it's not collapsed if saved as false
        }
    }

    // Optional: Add active class based on URL (simple version)
    const currentUrl = window.location.href;
    const sidebarLinks = document.querySelectorAll('.sidebar-nav li a');
    sidebarLinks.forEach(link => {
        if (link.href === currentUrl) {
            link.closest('li').classList.add('active');
            // If it has a parent sub-menu structure, you might need to open it too
        }
        // More complex matching (e.g., for '/admin/products/edit/1' to match '/admin/products')
        // can be added here if needed.
    });

     // Basic Dropdown Toggle (if not using a framework like Bootstrap)
     const dropdownToggles = document.querySelectorAll('.dropdown-toggle');
     dropdownToggles.forEach(toggle => {
         toggle.addEventListener('click', function(e) {
             e.preventDefault();
             let dropdown = this.closest('.dropdown');
             if (dropdown) {
                 dropdown.classList.toggle('show'); // Add a 'show' class
                 let menu = dropdown.querySelector('.dropdown-menu');
                 if (menu) {
                     menu.style.display = dropdown.classList.contains('show') ? 'block' : 'none';
                 }
             }
         });
     });

     // Close dropdown if clicking outside
     document.addEventListener('click', function(e) {
         if (!e.target.closest('.dropdown')) {
             document.querySelectorAll('.dropdown.show').forEach(dropdown => {
                 dropdown.classList.remove('show');
                  let menu = dropdown.querySelector('.dropdown-menu');
                  if (menu) menu.style.display = 'none';
             });
         }
     });


});