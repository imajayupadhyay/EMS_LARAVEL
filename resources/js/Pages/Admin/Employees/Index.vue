<template>
  <AdminLayout>
    <div class="p-4">
      <h1 class="text-2xl font-bold text-orange-600 mb-4">Manage Employees</h1>
      <EmployeeList :employees="employees" :departments="departments" :designations="designations" :filters="filters" />
    </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import EmployeeList from './EmployeeList.vue'

const props = defineProps({
  employees: Array,
  departments: Array,
  designations: Array,
  filters: Object,
})
</script>
