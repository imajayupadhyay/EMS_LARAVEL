<script setup>
import { ref } from 'vue';
import { useForm, router, Head } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
  assignments: Object,
  employees: Array,
  leaveTypes: Array,
  flash: Object
});

const form = useForm({
  employee_id: '',
  leave_type_id: '',
  total_assigned: '',
  balance: ''
});

const editing = ref(null);

const submit = () => {
  if (editing.value) {
    form.post(route('admin.leave-assignments.update', editing.value.id), {
      preserveScroll: true,
      onSuccess: () => {
        form.reset();
        editing.value = null;
      }
    });
  } else {
    form.post(route('admin.leave-assignments.store'), {
      preserveScroll: true,
      onSuccess: () => form.reset()
    });
  }
};

const edit = (assignment) => {
  form.employee_id = assignment.employee?.id || '';
  form.leave_type_id = assignment.leave_type?.id || '';
  form.total_assigned = assignment.total_assigned;
  form.balance = assignment.balance;
  editing.value = assignment;
};

const deleteAssignment = (assignment) => {
  if (confirm(`Delete leave assignment for ${assignment.employee ? assignment.employee.first_name : 'NA'}?`)) {
    router.post(route('admin.leave-assignments.destroy', assignment.id), { preserveScroll: true });
  }
};
</script>

<template>
  <AdminLayout>
    <Head title="Leave Assignments" />
    <div class="max-w-5xl mx-auto p-4">
      <h1 class="text-3xl font-bold text-orange-600 mb-6">📝 Leave Assignments</h1>

      <div v-if="flash?.success" class="bg-green-100 border border-green-300 text-green-700 px-4 py-2 rounded mb-4 shadow">
        {{ flash.success }}
      </div>

      <div class="bg-white rounded-lg shadow p-4 mb-6 border border-gray-200">
        <h2 class="font-semibold mb-2">{{ editing ? 'Edit Assignment' : 'Add Assignment' }}</h2>
        <form @submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <select v-model="form.employee_id" class="w-full border rounded px-3 py-2">
              <option value="">Select Employee</option>
              <option v-for="emp in employees" :key="emp.id" :value="emp.id">
                {{ emp.first_name }} {{ emp.last_name }}
              </option>
            </select>
            <div v-if="form.errors.employee_id" class="text-red-600 text-sm">{{ form.errors.employee_id }}</div>
          </div>
          <div>
            <select v-model="form.leave_type_id" class="w-full border rounded px-3 py-2">
              <option value="">Select Leave Type</option>
              <option v-for="lt in leaveTypes" :key="lt.id" :value="lt.id">{{ lt.name }}</option>
            </select>
            <div v-if="form.errors.leave_type_id" class="text-red-600 text-sm">{{ form.errors.leave_type_id }}</div>
          </div>
          <div>
            <input type="number" v-model="form.total_assigned" placeholder="Total Assigned" class="w-full border rounded px-3 py-2" />
            <div v-if="form.errors.total_assigned" class="text-red-600 text-sm">{{ form.errors.total_assigned }}</div>
          </div>
          <div>
            <input type="number" v-model="form.balance" placeholder="Balance" class="w-full border rounded px-3 py-2" />
            <div v-if="form.errors.balance" class="text-red-600 text-sm">{{ form.errors.balance }}</div>
          </div>
          <div class="md:col-span-2 text-right">
            <button type="submit" class="bg-orange-500 text-white px-4 py-2 rounded shadow hover:bg-orange-600">
              {{ editing ? 'Update' : 'Add' }}
            </button>
          </div>
        </form>
      </div>

      <div class="bg-white rounded-lg shadow border border-gray-200 overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="bg-orange-50 text-gray-700">
            <tr>
              <th class="py-2 px-3 text-left">Employee</th>
              <th class="py-2 px-3 text-left">Leave Type</th>
              <th class="py-2 px-3 text-left">Total Assigned</th>
              <th class="py-2 px-3 text-left">Balance</th>
              <th class="py-2 px-3 text-left">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="a in assignments.data" :key="a.id" class="border-t hover:bg-orange-50">
              <td class="py-2 px-3">
                {{ a.employee ? (a.employee.first_name + ' ' + a.employee.last_name) : 'NA' }}
              </td>
              <td class="py-2 px-3">{{ a.leave_type?.name || 'NA' }}</td>
              <td class="py-2 px-3">{{ a.total_assigned }}</td>
              <td class="py-2 px-3">{{ a.balance }}</td>
              <td class="py-2 px-3 space-x-2">
                <button @click="edit(a)" class="text-blue-600 hover:underline font-semibold">Edit</button>
                <button @click="deleteAssignment(a)" class="text-red-600 hover:underline font-semibold">Delete</button>
              </td>
            </tr>
          </tbody>
        </table>

        <div class="flex justify-between p-3">
          <button v-if="assignments.prev_page_url" @click="router.get(assignments.prev_page_url, {}, { preserveScroll: true })"
            class="bg-orange-500 text-white px-3 py-1 rounded shadow hover:bg-orange-600">
            Previous
          </button>
          <button v-if="assignments.next_page_url" @click="router.get(assignments.next_page_url, {}, { preserveScroll: true })"
            class="bg-orange-500 text-white px-3 py-1 rounded shadow hover:bg-orange-600">
            Next
          </button>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
