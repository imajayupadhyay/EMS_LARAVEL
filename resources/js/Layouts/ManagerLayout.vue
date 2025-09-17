<template>
  <div class="manager-layout">
    <Head />

    <!-- Modern Sidebar Component -->
    <ManagerSidebar
      :collapsed="collapsed"
      :mobile-open="mobileOpen"
      :is-mobile="isMobile"
      @toggle="collapsed = !collapsed"
      @close-mobile="mobileOpen = false"
      @open-mobile="mobileOpen = true"
    />

    <!-- Main Content Area -->
    <div class="main-content" :class="{ 'collapsed': collapsed && !isMobile, 'mobile': isMobile }">
      
      <!-- Modern Top Bar -->
      <header class="top-bar">
        <div class="top-bar-left">
          <!-- Mobile hamburger menu -->
          <button v-if="isMobile" class="mobile-menu-btn" @click="mobileOpen = true">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <line x1="4" y1="12" x2="20" y2="12"/>
              <line x1="4" y1="6" x2="20" y2="6"/>
              <line x1="4" y1="18" x2="20" y2="18"/>
            </svg>
          </button>

          <!-- Desktop collapse button -->
          <button v-if="!isMobile" class="collapse-btn" @click="collapsed = !collapsed">
            <svg v-if="!collapsed" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M21 3H3C2 3 1 4 1 5V19C1 20 2 21 3 21H21C22 21 23 20 23 19V5C23 4 22 3 21 3Z"/>
              <path d="M10 4V20"/>
            </svg>
            <svg v-else width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M3 3H21C22 3 23 4 23 5V19C23 20 22 21 21 21H3C2 21 1 20 1 19V5C1 4 2 3 3 3Z"/>
              <path d="M9 9L15 15M15 9L9 15"/>
            </svg>
          </button>

          <!-- Page Title and Breadcrumb -->
          <div class="page-info">
            <h1 class="page-title">{{ title || pageTitle }}</h1>
            <nav class="breadcrumb" v-if="breadcrumbs.length > 0">
              <ol class="breadcrumb-list">
                <li v-for="(crumb, index) in breadcrumbs" :key="index" class="breadcrumb-item">
                  <Link v-if="crumb.href && index < breadcrumbs.length - 1" :href="crumb.href" class="breadcrumb-link">
                    {{ crumb.name }}
                  </Link>
                  <span v-else class="breadcrumb-current">{{ crumb.name }}</span>
                  <svg v-if="index < breadcrumbs.length - 1" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="breadcrumb-separator">
                    <path d="m9 18 6-6-6-6"/>
                  </svg>
                </li>
              </ol>
            </nav>
          </div>
        </div>

        <!-- Right side of top bar -->
        <div class="top-bar-right">
          <!-- Search (desktop only) -->
          <div v-if="!isMobile" class="search-container">
            <div class="search-box">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="search-icon">
                <circle cx="11" cy="11" r="8"/>
                <path d="m21 21-4.35-4.35"/>
              </svg>
              <input type="text" placeholder="Search..." class="search-input" />
              <kbd class="search-kbd">⌘K</kbd>
            </div>
          </div>

          <!-- Notifications -->
          <div class="notification-container">
            <button class="notification-btn">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
              </svg>
              <span class="notification-badge">3</span>
            </button>
          </div>

          <!-- User Profile Dropdown -->
          <div class="user-dropdown" v-if="!isMobile">
            <button class="user-btn" @click="showUserMenu = !showUserMenu">
              <div class="user-avatar">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                  <circle cx="12" cy="7" r="4"/>
                </svg>
              </div>
              <div class="user-info">
                <span class="user-name">Manager</span>
                <span class="user-role">Administrator</span>
              </div>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="dropdown-arrow">
                <path d="M6 9l6 6 6-6"/>
              </svg>
            </button>

            <!-- Dropdown Menu -->
            <Transition name="dropdown">
              <div v-if="showUserMenu" class="user-menu" @click.stop>
                <Link v-if="profileRoute" :href="profileRoute" class="user-menu-item">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                  </svg>
                  Profile Settings
                </Link>
                <div class="menu-divider"></div>
                <button class="user-menu-item logout-item" @click="doLogout">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                    <polyline points="16,17 21,12 16,7"/>
                    <line x1="21" y1="12" x2="9" y2="12"/>
                  </svg>
                  Sign Out
                </button>
              </div>
            </Transition>
          </div>
        </div>
      </header>

      <!-- Main Content -->
      <main class="content-area">
        <div class="content-wrapper">
          <slot />
        </div>
      </main>

      <!-- Modern Footer -->
      <footer class="footer">
        <div class="footer-content">
          <div class="footer-left">
            <span class="copyright">© {{ new Date().getFullYear() }} HRMS Manager Portal</span>
            <span class="version">v2.1.0</span>
          </div>
          <div class="footer-right">
            <a href="#" class="footer-link">Help</a>
            <a href="#" class="footer-link">Privacy</a>
            <a href="#" class="footer-link">Terms</a>
          </div>
        </div>
      </footer>
    </div>

    <!-- Hidden logout form -->
    <form ref="logoutForm" :action="logoutUrl" method="POST" style="display: none;">
      <input type="hidden" name="_token" :value="csrfToken" />
    </form>

    <!-- Click outside to close user menu -->
    <div v-if="showUserMenu" class="overlay" @click="showUserMenu = false"></div>
  </div>
</template>

<script setup>
import ManagerSidebar from '@/Components/ManagerSidebar.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
import { route as ziggyRoute } from 'ziggy-js';

const props = defineProps({ 
  title: { type: String, default: null },
  breadcrumbs: { type: Array, default: () => [] }
});

// Layout state
const collapsed = ref(false);
const mobileOpen = ref(false);
const isMobile = ref(false);
const showUserMenu = ref(false);

// Responsive handling
function updateIsMobile() { 
  isMobile.value = window.innerWidth <= 768; 
  if (!isMobile.value) {
    mobileOpen.value = false;
  }
}

onMounted(() => { 
  updateIsMobile(); 
  window.addEventListener('resize', updateIsMobile);
  // Close user menu when clicking outside
  document.addEventListener('click', (e) => {
    if (!e.target.closest('.user-dropdown')) {
      showUserMenu.value = false;
    }
  });
});

onBeforeUnmount(() => { 
  window.removeEventListener('resize', updateIsMobile); 
});

// Page and route data
const page = usePage();
const pageProps = computed(() => page.props || {});
const currentRouteName = computed(() => pageProps.value.routeName ?? pageProps.value.currentRouteName ?? null);

// Auth and navigation
const csrfToken = typeof document !== 'undefined' ? document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '' : '';
let logoutUrl = '/logout';
try { logoutUrl = ziggyRoute('logout') || logoutUrl; } catch (e) {}

let profileRoute = null;
try { profileRoute = ziggyRoute('manager.profile'); } catch (e) { profileRoute = null; }

const pageTitle = computed(() => {
  const p = pageProps.value;
  return p.title || (p.page?.title ?? 'Manager Dashboard');
});

// Logout functionality
const logoutForm = ref(null);

function doLogout() {
  showUserMenu.value = false;
  setTimeout(() => {
    if (logoutForm.value && typeof logoutForm.value.submit === 'function') {
      logoutForm.value.submit();
    } else {
      const form = document.createElement('form');
      form.method = 'POST';
      form.action = logoutUrl;
      const token = document.createElement('input');
      token.type = 'hidden';
      token.name = '_token';
      token.value = csrfToken;
      form.appendChild(token);
      document.body.appendChild(form);
      form.submit();
    }
  }, 100);
}
</script>

<style scoped>
/* === LAYOUT STRUCTURE === */
.manager-layout {
  @apply min-h-screen bg-gray-50 flex;
}

.main-content {
  @apply flex-1 flex flex-col transition-all duration-300 ease-in-out;
  margin-left: 280px;
}

.main-content.collapsed {
  margin-left: 80px;
}

.main-content.mobile {
  margin-left: 0;
}

/* === TOP BAR === */
.top-bar {
  @apply bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between shadow-sm sticky top-0 z-30;
}

.top-bar-left {
  @apply flex items-center gap-4;
}

.mobile-menu-btn {
  @apply lg:hidden flex items-center justify-center w-10 h-10 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-600 transition-colors;
}

.collapse-btn {
  @apply hidden lg:flex items-center justify-center w-10 h-10 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-600 transition-colors;
}

.page-info {
  @apply flex flex-col;
}

.page-title {
  @apply text-xl font-semibold text-gray-900 leading-tight;
}

.breadcrumb {
  @apply mt-1;
}

.breadcrumb-list {
  @apply flex items-center gap-1;
}

.breadcrumb-item {
  @apply flex items-center gap-1;
}

.breadcrumb-link {
  @apply text-sm text-gray-500 hover:text-gray-700 transition-colors;
  text-decoration: none;
}

.breadcrumb-current {
  @apply text-sm text-gray-900 font-medium;
}

.breadcrumb-separator {
  @apply text-gray-400 flex-shrink-0;
}

/* === TOP BAR RIGHT === */
.top-bar-right {
  @apply flex items-center gap-4;
}

/* Search */
.search-container {
  @apply hidden md:block;
}

.search-box {
  @apply relative flex items-center;
}

.search-input {
  @apply w-64 pl-10 pr-12 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent;
}

.search-icon {
  @apply absolute left-3 text-gray-400;
}

.search-kbd {
  @apply absolute right-3 px-2 py-0.5 text-xs bg-gray-200 text-gray-500 rounded border;
}

/* Notifications */
.notification-container {
  @apply relative;
}

.notification-btn {
  @apply relative flex items-center justify-center w-10 h-10 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-600 transition-colors;
}

.notification-badge {
  @apply absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center font-medium;
}

/* User Dropdown */
.user-dropdown {
  @apply relative;
}

.user-btn {
  @apply flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 transition-colors;
}

.user-avatar {
  @apply w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center;
}

.user-info {
  @apply flex flex-col items-start;
}

.user-name {
  @apply text-sm font-medium text-gray-900 leading-tight;
}

.user-role {
  @apply text-xs text-gray-500;
}

.dropdown-arrow {
  @apply text-gray-400 transition-transform duration-200;
}

.user-btn:hover .dropdown-arrow {
  @apply rotate-180;
}

.user-menu {
  @apply absolute right-0 top-12 w-56 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-40;
}

.user-menu-item {
  @apply flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 transition-colors;
  text-decoration: none;
}

.logout-item {
  @apply text-red-600 hover:bg-red-50;
}

.menu-divider {
  @apply h-px bg-gray-200 my-1;
}

.overlay {
  @apply fixed inset-0 z-10;
}

/* === CONTENT AREA === */
.content-area {
  @apply flex-1 overflow-auto;
}

.content-wrapper {
  @apply p-6 max-w-full;
}

/* === FOOTER === */
.footer {
  @apply bg-white border-t border-gray-200 px-6 py-4;
}

.footer-content {
  @apply flex items-center justify-between;
}

.footer-left {
  @apply flex items-center gap-4;
}

.copyright {
  @apply text-sm text-gray-600;
}

.version {
  @apply text-xs text-gray-400 bg-gray-100 px-2 py-1 rounded;
}

.footer-right {
  @apply flex items-center gap-4;
}

.footer-link {
  @apply text-sm text-gray-500 hover:text-gray-700 transition-colors;
  text-decoration: none;
}

/* === TRANSITIONS === */
.dropdown-enter-active,
.dropdown-leave-active {
  @apply transition-all duration-200 ease-out;
}

.dropdown-enter-from,
.dropdown-leave-to {
  @apply opacity-0 transform scale-95 translate-y-2;
}

.dropdown-enter-to,
.dropdown-leave-from {
  @apply opacity-100 transform scale-100 translate-y-0;
}

/* === RESPONSIVE ADJUSTMENTS === */
@media (max-width: 768px) {
  .main-content {
    margin-left: 0;
  }
  
  .top-bar {
    @apply px-4 py-3;
  }
  
  .page-title {
    @apply text-lg;
  }
  
  .content-wrapper {
    @apply p-4;
  }
  
  .footer-content {
    @apply flex-col gap-2;
  }
}

@media (max-width: 640px) {
  .search-input {
    @apply w-48;
  }
  
  .user-info {
    @apply hidden;
  }
  
  .footer-right {
    @apply gap-2;
  }
}

/* === FOCUS STYLES === */
.mobile-menu-btn:focus,
.collapse-btn:focus,
.notification-btn:focus,
.user-btn:focus {
  @apply outline-none ring-2 ring-blue-500 ring-offset-2;
}

/* === SCROLLBAR STYLING === */
.content-area::-webkit-scrollbar {
  @apply w-2;
}

.content-area::-webkit-scrollbar-track {
  @apply bg-gray-100;
}

.content-area::-webkit-scrollbar-thumb {
  @apply bg-gray-300 rounded-full;
}

.content-area::-webkit-scrollbar-thumb:hover {
  @apply bg-gray-400;
}
</style>