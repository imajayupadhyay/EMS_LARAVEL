]<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import axios from 'axios';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
  policies: Object,
  filters: Object
});

const deleting = ref(null); // holds id that's deleting, or null

function buildUrlencoded(obj) {
  const params = new URLSearchParams();
  Object.entries(obj).forEach(([k,v]) => {
    if (Array.isArray(v)) {
      v.forEach(item => params.append(`${k}[]`, item));
    } else if (v !== undefined && v !== null) {
      params.append(k, v);
    }
  });
  return params;
}

const confirmDelete = async (id) => {
  if (!confirm('Delete this policy? This will soft-delete the policy.')) return;
  deleting.value = id;
  try {
    const url = route('admin.policies.destroy', id);
    // Use POST + _method=DELETE as form-encoded so Laravel recognizes method spoofing.
    const body = buildUrlencoded({ _method: 'DELETE' });
    const res = await axios.post(url, body);
    if (res.data && res.data.success) {
      await router.reload({ only: ['policies', 'flash'] });
    } else {
      alert('Delete failed: ' + (res.data?.message || 'Unknown error'));
      console.error('Delete response:', res.data);
    }
  } catch (err) {
    console.error('Delete failed', err.response?.data ?? err);
    alert('Delete request failed. See console/network tab.');
  } finally {
    deleting.value = null;
  }
};
</script>

<template>
  <AdminLayout>
    <div class="max-w-6xl mx-auto p-6">
      <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-extrabold">Policies</h1>
        <div>
          <Link :href="route('admin.policies.create')" class="inline-block rounded-lg px-4 py-2 bg-indigo-600 text-white hover:bg-indigo-700">Create Policy</Link>
        </div>
      </div>

      <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-4 text-left text-sm font-medium text-gray-600">Title</th>
              <th class="px-6 py-4 text-left text-sm font-medium text-gray-600">Audience</th>
              <th class="px-6 py-4 text-left text-sm font-medium text-gray-600">Active</th>
              <th class="px-6 py-4 text-left text-sm font-medium text-gray-600">Created</th>
              <th class="px-6 py-4 text-right text-sm font-medium text-gray-600">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-100">
            <tr v-for="p in props.policies.data" :key="p.id">
              <td class="px-6 py-4">
                <div class="font-semibold text-gray-800">{{ p.title }}</div>
                <div class="text-xs text-gray-400 mt-1">slug: {{ p.slug }}</div>
              </td>
              <td class="px-6 py-4 capitalize text-gray-700">{{ p.audience }}</td>
              <td class="px-6 py-4">
                <span :class="p.is_active ? 'text-green-600 font-semibold' : 'text-gray-400'">
                  {{ p.is_active ? 'Active' : 'Inactive' }}
                </span>
              </td>
              <td class="px-6 py-4 text-sm text-gray-600">{{ new Date(p.created_at).toLocaleString() }}</td>
              <td class="px-6 py-4 text-right">
                <Link :href="route('admin.policies.edit', p.id)" class="text-indigo-600 mr-4">Edit</Link>
                <button :disabled="deleting === p.id" @click="confirmDelete(p.id)" class="text-red-600">
                  <span v-if="deleting === p.id">Deleting…</span>
                  <span v-else>Delete</span>
                </button>
              </td>
            </tr>
          </tbody>
        </table>

        <div class="p-4 flex items-center justify-between bg-gray-50">
          <div class="text-sm text-gray-600">Showing {{ props.policies.from }} - {{ props.policies.to }} of {{ props.policies.total }}</div>
          <div>
            <Link v-if="props.policies.prev_page_url" :href="props.policies.prev_page_url" class="px-3 py-1 border rounded mr-2">Prev</Link>
            <Link v-if="props.policies.next_page_url" :href="props.policies.next_page_url" class="px-3 py-1 border rounded">Next</Link>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<style scoped>
/* small cosmetic polish (works with Tailwind) */
table td, table th { vertical-align: middle; }
</style>
