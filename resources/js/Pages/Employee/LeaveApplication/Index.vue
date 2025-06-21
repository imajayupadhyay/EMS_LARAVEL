<script setup>
import { ref } from 'vue'
import { useForm, router, Head } from '@inertiajs/vue3'
import EmployeeLayout from '@/Layouts/EmployeeLayout.vue'

const props = defineProps({
  leaveTypes: Array,
  applications: Object,
  flash: Object
})

const editing = ref(null)

const form = useForm({
  leave_type_id: '',
  start_date: '',
  end_date: '',
  reason: '',
  day_type: 'full' // default full day
})

const submit = () => {
  if (editing.value) {
    form.post(route('employee.leave-applications.update', editing.value.id), {
      preserveScroll: true,
      onSuccess: () => {
        form.reset()
        editing.value = null
      }
    })
  } else {
    form.post(route('employee.leave-applications.store'), {
      preserveScroll: true,
      onSuccess: () => form.reset()
    })
  }
}

const edit = (app) => {
  form.leave_type_id = app.leave_type_id
  form.start_date = app.start_date
  form.end_date = app.end_date
  form.reason = app.reason
  form.day_type = app.day_type || 'full'
  editing.value = app
}

const deleteApp = (app) => {
  if (confirm('Delete this leave request?')) {
    router.post(route('employee.leave-applications.destroy', app.id), {
      preserveScroll: true
    })
  }
}
</script>

<template>
  <EmployeeLayout>
    <Head title="Apply for Leave" />
    <div class="max-w-4xl mx-auto p-4 animate-fadeIn">
      <h1 class="text-2xl font-bold text-orange-600 mb-4">Apply for Leave</h1>

      <div v-if="flash.success" class="bg-green-100 text-green-700 px-4 py-2 rounded mb-4">
        {{ flash.success }}
      </div>
      <div v-if="flash.error" class="bg-red-100 text-red-700 px-4 py-2 rounded mb-4">
        {{ flash.error }}
      </div>

      <!-- Form -->
      <div class="bg-white shadow rounded-lg p-4 mb-6">
        <form @submit.prevent="submit" class="space-y-3">
          <select v-model="form.leave_type_id" class="form-input">
            <option value="">Select Leave Type</option>
            <option v-for="type in props.leaveTypes" :key="type.id" :value="type.id">
              {{ type.name }}
            </option>
          </select>
          <div v-if="form.errors.leave_type_id" class="text-red-600 text-sm">{{ form.errors.leave_type_id }}</div>

          <div class="flex gap-2">
            <input type="date" v-model="form.start_date" class="form-input" />
            <input type="date" v-model="form.end_date" class="form-input" />
          </div>
          <div class="text-sm text-gray-600">Leave duration</div>
          <div v-if="form.errors.start_date" class="text-red-600 text-sm">{{ form.errors.start_date }}</div>
          <div v-if="form.errors.end_date" class="text-red-600 text-sm">{{ form.errors.end_date }}</div>

          <select v-model="form.day_type" class="form-input">
            <option value="full">Full Day</option>
            <option value="half">Half Day</option>
          </select>
          <div v-if="form.errors.day_type" class="text-red-600 text-sm">{{ form.errors.day_type }}</div>

          <textarea v-model="form.reason" class="form-input" placeholder="Reason"></textarea>
          <div v-if="form.errors.reason" class="text-red-600 text-sm">{{ form.errors.reason }}</div>

          <button type="submit" class="bg-orange-500 text-white px-4 py-2 rounded hover:bg-orange-600 transition">
            {{ editing ? 'Update' : 'Apply' }}
          </button>
        </form>
      </div>

      <!-- List -->
      <div class="bg-white shadow rounded-lg overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-orange-50">
            <tr>
              <th class="p-2">Type</th>
              <th class="p-2">From</th>
              <th class="p-2">To</th>
              <th class="p-2">Day Type</th>
              <th class="p-2">Status</th>
              <th class="p-2">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="app in props.applications.data" :key="app.id" class="border-t hover:bg-orange-50 transition">
              <td class="p-2">{{ app.leave_type.name }}</td>
              <td class="p-2">{{ app.start_date }}</td>
              <td class="p-2">{{ app.end_date }}</td>
              <td class="p-2 capitalize">{{ app.day_type }}</td>
              <td class="p-2">{{ app.status }}</td>
              <td class="p-2 space-x-2">
                <button v-if="app.created_at.split('T')[0] === (new Date()).toISOString().split('T')[0]"
                  @click="edit(app)" class="text-blue-600 hover:underline">Edit</button>
                <button v-if="app.created_at.split('T')[0] === (new Date()).toISOString().split('T')[0]"
                  @click="deleteApp(app)" class="text-red-600 hover:underline">Delete</button>
              </td>
            </tr>
          </tbody>
        </table>

        <!-- Pagination -->
        <div class="flex justify-between p-2">
          <button v-if="props.applications.prev_page_url" @click="router.get(props.applications.prev_page_url, {}, { preserveScroll: true })"
            class="px-3 py-1 bg-orange-500 text-white rounded">Previous</button>
          <button v-if="props.applications.next_page_url" @click="router.get(props.applications.next_page_url, {}, { preserveScroll: true })"
            class="px-3 py-1 bg-orange-500 text-white rounded">Next</button>
        </div>
      </div>
    </div>
  </EmployeeLayout>
</template>

<style scoped>
.form-input {
  @apply border border-gray-300 rounded px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-orange-500;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fadeIn {
  animation: fadeIn 0.5s ease-out;
}
</style>
