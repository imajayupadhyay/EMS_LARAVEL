<template>
  <div class="admin-layout">
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
              <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect x="3" y="3" width="18" height="18" rx="4" fill="white" fill-opacity="0.9"/>
                <path d="M12 8v4m0 0v4m0-4h4m-4 0H8" stroke="#8b5cf6" stroke-width="2" stroke-linecap="round"/>
              </svg>
            </div>
            <div class="brand-text">
              <h2>Admin Portal</h2>
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
          <template v-for="section in navSections" :key="section.label">
            <div class="nav-section">
              <h3 class="nav-section-title">{{ section.label }}</h3>
              <ul class="nav-list">
                <li v-for="item in section.links" :key="item.name">
                  <Link 
                    :href="item.href" 
                    class="nav-link" 
                    :class="{ active: route().current(item.route) }"
                    @click="handleNavClick"
                  >
                    <span class="nav-icon">{{ item.icon }}</span>
                    <span class="nav-text">{{ item.name }}</span>
                    <span class="active-indicator"></span>
                  </Link>
                </li>
              </ul>
            </div>
          </template>
        </nav>

        <!-- Sidebar Footer -->
        <div class="sidebar-footer">
          <div class="user-profile">
            <div class="user-avatar">{{ getUserInitials() }}</div>
            <div class="user-info">
              <p class="user-name">{{ auth?.user?.name || 'Admin' }}</p>
              <p class="user-role">Administrator</p>
            </div>
          </div>
        </div>
      </aside>
    </Transition>

    <!-- Main Content Area -->
    <div class="main-content">
      <!-- Modern Topbar -->
      <header class="topbar">
        <div class="topbar-left">
          <button class="menu-toggle md:hidden" @click="toggleSidebar">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <line x1="4" y1="12" x2="20" y2="12"/>
              <line x1="4" y1="6" x2="20" y2="6"/>
              <line x1="4" y1="18" x2="20" y2="18"/>
            </svg>
          </button>
          <div class="page-title">
            <h1>{{ pageTitle }}</h1>
          </div>
        </div>

        <div class="topbar-right">
          <!-- Notifications -->
          <div class="notification-wrapper" ref="notificationRef">
            <button @click="toggleNotifications" class="notification-btn">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
              </svg>
              <span v-if="unreadCount > 0" class="notification-badge">{{ unreadCount }}</span>
            </button>

            <Transition name="dropdown">
              <div v-if="showNotifications" class="notification-dropdown">
                <div class="dropdown-header">
                  <h3>Notifications</h3>
                  <div class="header-actions">
                    <button @click="markAllAsRead" :disabled="markingRead || unreadCount === 0" class="mark-read-btn">
                      Mark all as read
                    </button>
                    <button @click="clearAllNotifications" :disabled="notifications.length === 0" class="clear-all-btn">
                      Clear all
                    </button>
                  </div>
                </div>
                <div class="dropdown-body">
                  <div v-if="loadingNotifications" class="loading-state">
                    <div class="spinner"></div>
                    <p>Loading...</p>
                  </div>
                  <div v-else-if="notifications.length" class="notification-list">
                    <div v-for="note in notifications" :key="note.id" class="notification-item" :class="{ 'unread': !note.is_read }">
                      <div class="notification-content">
                        <p class="notification-title">{{ note.title }}</p>
                        <p class="notification-message" v-if="note.message">{{ note.message }}</p>
                        <p class="notification-time">{{ note.created_at }}</p>
                      </div>
                      <div class="notification-actions">
                        <button v-if="!note.is_read" @click="markSingleAsRead(note.id)" class="action-btn mark-read">
                          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20 6 9 17 4 12"/>
                          </svg>
                        </button>
                        <button @click="deleteNotification(note.id)" class="action-btn delete">
                          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="18" y1="6" x2="6" y2="18"/>
                            <line x1="6" y1="6" x2="18" y2="18"/>
                          </svg>
                        </button>
                      </div>
                    </div>
                  </div>
                  <div v-else class="empty-state">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                      <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                      <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                    </svg>
                    <p>No notifications</p>
                  </div>
                </div>
              </div>
            </Transition>
          </div>

          <!-- User Menu -->
          <div class="user-menu">
            <span class="user-greeting">👋 {{ auth?.user?.name || 'Admin' }}</span>
            <Link href="/logout" method="post" as="button" class="logout-btn">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                <polyline points="16 17 21 12 16 7"/>
                <line x1="21" y1="12" x2="9" y2="12"/>
              </svg>
              Logout
            </Link>
          </div>
        </div>
      </header>

      <!-- Main Content -->
      <main class="main-wrapper">
        <slot />
      </main>

      <!-- Flash Message -->
      <Transition name="fade">
        <div v-if="showFlash" class="flash-message">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="20 6 9 17 4 12"/>
          </svg>
          {{ flash.success }}
        </div>
      </Transition>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { usePage, Link } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import axios from 'axios'

const page = usePage()
const flash = page.props.flash || {}
const auth = page.props.auth || {}

const isSidebarOpen = ref(false)
const isDesktop = ref(window.innerWidth >= 768)
const showFlash = ref(!!flash.success)

const showNotifications = ref(false)
const notifications = ref([])
const unreadCount = ref(0)
const loadingNotifications = ref(false)
const markingRead = ref(false)
const notificationRef = ref(null)

const pageTitle = computed(() => {
  const title = page.props.title || 'Dashboard'
  return title
})

const navSections = [
  {
    label: 'Employee Management',
    links: [
      { name: 'Add Employee', href: route('admin.employees.create'), route: 'admin.employees.create', icon: '👤' },
      { name: 'Employee List', href: route('admin.employees.manage'), route: 'admin.employees.manage', icon: '📋' },
    ]
  },
  // {
  //   label: 'Marketer Management',
  //   links: [
  //     { name: 'Add Marketer', href: route('admin.marketers.create'), route: 'admin.marketers.create', icon: '🧑‍💼' },
  //     { name: 'Marketer List', href: route('admin.marketers.index'), route: 'admin.marketers.index', icon: '📊' },
  //     { name: 'Live Tracking', href: route('admin.marketers.live'), route: 'admin.marketers.live', icon: '🛰️' },
  //   ]
  // },
  {
    label: 'Attendance Management',
    links: [
      { name: 'Attendance', href: route('admin.attendance.index'), route: 'admin.attendance.index', icon: '🕒' },
      { name: 'View Tasks', href: route('admin.tasks.index'), route: 'admin.tasks.index', icon: '✅' },
    ]
  },
  {
    label: 'Leave Management',
    links: [
      { name: 'Leave Types', href: route('admin.leave-types.index'), route: 'admin.leave-types.index', icon: '📌' },
      { name: 'Holidays', href: route('admin.holidays.index'), route: 'admin.holidays.index', icon: '🎊' },
      { name: 'Leave Assignments', href: route('admin.leave-assignments.index'), route: 'admin.leave-assignments.index', icon: '📝' },
      { name: 'Leave Requests', href: route('admin.leave-applications.index'), route: 'admin.leave-applications.index', icon: '📝' },
    ]
  },
  {
    label: 'Organization Settings',
    links: [
      { name: 'Departments', href: route('admin.departments.index'), route: 'admin.departments.index', icon: '🏢' },
      { name: 'Designations', href: route('admin.designations.index'), route: 'admin.designations.index', icon: '🏷️' },
      { name: 'Shifts', href: route('admin.shifts.index'), route: 'admin.shifts.index', icon: '⏰' },
      { name: 'Locations', href: route('admin.locations.index'), route: 'admin.locations.index', icon: '📍' },
       { name: 'KRA Management', href: route('admin.kras.index'), route: 'admin.kras.index', icon: '🎯' },
      { name: 'Admins / Managers', href: route('admin.users.index'), route: 'admin.users.index', icon: '🛡️' },
      { name: 'Policies', href: route('admin.policies.index'), route: 'admin.policies.index', icon: '📜' },
    ]
  },
  {
    label: 'Reports',
    links: [
      { name: 'Salary Report', href: route('admin.salary-report.index'), route: 'admin.salary-report.index', icon: '💰' }
    ]
  }
]

const toggleSidebar = () => {
  isSidebarOpen.value = !isSidebarOpen.value
}

const handleNavClick = () => {
  if (!isDesktop.value) {
    isSidebarOpen.value = false
  }
}

const toggleNotifications = async () => {
  showNotifications.value = !showNotifications.value
  if (showNotifications.value) await loadNotifications()
}

const loadNotifications = async () => {
  loadingNotifications.value = true
  try {
    const res = await axios.get(route('admin.notifications.index'))
    notifications.value = res.data.notifications || []
    unreadCount.value = res.data.unread_count || 0
  } catch (e) {
    console.error("Failed to load notifications", e)
    notifications.value = []
    unreadCount.value = 0
  } finally {
    loadingNotifications.value = false
  }
}

const markAllAsRead = async () => {
  markingRead.value = true
  try {
    await axios.post(route('admin.notifications.markAsRead'))
    notifications.value.forEach(n => n.is_read = true)
    unreadCount.value = 0
  } catch (e) {
    console.error("Failed to mark as read", e)
  } finally {
    markingRead.value = false
  }
}

const markSingleAsRead = async (id) => {
  try {
    await axios.post(route('admin.notifications.markSingleAsRead', id))
    const notification = notifications.value.find(n => n.id === id)
    if (notification && !notification.is_read) {
      notification.is_read = true
      unreadCount.value = Math.max(0, unreadCount.value - 1)
    }
  } catch (e) {
    console.error("Failed to mark notification as read", e)
  }
}

const deleteNotification = async (id) => {
  try {
    await axios.delete(route('admin.notifications.destroy', id))
    const index = notifications.value.findIndex(n => n.id === id)
    if (index > -1) {
      const notification = notifications.value[index]
      if (!notification.is_read) {
        unreadCount.value = Math.max(0, unreadCount.value - 1)
      }
      notifications.value.splice(index, 1)
    }
  } catch (e) {
    console.error("Failed to delete notification", e)
  }
}

const clearAllNotifications = async () => {
  try {
    await axios.post(route('admin.notifications.clear'))
    notifications.value = []
    unreadCount.value = 0
  } catch (e) {
    console.error("Failed to clear all notifications", e)
  }
}

const getUserInitials = () => {
  const name = auth?.user?.name || 'Admin'
  const parts = name.split(' ')
  return parts.length > 1 
    ? (parts[0].charAt(0) + parts[1].charAt(0)).toUpperCase()
    : name.charAt(0).toUpperCase()
}

const handleClickOutside = (e) => {
  if (notificationRef.value && !notificationRef.value.contains(e.target)) {
    showNotifications.value = false
  }
}

const handleResize = () => {
  isDesktop.value = window.innerWidth >= 768
  if (isDesktop.value) {
    isSidebarOpen.value = false
  }
}

onMounted(() => {
  window.addEventListener('resize', handleResize)
  window.addEventListener('click', handleClickOutside)

  if (flash.success) {
    setTimeout(() => showFlash.value = false, 3000)
  }

  loadNotifications()
})

onBeforeUnmount(() => {
  window.removeEventListener('resize', handleResize)
  window.removeEventListener('click', handleClickOutside)
})
</script>

<style scoped>
/* Layout Structure */
.admin-layout {
  min-height: 100vh;
  display: flex;
  background-color: #f9fafb;
}

/* Mobile Overlay */
.mobile-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.5);
  backdrop-filter: blur(4px);
  z-index: 998;
}

/* Modern Sidebar - White with Purple Accents */
.modern-sidebar {
  position: fixed;
  left: 0;
  top: 0;
  height: 100vh;
  width: 280px;
  background: white;
  color: #1f2937;
  display: flex;
  flex-direction: column;
  z-index: 999;
  box-shadow: 4px 0 24px rgba(0, 0, 0, 0.08);
  border-right: 1px solid #f3f4f6;
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
    position: static;
    transform: translateX(0);
  }
}

/* Sidebar Header */
.sidebar-header {
  padding: 1.5rem;
  border-bottom: 1px solid #f3f4f6;
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
}

.brand {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.brand-icon {
  width: 48px;
  height: 48px;
  background: rgba(255, 255, 255, 0.2);
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
  color: white;
}

.brand-text span {
  font-size: 0.75rem;
  opacity: 0.9;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  color: white;
}

.close-btn {
  background: rgba(255, 255, 255, 0.15);
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
  background: rgba(255, 255, 255, 0.25);
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
  color: #9ca3af;
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
  color: #6b7280;
  text-decoration: none;
  transition: all 0.2s ease;
  margin-bottom: 0.25rem;
  position: relative;
  font-weight: 500;
  font-size: 0.9375rem;
}

.nav-link:hover {
  background: #f9fafb;
  color: #8b5cf6;
  transform: translateX(4px);
}

.nav-link.active {
  background: linear-gradient(135deg, #ede9fe 0%, #e9d5ff 100%);
  color: #7c3aed;
  box-shadow: 0 2px 8px rgba(139, 92, 246, 0.15);
}

.nav-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 20px;
  height: 20px;
  flex-shrink: 0;
  font-size: 1.125rem;
}

.nav-text {
  flex: 1;
}

.active-indicator {
  width: 6px;
  height: 6px;
  background: #7c3aed;
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
  border-top: 1px solid #f3f4f6;
  background: #fafafa;
}

.user-profile {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.user-avatar {
  width: 42px;
  height: 42px;
  background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  font-size: 0.875rem;
  flex-shrink: 0;
  color: white;
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
  color: #1f2937;
}

.user-role {
  font-size: 0.75rem;
  color: #9ca3af;
  margin: 0;
}

/* Main Content Area */
.main-content {
  flex: 1;
  display: flex;
  flex-direction: column;
  width: 100%;
}

@media (min-width: 769px) {
  .main-content {
    margin-left: 0;
  }
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
  color: #8b5cf6;
  transition: all 0.2s;
}

.menu-toggle:hover {
  background: #ede9fe;
  color: #7c3aed;
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

/* Notifications */
.notification-wrapper {
  position: relative;
}

.notification-btn {
  position: relative;
  background: #f3f4f6;
  border: none;
  width: 40px;
  height: 40px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  color: #6b7280;
  transition: all 0.2s;
}

.notification-btn:hover {
  background: #ede9fe;
  color: #8b5cf6;
}

.notification-badge {
  position: absolute;
  top: -4px;
  right: -4px;
  background: #ef4444;
  color: white;
  font-size: 0.625rem;
  font-weight: 600;
  width: 18px;
  height: 18px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.notification-dropdown {
  position: absolute;
  top: calc(100% + 0.5rem);
  right: 0;
  width: 320px;
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
  z-index: 100;
}

.dropdown-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1rem;
  border-bottom: 1px solid #f3f4f6;
}

.header-actions {
  display: flex;
  gap: 0.5rem;
}

.dropdown-header h3 {
  font-size: 1rem;
  font-weight: 600;
  color: #111827;
  margin: 0;
}

.mark-read-btn {
  background: none;
  border: none;
  color: #8b5cf6;
  font-size: 0.75rem;
  font-weight: 500;
  cursor: pointer;
  transition: color 0.2s;
}

.mark-read-btn:hover:not(:disabled) {
  color: #7c3aed;
}

.mark-read-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.clear-all-btn {
  background: none;
  border: none;
  color: #ef4444;
  font-size: 0.75rem;
  font-weight: 500;
  cursor: pointer;
  transition: color 0.2s;
}

.clear-all-btn:hover:not(:disabled) {
  color: #dc2626;
}

.clear-all-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.dropdown-body {
  max-height: 320px;
  overflow-y: auto;
}

.loading-state, .empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 2rem;
  color: #6b7280;
  gap: 0.75rem;
}

.spinner {
  width: 24px;
  height: 24px;
  border: 3px solid #f3f4f6;
  border-top-color: #8b5cf6;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.notification-list {
  padding: 0.5rem;
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.notification-item {
  padding: 0.75rem;
  border-radius: 8px;
  transition: all 0.2s;
  display: flex;
  align-items: flex-start;
  gap: 0.75rem;
  border-left: 3px solid transparent;
  border: 1px solid #f3f4f6;
  background: #fafafa;
}

.notification-item.unread {
  border-left-color: #8b5cf6;
  background: #faf5ff;
}

.notification-item:hover {
  background: #ffffff;
  border-color: #e5e7eb;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  transform: translateY(-1px);
}

.notification-item.unread:hover {
  background: #f8faff;
  border-color: #c4b5fd;
  box-shadow: 0 2px 8px rgba(139, 92, 246, 0.15);
}

.notification-content {
  flex: 1;
  min-width: 0;
}

.notification-actions {
  display: flex;
  gap: 0.25rem;
  opacity: 0;
  transition: opacity 0.2s;
}

.notification-item:hover .notification-actions {
  opacity: 1;
}

.action-btn {
  background: none;
  border: none;
  width: 24px;
  height: 24px;
  border-radius: 4px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s;
}

.action-btn.mark-read {
  color: #10b981;
}

.action-btn.mark-read:hover {
  background: #dcfce7;
  color: #059669;
}

.action-btn.delete {
  color: #ef4444;
}

.action-btn.delete:hover {
  background: #fee2e2;
  color: #dc2626;
}

.notification-title {
  font-size: 0.875rem;
  font-weight: 500;
  color: #111827;
  margin: 0 0 0.25rem;
}

.notification-message {
  font-size: 0.75rem;
  color: #6b7280;
  margin: 0 0 0.25rem;
  line-height: 1.4;
}

.notification-time {
  font-size: 0.75rem;
  color: #9ca3af;
  margin: 0;
}

/* User Menu */
.user-menu {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.user-greeting {
  font-size: 0.875rem;
  color: #6b7280;
  font-weight: 500;
}

.logout-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  background: #fef2f2;
  border: none;
  color: #dc2626;
  padding: 0.5rem 1rem;
  border-radius: 8px;
  font-size: 0.875rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
  text-decoration: none;
}

.logout-btn:hover {
  background: #fee2e2;
}

/* Main Wrapper */
.main-wrapper {
  flex: 1;
  padding: 1.5rem;
}

/* Flash Message */
.flash-message {
  position: fixed;
  bottom: 1.5rem;
  right: 1.5rem;
  background: #10b981;
  color: white;
  padding: 1rem 1.5rem;
  border-radius: 12px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
  display: flex;
  align-items: center;
  gap: 0.75rem;
  font-weight: 500;
  z-index: 1000;
}

/* Transitions */
.overlay-enter-active, .overlay-leave-active {
  transition: opacity 0.3s ease;
}

.overlay-enter-from, .overlay-leave-to {
  opacity: 0;
}

.slide-enter-active, .slide-leave-active {
  transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.slide-enter-from, .slide-leave-to {
  transform: translateX(-100%);
}

.dropdown-enter-active, .dropdown-leave-active {
  transition: all 0.2s ease;
}

.dropdown-enter-from, .dropdown-leave-to {
  opacity: 0;
  transform: translateY(-8px);
}

.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

/* Responsive Adjustments */
@media (max-width: 640px) {
  .user-greeting {
    display: none;
  }

  .main-wrapper {
    padding: 1rem;
  }

  .topbar {
    padding: 1rem;
  }

  .notification-dropdown {
    width: 280px;
    right: -1rem;
  }
}
</style>