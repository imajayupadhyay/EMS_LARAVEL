<template>
  <div class="min-h-screen flex flex-col md:flex-row bg-gray-100">
    <!-- Sidebar -->
    <transition name="slide">
      <aside
        v-show="isSidebarOpen || isDesktop"
        class="bg-white md:w-72 w-full h-screen md:relative shadow-lg z-30"
      >
        <!-- Sidebar Header -->
        <div class="flex items-center justify-between p-4 border-b">
          <h2 class="text-xl font-bold text-orange-600">EMS Employee</h2>
          <button class="md:hidden text-gray-700" @click="isSidebarOpen = false">✕</button>
        </div>

        <!-- Navigation -->
        <nav class="p-4 space-y-4">
          <!-- Main -->
          <div>
            <h3 class="text-xs font-semibold text-gray-500 uppercase mb-1">Main</h3>
            <Link :href="route('employee.dashboard')" class="nav-link" :class="{ active: isActive('/employee/dashboard') }">
              🏠 Dashboard
            </Link>
            <Link :href="route('employee.punches.index')" class="nav-link" :class="{ active: isActive('/employee/punches') }">
              ⏱ Punch In / Out
            </Link>
            <Link :href="route('employee.tasks.index')" class="nav-link" :class="{ active: isActive('/employee/tasks') }">
              📝 Daily Tasks
            </Link>
          </div>

          <!-- Leave & Attendance -->
          <div>
            <h3 class="text-xs font-semibold text-gray-500 uppercase mb-1">Leave & Attendance</h3>
            <Link :href="route('employee.attendance.index')" class="nav-link" :class="{ active: isActive('/employee/attendance') }">
              📊 Attendance
            </Link>
            <Link :href="route('employee.leave-applications.index')" class="nav-link" :class="{ active: isActive('/employee/leave-applications') }">
              🌿 Leave Applications
            </Link>
            <Link :href="route('employee.leave-summary.index')" class="nav-link" :class="{ active: isActive('/employee/leave-summary') }">
  📈 Leave Summary
</Link>

          </div>

          <div>
            <h3 class="text-xs font-semibold text-gray-500 uppercase mb-1">Company Info</h3>
            <Link :href="route('employee.holidays.index')" class="nav-link" :class="{ active: isActive('/employee/holidays') }">
              🎉 Holidays
            </Link>
          </div>
        </nav>
      </aside>
    </transition>

    <!-- Main Content -->
    <div class="flex-1">
      <!-- Topbar -->
      <header class="flex justify-between items-center bg-white shadow p-4 md:px-6 sticky top-0 z-10">
        <button class="md:hidden text-orange-600" @click="toggleSidebar">☰</button>
        <div class="flex items-center gap-4 text-sm">
          <span class="text-gray-600">👤 {{ auth?.user?.name || 'Employee' }}</span>
          <Link href="/logout" method="post" as="button" class="text-red-600 hover:underline">Logout</Link>
        </div>
      </header>

      <!-- Page Content -->
      <main class="p-4 md:p-6">
        <slot />
      </main>

      <!-- Flash -->
      <transition name="fade">
        <div v-if="showFlash" class="fixed bottom-5 right-5 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg">
          {{ flash.success }}
        </div>
      </transition>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { usePage, Link } from '@inertiajs/vue3'
import { route } from 'ziggy-js'

const page = usePage()
const flash = page.props.flash || {}
const auth = page.props.auth || {}

const isSidebarOpen = ref(false)
const isDesktop = ref(window.innerWidth >= 768)
const showFlash = ref(!!flash.success)

const toggleSidebar = () => {
  isSidebarOpen.value = !isSidebarOpen.value
}

const isActive = (url) => window.location.pathname.startsWith(url)

onMounted(() => {
  window.addEventListener('resize', () => {
    isDesktop.value = window.innerWidth >= 768
  })
  if (flash.success) {
    setTimeout(() => {
      showFlash.value = false
    }, 3000)
  }
})
</script>

<style scoped>
.slide-enter-active,
.slide-leave-active {
  transition: transform 0.3s ease;
}
.slide-enter-from,
.slide-leave-to {
  transform: translateX(-100%);
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
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
