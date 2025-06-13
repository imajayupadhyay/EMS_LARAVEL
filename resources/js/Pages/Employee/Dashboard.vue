<template>
  <div>
    <h1 class="text-2xl font-bold text-orange-600 mb-4">Welcome, {{ auth?.user?.name || 'Employee' }}</h1>
    <p class="text-gray-600">This is your employee dashboard.</p>
  </div>
</template>

<script setup>
import { usePage } from '@inertiajs/vue3'
import EmployeeLayout from '@/Layouts/EmployeeLayout.vue' // <-- import layout

const page = usePage()
const auth = page.props.auth || {}

defineOptions({ layout: EmployeeLayout }) // <-- assign layout
</script>
