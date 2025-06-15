<template>
  <div>
    <h1 class="text-2xl font-bold text-orange-600 mb-4">Notifications</h1>
    <div class="bg-white rounded shadow p-4">
      <div v-if="notifications.length">
        <div v-for="note in notifications" :key="note.id" class="border-b py-2">
          <div class="font-semibold">{{ note.title }}</div>
          <div class="text-sm">{{ note.message }}</div>
          <div class="text-xs text-gray-500">{{ note.created_at }}</div>
        </div>
      </div>
      <div v-else class="text-gray-500">No notifications found.</div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  notifications: Array
})
</script>
