<template>
  <div class="min-h-screen flex bg-gray-100 relative">
    <!-- Sidebar -->
    <transition name="slide">
      <aside
        v-show="isSidebarOpen || isDesktop"
        class="fixed md:relative z-30 bg-white w-64 min-h-screen shadow-lg"
      >
        <div class="flex items-center justify-between p-4 border-b">
          <h2 class="text-xl font-bold text-orange-600">EMS Admin</h2>
          <button class="md:hidden text-gray-700" @click="isSidebarOpen = false">✕</button>
        </div>

        <nav class="p-4 space-y-4">
          <div>
            <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Employee Management</p>
            <Link :href="route('admin.employees.create')" class="nav-link" :class="{ active: route().current('admin.employees.create') }">👤 Add Employee</Link>
            <Link :href="route('admin.employees.index')" class="nav-link" :class="{ active: route().current('admin.employees.index') }">📋 Employee List</Link>
          </div>

          <div>
            <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Attendance Management</p>
            <Link :href="route('admin.attendance.index')" class="nav-link" :class="{ active: route().current('admin.attendance.index') }">🕒 Attendance</Link>
            <Link :href="route('admin.tasks.index')" class="nav-link" :class="{ active: route().current('admin.tasks.index') }">✅ View Tasks</Link>
          </div>

          <div>
            <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Leave Management</p>
            <Link :href="route('admin.leave-types.index')" class="nav-link" :class="{ active: route().current('admin.leave-types.index') }">📌 Leave Types</Link>
            <Link :href="route('admin.holidays.index')" class="nav-link" :class="{ active: route().current('admin.holidays.index') }">🎊 Holidays</Link>
            <Link :href="route('admin.leave-assignments.index')" class="nav-link" :class="{ active: route().current('admin.leave-assignments.index') }">📝 Leave Assignments</Link>
            <Link :href="route('admin.leave-applications.index')" class="nav-link" :class="{ active: route().current('admin.leave-applications.index') }">📝 Leave Requests</Link>
          </div>

          <div>
            <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Organization Settings</p>
            <Link :href="route('admin.departments.index')" class="nav-link" :class="{ active: route().current('admin.departments.index') }">🏢 Departments</Link>
            <Link :href="route('admin.designations.index')" class="nav-link" :class="{ active: route().current('admin.designations.index') }">🏷️ Designations</Link>
            <Link :href="route('admin.locations.index')" class="nav-link" :class="{ active: route().current('admin.locations.index') }">📍 Locations</Link>
          </div>
          <div>
  <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Reports</p>
  <Link :href="route('admin.salary-report.index')" class="nav-link" :class="{ active: route().current('admin.salary-report.index') }">
    💰 Salary Report
  </Link>
</div>

        </nav>
      </aside>
    </transition>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col">
      <header class="flex justify-end items-center bg-white shadow p-4 md:px-6 sticky top-0 z-10">
        <div class="flex items-center gap-4 text-sm relative notification-wrapper">
          <button @click.stop="toggleNotifications" class="relative">
            🔔
            <span v-if="unreadCount > 0" class="absolute -top-1 -right-1 bg-red-600 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
              {{ unreadCount }}
            </span>
          </button>

          <div v-if="showNotifications" class="absolute right-0 mt-2 w-72 bg-white border rounded shadow-lg z-50">
            <div v-if="loadingNotifications" class="p-4 text-center text-gray-500">Loading...</div>
            <div v-else>
              <div v-if="notifications.length" class="max-h-60 overflow-y-auto">
                <div v-for="note in notifications" :key="note.id" class="p-2 border-b text-sm">
                  <p class="font-medium">{{ note.title }}</p>
                  <p class="text-xs text-gray-500">{{ note.created_at }}</p>
                </div>
              </div>
              <div v-else class="p-4 text-gray-500 text-center">No notifications</div>
              <button @click.stop="markAllAsRead" :disabled="markingRead" class="w-full text-xs text-orange-600 py-1 disabled:opacity-50">Mark all as read</button>
            </div>
          </div>

          <span class="text-gray-600">👤 {{ auth?.user?.name || 'Admin' }}</span>
          <Link href="/logout" method="post" as="button" class="text-red-600 hover:underline">Logout</Link>
        </div>
      </header>

      <main class="p-4 md:p-6">
        <slot />
      </main>

      <transition name="fade">
        <div v-if="showFlash" class="fixed bottom-5 right-5 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg">
          {{ flash.success }}
        </div>
      </transition>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
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

const toggleSidebar = () => {
  isSidebarOpen.value = !isSidebarOpen.value
}

const toggleNotifications = async () => {
  showNotifications.value = !showNotifications.value
  if (showNotifications.value) {
    await loadNotifications()
  }
}

const loadNotifications = async () => {
  loadingNotifications.value = true
  try {
    const res = await axios.get(route('admin.notifications.index'))
    notifications.value = res.data.notifications
    unreadCount.value = notifications.value.filter(n => !n.is_read).length
  } catch (e) {
    console.error("Failed to load notifications", e)
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

const handleClickOutside = (e) => {
  if (!e.target.closest('.notification-wrapper')) {
    showNotifications.value = false
  }
}

onMounted(() => {
  window.addEventListener('resize', () => {
    isDesktop.value = window.innerWidth >= 768
  })
  window.addEventListener('click', handleClickOutside)

  if (flash.success) {
    setTimeout(() => {
      showFlash.value = false
    }, 3000)
  }

  loadNotifications()
})

onBeforeUnmount(() => {
  window.removeEventListener('click', handleClickOutside)
})
</script>

<style scoped>
.slide-enter-active, .slide-leave-active {
  transition: transform 0.3s ease;
}
.slide-enter-from, .slide-leave-to {
  transform: translateX(-100%);
}
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
.nav-link {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1rem;
  border-radius: 0.375rem;
  color: #374151;
  font-weight: 500;
}
.nav-link:hover {
  background-color: #fff7ed;
}
.nav-link.active {
  background-color: #f97316;
  color: white;
}
</style>
