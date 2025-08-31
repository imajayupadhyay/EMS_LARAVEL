<template>
  <div class="min-h-screen bg-gradient-to-r from-orange-500 to-pink-600 flex flex-col">
    <!-- Topbar -->
    <header class="bg-white shadow p-4 flex justify-between items-center">
      <h1 class="text-lg font-bold text-gray-800">👋 Hi {{ marketerName }}</h1>
      <button v-if="!locationAllowed" @click="requestLocation" class="btn-warning">
        Allow Location Access
      </button>
    </header>

    <!-- Main -->
    <main class="flex-1 flex items-center justify-center p-6">
      <div class="bg-white w-full max-w-md p-6 rounded-2xl shadow-xl text-center">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Marketer Dashboard</h2>

        <!-- Loading -->
        <div v-if="punchedIn === null">
          <p class="text-gray-500">Checking your status...</p>
        </div>

        <!-- Punched In -->
        <div v-else-if="punchedIn">
          <p class="text-green-600 font-semibold text-lg mb-2">🟢 You are Punched In</p>
          <p class="text-gray-500 text-sm">Since: {{ punchInTime }}</p>
          <p class="text-gray-700 font-bold mt-2">⏱ Live Timer: {{ liveTimer }}</p>
          <p class="text-gray-500 text-sm">Today’s Total: {{ totalWorked }}</p>
          <button @click="punchOut" class="btn-danger mt-4">Punch Out</button>
        </div>

        <!-- Punched Out -->
        <div v-else>
          <p class="text-red-600 font-semibold text-lg mb-2">🔴 You are Punched Out</p>
          <p class="text-gray-500 text-sm">Today’s Total: {{ totalWorked }}</p>
          <button @click="punchIn" class="btn-primary mt-4">Punch In</button>
        </div>
      </div>
    </main>

    <!-- Popup -->
    <transition name="fade">
      <div v-if="popup.show"
           :class="popup.type === 'error' ? 'bg-red-500' : 'bg-green-500'"
           class="fixed bottom-5 right-5 px-4 py-3 rounded-lg shadow-lg text-white">
        {{ popup.message }}
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import { usePage } from "@inertiajs/vue3";

const { props } = usePage();
const marketerName = props.auth?.user?.name || "Marketer";

const punchedIn = ref(null); // null = loading
const punchInTime = ref(null);
const totalWorked = ref("00:00:00");
const liveTimer = ref("00:00:00");
const locationAllowed = ref(false);

let intervalCheck = null;
let intervalTimer = null;

// ✅ Popup
const popup = ref({ show: false, message: "", type: "success" });
const showPopup = (msg, type = "success") => {
  popup.value = { show: true, message: msg, type };
  setTimeout(() => (popup.value.show = false), 5000);
};

// ✅ Location
const requestLocation = () => {
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(
      () => (locationAllowed.value = true),
      () => (locationAllowed.value = false)
    );
  }
};

// ✅ Punch In
const punchIn = async () => {
  navigator.geolocation.getCurrentPosition(async (pos) => {
    try {
      const res = await axios.post("/marketer/punch-in", {
        latitude: pos.coords.latitude,
        longitude: pos.coords.longitude,
      });
      punchedIn.value = true;
      punchInTime.value = new Date().toISOString();
      startLiveTimerFromDB(punchInTime.value);
      intervalCheck = setInterval(checkLocation, 30000);
      await fetchTotalWorked();
      showPopup(res.data.message || "Punched in ✅", "success");
    } catch (e) {
      showPopup(e.response?.data?.error || "Punch In failed ❌", "error");
    }
  });
};

// ✅ Punch Out
const punchOut = async () => {
  try {
    const res = await axios.post("/marketer/punch-out");
    punchedIn.value = false;
    stopLiveTimer();
    clearInterval(intervalCheck);
    await fetchTotalWorked();
    showPopup(res.data.message || "Punched out ✅", "success");
  } catch (e) {
    showPopup(e.response?.data?.error || "Punch Out failed ❌", "error");
  }
};

// ✅ Location check every 30s
const checkLocation = () => {
  navigator.geolocation.getCurrentPosition(async (pos) => {
    const res = await axios.post("/marketer/update-location", {
      latitude: pos.coords.latitude,
      longitude: pos.coords.longitude,
    });
    if (res.data.message?.includes("Auto punch-out")) {
      punchedIn.value = false;
      stopLiveTimer();
      clearInterval(intervalCheck);
      await fetchTotalWorked();
      showPopup(res.data.message, "error");
    }
  });
};

// ✅ Total Worked
const fetchTotalWorked = async () => {
  const res = await axios.get("/marketer/today-worked");
  totalWorked.value = res.data.total || "00:00:00";
};

// ✅ Live Timer
const startLiveTimerFromDB = (dbTime) => {
  stopLiveTimer();
  let start = new Date(dbTime);
  intervalTimer = setInterval(() => {
    let diff = Math.floor((new Date() - start) / 1000);
    const h = String(Math.floor(diff / 3600)).padStart(2, "0");
    const m = String(Math.floor((diff % 3600) / 60)).padStart(2, "0");
    const s = String(diff % 60).padStart(2, "0");
    liveTimer.value = `${h}:${m}:${s}`;
  }, 1000);
};
const stopLiveTimer = () => {
  clearInterval(intervalTimer);
  liveTimer.value = "00:00:00";
};

// ✅ On Page Load
onMounted(async () => {
  requestLocation();

  try {
    const res = await axios.get("/marketer/status");
    punchedIn.value = res.data.punchedIn;
    punchInTime.value = res.data.punchInTime;

    await fetchTotalWorked();

    if (punchedIn.value && punchInTime.value) {
      startLiveTimerFromDB(punchInTime.value);
      intervalCheck = setInterval(checkLocation, 30000);
    }
  } catch (e) {
    showPopup("Failed to load status ❌", "error");
    punchedIn.value = false; // fallback
  }
});
</script>

<style scoped>
.btn-primary {
  @apply bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg shadow-md w-full;
}
.btn-danger {
  @apply bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg shadow-md w-full;
}
.btn-warning {
  @apply bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg shadow-md;
}
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
