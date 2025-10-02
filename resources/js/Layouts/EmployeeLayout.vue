<template>
  <div class="min-h-screen flex flex-col md:flex-row bg-gray-50">
    <!-- Mobile Overlay -->
    <Transition name="overlay">
      <div 
        v-if="isSidebarOpen && !isDesktop" 
        class="mobile-overlay"
        @click="toggleSidebar"
      ></div>
    </Transition>

    <!-- Modern Sidebar -->
    <Transition name="slide">
      <aside
        v-show="isSidebarOpen || isDesktop"
        class="modern-sidebar"
        :class="{ 'mobile-open': isSidebarOpen && !isDesktop }"
      >
        <!-- Sidebar Header -->
        <div class="sidebar-header">
          <div class="brand">
            <div class="brand-icon">
              <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"></path>
                <circle cx="12" cy="7" r="4"></circle>
              </svg>
            </div>
            <div class="brand-text">
              <h2>Employee Portal</h2>
              <span>HRMS System</span>
            </div>
          </div>
          <button class="close-btn md:hidden" @click="toggleSidebar">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <line x1="18" y1="6" x2="6" y2="18"></line>
              <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
          </button>
        </div>

        <!-- Navigation -->
        <nav class="sidebar-nav">
          <!-- Main Section -->
          <div class="nav-section">
            <h3 class="nav-section-title">Main</h3>
            <ul class="nav-list">
              <li>
                <Link 
                  :href="route('employee.dashboard')" 
                  class="nav-link" 
                  :class="{ active: isActive('/employee/dashboard') }"
                  @click="handleNavClick"
                >
                  <span class="nav-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <rect x="3" y="3" width="7" height="9"></rect>
                      <rect x="14" y="3" width="7" height="5"></rect>
                      <rect x="14" y="12" width="7" height="9"></rect>
                      <rect x="3" y="16" width="7" height="5"></rect>
                    </svg>
                  </span>
                  <span class="nav-text">Dashboard</span>
                  <span class="active-indicator"></span>
                </Link>
              </li>
              <li>
                <Link 
                  :href="route('employee.punches.index')" 
                  class="nav-link" 
                  :class="{ active: isActive('/employee/punches') }"
                  @click="handleNavClick"
                >
                  <span class="nav-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <circle cx="12" cy="12" r="10"></circle>
                      <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                  </span>
                  <span class="nav-text">Punch In / Out</span>
                  <span class="active-indicator"></span>
                </Link>
              </li>
              <li>
                <Link 
                  :href="route('employee.tasks.index')" 
                  class="nav-link" 
                  :class="{ active: isActive('/employee/tasks') }"
                  @click="handleNavClick"
                >
                  <span class="nav-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <polyline points="9 11 12 14 22 4"></polyline>
                      <path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"></path>
                    </svg>
                  </span>
                  <span class="nav-text">Daily Tasks</span>
                  <span class="active-indicator"></span>
                </Link>
              </li>
            </ul>
          </div>

          <!-- Leave & Attendance Section -->
          <div class="nav-section">
            <h3 class="nav-section-title">Leave & Attendance</h3>
            <ul class="nav-list">
              <li>
                <Link 
                  :href="route('employee.attendance.index')" 
                  class="nav-link" 
                  :class="{ active: isActive('/employee/attendance') }"
                  @click="handleNavClick"
                >
                  <span class="nav-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                      <line x1="16" y1="2" x2="16" y2="6"></line>
                      <line x1="8" y1="2" x2="8" y2="6"></line>
                      <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                  </span>
                  <span class="nav-text">Attendance</span>
                  <span class="active-indicator"></span>
                </Link>
              </li>
              <li>
                <Link 
                  :href="route('employee.leave-applications.index')" 
                  class="nav-link" 
                  :class="{ active: isActive('/employee/leave-applications') }"
                  @click="handleNavClick"
                >
                  <span class="nav-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"></path>
                      <polyline points="14 2 14 8 20 8"></polyline>
                      <line x1="16" y1="13" x2="8" y2="13"></line>
                      <line x1="16" y1="17" x2="8" y2="17"></line>
                      <polyline points="10 9 9 9 8 9"></polyline>
                    </svg>
                  </span>
                  <span class="nav-text">Leave Applications</span>
                  <span class="active-indicator"></span>
                </Link>
              </li>
              <li>
                <Link 
                  :href="route('employee.leave-summary.index')" 
                  class="nav-link" 
                  :class="{ active: isActive('/employee/leave-summary') }"
                  @click="handleNavClick"
                >
                  <span class="nav-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <line x1="18" y1="20" x2="18" y2="10"></line>
                      <line x1="12" y1="20" x2="12" y2="4"></line>
                      <line x1="6" y1="20" x2="6" y2="14"></line>
                    </svg>
                  </span>
                  <span class="nav-text">Leave Summary</span>
                  <span class="active-indicator"></span>
                </Link>
              </li>
            </ul>
          </div>

          <!-- Company Info Section -->
          <div class="nav-section">
            <h3 class="nav-section-title">Company Info</h3>
            <ul class="nav-list">
              <li>
                <Link
                  :href="route('employee.holidays.index')"
                  class="nav-link"
                  :class="{ active: isActive('/employee/holidays') }"
                  @click="handleNavClick"
                >
                  <span class="nav-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"></path>
                      <line x1="7" y1="7" x2="7.01" y2="7"></line>
                    </svg>
                  </span>
                  <span class="nav-text">Holidays</span>
                  <span class="active-indicator"></span>
                </Link>
              </li>
              <li>
                <Link
                  :href="route('employee.policies.index')"
                  class="nav-link"
                  :class="{ active: isActive('/employee/policies') }"
                  @click="handleNavClick"
                >
                  <span class="nav-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                      <polyline points="14 2 14 8 20 8"></polyline>
                      <line x1="16" y1="13" x2="8" y2="13"></line>
                      <line x1="16" y1="17" x2="8" y2="17"></line>
                      <polyline points="10 9 9 9 8 9"></polyline>
                    </svg>
                  </span>
                  <span class="nav-text">Policies</span>
                  <span class="active-indicator"></span>
                </Link>
              </li>
              <li>
                <Link
                  :href="route('employee.kra.index')"
                  class="nav-link"
                  :class="{ active: isActive('/employee/kra') }"
                  @click="handleNavClick"
                >
                  <span class="nav-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                  </span>
                  <span class="nav-text">KRA</span>
                  <span class="active-indicator"></span>
                </Link>
              </li>
            </ul>
          </div>
        </nav>

        <!-- User Profile Footer -->
        <div class="sidebar-footer">
          <div class="user-profile">
            <div class="user-avatar">
              <span>{{ getUserInitials() }}</span>
            </div>
            <div class="user-info">
              <p class="user-name">{{ getUserName() }}</p>
              <p class="user-role">Employee Account</p>
            </div>
          </div>
        </div>
      </aside>
    </Transition>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-h-screen">
      <!-- Modern Topbar -->
      <header class="topbar">
        <div class="topbar-left">
          <button class="menu-toggle" @click="toggleSidebar">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <line x1="3" y1="12" x2="21" y2="12"></line>
              <line x1="3" y1="6" x2="21" y2="6"></line>
              <line x1="3" y1="18" x2="21" y2="18"></line>
            </svg>
          </button>
          <div class="page-title">
            <h1>{{ getPageTitle() }}</h1>
          </div>
        </div>

        <div class="topbar-right">
          <!-- User Info -->
          <div class="user-badge">
            <div class="user-avatar-small">
              {{ getUserInitials() }}
            </div>
            <span class="user-name-text">{{ getUserName() }}</span>
          </div>

          <!-- Logout Button -->
          <Link 
            href="/logout" 
            method="post" 
            as="button" 
            class="logout-btn"
          >
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"></path>
              <polyline points="16 17 21 12 16 7"></polyline>
              <line x1="21" y1="12" x2="9" y2="12"></line>
            </svg>
            <span>Logout</span>
          </Link>
        </div>
      </header>

      <!-- Page Content -->
      <main class="main-content">
        <slot />
      </main>

      <!-- Success Flash Message -->
      <Transition name="fade">
        <div v-if="showFlash" class="flash-message">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="20 6 9 17 4 12"></polyline>
          </svg>
          <span>{{ flash.success }}</span>
        </div>
      </Transition>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import { usePage, Link } from '@inertiajs/vue3'
import { route } from 'ziggy-js'

const page = usePage()
const flash = ref(page.props.flash || {})
const auth = ref(page.props.auth || {})

const isSidebarOpen = ref(false)
const isDesktop = ref(false)
const showFlash = ref(false)

onMounted(() => {
  // Initialize desktop check
  isDesktop.value = window.innerWidth >= 768
  
  // Initialize flash message
  if (flash.value && flash.value.success) {
    showFlash.value = true
    setTimeout(() => {
      showFlash.value = false
    }, 3000)
  }
  
  // Add resize listener
  const handleResize = () => {
    isDesktop.value = window.innerWidth >= 768
    if (isDesktop.value) {
      isSidebarOpen.value = false
    }
  }
  
  window.addEventListener('resize', handleResize)
  
  // Cleanup
  onBeforeUnmount(() => {
    window.removeEventListener('resize', handleResize)
  })
})

const toggleSidebar = () => {
  isSidebarOpen.value = !isSidebarOpen.value
}

const handleNavClick = () => {
  if (!isDesktop.value) {
    isSidebarOpen.value = false
  }
}

const isActive = (url) => {
  if (typeof window === 'undefined') return false
  return window.location.pathname.startsWith(url)
}

const getPageTitle = () => {
  if (typeof window === 'undefined') return 'Employee Portal'
  const path = window.location.pathname
  if (path.includes('/employee/dashboard')) return 'Dashboard'
  if (path.includes('/employee/punches')) return 'Punch In / Out'
  if (path.includes('/employee/tasks')) return 'Daily Tasks'
  if (path.includes('/employee/attendance')) return 'Attendance'
  if (path.includes('/employee/leave-applications')) return 'Leave Applications'
  if (path.includes('/employee/leave-summary')) return 'Leave Summary'
  if (path.includes('/employee/holidays')) return 'Holidays'
  if (path.includes('/employee/policies')) return 'Company Policies'
  if (path.includes('/employee/kra')) return 'Key Result Areas (KRA)'
  return 'Employee Portal'
}

const getUserName = () => {
  return auth.value?.user?.first_name || auth.value?.user?.name || 'Employee'
}

const getUserInitials = () => {
  const name = auth.value?.user?.first_name || auth.value?.user?.name || 'E'
  const lastName = auth.value?.user?.last_name || 'P'
  return (name.charAt(0) + (lastName.charAt(0) || '')).toUpperCase()
}
</script>

<style scoped>
/* Mobile Overlay */
.mobile-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.5);
  backdrop-filter: blur(4px);
  z-index: 998;
}

/* Modern Sidebar - Navy Blue Theme */
.modern-sidebar {
  position: fixed;
  left: 0;
  top: 0;
  height: 100vh;
  width: 280px;
  background: linear-gradient(180deg, #1e3a8a 0%, #1e40af 50%, #1d4ed8 100%);
  color: white;
  display: flex;
  flex-direction: column;
  z-index: 999;
  box-shadow: 4px 0 24px rgba(0, 0, 0, 0.15);
  transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

@media (max-width: 768px) {
  .modern-sidebar {
    transform: translateX(-100%);
  }
  
  .modern-sidebar.mobile-open {
    transform: translateX(0);
  }
}

@media (min-width: 769px) {
  .modern-sidebar {
    position: relative;
    transform: translateX(0);
  }
}

/* Sidebar Header */
.sidebar-header {
  padding: 1.5rem;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.brand {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.brand-icon {
  width: 48px;
  height: 48px;
  background: rgba(255, 255, 255, 0.15);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.brand-text h2 {
  font-size: 1.25rem;
  font-weight: 700;
  margin: 0;
  line-height: 1.2;
}

.brand-text span {
  font-size: 0.75rem;
  opacity: 0.8;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.close-btn {
  background: rgba(255, 255, 255, 0.1);
  border: none;
  color: white;
  width: 36px;
  height: 36px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: background 0.2s;
}

.close-btn:hover {
  background: rgba(255, 255, 255, 0.2);
}

/* Sidebar Navigation */
.sidebar-nav {
  flex: 1;
  overflow-y: auto;
  padding: 1.5rem 0;
}

.sidebar-nav::-webkit-scrollbar {
  width: 4px;
}

.sidebar-nav::-webkit-scrollbar-track {
  background: transparent;
}

.sidebar-nav::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.2);
  border-radius: 4px;
}

.nav-section {
  margin-bottom: 2rem;
  padding: 0 1rem;
}

.nav-section-title {
  font-size: 0.6875rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  opacity: 0.6;
  margin-bottom: 0.75rem;
  padding: 0 0.75rem;
}

.nav-list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.nav-link {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.875rem 0.75rem;
  border-radius: 12px;
  color: rgba(255, 255, 255, 0.85);
  text-decoration: none;
  transition: all 0.2s ease;
  margin-bottom: 0.25rem;
  position: relative;
  font-weight: 500;
  font-size: 0.9375rem;
}

.nav-link:hover {
  background: rgba(255, 255, 255, 0.1);
  color: white;
  transform: translateX(4px);
}

.nav-link.active {
  background: rgba(255, 255, 255, 0.15);
  color: white;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.nav-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 20px;
  height: 20px;
  flex-shrink: 0;
}

.nav-text {
  flex: 1;
}

.active-indicator {
  width: 6px;
  height: 6px;
  background: white;
  border-radius: 50%;
  opacity: 0;
  transition: opacity 0.2s;
}

.nav-link.active .active-indicator {
  opacity: 1;
}

/* Sidebar Footer */
.sidebar-footer {
  padding: 1.5rem;
  border-top: 1px solid rgba(255, 255, 255, 0.1);
  background: rgba(0, 0, 0, 0.1);
}

.user-profile {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.user-avatar {
  width: 42px;
  height: 42px;
  background: rgba(255, 255, 255, 0.2);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  font-size: 0.875rem;
  flex-shrink: 0;
}

.user-info {
  flex: 1;
  min-width: 0;
}

.user-name {
  font-weight: 600;
  font-size: 0.9375rem;
  margin: 0;
  line-height: 1.3;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.user-role {
  font-size: 0.75rem;
  opacity: 0.7;
  margin: 0;
}

/* Main Content Area */
.flex-1 {
  flex: 1;
  display: flex;
  flex-direction: column;
}

/* Modern Topbar */
.topbar {
  background: white;
  border-bottom: 1px solid #e5e7eb;
  padding: 1rem 1.5rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  position: sticky;
  top: 0;
  z-index: 99;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.topbar-left {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.menu-toggle {
  background: #f3f4f6;
  border: none;
  width: 40px;
  height: 40px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  color: #1e3a8a;
  transition: all 0.2s;
}

.menu-toggle:hover {
  background: #e5e7eb;
  color: #1e40af;
}

.page-title h1 {
  font-size: 1.5rem;
  font-weight: 700;
  color: #111827;
  margin: 0;
}

.topbar-right {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.user-badge {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1rem;
  background: #f3f4f6;
  border-radius: 12px;
}

.user-avatar-small {
  width: 32px;
  height: 32px;
  background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
  color: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  font-size: 0.75rem;
}

.user-name-text {
  font-weight: 500;
  color: #374151;
  font-size: 0.875rem;
}

.logout-btn {
  background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
  border: none;
  padding: 0.625rem 1.25rem;
  border-radius: 10px;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: white;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  font-size: 0.875rem;
}

.logout-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
}

/* Main Content */
.main-content {
  flex: 1;
  padding: 1.5rem;
  background: #f9fafb;
}

/* Flash Message */
.flash-message {
  position: fixed;
  bottom: 2rem;
  right: 2rem;
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  color: white;
  padding: 1rem 1.5rem;
  border-radius: 12px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
  display: flex;
  align-items: center;
  gap: 0.75rem;
  z-index: 1000;
  font-weight: 500;
}

/* Transitions */
.overlay-enter-active,
.overlay-leave-active {
  transition: opacity 0.3s;
}

.overlay-enter-from,
.overlay-leave-to {
  opacity: 0;
}

.slide-enter-active,
.slide-leave-active {
  transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.slide-enter-from,
.slide-leave-to {
  transform: translateX(-100%);
}

.fade-enter-active,
.fade-leave-active {
  transition: all 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: translateY(1rem);
}

/* Responsive Adjustments */
@media (max-width: 768px) {
  .page-title h1 {
    font-size: 1.25rem;
  }
  
  .user-name-text {
    display: none;
  }
  
  .user-badge {
    padding: 0.5rem;
  }
  
  .logout-btn span {
    display: none;
  }
  
  .logout-btn {
    padding: 0.625rem;
    width: 40px;
    height: 40px;
    justify-content: center;
  }
  
  .main-content {
    padding: 1rem;
  }
  
  .flash-message {
    bottom: 1rem;
    right: 1rem;
    left: 1rem;
  }
}

@media (max-width: 640px) {
  .topbar {
    padding: 0.75rem 1rem;
  }
  
  .page-title h1 {
    font-size: 1.125rem;
  }
}
</style>