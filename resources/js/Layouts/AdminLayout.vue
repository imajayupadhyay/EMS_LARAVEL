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
          <!-- Employee Management -->
          <div>
            <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Employee Management</p>
            <Link :href="route('admin.employees.create')" class="nav-link" :class="{ active: route().current('admin.employees.create') }">
              👤 Add Employee
            </Link>
            <Link :href="route('admin.employees.index')" class="nav-link" :class="{ active: route().current('admin.employees.index') }">
              📋 Employee List
            </Link>
          </div>

          <!-- Attendance Management -->
          <div>
            <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Attendance Management</p>
            <Link :href="route('admin.attendance.index')" class="nav-link" :class="{ active: route().current('admin.attendance.index') }">
              🕒 Attendance
            </Link>
            <Link :href="route('admin.tasks.index')" class="nav-link" :class="{ active: route().current('admin.tasks.index') }">
              ✅ View Tasks
            </Link>
          </div>

          <!-- Organization Settings -->
          <div>
            <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Organization Settings</p>
            <Link :href="route('admin.departments.index')" class="nav-link" :class="{ active: route().current('admin.departments.index') }">
              🏢 Departments
            </Link>
            <Link :href="route('admin.designations.index')" class="nav-link" :class="{ active: route().current('admin.designations.index') }">
              🏷️ Designations
            </Link>
            <Link :href="route('admin.locations.index')" class="nav-link" :class="{ active: route().current('admin.locations.index') }">
              📍 Locations
            </Link>
          </div>
          <!-- Leave Management -->
<div>
  <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Leave Management</p>
  <Link 
    :href="route('admin.leave-types.index')" 
    class="nav-link" 
    :class="{ active: route().current('admin.leave-types.index') }"
  >
    📌 Leave Types
  </Link>
</div>

        </nav>
      </aside>
    </transition>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col">
      <!-- Topbar -->
      <header class="flex justify-between items-center bg-white shadow p-4 md:px-6 sticky top-0 z-10">
        <button class="md:hidden text-orange-600" @click="toggleSidebar">☰</button>
        <div class="flex items-center gap-4 text-sm">
          <span class="text-gray-600">👤 {{ auth?.user?.name || 'Admin' }}</span>
          <Link href="/logout" method="post" as="button" class="text-red-600 hover:underline">Logout</Link>
        </div>
      </header>

      <!-- Page Slot -->
      <main class="p-4 md:p-6">
        <slot />
      </main>

      <!-- Flash Message -->
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
