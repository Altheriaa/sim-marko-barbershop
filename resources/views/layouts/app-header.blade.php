<header class="sticky top-0 flex w-full bg-white border-gray-200 z-40 dark:border-gray-800 dark:bg-gray-900 xl:border-b"
    x-data="{
        isApplicationMenuOpen: false,
        toggleApplicationMenu() {
            this.isApplicationMenuOpen = !this.isApplicationMenuOpen;
        }
    }">
    <div class="flex flex-col items-center justify-between grow xl:flex-row xl:px-6">
        <div
            class="flex items-center justify-between w-full gap-2 px-3 py-3 border-b border-gray-200 dark:border-gray-800 sm:gap-4 xl:justify-normal xl:border-b-0 xl:px-0 lg:py-3.5">

            <!-- Desktop Sidebar Toggle Button (visible on xl and up) -->
            <button
                class="hidden xl:flex items-center justify-center w-10 h-10 text-gray-500 border border-gray-200 rounded-lg dark:border-gray-800 dark:text-gray-400 lg:h-11 lg:w-11 hover:bg-gray-100 dark:hover:bg-gray-800 transition"
                :class="{ 'bg-gray-100 dark:bg-white/[0.03]': !$store.sidebar.isExpanded }"
                @click="$store.sidebar.toggleExpanded()" aria-label="Toggle Sidebar">
                <svg x-show="!$store.sidebar.isMobileOpen" width="16" height="12" viewBox="0 0 16 12" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M0.583252 1C0.583252 0.585788 0.919038 0.25 1.33325 0.25H14.6666C15.0808 0.25 15.4166 0.585786 15.4166 1C15.4166 1.41421 15.0808 1.75 14.6666 1.75L1.33325 1.75C0.919038 1.75 0.583252 1.41422 0.583252 1ZM0.583252 11C0.583252 10.5858 0.919038 10.25 1.33325 10.25L14.6666 10.25C15.0808 10.25 15.4166 10.5858 15.4166 11C15.4166 11.4142 15.0808 11.75 14.6666 11.75L1.33325 11.75C0.919038 11.75 0.583252 11.4142 0.583252 11ZM1.33325 5.25C0.919038 5.25 0.583252 5.58579 0.583252 6C0.583252 6.41421 0.919038 6.75 7.99992 6.75C8.41413 6.75 8.74992 6.41421 8.74992 6C8.74992 5.58579 8.41413 5.25 7.99992 5.25L1.33325 5.25Z"
                        fill="currentColor"></path>
                </svg>
            </button>

            <!-- Mobile Menu Toggle Hamburger (visible below xl) -->
            <button
                class="flex xl:hidden items-center justify-center w-10 h-10 text-gray-500 rounded-lg dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition"
                :class="{ 'bg-gray-100 dark:bg-white/[0.03]': $store.sidebar.isMobileOpen }"
                @click="$store.sidebar.toggleMobileOpen()" aria-label="Toggle Mobile Menu">
                <i class="fa-solid fa-bars text-lg"></i>
            </button>

            <!-- Logo (mobile only) -->
            <a href="/" class="xl:hidden flex items-center">
                <img src="/images/logo-marko.png" alt="Marko Barbershop" class="h-8 sm:h-9 w-auto object-contain" />
            </a>

            <!-- Global Search Component (Desktop + Mobile Responsive) -->
            <div class="relative flex-1 max-w-md ml-2 xl:ml-0" x-data="{
                query: '',
                results: [],
                loading: false,
                open: false,
                search() {
                    if (this.query.length < 2) {
                        this.results = [];
                        this.open = false;
                        return;
                    }
                    this.loading = true;
                    fetch('/global-search?q=' + encodeURIComponent(this.query))
                        .then(res => res.json())
                        .then(data => {
                            this.results = data.results || [];
                            this.loading = false;
                            this.open = true;
                        }).catch(() => {
                            this.loading = false;
                        });
                }
            }" @click.outside="open = false" @keydown.escape="open = false"
                @keydown.cmd.k.window.prevent="$refs.searchInput.focus()"
                @keydown.ctrl.k.window.prevent="$refs.searchInput.focus()">
                @if (in_array(auth()->user()->role, ['kasir', 'admin']))
                    <div class="relative">
                        <span class="absolute -translate-y-1/2 pointer-events-none left-3.5 top-1/2 text-gray-400">
                            <i class="fa-solid fa-magnifying-glass text-sm"></i>
                        </span>
                        <input type="text" x-ref="searchInput" x-model="query" @input.debounce.300ms="search()"
                            @focus="if(query.length >= 2) open = true" placeholder="Cari Booking, Barber..."
                            class="dark:bg-dark-900 h-10 w-full rounded-lg border border-gray-200 bg-transparent py-2 pl-9 pr-10 md:pr-14 text-xs md:text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-none focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/3 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />
                        <button type="button" @click="$refs.searchInput.focus()"
                            class="hidden md:inline-flex absolute right-2 top-1/2 -translate-y-1/2 items-center gap-0.5 rounded-lg border border-gray-200 bg-gray-50 px-1.5 py-0.5 text-[10px] text-gray-500 dark:border-gray-800 dark:bg-white/[0.03] dark:text-gray-400">
                            <span>⌘</span><span>K</span>
                        </button>
                    </div>
                @endif

                {{-- Live Search Results Dropdown --}}
                <div x-show="open" x-transition
                    class="absolute left-0 top-full mt-2 w-full min-w-[280px] sm:min-w-[360px] rounded-xl border border-gray-200 bg-white p-2 shadow-xl dark:border-gray-800 dark:bg-gray-900 z-50 max-h-96 overflow-y-auto">
                    <template x-if="loading">
                        <div class="py-6 text-center text-xs text-gray-500 dark:text-gray-400">
                            <i class="fa-solid fa-spinner fa-spin mr-2"></i> Mencari data...
                        </div>
                    </template>
                    <template x-if="!loading && results.length === 0">
                        <div class="py-6 text-center text-xs text-gray-500 dark:text-gray-400">
                            Tidak ditemukan hasil untuk "<span x-text="query"></span>"
                        </div>
                    </template>
                    <template x-if="!loading && results.length > 0">
                        <div class="divide-y divide-gray-100 dark:divide-gray-800">
                            <template x-for="item in results" :key="item.title + item.subtitle">
                                <a :href="item.url"
                                    class="flex items-start gap-3 p-2.5 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800/60 transition group">
                                    <div
                                        class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">
                                        <i class="fa-solid" :class="item.icon"></i>
                                    </div>
                                    <div class="flex flex-col min-w-0">
                                        <div class="flex items-center gap-2">
                                            <span
                                                class="text-[10px] font-bold uppercase tracking-wider text-brand-600 dark:text-brand-400"
                                                x-text="item.type"></span>
                                        </div>
                                        <span
                                            class="text-xs sm:text-sm font-semibold text-gray-900 dark:text-white truncate"
                                            x-text="item.title"></span>
                                        <span class="text-[11px] text-gray-500 dark:text-gray-400 truncate"
                                            x-text="item.subtitle"></span>
                                    </div>
                                </a>
                            </template>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Application Menu Toggle (mobile right dots) -->
            <button @click="toggleApplicationMenu()"
                class="flex items-center justify-center w-10 h-10 text-gray-700 rounded-lg hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800 xl:hidden">
                <i class="fa-solid fa-ellipsis-vertical text-lg"></i>
            </button>
        </div>

        <!-- Application Menu (mobile) and Right Side Actions (desktop) -->
        <div :class="isApplicationMenuOpen ? 'flex' : 'hidden'"
            class="items-center justify-between w-full gap-4 px-5 py-3 xl:flex shadow-theme-md xl:justify-end xl:px-0 xl:shadow-none border-b xl:border-b-0 border-gray-200 dark:border-gray-800">
            <div class="flex items-center gap-2 2xsm:gap-3">
                <!-- Theme Toggle Button -->
                <button
                    class="relative flex items-center justify-center text-gray-500 transition-colors bg-white border border-gray-200 rounded-full hover:text-dark-900 h-10 w-10 hover:bg-gray-100 hover:text-gray-700 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white shadow-xs"
                    @click="$store.theme.toggle()" aria-label="Toggle Theme">
                    <i x-show="$store.theme.theme === 'dark'" class="fa-solid fa-sun text-amber-400 text-base"></i>
                    <i x-show="$store.theme.theme !== 'dark'" class="fa-solid fa-moon text-gray-600 text-base"></i>
                </button>

                <!-- Notification Dropdown -->
                <x-header.notification-dropdown />
            </div>

            <!-- User Dropdown -->
            <x-header.user-dropdown />
        </div>
    </div>
</header>