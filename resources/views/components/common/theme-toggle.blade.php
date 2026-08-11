<button type="button" onclick="toggleGlobalTheme()" id="theme-toggle-btn"
    class="relative flex items-center justify-center text-gray-500 transition-colors bg-white border border-gray-200 rounded-full hover:text-dark-900 h-11 w-11 hover:bg-gray-100 hover:text-gray-700 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white shadow-lg"
    aria-label="Toggle Theme">
    <!-- Sun Icon (Tampil saat Dark Mode aktif) -->
    <i id="theme-sun-icon" class="fa-solid fa-sun text-amber-400 text-lg hidden"></i>

    <!-- Moon Icon (Tampil saat Light Mode aktif) -->
    <i id="theme-moon-icon" class="fa-solid fa-moon text-gray-600 text-lg"></i>
</button>

<script>
    function updateThemeUI(theme) {
        const isDark = theme === 'dark';
        const html = document.documentElement;
        const body = document.body;

        if (isDark) {
            html.classList.add('dark');
            body.classList.add('dark', 'bg-gray-900');
        } else {
            html.classList.remove('dark');
            body.classList.remove('dark', 'bg-gray-900');
        }

        const sunIcon = document.getElementById('theme-sun-icon');
        const moonIcon = document.getElementById('theme-moon-icon');

        if (sunIcon && moonIcon) {
            if (isDark) {
                sunIcon.classList.remove('hidden');
                moonIcon.classList.add('hidden');
            } else {
                sunIcon.classList.add('hidden');
                moonIcon.classList.remove('hidden');
            }
        }
    }

    function toggleGlobalTheme() {
        const current = localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
        const next = current === 'dark' ? 'light' : 'dark';
        localStorage.setItem('theme', next);
        updateThemeUI(next);

        if (window.Alpine && window.Alpine.store('theme')) {
            window.Alpine.store('theme').theme = next;
        }
    }

    // Inisialisasi awal saat script dimuat
    (function () {
        const saved = localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
        document.addEventListener('DOMContentLoaded', () => {
            updateThemeUI(saved);
        });
        updateThemeUI(saved);
    })();
</script>