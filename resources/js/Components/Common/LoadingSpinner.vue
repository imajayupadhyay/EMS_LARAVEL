<template>
  <div class="flex items-center justify-center py-12">
    <div class="flex flex-col items-center space-y-3">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
      <p v-if="text" class="text-sm text-gray-500">{{ text }}</p>
    </div>
  </div>
</template>

<script>
export default {
  name: 'LoadingSpinner',
  
  props: {
    text: {
      type: String,
      default: 'Loading...'
    }
  }
};
</script>