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

// 🚀 Calculate total worked seconds so far (completed punches + current)
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

// 🚀 Timer display HH:MM:SS
const displayTimer = computed(() => {
  const h = String(Math.floor(timer.value / 3600)).padStart(2, '0');
  const m = String(Math.floor((timer.value % 3600) / 60)).padStart(2, '0');
  const s = String(timer.value % 60).padStart(2, '0');
  return `${h}:${m}:${s}`;
});

// 🚀 Location logic
const getLocation = () => {
  navigator.geolocation.getCurrentPosition(
    (pos) => {
      userLocation.value = {
        lat: pos.coords.latitude,
        lng: pos.coords.longitude
      };
      checkProximity();
    },
    () => isWithinRange.value = false
  );
};

const checkProximity = () => {
  if (!userLocation.value || !props.allowedLocation) return;
  const toRad = (v) => (v * Math.PI) / 180;
  const earthRadius = 6371;
  const dLat = toRad(props.allowedLocation.lat - userLocation.value.lat);
  const dLng = toRad(props.allowedLocation.lng - userLocation.value.lng);
  const a = Math.sin(dLat/2)**2 + Math.cos(toRad(userLocation.value.lat)) * Math.cos(toRad(props.allowedLocation.lat)) * Math.sin(dLng/2)**2;
  const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
  isWithinRange.value = (earthRadius * c) <= 0.5;
};

// 🚀 Timer control
const startTimer = () => {
  stopTimer();
  interval = setInterval(() => {
    timer.value++;
    checkDateChange();
  }, 1000);
};

const stopTimer = () => {
  if (interval) clearInterval(interval);
};

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
    location: `${userLocation.value.lat},${userLocation.value.lng}`
  }, {
    preserveScroll: true,
    onSuccess: () => {
      router.visit(route('employee.punches.index'), { preserveScroll: true });
    }
  });
};

// 🚀 Init on load
onMounted(() => {
  getLocation();
  timer.value = calculateTotalWorkedSeconds();
  if (props.isPunchedIn) startTimer();
});

// 🚀 Watch punch state change (reactive on reload)
watch(() => props.isPunchedIn, (newVal) => {
  if (newVal) {
    startTimer();
  } else {
    stopTimer();
  }
});
</script>

<template>
  <EmployeeLayout>
    <Head title="Punch In / Out" />
    <div class="max-w-4xl mx-auto p-6">
      <div class="bg-white shadow-lg rounded-lg p-6">
        <h1 class="text-2xl font-bold text-orange-600 mb-4">Punch In / Punch Out</h1>

        <div class="text-center text-4xl font-mono mb-4 text-gray-800">
          🕒 {{ displayTimer }}
        </div>

        <button
          @click="handlePunch"
          :disabled="!isWithinRange"
          class="bg-orange-500 text-white font-semibold px-6 py-3 rounded hover:bg-orange-600 disabled:opacity-50"
        >
          {{ props.isPunchedIn ? 'Punch Out' : 'Punch In' }}
        </button>

        <p v-if="!isWithinRange" class="text-sm text-red-500 mt-2">You must be at the designated location.</p>
      </div>

      <div class="mt-6 bg-white shadow rounded-lg p-4">
        <h2 class="text-lg font-semibold text-gray-700 mb-3">Punch Log</h2>
        <table class="w-full text-sm">
          <thead class="bg-gray-100">
            <tr>
              <th class="p-2">Punch In</th>
              <th class="p-2">Punch Out</th>
              <th class="p-2">Duration</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="p in props.punches" :key="p.id" class="border-t">
              <td class="p-2">{{ p.punched_in_at ? new Date(p.punched_in_at).toLocaleTimeString() : '—' }}</td>
              <td class="p-2">{{ p.punched_out_at ? new Date(p.punched_out_at).toLocaleTimeString() : '—' }}</td>
              <td class="p-2">
                <span v-if="p.punched_out_at">
                  {{
                    Math.floor((new Date(p.punched_out_at) - new Date(p.punched_in_at)) / 3600000)
                  }}h {{
                    Math.floor(((new Date(p.punched_out_at) - new Date(p.punched_in_at)) % 3600000) / 60000)
                  }}m
                </span>
                <span v-else>—</span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </EmployeeLayout>
</template>

<style scoped>
.font-mono {
  font-family: monospace;
}
</style>
