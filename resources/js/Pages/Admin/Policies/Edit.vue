<script setup>
import { reactive, ref } from 'vue';
import axios from 'axios';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
  policy: Object,
  departments: Array,
  designations: Array,
  employees: Array
});

const form = reactive({
  title: props.policy.title || '',
  slug: props.policy.slug || '',
  content: props.policy.content || '',
  audience: props.policy.audience || 'all',
  audience_value: props.policy.audience_value ? [...props.policy.audience_value] : [],
  is_active: props.policy.is_active ?? true,
});

const saving = ref(false);
const deleting = ref(false);

function buildUrlencoded(obj) {
  const params = new URLSearchParams();
  Object.entries(obj).forEach(([k, v]) => {
    if (Array.isArray(v)) {
      v.forEach(item => params.append(`${k}[]`, item));
    } else if (v !== undefined && v !== null) {
      params.append(k, v);
    }
  });
  return params;
}

const submit = async () => {
  saving.value = true;
  try {
    const url = route('admin.policies.update', props.policy.id);
    const payload = buildUrlencoded({
      _method: 'PUT',
      title: form.title,
      slug: form.slug || undefined,
      content: form.content,
      audience: form.audience,
      audience_value: form.audience_value,
      is_active: form.is_active ? 1 : 0
    });
    const res = await axios.post(url, payload);
    if (res.data && res.data.success === false) {
      alert('Update failed: ' + (res.data.message || 'Unknown'));
      return;
    }
    router.visit(route('admin.policies.index'));
  } catch (err) {
    console.error('Update error', err.response?.data ?? err);
    if (err.response?.status === 422) {
      alert('Validation failed. Check fields.');
    } else {
      alert('Update failed. See console / network.');
    }
  } finally {
    saving.value = false;
  }
};

const remove = async () => {
  if (!confirm('Delete this policy?')) return;
  deleting.value = true;
  try {
    const url = route('admin.policies.destroy', props.policy.id);
    const payload = buildUrlencoded({ _method: 'DELETE' });
    const res = await axios.post(url, payload);
    if (res.data && res.data.success === false) {
      alert('Delete failed: ' + (res.data.message || 'Unknown'));
      return;
    }
    router.visit(route('admin.policies.index'));
  } catch (err) {
    console.error('Delete error', err.response?.data ?? err);
    alert('Delete failed. See console / network.');
  } finally {
    deleting.value = false;
  }
};
</script>

<template>
  <AdminLayout>
    <div class="max-w-3xl mx-auto p-6">
      <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex items-start justify-between mb-4">
          <h1 class="text-2xl font-bold">Edit Policy</h1>
          <button @click="remove" :disabled="deleting" class="text-red-600">
            {{ deleting ? 'Deleting…' : 'Delete' }}
          </button>
        </div>

        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700">Title</label>
            <input v-model="form.title" class="mt-1 w-full border rounded p-2" type="text" />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Slug (optional)</label>
            <input v-model="form.slug" class="mt-1 w-full border rounded p-2" type="text" />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Content</label>
            <textarea v-model="form.content" rows="8" class="mt-1 w-full border rounded p-2"></textarea>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Audience</label>
            <select v-model="form.audience" class="mt-1 w-full border rounded p-2">
              <option value="all">All Employees</option>
              <option value="department">Departments</option>
              <option value="designation">Designations</option>
              <option value="employee">Specific Employees</option>
              <option value="custom">Custom (mix)</option>
            </select>
          </div>

          <div v-if="form.audience !== 'all'">
            <div v-if="form.audience === 'department' || form.audience === 'custom'">
              <label class="block text-sm font-medium text-gray-700">Departments</label>
              <select v-model="form.audience_value" multiple class="mt-1 w-full border rounded p-2">
                <option v-for="d in props.departments" :value="d.id" :key="d.id">{{ d.name }}</option>
              </select>
            </div>

            <div v-if="form.audience === 'designation' || form.audience === 'custom'">
              <label class="block text-sm font-medium text-gray-700 mt-2">Designations</label>
              <select v-model="form.audience_value" multiple class="mt-1 w-full border rounded p-2">
                <option v-for="d in props.designations" :value="d.id" :key="d.id">{{ d.name }}</option>
              </select>
            </div>

            <div v-if="form.audience === 'employee' || form.audience === 'custom'">
              <label class="block text-sm font-medium text-gray-700 mt-2">Employees</label>
              <select v-model="form.audience_value" multiple class="mt-1 w-full border rounded p-2">
                <option v-for="e in props.employees" :value="e.id" :key="e.id">{{ e.first_name }} {{ e.last_name }} — {{ e.email }}</option>
              </select>
            </div>
          </div>

          <div class="flex items-center">
            <input type="checkbox" v-model="form.is_active" id="is_active" class="mr-2" />
            <label for="is_active" class="text-sm text-gray-700">Active</label>
          </div>

          <div class="flex items-center space-x-3">
            <button @click="submit" :disabled="saving" class="rounded-lg px-4 py-2 bg-blue-600 text-white hover:bg-blue-700">
              {{ saving ? 'Saving…' : 'Save Changes' }}
            </button>
            <a :href="route('admin.policies.index')" class="text-sm text-gray-600">Back</a>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<style scoped>
/* small polish; works with Tailwind */
</style>
