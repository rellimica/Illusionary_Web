<?php
/**
 * ADMIN FOOTER
 */
?>
    </main>
    <?php include '../null-egg.php'; ?>
    <script>
        const IMAGES_PATH = '<?php echo $IMAGES_PATH; ?>';
        
        /**
         * secureFetch()
         * Centralized fetch helper for all admin pages.
         * Communicates with the Admin API at ../api/admin.php
         */
        async function secureFetch(url, options = {}) {
            // Prepend relative path if it's an api call
            if (url.startsWith('api/')) {
                url = '../' + url;
            } else if (url.indexOf('api/admin.php') !== -1 && !url.startsWith('http') && !url.startsWith('../')) {
                url = '../' + url;
            }

            try {
                const response = await fetch(url, options);
                
                // Handle HTTP-level errors (404, 500, etc)
                if (!response.ok) {
                    const err = await response.json().catch(() => ({ error: `Server Error (${response.status})` }));
                    throw new Error(err.error || 'Network request failed');
                }

                const data = await response.json();
                
                // Handle API-level errors (success: false)
                if (data && data.success === false) {
                    throw new Error(data.error || 'API execution failed');
                }

                return data;
            } catch (e) {
                console.error("Fetch Logic Error:", e);
                throw e;
            }
        }

        /**
         * updateLocalTimes()
         * Converts unix timestamps to relative local time strings.
         */
        function updateLocalTimes() {
            document.querySelectorAll('.local-time').forEach(el => {
                const ts = el.dataset.ts;
                if (!ts || ts == 0) return;
                const date = new Date(ts * 1000);
                el.innerText = date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                el.title = date.toLocaleString();
            });
        }
    </script>
</body>
</html>
