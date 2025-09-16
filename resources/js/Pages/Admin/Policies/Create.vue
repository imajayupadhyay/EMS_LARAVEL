<script setup>
import { reactive, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
  departments: Array,
  designations: Array,
  employees: Array
});

const form = reactive({
  title: '',
  slug: '',
  content: '',
  audience: 'all',
  audience_value: [],
  is_active: true,
});

const saving = ref(false);

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
    const payload = buildUrlencoded({
      title: form.title,
      slug: form.slug || undefined,
      content: form.content,
      audience: form.audience,
      'audience_value': form.audience_value,
      is_active: form.is_active ? 1 : 0
    });
    const res = await axios.post(route('admin.policies.store'), payload);
    if (res.data && res.data.success === false) {
      alert('Save failed: ' + (res.data.message || 'unknown'));
      return;
    }
    // redirect or reload
    router.visit(route('admin.policies.index'));
  } catch (err) {
    console.error(err.response?.data ?? err);
    if (err.response?.status === 422) {
      // validation error
      alert('Validation failed. Check form fields.');
    } else {
      alert('Save failed. See console / network.');
    }
  } finally {
    saving.value = false;
  }
};
</script>

<template>
  <AdminLayout>
    <div class="max-w-3xl mx-auto p-6">
      <div class="bg-white rounded-lg shadow-lg p-6">
        <h1 class="text-2xl font-bold mb-4">Create Policy</h1>

        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700">Title</label>
            <input v-model="form.title" class="mt-1 w-full border rounded p-2" type="text" />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Slug (optional)</label>
            <input v-model="form.slug" class="mt-1 w-full border rounded p-2" type="text" placeholder="auto-generated if empty" />
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
            <button @click="submit" :disabled="saving" class="rounded-lg px-4 py-2 bg-green-600 text-white hover:bg-green-700">
              {{ saving ? 'Saving…' : 'Save Policy' }}
            </button>
            <a :href="route('admin.policies.index')" class="text-sm text-gray-600">Cancel</a>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<style scoped>
/* minor custom polish */
</style>
