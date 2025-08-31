<template>
  <AdminLayout>
    <div class="p-6">
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Manage Marketers</h1>
        <a href="/admin/marketers/create" class="btn-primary">+ Add New Marketer</a>
      </div>

      <div class="bg-white rounded-2xl shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-100">
            <tr>
              <th class="px-4 py-2">Name</th>
              <th class="px-4 py-2">Email</th>
              <th class="px-4 py-2">Username</th>
              <th class="px-4 py-2">Contact</th>
              <th class="px-4 py-2">Last Location</th>
              <th class="px-4 py-2">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="m in marketers" :key="m.id">
              <td class="px-4 py-2">{{ m.first_name }} {{ m.last_name }}</td>
              <td class="px-4 py-2">{{ m.email }}</td>
              <td class="px-4 py-2">{{ m.username }}</td>
              <td class="px-4 py-2">{{ m.contact }}</td>
              <td class="px-4 py-2">
                <span v-if="m.locations.length > 0">
                  Lat: {{ m.locations[m.locations.length - 1].latitude }},
                  Lng: {{ m.locations[m.locations.length - 1].longitude }}
                </span>
                <span v-else class="text-gray-400">No location</span>
              </td>
              <td class="px-4 py-2 flex gap-2">
                <a :href="`/admin/marketers/${m.id}/edit`" class="btn-secondary">Edit</a>
                <button @click="deleteMarketer(m.id)" class="btn-danger">Delete</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { router } from "@inertiajs/vue3";

defineProps({
  marketers: Array
});

const deleteMarketer = (id) => {
  if (!confirm("Are you sure you want to delete this marketer?")) return;
  router.delete(`/admin/marketers/${id}`);
};
</script>

<style scoped>
.btn-primary {
  @apply bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow transition;
}
.btn-secondary {
  @apply bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded;
}
.btn-danger {
  @apply bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded;
}
</style>
