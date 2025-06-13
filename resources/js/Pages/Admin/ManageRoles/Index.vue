<template>
  <div class="max-w-7xl mx-auto py-10 px-6">
    <h1 class="text-2xl font-bold text-orange-600 mb-6">Manage Roles</h1>

    <div class="flex gap-4 mb-6">
      <button @click="activeTab = 'departments'" :class="tabClass('departments')">Departments</button>
      <button @click="activeTab = 'designations'" :class="tabClass('designations')">Designations</button>
    </div>

    <DepartmentList v-if="activeTab === 'departments'" />
    <DesignationList v-if="activeTab === 'designations'" />
  </div>
</template>

<script setup>
import { ref } from 'vue'
import DepartmentList from '../Departments/DepartmentList.vue'
import DesignationList from '../Designations/DesignationList.vue'

const activeTab = ref('departments')

const tabClass = (tab) => [
  'px-4 py-2 rounded font-semibold',
  activeTab.value === tab ? 'bg-orange-500 text-white' : 'bg-gray-200 text-gray-600'
]
</script>
