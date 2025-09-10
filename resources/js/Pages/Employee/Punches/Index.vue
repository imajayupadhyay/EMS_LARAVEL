<script setup>
import { ref } from 'vue';
import { usePage, Head, router } from '@inertiajs/vue3';
import EmployeeLayout from '@/Layouts/EmployeeLayout.vue';
import axios from 'axios';

const props = defineProps({
  // keep only what's needed in the simplified view
  isPunchedIn: Boolean,
  // punches prop is accepted but not used to render a table in this version
  punches: {
    type: Array,
    default: () => []
  }
});

const flash = usePage().props.flash || {};
const isProcessing = ref(false);

// Popup state
const popup = ref({ show: false, message: '', type: 'success' });
const showPopup = (msg, type = 'success') => {
  popup.value = { show: true, message: msg, type };
  // auto-hide
  setTimeout(() => (popup.value.show = false), 3000);
};

const handlePunch = async () => {
  if (isProcessing.value) return;
  isProcessing.value = true;

  try {
    const res = await axios.post(route('employee.punches.store'));
    if (res.data && res.data.success) {
      showPopup(res.data.message || 'Action successful', 'success');
      // reload only the properties we care about
      await router.reload({ only: ['isPunchedIn', 'punches', 'flash'] });
    } else {
      showPopup((res.data && res.data.message) || 'Punch failed', 'error');
    }
  } catch (err) {
    const data = err.response?.data;
    if (data?.message) {
      showPopup(data.message, 'error');
      console.error('Punch error details:', data);
    } else {
      showPopup('Something went wrong ❌', 'error');
      console.error('Punch error', err);
    }
  } finally {
    isProcessing.value = false;
  }
};

// show sunday flash if any
if (flash && flash.sunday_incremented) {
  showPopup('Sunday leave credited +1 ✅', 'success');
}
</script>

<template>
  <EmployeeLayout>
    <Head title="Punch In / Out" />
    <div class="max-w-4xl mx-auto p-6">
      <!-- Status Banner -->
      <div
        :class="props.isPunchedIn ? 'bg-green-100 text-green-700 border-green-400' : 'bg-red-100 text-red-700 border-red-400'"
        class="border px-4 py-3 rounded mb-6 text-center font-semibold text-lg shadow-sm"
      >
        {{ props.isPunchedIn ? '✅ You are Punched In' : '❌ You are Punched Out' }}
      </div>

      <!-- Punch Button -->
      <div class="bg-white shadow-lg rounded-lg p-6 text-center">
        <button
          @click="handlePunch"
          :disabled="isProcessing"
          class="font-semibold px-8 py-3 rounded-lg shadow-md hover:opacity-90 disabled:opacity-50 text-lg"
          :class="props.isPunchedIn ? 'bg-red-600 text-white' : 'bg-green-600 text-white'"
        >
          {{ isProcessing ? 'Processing...' : (props.isPunchedIn ? 'Punch Out' : 'Punch In') }}
        </button>

        <!-- optional guidance -->
        <p class="text-sm text-gray-500 mt-3">
          Click to {{ props.isPunchedIn ? 'punch out' : 'punch in' }}. If something fails, you will see an error popup.
        </p>
      </div>

      <!-- lightweight punches summary (optional) -->
      <div v-if="props.punches && props.punches.length" class="mt-6 bg-gray-50 border rounded-lg p-4">
        <p class="font-medium text-gray-700 mb-2">Today’s punches (summary)</p>
        <ul class="text-sm text-gray-600">
          <li v-for="(p, i) in props.punches" :key="p.id" class="py-1">
            {{ i + 1 }}.
            <span v-if="p.punched_in_at">In: {{ new Date(p.punched_in_at).toLocaleTimeString() }}</span>
            <span v-else>In: -</span>
            &nbsp;|&nbsp;
            <span v-if="p.punched_out_at">Out: {{ new Date(p.punched_out_at).toLocaleTimeString() }}</span>
            <span v-else>Out: -</span>
          </li>
        </ul>
      </div>
    </div>

    <!-- Popup -->
    <transition name="fade">
      <div v-if="popup.show"
           :class="popup.type === 'error' ? 'bg-red-600' : 'bg-green-600'"
           class="fixed bottom-6 right-6 px-6 py-3 rounded-lg shadow-lg text-white font-semibold z-50">
        {{ popup.message }}
      </div>
    </transition>
  </EmployeeLayout>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>
