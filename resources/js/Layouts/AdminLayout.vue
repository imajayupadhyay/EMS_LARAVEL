<template>
  <div class="min-h-screen flex bg-gray-100">
    <!-- Sidebar -->
    <transition name="slide">
  <aside
    v-show="isSidebarOpen || isDesktop"
    class="fixed md:relative z-30 bg-white w-64 h-screen md:min-h-screen shadow-lg transition-all duration-300 ease-in-out flex flex-col"
  >
    <!-- Top Header in Sidebar -->
    <div class="flex items-center justify-between p-4 border-b flex-shrink-0">
      <h2 class="text-xl font-bold text-orange-600">EMS Admin</h2>
      <button class="md:hidden text-gray-700 text-xl" @click="isSidebarOpen = false">✕</button>
    </div>

    <!-- Scrollable nav container -->
    <div class="flex-1 overflow-y-auto">
      <nav class="p-4 space-y-4">
        <template v-for="section in navSections" :key="section.label">
          <div>
            <p class="text-xs font-semibold text-gray-500 uppercase mb-1">{{ section.label }}</p>
            <Link
              v-for="item in section.links"
              :key="item.name"
              :href="item.href"
              class="nav-link"
              :class="{ active: route().current(item.route) }"
            >
              {{ item.icon }} {{ item.name }}
            </Link>
          </div>
        </template>
      </nav>
    </div>
  </aside>
</transition>



    <!-- Main Content -->
    <div class="flex-1 flex flex-col">
      <header class="flex justify-between items-center bg-white shadow p-4 sticky top-0 z-20">
        <button class="md:hidden text-orange-600 text-2xl" @click="isSidebarOpen = true">☰</button>
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

const navSections = [
  {
    label: 'Employee Management',
    links: [
      { name: 'Add Employee', href: route('admin.employees.create'), route: 'admin.employees.create', icon: '👤' },
      { name: 'Employee List', href: route('admin.employees.manage'), route: 'admin.employees.manage', icon: '📋' },
    ]
  },
    {
    label: 'Marketer Management',   // ✅ New Section
    links: [
      { name: 'Add Marketer', href: route('admin.marketers.create'), route: 'admin.marketers.create', icon: '🧑‍💼' },
      { name: 'Marketer List', href: route('admin.marketers.index'), route: 'admin.marketers.index', icon: '📊' },
      { name: 'Live Tracking', href: route('admin.marketers.live'), route: 'admin.marketers.live', icon: '🛰️' },
    ]
  },
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
      { name: 'Locations', href: route('admin.locations.index'), route: 'admin.locations.index', icon: '📍' },
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

const toggleNotifications = async () => {
  showNotifications.value = !showNotifications.value
  if (showNotifications.value) await loadNotifications()
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
  if (!e.target.closest('.notification-wrapper')) showNotifications.value = false
}

onMounted(() => {
  window.addEventListener('resize', () => {
    isDesktop.value = window.innerWidth >= 768
  })
  window.addEventListener('click', handleClickOutside)

  if (flash.success) {
    setTimeout(() => showFlash.value = false, 3000)
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
