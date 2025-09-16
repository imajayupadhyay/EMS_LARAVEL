<template>
  <div class="space-y-4">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
      <h2 class="text-2xl font-semibold">Employees</h2>
      <div class="flex items-center gap-2">
        <!-- keep create only if allowed -->
        <Link v-if="canCreate" :href="route('manager.employees.create')" class="inline-block px-3 py-1 rounded bg-blue-600 text-white text-sm">New Employee</Link>
      </div>
    </div>

    <!-- Filters -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:gap-3">
      <input v-model="filtersLocal.name" @keyup.enter="applyFilters" placeholder="Search name or ID" class="input flex-1" />
      <select v-model="filtersLocal.department_id" @change="applyFilters" class="input w-full sm:w-56">
        <option value="">All Departments</option>
        <option v-for="d in departments" :key="d.id" :value="d.id">{{ d.name }}</option>
      </select>
      <select v-model="filtersLocal.designation_id" @change="applyFilters" class="input w-full sm:w-56">
        <option value="">All Designations</option>
        <option v-for="d in designations" :key="d.id" :value="d.id">{{ d.name }}</option>
      </select>
      <div class="flex gap-2 mt-2 sm:mt-0">
        <button @click="applyFilters" class="px-3 py-1 rounded bg-blue-600 text-white text-sm">Apply</button>
        <button @click="resetFilters" class="px-3 py-1 rounded border text-sm">Reset</button>
      </div>
    </div>

    <!-- Desktop Table (md and up) -->
    <div class="bg-white shadow rounded overflow-auto hidden md:block">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">#</th>
            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Name</th>
            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Email</th>
            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Contact</th>
            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Department</th>
            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Designation</th>
            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">DOJ</th>
            <th class="px-4 py-3 text-right text-sm font-medium text-gray-700">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="(emp, idx) in employees.data" :key="emp.id">
            <td class="px-4 py-3 text-sm text-gray-700">{{ (employees.current_page - 1) * employees.per_page + idx + 1 }}</td>
            <td class="px-4 py-3 text-sm">
              <div class="font-medium">{{ emp.first_name }} {{ emp.last_name }}</div>
              <div class="text-xs text-gray-500">{{ emp.employee_code ?? '' }}</div>
            </td>
            <td class="px-4 py-3 text-sm text-gray-700">{{ emp.email }}</td>
            <td class="px-4 py-3 text-sm text-gray-700">{{ emp.contact }}</td>
            <td class="px-4 py-3 text-sm text-gray-700">{{ emp.department?.name ?? '-' }}</td>
            <td class="px-4 py-3 text-sm text-gray-700">{{ emp.designation?.name ?? '-' }}</td>
            <td class="px-4 py-3 text-sm text-gray-700">{{ emp.doj ?? '-' }}</td>
            <td class="px-4 py-3 text-right text-sm">
              <button @click="view(emp.id)" class="inline-block px-2 py-1 rounded bg-blue-50 text-blue-700 text-sm hover:bg-blue-100">View</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Mobile Cards (sm and down) -->
    <div class="space-y-3 md:hidden">
      <div v-for="(emp, idx) in employees.data" :key="emp.id" class="bg-white p-4 rounded shadow">
        <div class="flex items-start justify-between">
          <div>
            <div class="font-medium text-base">{{ emp.first_name }} {{ emp.last_name }}</div>
            <div class="text-xs text-gray-500">{{ emp.employee_code ?? '' }}</div>
          </div>
          <div class="text-sm text-gray-500">{{ emp.doj ?? '-' }}</div>
        </div>

        <div class="mt-3 grid grid-cols-1 gap-1 text-sm text-gray-700">
          <div><span class="font-medium">Email:</span> {{ emp.email }}</div>
          <div><span class="font-medium">Contact:</span> {{ emp.contact }}</div>
          <div><span class="font-medium">Department:</span> {{ emp.department?.name ?? '-' }}</div>
          <div><span class="font-medium">Designation:</span> {{ emp.designation?.name ?? '-' }}</div>
        </div>

        <div class="mt-3 flex justify-end">
          <button @click="view(emp.id)" class="px-3 py-1 rounded bg-blue-600 text-white text-sm">View</button>
        </div>
      </div>
    </div>

    <!-- Pagination -->
    <div v-if="employees && employees.last_page > 1" class="mt-2">
      <Pagination :data="employees" @pagination-change-page="gotoPage" />
    </div>

    <!-- Detail Modal -->
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
      <div class="bg-white rounded shadow-lg w-full max-w-2xl p-6 overflow-auto max-h-[90vh]">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold">{{ detail.full_name }}</h3>
          <button @click="closeModal" class="text-gray-500 text-lg">✕</button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm text-gray-700">
          <div><strong>Email:</strong> {{ detail.email }}</div>
          <div><strong>Contact:</strong> {{ detail.contact }}</div>
          <div><strong>Emergency Contact:</strong> {{ detail.emergency_contact }}</div>
          <div><strong>Gender:</strong> {{ detail.gender }}</div>
          <div><strong>DOB:</strong> {{ detail.dob }}</div>
          <div><strong>DOJ:</strong> {{ detail.doj }}</div>
          <div><strong>Department:</strong> {{ detail.department?.name ?? '-' }}</div>
          <div><strong>Designation:</strong> {{ detail.designation?.name ?? '-' }}</div>
          <div class="md:col-span-2"><strong>Address:</strong> {{ detail.address }}</div>
        </div>

        <div class="mt-6 text-right">
          <button @click="closeModal" class="px-3 py-1 rounded border text-sm">Close</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import ManagerLayout from '@/Layouts/ManagerLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import { Link } from '@inertiajs/vue3';

export default {
  layout: ManagerLayout,
  props: {
    employees: Object,
    departments: Array,
    designations: Array,
    filters: Object,
    canCreate: { type: Boolean, default: false },
  },
  components: { Pagination, Link },
  data() {
    return {
      filtersLocal: {
        name: this.filters?.name ?? '',
        department_id: this.filters?.department_id ?? '',
        designation_id: this.filters?.designation_id ?? '',
      },
      showModal: false,
      detail: {},
      csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
    };
  },
  methods: {
    applyFilters() {
      const params = new URLSearchParams();
      if (this.filtersLocal.name) params.append('name', this.filtersLocal.name);
      if (this.filtersLocal.department_id) params.append('department_id', this.filtersLocal.department_id);
      if (this.filtersLocal.designation_id) params.append('designation_id', this.filtersLocal.designation_id);

      const url = route('manager.employees.index') + (params.toString() ? `?${params.toString()}` : '');
      window.location.href = url;
    },
    resetFilters() {
      this.filtersLocal = { name: '', department_id: '', designation_id: '' };
      this.applyFilters();
    },
    gotoPage(page) {
      const params = new URLSearchParams(window.location.search);
      params.set('page', page);
      window.location.href = route('manager.employees.index') + '?' + params.toString();
    },
    async view(id) {
      this.showModal = true;
      this.detail = {};
      try {
        const res = await fetch(route('manager.employees.show', id), { headers: { Accept: 'application/json' } });
        const json = await res.json();
        if (json?.success) this.detail = json.data;
        else this.detail = {};
      } catch (e) {
        this.detail = {};
      }
    },
    closeModal() {
      this.showModal = false;
      this.detail = {};
    },
  },
};
</script>

<style scoped>
.input { padding: 0.5rem 0.75rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; }
button[disabled] { opacity: 0.6; cursor: not-allowed; }
</style>
