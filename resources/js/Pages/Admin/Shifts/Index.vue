<script setup>
import { ref } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import axios from 'axios'


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
    form.post(route('admin.shifts.update'), {
      preserveScroll: true,
      onSuccess: () => {
        closeModal()
      }
    })
  } else {
    form.post(route('admin.shifts.store'), {
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
    
    await axios.post(route('admin.shifts.delete'), formData, {
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
</script>

<template>
  <AdminLayout>
    <div class="max-w-6xl mx-auto p-6">
      <!-- Header -->
      <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-extrabold text-orange-600">Shift Management</h1>
        <button 
          @click="openAddModal" 
          class="inline-block rounded-lg px-4 py-2 bg-indigo-600 text-white hover:bg-indigo-700 transition-colors"
        >
          Add Shift
        </button>
      </div>

      <!-- Success Message -->
      <div v-if="flash || props.flash" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
        <strong class="font-bold">Success:</strong>
        <span class="ml-2">{{ flash || props.flash }}</span>
      </div>

      <!-- Shifts Table -->
      <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-4 text-left text-sm font-medium text-gray-600">#</th>
              <th class="px-6 py-4 text-left text-sm font-medium text-gray-600">Shift Name</th>
              <th class="px-6 py-4 text-left text-sm font-medium text-gray-600">Time From</th>
              <th class="px-6 py-4 text-left text-sm font-medium text-gray-600">Time To</th>
              <th class="px-6 py-4 text-right text-sm font-medium text-gray-600">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-100">
            <tr v-if="!shifts || shifts.length === 0">
              <td colspan="5" class="px-6 py-8 text-center text-gray-500">No shifts found</td>
            </tr>
            <tr v-for="(shift, index) in shifts" :key="shift.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 text-gray-700">{{ index + 1 }}</td>
              <td class="px-6 py-4">
                <div class="font-semibold text-gray-800">{{ shift.name }}</div>
              </td>
              <td class="px-6 py-4 text-gray-700">{{ shift.time_from?.substring(0, 5) || '-' }}</td>
              <td class="px-6 py-4 text-gray-700">{{ shift.time_to?.substring(0, 5) || '-' }}</td>
              <td class="px-6 py-4 text-right">
                <button 
                  @click="openEditModal(shift)" 
                  class="text-indigo-600 hover:text-indigo-800 mr-4 font-medium"
                >
                  Edit
                </button>
                <button 
                  :disabled="deleting === shift.id"
                  @click="confirmDelete(shift.id)" 
                  class="text-red-600 hover:text-red-800 font-medium disabled:opacity-50"
                >
                  <span v-if="deleting === shift.id">Deleting...</span>
                  <span v-else>Delete</span>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
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
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
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
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
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
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
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
                class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 disabled:opacity-50"
              >
                <span v-if="form.processing">Saving...</span>
                <span v-else>{{ isEditing ? 'Update' : 'Save' }}</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<style scoped>
table td, table th { 
  vertical-align: middle; 
}
</style>