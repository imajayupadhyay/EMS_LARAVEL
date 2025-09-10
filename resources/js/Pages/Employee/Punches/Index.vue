<script setup>
import { ref, onMounted } from 'vue';
import { usePage, Head, router } from '@inertiajs/vue3';
import EmployeeLayout from '@/Layouts/EmployeeLayout.vue';
import axios from 'axios';

const props = defineProps({
  isPunchedIn: Boolean
});

const flash = usePage().props.flash || {};
const isProcessing = ref(false);

// location
const userLocation = ref(null); // { latitude, longitude, formatted }

// popup
const popup = ref({ show: false, message: '', type: 'success' });
const showPopup = (msg, type = 'success') => {
  popup.value = { show: true, message: msg, type };
  setTimeout(() => (popup.value.show = false), 3000);
};

// get geolocation and format it safely
const getLocation = () => {
  if (!('geolocation' in navigator)) {
    showPopup('Geolocation not supported by this browser.', 'error');
    return;
  }

  navigator.geolocation.getCurrentPosition(
    (pos) => {
      const lat = Number(pos.coords.latitude);
      const lng = Number(pos.coords.longitude);
      // format to fixed length decimals to avoid locale-variations and unnecessary precision
      const formatted = `${lat.toFixed(6)},${lng.toFixed(6)}`;
      userLocation.value = { latitude: lat, longitude: lng, formatted };
    },
    (err) => {
      console.error('Geolocation error:', err);
      // provide helpful message — user likely denied permission
      showPopup('Location permission denied or unavailable. Allow location to punch in/out.', 'error');
      userLocation.value = null;
    },
    {
      enableHighAccuracy: true,
      timeout: 10000,
      maximumAge: 0
    }
  );
};

// call location on mount so the button becomes enabled quickly
onMounted(() => {
  getLocation();

  // sunday flash (if backend set flash)
  if (flash && flash.sunday_incremented) {
    showPopup('Sunday leave credited +1 ✅', 'success');
  }
});

// handle punch
const handlePunch = async () => {
  if (isProcessing.value) return;

  if (!userLocation.value || !userLocation.value.formatted) {
    // attempt to get location once more before failing
    showPopup('Fetching location... please allow permission.', 'error');
    getLocation();
    return;
  }

  isProcessing.value = true;

  // Prepare a robust payload to match common backend expectations
  const payload = {
    location: userLocation.value.formatted,      // "lat,lng" string
    latitude: userLocation.value.latitude,       // number
    longitude: userLocation.value.longitude      // number
  };

  try {
    const res = await axios.post(route('employee.punches.store'), payload);

    if (res.data && res.data.success) {
      showPopup(res.data.message || 'Punch successful ✅', 'success');
      // reload only needed props
      await router.reload({ only: ['isPunchedIn', 'punches', 'flash'] });
    } else {
      // backend returned non-success payload
      showPopup((res.data && res.data.message) || 'Punch failed', 'error');
      console.error('Punch response:', res.data);
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
          :disabled="isProcessing || !userLocation"
          class="font-semibold px-8 py-3 rounded-lg shadow-md hover:opacity-90 disabled:opacity-50 text-lg"
          :class="props.isPunchedIn ? 'bg-red-600 text-white' : 'bg-green-600 text-white'"
        >
          {{ isProcessing ? 'Processing...' : (props.isPunchedIn ? 'Punch Out' : 'Punch In') }}
        </button>

        <p class="text-sm mt-3">
          <span v-if="!userLocation" class="text-red-500">
            Location required — please allow location permission in your browser.
          </span>
          <span v-else class="text-gray-500">
            Location detected: {{ userLocation.formatted }}
          </span>
        </p>
      </div>

      <!-- NOTE: The daily punches summary and the worked-time table have been removed as requested -->
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
