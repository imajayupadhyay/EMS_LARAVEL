<script setup>
import { ref } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import ManagerLayout from '@/Layouts/ManagerLayout.vue'
import axios from 'axios'

defineOptions({ layout: ManagerLayout })

const props = defineProps({
  shifts: Array,
  flash: String,
})

const showModal = ref(false)
const isEditing = ref(false)
const deleting = ref(null)

const form = useForm({
  id: null,
  name: '',
  time_from: '',
  time_to: '',
})

function openAddModal() {
  isEditing.value = false
  form.reset()
  form.clearErrors()
  showModal.value = true
}

function openEditModal(shift) {
  isEditing.value = true
  form.id = shift.id
  form.name = shift.name
  form.time_from = shift.time_from ? shift.time_from.substring(0, 5) : ''
  form.time_to = shift.time_to ? shift.time_to.substring(0, 5) : ''
  form.clearErrors()
  showModal.value = true
}

function closeModal() {
  showModal.value = false
  form.reset()
  form.clearErrors()
}

function submitForm() {
  if (isEditing.value) {
    form.post(route('manager.shifts.update'), {
      preserveScroll: true,
      onSuccess: () => {
        closeModal()
      }
    })
  } else {
    form.post(route('manager.shifts.store'), {
      preserveScroll: true,
      onSuccess: () => {
        closeModal()
      }
    })
  }
}

async function confirmDelete(id) {
  if (!confirm('Are you sure you want to delete this shift?')) return

  deleting.value = id

  try {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
    const formData = new URLSearchParams()
    formData.append('_token', csrfToken)
    formData.append('id', id)

    await axios.post(route('manager.shifts.delete'), formData, {
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
    })

    await router.reload({ only: ['shifts', 'flash'] })
  } catch (err) {
    console.error('Delete failed', err)
    alert('Failed to delete shift')
  } finally {
    deleting.value = null
  }
}

function calculateDuration(start, end) {
  if (!start || !end) return 'N/A'

  const startTime = start.substring(0, 5).split(':')
  const endTime = end.substring(0, 5).split(':')

  let startMinutes = parseInt(startTime[0]) * 60 + parseInt(startTime[1])
  let endMinutes = parseInt(endTime[0]) * 60 + parseInt(endTime[1])

  if (endMinutes < startMinutes) {
    endMinutes += 24 * 60 // Add 24 hours if shift crosses midnight
  }

  const totalMinutes = endMinutes - startMinutes
  const hours = Math.floor(totalMinutes / 60)
  const minutes = totalMinutes % 60

  return `${hours}h ${minutes}m`
}
</script>

<template>
  <Head title="Shifts">
    <meta name="description" content="View and manage work shift schedules." head-key="description" />
  </Head>
  <div class="p-6">
    <!-- Header -->
    <div class="mb-8">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold text-gray-900 mb-2">Shift Management</h1>
          <p class="text-gray-600">Manage all available shifts and their timings</p>
        </div>
        <button
          @click="openAddModal"
          class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors shadow-sm"
        >
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
          </svg>
          Add Shift
        </button>
      </div>
    </div>

    <!-- Success Message -->
    <div v-if="flash || props.flash" class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center">
      <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
      </svg>
      {{ flash || props.flash }}
    </div>

    <!-- Shifts Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
      <div
        v-for="shift in shifts"
        :key="shift.id"
        class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all duration-300 overflow-hidden"
      >
        <!-- Card Header -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-4">
          <h3 class="text-lg font-semibold text-white">{{ shift.name }}</h3>
          <p class="text-blue-100 text-sm">{{ shift.employees_count || 0 }} employees assigned</p>
        </div>

        <!-- Card Body -->
        <div class="p-6 space-y-4">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-gray-500 mb-1">Start Time</p>
              <p class="text-2xl font-bold text-gray-900">{{ shift.time_from?.substring(0, 5) }}</p>
            </div>
            <div class="text-gray-300 text-2xl">→</div>
            <div>
              <p class="text-sm text-gray-500 mb-1">End Time</p>
              <p class="text-2xl font-bold text-gray-900">{{ shift.time_to?.substring(0, 5) }}</p>
            </div>
          </div>

          <div class="pt-4 border-t border-gray-100">
            <p class="text-sm text-gray-600">
              <span class="font-semibold">Duration:</span>
              {{ calculateDuration(shift.time_from, shift.time_to) }}
            </p>
          </div>

          <!-- Action Buttons -->
          <div class="flex items-center justify-end space-x-2 pt-2">
            <button
              @click="openEditModal(shift)"
              class="inline-flex items-center px-3 py-1.5 bg-indigo-50 text-indigo-600 rounded-lg hover:bg-indigo-100 transition-colors text-sm font-medium"
            >
              <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
              </svg>
              Edit
            </button>
            <button
              :disabled="deleting === shift.id"
              @click="confirmDelete(shift.id)"
              class="inline-flex items-center px-3 py-1.5 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors text-sm font-medium disabled:opacity-50"
            >
              <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
              </svg>
              <span v-if="deleting === shift.id">Deleting...</span>
              <span v-else>Delete</span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-if="!shifts || shifts.length === 0" class="text-center py-12">
      <div class="text-gray-400 text-6xl mb-4">⏰</div>
      <h3 class="text-xl font-semibold text-gray-900 mb-2">No shifts available</h3>
      <p class="text-gray-600 mb-4">Get started by creating your first shift</p>
      <button
        @click="openAddModal"
        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
      >
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Add Shift
      </button>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" @click.self="closeModal">
      <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4">
        <!-- Modal Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b">
          <h3 class="text-xl font-semibold text-gray-900">
            {{ isEditing ? 'Edit Shift' : 'Add New Shift' }}
          </h3>
          <button @click="closeModal" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Modal Body -->
        <form @submit.prevent="submitForm">
          <div class="px-6 py-4 space-y-4">
            <!-- Shift Name -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Shift Name <span class="text-red-500">*</span>
              </label>
              <input
                v-model="form.name"
                type="text"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                :class="{ 'border-red-500': form.errors.name }"
                placeholder="e.g., Morning Shift"
              />
              <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
            </div>

            <!-- Time From -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Time From <span class="text-red-500">*</span>
              </label>
              <input
                v-model="form.time_from"
                type="time"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                :class="{ 'border-red-500': form.errors.time_from }"
              />
              <p v-if="form.errors.time_from" class="mt-1 text-sm text-red-600">{{ form.errors.time_from }}</p>
            </div>

            <!-- Time To -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Time To <span class="text-red-500">*</span>
              </label>
              <input
                v-model="form.time_to"
                type="time"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                :class="{ 'border-red-500': form.errors.time_to }"
              />
              <p v-if="form.errors.time_to" class="mt-1 text-sm text-red-600">{{ form.errors.time_to }}</p>
            </div>
          </div>

          <!-- Modal Footer -->
          <div class="flex items-center justify-end gap-3 px-6 py-4 bg-gray-50 border-t">
            <button
              type="button"
              @click="closeModal"
              class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
            >
              Cancel
            </button>
            <button
              type="submit"
              :disabled="form.processing"
              class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 disabled:opacity-50"
            >
              <span v-if="form.processing">Saving...</span>
              <span v-else>{{ isEditing ? 'Update' : 'Save' }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<style scoped>
table td, table th {
  vertical-align: middle;
}
</style>