<template>
  <!-- DESKTOP: Modern fixed sidebar -->
  <aside v-if="!isMobile" :class="['modern-sidebar', { 'collapsed': collapsed }]">
    <!-- Logo/Brand Section -->
    <div class="sidebar-header">
      <div class="brand-container">
        <div class="brand-icon">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x="3" y="3" width="18" height="18" rx="4" fill="url(#gradient1)"/>
            <path d="M9 12l2 2 4-4" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <defs>
              <linearGradient id="gradient1" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" style="stop-color:#3B82F6"/>
                <stop offset="100%" style="stop-color:#1E40AF"/>
              </linearGradient>
            </defs>
          </svg>
        </div>
        <div class="brand-text" v-if="!collapsed">
          <h2 class="brand-title">HRMS</h2>
          <span class="brand-subtitle">Manager Portal</span>
        </div>
      </div>
    </div>

    <!-- Navigation Menu -->
    <nav class="sidebar-nav">
      <div class="nav-content">
        <template v-for="(section, sidx) in sections" :key="sidx">
          <!-- Section Header -->
          <div v-if="!collapsed" class="section-header">
            {{ section.title }}
          </div>

          <!-- Menu Items -->
          <ul class="menu-list">
            <li v-for="item in section.items" :key="item.name" class="menu-item">
              <template v-if="safeRoute(item.route)">
                <Link 
                  :href="safeRoute(item.route)"
                  :class="['menu-link', { 'active': isActive(item) }]"
                  @click="onLinkClick"
                >
                  <div class="menu-icon" v-html="item.svg"></div>
                  <span v-if="!collapsed" class="menu-text">{{ item.name }}</span>
                  <div v-if="isActive(item)" class="active-indicator"></div>
                </Link>
              </template>
              <template v-else>
                <div class="menu-link disabled">
                  <div class="menu-icon" v-html="item.svg"></div>
                  <span v-if="!collapsed" class="menu-text">{{ item.name }}</span>
                </div>
              </template>
            </li>
          </ul>

          <!-- Section Divider -->
          <div v-if="sidx < sections.length - 1 && !collapsed" class="section-divider"></div>
        </template>
      </div>
    </nav>

    <!-- Sidebar Footer -->
    <div class="sidebar-footer">
      <div class="footer-content">
        <div v-if="!collapsed" class="company-info">
          <div class="company-name">Your Company</div>
          <div class="company-role">Manager Dashboard</div>
        </div>
        <button 
          class="collapse-btn" 
          @click="$emit('toggle')"
          :title="collapsed ? 'Expand sidebar' : 'Collapse sidebar'"
        >
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path v-if="collapsed" d="M9 18l6-6-6-6" stroke-linecap="round" stroke-linejoin="round"/>
            <path v-else d="M15 18l-6-6 6-6" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </button>
      </div>
    </div>
  </aside>

  <!-- MOBILE: Sliding drawer with backdrop -->
  <Teleport to="body" v-if="isMobile">
    <Transition name="mobile-overlay">
      <div v-if="mobileOpen" class="mobile-overlay" @click.self="closeMobile">
        <Transition name="mobile-drawer">
          <div v-if="mobileOpen" class="mobile-drawer" role="dialog" aria-modal="true">
            <!-- Mobile Header -->
            <div class="mobile-header">
              <div class="mobile-brand">
                <div class="brand-icon">
                  <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect x="3" y="3" width="18" height="18" rx="4" fill="url(#gradient2)"/>
                    <path d="M9 12l2 2 4-4" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <defs>
                      <linearGradient id="gradient2" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" style="stop-color:#3B82F6"/>
                        <stop offset="100%" style="stop-color:#1E40AF"/>
                      </linearGradient>
                    </defs>
                  </svg>
                </div>
                <div class="mobile-brand-text">
                  <h2 class="mobile-title">HRMS Manager</h2>
                  <span class="mobile-subtitle">Dashboard Portal</span>
                </div>
              </div>
              <button class="mobile-close" @click="closeMobile" aria-label="Close menu">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M18 6L6 18M6 6l12 12" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </button>
            </div>

            <!-- Mobile Navigation -->
            <div class="mobile-nav-container">
              <nav class="mobile-nav">
                <template v-for="(section, sidx) in sections" :key="sidx">
                  <div class="mobile-section-header">{{ section.title }}</div>
                  
                  <ul class="mobile-menu-list">
                    <li v-for="item in section.items" :key="item.name">
                      <template v-if="safeRoute(item.route)">
                        <Link 
                          :href="safeRoute(item.route)"
                          :class="['mobile-menu-link', { 'active': isActive(item) }]"
                          @click="closeAndNavigate"
                        >
                          <div class="mobile-menu-icon" v-html="item.svg"></div>
                          <span class="mobile-menu-text">{{ item.name }}</span>
                          <svg v-if="isActive(item)" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path d="M9 12l2 2 4-4" stroke-linecap="round" stroke-linejoin="round"/>
                          </svg>
                        </Link>
                      </template>
                      <template v-else>
                        <div class="mobile-menu-link disabled">
                          <div class="mobile-menu-icon" v-html="item.svg"></div>
                          <span class="mobile-menu-text">{{ item.name }}</span>
                        </div>
                      </template>
                    </li>
                  </ul>
                </template>
              </nav>
            </div>

            <!-- Mobile Footer with Logout -->
            <div class="mobile-footer">
              <div class="mobile-user-section">
                <div class="mobile-user-info">
                  <div class="user-avatar">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                      <circle cx="12" cy="7" r="4"/>
                    </svg>
                  </div>
                  <div class="user-details">
                    <div class="user-name">Manager</div>
                    <div class="user-role">Administrator</div>
                  </div>
                </div>
                <form ref="logoutForm" :action="logoutUrl" method="POST" class="logout-form">
                  <input type="hidden" name="_token" :value="csrfToken" />
                  <button type="button" class="mobile-logout-btn" @click.prevent="doLogout">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                      <polyline points="16,17 21,12 16,7"/>
                      <line x1="21" y1="12" x2="9" y2="12"/>
                    </svg>
                    Logout
                  </button>
                </form>
              </div>
            </div>
          </div>
        </Transition>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { route as ziggyRoute } from 'ziggy-js';
import { ref, computed } from 'vue';

/* Props & events */
const props = defineProps({
  collapsed: { type: Boolean, default: false },
  mobileOpen: { type: Boolean, default: false },
  isMobile: { type: Boolean, default: false },
});
const emit = defineEmits(['toggle', 'close-mobile', 'open-mobile']);

/* Inertia page (reactive) */
const page = usePage();

/* CSRF and logout */
const csrfToken = (typeof document !== 'undefined') ? document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '' : '';
let logoutUrl = '/logout';
try { logoutUrl = ziggyRoute('logout') || logoutUrl; } catch (e) {}

/* logout form ref */
const logoutForm = ref(null);

/* Enhanced menu sections with better icons */
const sections = [
  {
    title: 'MAIN',
    items: [
      { 
        name: 'Dashboard', 
        route: 'manager.dashboard', 
        svg: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>' 
      },
      { 
        name: 'Employees', 
        route: 'manager.employees.index', 
        svg: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>' 
      }
    ]
  },
  {
  title: 'Shift Management',
  items: [
    { 
      name: 'View Shifts', 
      route: 'manager.shifts.index',
      svg: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/></svg>'
    },
     { 
      name: 'Assign Shifts', 
      route: 'manager.employees.shifts.index',
      svg: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/><path d="M22 12h-6"/><path d="M19 9v6"/></svg>'
    },
  ]
},
  {
    title: 'HR MANAGEMENT',
    items: [
      { 
        name: 'Leaves', 
        route: 'manager.leaves.index', 
        svg: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>' 
      },
      { 
        name: 'Attendance', 
        route: 'manager.attendance.index', 
        svg: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/></svg>' 
      }
    ]
  }
];

/* reactive current route name */
const currentRouteName = computed(() => {
  try {
    return page.props.value?.routeName ?? page.props.value?.currentRouteName ?? null;
  } catch (e) {
    return null;
  }
});

/* helper: resolve named route (safe) */
function safeRoute(name) {
  if (!name) return null;
  try { return ziggyRoute(name); } catch (e) { return null; }
}

/* Enhanced active detection */
function activeByName(itemRoute) {
  if (!itemRoute) return false;
  const current = currentRouteName.value;
  if (!current) return false;
  return current === itemRoute || current.startsWith(itemRoute + '.');
}

function activeByPath(itemRoute) {
  const href = safeRoute(itemRoute);
  if (!href) return false;
  try {
    const target = new URL(href, window.location.origin).pathname.replace(/\/$/, '');
    const current = window.location.pathname.replace(/\/$/, '');
    return current === target || current.startsWith(target + '/');
  } catch (e) {
    return false;
  }
}

function isActive(item) {
  return activeByName(item.route) || activeByPath(item.route);
}

/* Event handlers */
function onLinkClick() {
  // Desktop link click - no special handling needed
}

function closeAndNavigate() {
  emit('close-mobile');
}

function closeMobile() {
  emit('close-mobile');
}

/* Enhanced logout with better UX */
function doLogout() {
  emit('close-mobile');
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
  }, 200);
}
</script>

<style scoped>
/* === DESKTOP SIDEBAR STYLES === */
.modern-sidebar {
  position: fixed;
  left: 0;
  top: 0;
  height: 100vh;
  background-color: white;
  box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
  border-right: 1px solid rgb(243 244 246);
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  z-index: 40;
  width: 280px;
  min-width: 280px;
}

.modern-sidebar.collapsed {
  width: 80px;
  min-width: 80px;
}

/* Sidebar Header */
.sidebar-header {
  padding: 1.5rem;
  border-bottom: 1px solid rgb(243 244 246);
}

.brand-container {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.brand-icon {
  flex-shrink: 0;
}

.brand-text {
  display: flex;
  flex-direction: column;
}

.brand-title {
  font-size: 1.25rem;
  line-height: 1.75rem;
  font-weight: 700;
  color: rgb(17 24 39);
  line-height: 1.25;
}

.brand-subtitle {
  font-size: 0.875rem;
  line-height: 1.25rem;
  color: rgb(107 114 128);
  font-weight: 500;
}

/* Navigation */
.sidebar-nav {
  flex: 1 1 auto;
  overflow-y: auto;
  padding: 1rem 0;
}

.nav-content {
  padding: 0 1rem;
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.section-header {
  padding: 0.5rem 0.75rem;
  font-size: 0.75rem;
  line-height: 1rem;
  font-weight: 600;
  color: rgb(107 114 128);
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.menu-list {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.menu-item {
  position: relative;
}

.menu-link {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.625rem 0.75rem;
  border-radius: 0.75rem;
  color: rgb(55 65 81);
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
  position: relative;
  text-decoration: none;
}

.menu-link:hover {
  background-color: rgb(249 250 251);
  color: rgb(17 24 39);
}

.menu-link.active {
  background-color: rgb(239 246 255);
  color: rgb(29 78 216);
  box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
}

.menu-link.disabled {
  opacity: 0.4;
  cursor: not-allowed;
}

.menu-icon {
  flex-shrink: 0;
  width: 1.25rem;
  height: 1.25rem;
}

.menu-icon svg {
  width: 100%;
  height: 100%;
}

.menu-link.active .menu-icon svg {
  stroke: rgb(37 99 235);
}

.menu-text {
  font-weight: 500;
  font-size: 0.875rem;
  line-height: 1.25rem;
}

.active-indicator {
  position: absolute;
  right: 0.5rem;
  width: 0.5rem;
  height: 0.5rem;
  background-color: rgb(37 99 235);
  border-radius: 9999px;
}

.section-divider {
  height: 1px;
  background-color: rgb(229 231 235);
  margin: 1rem 0;
}

/* Sidebar Footer */
.sidebar-footer {
  border-top: 1px solid rgb(243 244 246);
  padding: 1rem;
}

.footer-content {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.company-info {
  display: flex;
  flex-direction: column;
}

.company-name {
  font-size: 0.875rem;
  line-height: 1.25rem;
  font-weight: 600;
  color: rgb(17 24 39);
}

.company-role {
  font-size: 0.75rem;
  line-height: 1rem;
  color: rgb(107 114 128);
}

.collapse-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 2rem;
  height: 2rem;
  border-radius: 0.5rem;
  background-color: rgb(243 244 246);
  color: rgb(107 114 128);
  border: none;
  cursor: pointer;
  transition: background-color 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

.collapse-btn:hover {
  background-color: rgb(229 231 235);
}

/* === MOBILE STYLES === */
.mobile-overlay {
  position: fixed;
  inset: 0;
  background-color: rgb(0 0 0 / 0.5);
  backdrop-filter: blur(4px);
  z-index: 50;
  display: flex;
}

.mobile-drawer {
  width: 20rem;
  height: 100%;
  background-color: white;
  box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
  display: flex;
  flex-direction: column;
}

/* Mobile Header */
.mobile-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1.5rem;
  border-bottom: 1px solid rgb(243 244 246);
  background: linear-gradient(to right, rgb(37 99 235), rgb(29 78 216));
}

.mobile-brand {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.mobile-brand-text {
  display: flex;
  flex-direction: column;
}

.mobile-title {
  font-size: 1.125rem;
  line-height: 1.75rem;
  font-weight: 700;
  color: white;
}

.mobile-subtitle {
  font-size: 0.875rem;
  line-height: 1.25rem;
  color: rgb(191 219 254);
}

.mobile-close {
  padding: 0.5rem;
  border-radius: 0.5rem;
  background-color: rgb(255 255 255 / 0.1);
  color: white;
  border: none;
  cursor: pointer;
  transition: background-color 0.2s;
}

.mobile-close:hover {
  background-color: rgb(255 255 255 / 0.2);
}

/* Mobile Navigation */
.mobile-nav-container {
  flex: 1 1 auto;
  overflow-y: auto;
  padding: 1rem;
}

.mobile-nav {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.mobile-section-header {
  font-size: 0.75rem;
  line-height: 1rem;
  font-weight: 600;
  color: rgb(107 114 128);
  text-transform: uppercase;
  letter-spacing: 0.05em;
  padding: 0 0.75rem;
}

.mobile-menu-list {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.mobile-menu-link {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 0.75rem;
  padding: 1rem;
  border-radius: 0.75rem;
  color: rgb(55 65 81);
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
  text-decoration: none;
}

.mobile-menu-link:hover {
  background-color: rgb(249 250 251);
}

.mobile-menu-link.active {
  background-color: rgb(239 246 255);
  color: rgb(29 78 216);
}

.mobile-menu-link.disabled {
  opacity: 0.4;
}

.mobile-menu-icon {
  flex-shrink: 0;
  width: 1.25rem;
  height: 1.25rem;
}

.mobile-menu-icon svg {
  width: 100%;
  height: 100%;
}

.mobile-menu-link.active .mobile-menu-icon svg {
  stroke: rgb(37 99 235);
}

.mobile-menu-text {
  flex: 1 1 auto;
  font-weight: 500;
}

/* Mobile Footer */
.mobile-footer {
  border-top: 1px solid rgb(243 244 246);
  padding: 1rem;
  background-color: rgb(249 250 251);
}

.mobile-user-section {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
}

.mobile-user-info {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.user-avatar {
  width: 2.5rem;
  height: 2.5rem;
  border-radius: 9999px;
  background-color: rgb(229 231 235);
  display: flex;
  align-items: center;
  justify-content: center;
}

.user-details {
  display: flex;
  flex-direction: column;
}

.user-name {
  font-size: 0.875rem;
  line-height: 1.25rem;
  font-weight: 600;
  color: rgb(17 24 39);
}

.user-role {
  font-size: 0.75rem;
  line-height: 1rem;
  color: rgb(107 114 128);
}

.logout-form {
  display: flex;
}

.mobile-logout-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1rem;
  background-color: rgb(254 242 242);
  color: rgb(185 28 28);
  font-weight: 500;
  font-size: 0.875rem;
  line-height: 1.25rem;
  border-radius: 0.5rem;
  border: none;
  cursor: pointer;
  transition: background-color 0.2s;
}

.mobile-logout-btn:hover {
  background-color: rgb(254 226 226);
}

/* === TRANSITIONS === */
.mobile-overlay-enter-active,
.mobile-overlay-leave-active {
  transition: opacity 0.3s;
}

.mobile-overlay-enter-from,
.mobile-overlay-leave-to {
  opacity: 0;
}

.mobile-drawer-enter-active,
.mobile-drawer-leave-active {
  transition: transform 0.3s ease-out;
}

.mobile-drawer-enter-from,
.mobile-drawer-leave-to {
  transform: translateX(-100%);
}

/* === HOVER EFFECTS === */
.menu-link:hover .menu-icon svg {
  stroke: currentColor;
}

.mobile-menu-link:hover .mobile-menu-icon svg {
  stroke: currentColor;
}

/* === COLLAPSED SIDEBAR ADJUSTMENTS === */
.modern-sidebar.collapsed .section-header,
.modern-sidebar.collapsed .menu-text,
.modern-sidebar.collapsed .company-info {
  display: none;
}

.modern-sidebar.collapsed .menu-link {
  justify-content: center;
}

.modern-sidebar.collapsed .footer-content {
  justify-content: center;
}

.modern-sidebar.collapsed .brand-text {
  display: none;
}

/* === SCROLLBAR STYLING === */
.sidebar-nav::-webkit-scrollbar,
.mobile-nav-container::-webkit-scrollbar {
  width: 0.25rem;
}

.sidebar-nav::-webkit-scrollbar-track,
.mobile-nav-container::-webkit-scrollbar-track {
  background: transparent;
}

.sidebar-nav::-webkit-scrollbar-thumb,
.mobile-nav-container::-webkit-scrollbar-thumb {
  background-color: rgb(209 213 219);
  border-radius: 9999px;
}

.sidebar-nav::-webkit-scrollbar-thumb:hover,
.mobile-nav-container::-webkit-scrollbar-thumb:hover {
  background-color: rgb(156 163 175);
}
</style>
