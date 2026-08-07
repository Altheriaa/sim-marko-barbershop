{{-- Notification Dropdown Component --}}
<div class="relative" x-data="{
    dropdownOpen: false,
    notifying: false,
    unreadCount: 0,
    lastId: 0,
    items: [],
    init() {
        this.fetchNotifications();
        setInterval(() => this.fetchNotifications(), 8000);
    },
    async fetchNotifications() {
        try {
            const res = await fetch('{{ route('notifications.unread') }}');
            if (!res.ok) return;
            const data = await res.json();
            
            if (this.lastId > 0 && data.latest_id > this.lastId) {
                this.notifying = true;
            } else if (data.unread_count > 0 && this.lastId === 0) {
                this.notifying = true;
            }
            
            this.lastId = data.latest_id;
            this.unreadCount = data.unread_count;
            this.items = data.items;
        } catch (e) {
            console.error('Notification fetch error:', e);
        }
    },
    toggleDropdown() {
        this.dropdownOpen = !this.dropdownOpen;
        if (this.dropdownOpen) {
            this.notifying = false;
        }
    },
    closeDropdown() {
        this.dropdownOpen = false;
    }
}" @click.away="closeDropdown()">
    <!-- Notification Button -->
    <button
        class="relative flex items-center justify-center text-gray-500 transition-colors bg-white border border-gray-200 rounded-full hover:text-dark-900 h-11 w-11 hover:bg-gray-100 hover:text-gray-700 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white"
        @click="toggleDropdown()"
        type="button"
    >
        <!-- Notification Badge -->
        <span
            x-show="notifying || unreadCount > 0"
            class="absolute -right-0.5 -top-0.5 z-1 flex h-4 min-w-[16px] items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-bold text-white shadow-xs"
        >
            <span x-text="unreadCount > 9 ? '9+' : (unreadCount || '!')"></span>
            <span
                x-show="notifying"
                class="absolute inline-flex w-full h-full bg-red-400 rounded-full opacity-75 -z-1 animate-ping"
            ></span>
        </span>

        <!-- Bell Icon -->
        <svg
            class="fill-current"
            width="20"
            height="20"
            viewBox="0 0 20 20"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
        >
            <path
                fill-rule="evenodd"
                clip-rule="evenodd"
                d="M10.75 2.29248C10.75 1.87827 10.4143 1.54248 10 1.54248C9.58583 1.54248 9.25004 1.87827 9.25004 2.29248V2.83613C6.08266 3.20733 3.62504 5.9004 3.62504 9.16748V14.4591H3.33337C2.91916 14.4591 2.58337 14.7949 2.58337 15.2091C2.58337 15.6234 2.91916 15.9591 3.33337 15.9591H4.37504H15.625H16.6667C17.0809 15.9591 17.4167 15.6234 17.4167 15.2091C17.4167 14.7949 17.0809 14.4591 16.6667 14.4591H16.375V9.16748C16.375 5.9004 13.9174 3.20733 10.75 2.83613V2.29248ZM14.875 14.4591V9.16748C14.875 6.47509 12.6924 4.29248 10 4.29248C7.30765 4.29248 5.12504 6.47509 5.12504 9.16748V14.4591H14.875ZM8.00004 17.7085C8.00004 18.1228 8.33583 18.4585 8.75004 18.4585H11.25C11.6643 18.4585 12 18.1228 12 17.7085C12 17.2943 11.6643 16.9585 11.25 16.9585H8.75004C8.33583 16.9585 8.00004 17.2943 8.00004 17.7085Z"
                fill=""
            />
        </svg>
    </button>

    <!-- Dropdown Start -->
    <div
        x-show="dropdownOpen"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute -right-[240px] mt-[17px] flex h-[480px] w-[350px] flex-col rounded-2xl border border-gray-200 bg-white p-3 shadow-theme-lg dark:border-gray-800 dark:bg-gray-dark sm:w-[361px] lg:right-0 z-50"
        style="display: none;"
    >
        <!-- Dropdown Header -->
        <div class="flex items-center justify-between pb-3 mb-3 border-b border-gray-100 dark:border-gray-800">
            <div class="flex items-center gap-2">
                <h5 class="text-base font-bold text-gray-900 dark:text-white">Notifikasi Live</h5>
                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-600 dark:bg-emerald-950/40 dark:text-emerald-400">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Live
                </span>
            </div>

            <button @click="closeDropdown()" class="text-gray-500 dark:text-gray-400 hover:text-gray-700" type="button">
                <svg
                    class="fill-current"
                    width="20"
                    height="20"
                    viewBox="0 0 24 24"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <path
                        fill-rule="evenodd"
                        clip-rule="evenodd"
                        d="M6.21967 7.28131C5.92678 6.98841 5.92678 6.51354 6.21967 6.22065C6.51256 5.92775 6.98744 5.92775 7.28033 6.22065L11.999 10.9393L16.7176 6.22078C17.0105 5.92789 17.4854 5.92788 17.7782 6.22078C18.0711 6.51367 18.0711 6.98855 17.7782 7.28144L13.0597 12L17.7782 16.7186C18.0711 17.0115 18.0711 17.4863 17.7782 17.7792C17.4854 18.0721 17.0105 18.0721 16.7176 17.7792L11.999 13.0607L7.28033 17.7794C6.98744 18.0722 6.51256 18.0722 6.21967 17.7794C5.92678 17.4865 5.92678 17.0116 6.21967 16.7187L10.9384 12L6.21967 7.28131Z"
                        fill=""
                    />
                </svg>
            </button>
        </div>

        <!-- Notification List (Dynamic Alpine x-for) -->
        <ul class="flex flex-col h-auto overflow-y-auto custom-scrollbar flex-1">
            <template x-for="item in items" :key="item.id">
                <li @click="closeDropdown()">
                    <a
                        class="flex gap-3 rounded-lg border-b border-gray-100 p-3 px-4.5 py-3 hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-white/5 transition"
                        :href="item.target_url"
                    >
                        <span class="relative flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-brand-50 text-brand-600 font-bold text-sm dark:bg-brand-500/10 dark:text-brand-400">
                            <span x-text="item.initial"></span>
                            <span
                                class="absolute bottom-0 right-0 h-2.5 w-2.5 rounded-full border-[1.5px] border-white dark:border-gray-900"
                                :class="{
                                    'bg-amber-500': item.status === 'pending',
                                    'bg-blue-500': item.status === 'checked-in',
                                    'bg-green-500': item.status === 'completed',
                                    'bg-gray-400': !['pending', 'checked-in', 'completed'].includes(item.status)
                                }"
                            ></span>
                        </span>

                        <span class="block min-w-0 flex-1">
                            <span class="mb-1 block text-xs text-gray-500 dark:text-gray-400 leading-snug">
                                <strong class="font-bold text-gray-900 dark:text-white" x-text="item.user_name"></strong>
                                memesan <span class="font-semibold text-brand-600 dark:text-brand-400" x-text="item.layanan"></span>
                            </span>

                            <span class="flex items-center gap-2 text-[11px] text-gray-400">
                                <span>💈 <span x-text="item.barber"></span></span>
                                <span class="w-1 h-1 bg-gray-300 rounded-full dark:bg-gray-600"></span>
                                <span x-text="item.created_at_human"></span>
                            </span>
                        </span>
                    </a>
                </li>
            </template>

            <template x-if="items.length === 0">
                <li class="py-12 text-center text-xs text-gray-400">Belum ada notifikasi booking.</li>
            </template>
        </ul>

        <!-- View All Button -->
        <a
            href="{{ auth()->user()?->role === 'owner' ? route('owner.transaksi.index') : route('kasir.booking.index') }}"
            class="mt-3 flex justify-center rounded-lg border border-gray-300 bg-white p-2.5 text-xs font-semibold text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-white/[0.03]"
        >
            Lihat Semua Reservasi
        </a>
    </div>
    <!-- Dropdown End -->
</div>
