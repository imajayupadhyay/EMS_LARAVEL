<script setup>
import { ref } from 'vue'
import { useForm, router, Head } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  holidays: Object,
  filters: Object,
  flash: Object
})

const form = useForm({
  name: '',
  date: '',
  type: 'public'
})

const editing = ref(null)
const typeFilter = ref(props.filters.type || '')
const dateFilter = ref(props.filters.date || '')

const submit = () => {
  if (editing.value) {
    form.post(route('admin.holidays.update', editing.value.id), {
      preserveScroll: true,
      onSuccess: () => {
        form.reset()
        editing.value = null
      }
    })
  } else {
    form.post(route('admin.holidays.store'), {
      preserveScroll: true,
      onSuccess: () => form.reset()
    })
  }
}

const edit = (holiday) => {
  form.name = holiday.name
  form.date = holiday.date
  form.type = holiday.type
  editing.value = holiday
}

const deleteHoliday = (holiday) => {
  if (confirm(`Delete ${holiday.name}?`)) {
    router.post(route('admin.holidays.destroy', holiday.id), {
      preserveScroll: true
    })
  }
}

const applyFilters = () => {
  router.get(route('admin.holidays.index'), {
    type: typeFilter.value,
    date: dateFilter.value
  }, { preserveScroll: true })
}
</script>

<template>
  <AdminLayout>
    <Head title="Manage Holidays" />

    <div class="max-w-5xl mx-auto p-4">
      <h1 class="text-2xl font-bold text-orange-600 mb-4">🎉 Holiday Management</h1>

      <div v-if="flash?.success" class="bg-green-100 border border-green-300 text-green-700 px-4 py-2 rounded mb-4 shadow">
        {{ flash.success }}
      </div>

      <!-- Filters -->
      <div class="bg-white rounded-lg shadow p-4 mb-6 flex flex-col md:flex-row gap-3 border border-gray-200">
        <div class="flex-1">
          <label class="text-sm font-medium mb-1 block">Type</label>
          <select v-model="typeFilter" @change="applyFilters" class="border rounded px-3 py-2 w-full focus:ring-orange-500 focus:border-orange-500">
            <option value="">All</option>
            <option value="public">Public</option>
            <option value="restricted">Restricted</option>
          </select>
        </div>
        <div class="flex-1">
          <label class="text-sm font-medium mb-1 block">Date</label>
          <input type="date" v-model="dateFilter" @change="applyFilters" class="border rounded px-3 py-2 w-full focus:ring-orange-500 focus:border-orange-500" />
        </div>
      </div>

      <!-- Form -->
      <div class="bg-white rounded-lg shadow p-4 mb-6 border border-gray-200">
        <h2 class="font-semibold text-gray-700 mb-3">{{ editing ? '✏️ Edit Holiday' : '➕ Add Holiday' }}</h2>
        <form @submit.prevent="submit" class="space-y-3">
          <div>
            <input type="text" v-model="form.name" placeholder="Holiday Name" class="border rounded w-full px-3 py-2 focus:ring-orange-500 focus:border-orange-500" />
            <div v-if="form.errors.name" class="text-red-600 text-sm mt-1">{{ form.errors.name }}</div>
          </div>
          <div>
            <input type="date" v-model="form.date" class="border rounded w-full px-3 py-2 focus:ring-orange-500 focus:border-orange-500" />
            <div v-if="form.errors.date" class="text-red-600 text-sm mt-1">{{ form.errors.date }}</div>
          </div>
          <div>
            <select v-model="form.type" class="border rounded w-full px-3 py-2 focus:ring-orange-500 focus:border-orange-500">
              <option value="public">Public</option>
              <option value="restricted">Restricted</option>
            </select>
            <div v-if="form.errors.type" class="text-red-600 text-sm mt-1">{{ form.errors.type }}</div>
          </div>
          <button type="submit" class="bg-orange-500 text-white px-4 py-2 rounded shadow hover:bg-orange-600 transition">
            {{ editing ? 'Update' : 'Add' }}
          </button>
        </form>
      </div>

      <!-- List -->
      <div class="bg-white rounded-lg shadow border border-gray-200">
        <table class="w-full text-sm">
          <thead class="bg-orange-50 text-gray-700">
            <tr>
              <th class="p-2 text-left">Name</th>
              <th class="p-2 text-left">Date</th>
              <th class="p-2 text-left">Type</th>
              <th class="p-2 text-left">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="holiday in holidays.data" :key="holiday.id" class="border-t hover:bg-gray-50 transition">
              <td class="p-2 font-medium text-gray-800">{{ holiday.name }}</td>
              <td class="p-2">{{ holiday.date }}</td>
              <td class="p-2 capitalize">{{ holiday.type }}</td>
              <td class="p-2 space-x-2">
                <button @click="edit(holiday)" class="text-blue-600 hover:underline font-semibold">Edit</button>
                <button @click="deleteHoliday(holiday)" class="text-red-600 hover:underline font-semibold">Delete</button>
              </td>
            </tr>
          </tbody>
        </table>

        <!-- Pagination -->
        <div class="flex justify-between p-3">
          <button
            v-if="holidays.prev_page_url"
            @click="router.get(holidays.prev_page_url, {}, { preserveScroll: true })"
            class="px-3 py-1 bg-orange-500 text-white rounded hover:bg-orange-600 shadow"
          >
            Previous
          </button>
          <button
            v-if="holidays.next_page_url"
            @click="router.get(holidays.next_page_url, {}, { preserveScroll: true })"
            class="px-3 py-1 bg-orange-500 text-white rounded hover:bg-orange-600 shadow"
          >
            Next
          </button>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<style scoped>
/* Enhance transitions if needed */
</style>
