<template>
  <div>
    <h1 class="text-2xl font-bold text-orange-600 mb-6">Manage Locations</h1>

    <!-- Form -->
    <form @submit.prevent="form.id ? updateLocation() : createLocation()" class="mb-6 bg-white p-4 shadow rounded">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <input v-model="form.name" type="text" placeholder="Location Name" class="input" required />
        <input v-model="form.latitude" type="text" placeholder="Latitude" class="input" required />
        <input v-model="form.longitude" type="text" placeholder="Longitude" class="input" required />
      </div>
      <div class="mt-4">
        <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded">
          {{ form.id ? 'Update Location' : 'Add Location' }}
        </button>
        <button v-if="form.id" @click="resetForm" type="button" class="ml-2 text-gray-500 hover:underline">Cancel</button>
      </div>
    </form>

    <!-- Table -->
    <div class="bg-white shadow rounded">
      <table class="min-w-full text-sm">
        <thead class="bg-orange-100 text-left">
          <tr>
            <th class="p-4">Name</th>
            <th class="p-4">Latitude</th>
            <th class="p-4">Longitude</th>
            <th class="p-4 text-right">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="location in locations" :key="location.id" class="border-t">
            <td class="p-4">{{ location.name }}</td>
            <td class="p-4">{{ location.latitude }}</td>
            <td class="p-4">{{ location.longitude }}</td>
            <td class="p-4 text-right space-x-2">
              <button @click="editLocation(location)" class="text-blue-500 hover:underline">Edit</button>
              <button @click="deleteLocation(location.id)" class="text-red-500 hover:underline">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import { defineProps } from 'vue'

const props = defineProps({
  locations: Array,
  flash: Object
})

const form = useForm({
  id: null,
  name: '',
  latitude: '',
  longitude: ''
})

const createLocation = () => {
  form.post(route('admin.locations.store'), {
    onSuccess: () => resetForm(),
  })
}

const updateLocation = () => {
  form.post(route('admin.locations.update', form.id), {
    preserveScroll: true,
    onSuccess: () => resetForm(),
    _method: 'put',
  })
}

const deleteLocation = (id) => {
  if (confirm('Are you sure you want to delete this location?')) {
    router.post(route('admin.locations.destroy', id), {}, { _method: 'delete' })
  }
}

const editLocation = (location) => {
  form.id = location.id
  form.name = location.name
  form.latitude = location.latitude
  form.longitude = location.longitude
}

const resetForm = () => {
  form.reset()
  form.id = null
}
</script>

<style scoped>
.input {
  width: 100%;
  padding: 0.5rem;
  border: 1px solid #ccc;
  border-radius: 6px;
}
</style>
