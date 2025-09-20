<template>
  <AdminLayout>
    <div class="p-4">
      <EmployeeList :employees="employees" :departments="departments" :designations="designations"  :shifts="shifts" :filters="filters" />
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
  shifts: Array, 
  filters: Object,
})
</script>
