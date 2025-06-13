<template>
  <div class="min-h-screen flex flex-col md:flex-row bg-gray-100 relative overflow-x-hidden">
    
    <!-- Hamburger Button (Mobile) -->
    <div class="md:hidden flex justify-between items-center p-4 shadow bg-white z-10">
      <h1 class="text-xl font-bold text-orange-600">Admin</h1>
      <button @click="toggleSidebar" class="text-orange-600 focus:outline-none">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
          viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M4 6h16M4 12h16M4 18h16" />
        </svg>
      </button>
    </div>

    <!-- Sidebar -->
    <transition name="slide">
      <aside
        v-show="isSidebarOpen || isDesktop"
        class="fixed md:relative top-0 right-0 md:right-auto z-30 bg-white w-64 h-full md:h-auto shadow-lg md:block"
      >
        <div class="p-4 font-bold text-orange-600 text-xl border-b">Admin Panel</div>
        <nav class="p-4 space-y-2">
          <Link href="/admin/dashboard" class="block px-4 py-2 rounded hover:bg-orange-100">Dashboard</Link>
          <Link href="/admin/employees/create" class="block px-4 py-2 rounded hover:bg-orange-100">Add Employee</Link>
          <Link href="/admin/employees/manage" class="block px-4 py-2 rounded hover:bg-orange-100">Employee List</Link>
          <Link href="/admin/departments" class="block px-4 py-2 rounded hover:bg-orange-100">Departments</Link>
          <Link href="/admin/designations" class="block px-4 py-2 rounded hover:bg-orange-100">Designations</Link>
        </nav>
      </aside>
    </transition>

    <!-- Page Content -->
    <div class="flex-1 p-4 md:ml-0 relative">
      <slot />

      <!-- Flash Message -->
      <transition name="fade">
        <div
          v-if="showMessage"
          class="fixed bottom-5 right-5 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50"
        >
          {{ flash.success }}
        </div>
      </transition>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { usePage, Link } from '@inertiajs/vue3'

const isSidebarOpen = ref(false)
const isDesktop = ref(false)

const checkScreen = () => {
  isDesktop.value = window.innerWidth >= 768
}

const toggleSidebar = () => {
  isSidebarOpen.value = !isSidebarOpen.value
}

// Flash success message logic
const flash = usePage().props.flash
const showMessage = ref(!!flash?.success)

onMounted(() => {
  checkScreen()
  window.addEventListener('resize', checkScreen)

  if (flash?.success) {
    setTimeout(() => {
      showMessage.value = false
    }, 3000)
  }
})
</script>

<style scoped>
.slide-enter-active, .slide-leave-active {
  transition: transform 0.3s ease-in-out;
}
.slide-enter-from {
  transform: translateX(100%);
}
.slide-leave-to {
  transform: translateX(100%);
}

.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>
