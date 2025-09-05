<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { usePage, router, Head } from '@inertiajs/vue3';
import EmployeeLayout from '@/Layouts/EmployeeLayout.vue';

const props = defineProps({
  punches: Array,
  allowedLocation: Object,
  isPunchedIn: Boolean,
  date: String
});

const flash = usePage().props.flash || {};
const userLocation = ref(null);
const isWithinRange = ref(false);
const timer = ref(0);
let interval = null;
const todayDate = ref(new Date().toDateString());

// ✅ Popup state
const popup = ref({ show: false, message: '', type: 'success' });
const showPopup = (msg, type = 'success') => {
  popup.value = { show: true, message: msg, type };
  setTimeout(() => (popup.value.show = false), 3000);
};

// 🚀 Calculate total worked seconds so far
const calculateTotalWorkedSeconds = () => {
  let total = 0;
  props.punches.forEach(p => {
    if (p.punched_in_at && p.punched_out_at) {
      total += Math.floor((new Date(p.punched_out_at) - new Date(p.punched_in_at)) / 1000);
    } else if (p.punched_in_at && !p.punched_out_at) {
      total += Math.floor((new Date() - new Date(p.punched_in_at)) / 1000);
    }
  });
  return total;
};

// 🚀 Location logic (commented check for now)
const getLocation = () => {
  navigator.geolocation.getCurrentPosition(
    (pos) => {
      userLocation.value = {
        lat: pos.coords.latitude,
        lng: pos.coords.longitude
      };
      // checkProximity();  // commented for now
      isWithinRange.value = true; // ✅ force allow for now
    },
    () => isWithinRange.value = false
  );
};

// 🚀 Timer control
const startTimer = () => {
  stopTimer();
  interval = setInterval(() => {
    timer.value++;
    checkDateChange();
  }, 1000);
};
const stopTimer = () => { if (interval) clearInterval(interval); };
const checkDateChange = () => {
  if (new Date().toDateString() !== todayDate.value) {
    todayDate.value = new Date().toDateString();
    timer.value = 0;
    stopTimer();
  }
};

// 🚀 Handle punch in/out
const handlePunch = () => {
  router.post(route('employee.punches.store'), {
    location: userLocation.value
      ? `${userLocation.value.lat},${userLocation.value.lng}`
      : null
  }, {
    preserveScroll: true,
    onSuccess: () => {
      showPopup(props.isPunchedIn ? "You have punched IN 🕒" : "You have punched OUT ✅", "success");
      router.reload({ only: ['isPunchedIn', 'punches'] });
    },
    onError: () => {
      showPopup("Something went wrong ❌", "error");
    }
  });
};

// 🚀 Init
onMounted(() => {
  getLocation();
  timer.value = calculateTotalWorkedSeconds();
  if (props.isPunchedIn) startTimer();
});

// 🚀 Watch punch state
watch(() => props.isPunchedIn, (newVal) => {
  if (newVal) startTimer();
  else stopTimer();
});
</script>

<template>
  <EmployeeLayout>
    <Head title="Punch In / Out" />
    <div class="max-w-4xl mx-auto p-6">
      
      <!-- ✅ Status Banner -->
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
          :disabled="!isWithinRange"
          class="font-semibold px-8 py-3 rounded-lg shadow-md hover:opacity-90 disabled:opacity-50 text-lg"
          :class="props.isPunchedIn 
            ? 'bg-red-600 text-white' 
            : 'bg-green-600 text-white'"
        >
          {{ props.isPunchedIn ? 'Punch Out' : 'Punch In' }}
        </button>
        <p v-if="!isWithinRange" class="text-sm text-red-500 mt-2">
          You must be at the designated location.
        </p>
      </div>
    </div>

    <!-- ✅ Popup -->
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
