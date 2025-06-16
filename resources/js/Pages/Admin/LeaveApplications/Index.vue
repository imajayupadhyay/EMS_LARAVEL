<template>
  <AdminLayout>
    <Head title="Manage Leave Applications" />
    <div class="max-w-7xl mx-auto p-4">
      <h1 class="text-3xl font-extrabold text-orange-600 mb-6 animate-fade-slide">Leave Applications</h1>

      <!-- Flash Messages -->
      <transition name="fade">
        <div v-if="flash.success" class="bg-green-100 border border-green-300 text-green-700 px-4 py-2 rounded mb-4 shadow">
          {{ flash.success }}
        </div>
      </transition>
      <transition name="fade">
        <div v-if="flash.error" class="bg-red-100 border border-red-300 text-red-700 px-4 py-2 rounded mb-4 shadow">
          {{ flash.error }}
        </div>
      </transition>

      <!-- Filters -->
      <div class="bg-white rounded-lg shadow-md p-4 mb-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 animate-fade-in">
        <div>
          <label class="text-sm font-medium">Status</label>
          <select v-model="status" @change="applyFilters" class="w-full border rounded px-2 py-1 focus:ring-2 focus:ring-orange-400">
            <option value="">All</option>
            <option value="pending">Pending</option>
            <option value="approved">Approved</option>
            <option value="rejected">Rejected</option>
          </select>
        </div>
        <div>
          <label class="text-sm font-medium">Employee</label>
          <input v-model="employee" @input="applyFilters" placeholder="Search name" class="w-full border rounded px-2 py-1 focus:ring-2 focus:ring-orange-400">
        </div>
        <div>
          <label class="text-sm font-medium">Leave Type</label>
          <select v-model="leaveType" @change="applyFilters" class="w-full border rounded px-2 py-1 focus:ring-2 focus:ring-orange-400">
            <option value="">All</option>
            <option v-for="type in leaveTypes" :key="type.id" :value="type.name">{{ type.name }}</option>
          </select>
        </div>
        <div>
          <label class="text-sm font-medium">Date</label>
          <input type="date" v-model="date" @change="applyFilters" class="w-full border rounded px-2 py-1 focus:ring-2 focus:ring-orange-400">
        </div>
      </div>

      <!-- Applications Grid -->
      <div v-if="applications.data.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="app in applications.data"
          :key="app.id"
          @click="openPopup(app)"
          class="bg-white p-4 rounded-lg shadow-md hover:shadow-xl transition transform hover:scale-105 cursor-pointer animate-fade-in"
        >
          <div class="flex justify-between items-center mb-1">
            <div class="font-semibold text-gray-700">{{ app.employee.first_name }} {{ app.employee.last_name }}</div>
            <span :class="statusBadgeClass(app.status)" class="text-xs font-semibold rounded px-2 py-0.5">
              {{ app.status.toUpperCase() }}
            </span>
          </div>
          <div class="text-sm text-gray-500">{{ app.start_date }} - {{ app.end_date }}</div>
          <div class="inline-block bg-orange-100 text-orange-700 text-xs rounded-full px-2 py-0.5 mt-1">{{ app.leave_type.name }}</div>
          <p class="text-sm text-gray-600 mt-2 line-clamp-2">{{ app.reason }}</p>
        </div>
      </div>
      <p v-else class="text-center text-gray-500 mt-6 animate-fade-in">No leave applications found.</p>

      <!-- Pagination -->
      <div class="mt-6 flex justify-center gap-4">
        <button v-if="applications.prev_page_url" @click="router.get(applications.prev_page_url, {}, { preserveScroll: true })" class="bg-orange-500 text-white px-4 py-2 rounded shadow hover:bg-orange-600 transition">Previous</button>
        <button v-if="applications.next_page_url" @click="router.get(applications.next_page_url, {}, { preserveScroll: true })" class="bg-orange-500 text-white px-4 py-2 rounded shadow hover:bg-orange-600 transition">Next</button>
      </div>

      <!-- Popup -->
      <transition name="fade">
        <div v-if="selectedApp" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
          <div class="bg-white p-6 rounded-lg shadow-2xl max-w-md w-full relative animate-scale-in">
            <button @click="closePopup" class="absolute top-2 right-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-full w-6 h-6 flex items-center justify-center">✕</button>
            <h2 class="text-lg font-bold text-orange-600 mb-2">{{ selectedApp.employee.first_name }} {{ selectedApp.employee.last_name }}'s Leave</h2>
            <p class="text-sm text-gray-500">Type: {{ selectedApp.leave_type.name }}</p>
            <p class="text-sm text-gray-500">From: {{ selectedApp.start_date }}</p>
            <p class="text-sm text-gray-500">To: {{ selectedApp.end_date }}</p>
            <p class="text-sm text-gray-500 mb-2">Reason: {{ selectedApp.reason }}</p>
            <div class="mt-3 flex gap-2" v-if="selectedApp.status === 'pending'">
              <button @click="updateStatus(selectedApp, 'approved')" class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600 transition">Approve</button>
              <button @click="updateStatus(selectedApp, 'rejected')" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 transition">Reject</button>
            </div>
          </div>
        </div>
      </transition>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  applications: Object,
  leaveTypes: Array,
  filters: Object,
  flash: Object
})

const status = ref(props.filters.status || '')
const employee = ref(props.filters.employee || '')
const leaveType = ref(props.filters.leave_type || '')
const date = ref(props.filters.date || '')
const selectedApp = ref(null)

const applyFilters = () => {
  router.get(route('admin.leave-applications.index'), {
    status: status.value,
    employee: employee.value,
    leave_type: leaveType.value,
    date: date.value
  }, { preserveScroll: true })
}

const openPopup = (app) => {
  selectedApp.value = app
}

const closePopup = () => {
  selectedApp.value = null
}

const updateStatus = (app, status) => {
  router.post(route('admin.leave-applications.update', app.id), { status }, {
    preserveScroll: true,
    onSuccess: () => closePopup()
  })
}

const statusBadgeClass = (status) => {
  if (status === 'approved') return 'bg-green-100 text-green-700'
  if (status === 'pending') return 'bg-yellow-100 text-yellow-700'
  if (status === 'rejected') return 'bg-red-100 text-red-700'
  return 'bg-gray-100 text-gray-700'
}
</script>

<style scoped>
/* Animations */
@keyframes fade-slide {
  from { opacity: 0; transform: translateY(-10px); }
  to { opacity: 1; transform: translateY(0); }
}
@keyframes fade-in {
  from { opacity: 0; }
  to { opacity: 1; }
}
@keyframes scale-in {
  from { transform: scale(0.95); opacity: 0; }
  to { transform: scale(1); opacity: 1; }
}

.animate-fade-slide {
  animation: fade-slide 0.5s ease;
}
.animate-fade-in {
  animation: fade-in 0.4s ease;
}
.animate-scale-in {
  animation: scale-in 0.3s ease;
}

.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
