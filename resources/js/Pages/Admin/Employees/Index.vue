<template>
  <Head title="Employee Management">
    <meta name="description" content="Manage and view all employees in the organization." head-key="description" />
  </Head>
  <AdminLayout>
    <div class="p-4">
      <EmployeeList :employees="employees" :departments="departments" :designations="designations"  :shifts="shifts" :filters="filters" />
    </div>
  </AdminLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3'
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
