<script setup>
import { ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import EmployeeLayout from '@/Layouts/EmployeeLayout.vue';

const props = defineProps({
  policies: Array
});
</script>

<template>
  <EmployeeLayout>
    <div class="max-w-4xl mx-auto p-6">
      <h1 class="text-2xl font-semibold mb-4">Company Policies</h1>

      <div v-if="props.policies.length === 0" class="bg-white shadow rounded p-6 text-center">
        No policies available.
      </div>

      <div v-else class="space-y-4">
        <div v-for="p in props.policies" :key="p.id" class="bg-white shadow rounded p-4">
          <div class="flex justify-between items-start">
            <div>
              <h2 class="text-xl font-semibold">{{ p.title }}</h2>
              <div class="text-sm text-gray-500 mt-1">Published: {{ new Date(p.created_at).toLocaleDateString() }}</div>
            </div>
            <div>
              <button @click="$modal.show('policy-'+p.id)" class="text-indigo-600">View</button>
            </div>
          </div>

          <!-- Simple inline content preview -->
          <div class="mt-3 text-gray-700" v-html="p.content"></div>
        </div>
      </div>
    </div>
  </EmployeeLayout>
</template>

<style scoped>
/* Minimal styling - adapt to your design system */
</style>
