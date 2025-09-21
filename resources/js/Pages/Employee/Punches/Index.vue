<template>
  <EmployeeLayout>
    <Head title="Punch In / Out" />
    <div class="punch-container">
      <!-- Page Header -->
      <!-- Status Card -->
      <div class="status-card" :class="props.isPunchedIn ? 'status-active' : 'status-inactive'">
        <div class="status-icon-wrapper">
          <div class="status-icon">
            <svg v-if="props.isPunchedIn" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <path d="M22 11.08V12a10 10 0 11-5.93-9.14"></path>
              <polyline points="22 4 12 14.01 9 11.01"></polyline>
            </svg>
            <svg v-else width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <circle cx="12" cy="12" r="10"></circle>
              <line x1="15" y1="9" x2="9" y2="15"></line>
              <line x1="9" y1="9" x2="15" y2="15"></line>
            </svg>
          </div>
          <div class="pulse-ring" v-if="props.isPunchedIn"></div>
        </div>
        
        <div class="status-content">
          <h2 class="status-title">
            {{ props.isPunchedIn ? 'You are Punched In' : 'You are Punched Out' }}
          </h2>
          <p class="status-subtitle">
            {{ props.isPunchedIn ? 'Your attendance is being tracked' : 'Click below to start tracking your time' }}
          </p>
        </div>
      </div>

      <!-- Punch Action Card -->
      <div class="action-card">
        <div class="action-header">
          <div class="location-info">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"></path>
              <circle cx="12" cy="10" r="3"></circle>
            </svg>
            <div>
              <p class="location-label">Current Location</p>
              <p class="location-value" v-if="userLocation">
                {{ userLocation.formatted }}
              </p>
              <p class="location-error" v-else>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <circle cx="12" cy="12" r="10"></circle>
                  <line x1="12" y1="8" x2="12" y2="12"></line>
                  <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                Location required - please enable location access
              </p>
            </div>
          </div>
        </div>

        <!-- Punch Button -->
        <button
          @click="handlePunch"
          :disabled="isProcessing || !userLocation"
          class="punch-button"
          :class="props.isPunchedIn ? 'punch-out' : 'punch-in'"
        >
          <span class="button-icon">
            <svg v-if="isProcessing" class="animate-spin" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="10" stroke-dasharray="32" stroke-dashoffset="32">
                <animate attributeName="stroke-dashoffset" values="32;0" dur="1s" repeatCount="indefinite"/>
              </circle>
            </svg>
            <svg v-else width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="10"></circle>
              <polyline points="12 6 12 12 16 14"></polyline>
            </svg>
          </span>
          <span class="button-text">
            {{ isProcessing ? 'Processing...' : (props.isPunchedIn ? 'Punch Out' : 'Punch In') }}
          </span>
          <span class="button-arrow">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <line x1="5" y1="12" x2="19" y2="12"></line>
              <polyline points="12 5 19 12 12 19"></polyline>
            </svg>
          </span>
        </button>

        <!-- Helper Text -->
        <p class="helper-text">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"></circle>
            <path d="M12 16v-4"></path>
            <path d="M12 8h.01"></path>
          </svg>
          Make sure you're within the designated office area
        </p>
      </div>

      <!-- Quick Info Cards -->
      <div class="info-grid">
        <div class="info-card">
          <div class="info-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
              <line x1="16" y1="2" x2="16" y2="6"></line>
              <line x1="8" y1="2" x2="8" y2="6"></line>
              <line x1="3" y1="10" x2="21" y2="10"></line>
            </svg>
          </div>
          <p class="info-label">Today</p>
          <p class="info-value">{{ getCurrentDate() }}</p>
        </div>

        <div class="info-card">
          <div class="info-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="10"></circle>
              <polyline points="12 6 12 12 16 14"></polyline>
            </svg>
          </div>
          <p class="info-label">Current Time</p>
          <p class="info-value">{{ currentTime }}</p>
        </div>
      </div>
    </div>

    <!-- Success/Error Popup -->
    <Transition name="popup">
      <div v-if="popup.show" class="popup-overlay">
        <div class="popup-card" :class="popup.type === 'error' ? 'popup-error' : 'popup-success'">
          <div class="popup-icon">
            <svg v-if="popup.type === 'success'" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <path d="M22 11.08V12a10 10 0 11-5.93-9.14"></path>
              <polyline points="22 4 12 14.01 9 11.01"></polyline>
            </svg>
            <svg v-else width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <circle cx="12" cy="12" r="10"></circle>
              <line x1="15" y1="9" x2="9" y2="15"></line>
              <line x1="9" y1="9" x2="15" y2="15"></line>
            </svg>
          </div>
          <p class="popup-message">{{ popup.message }}</p>
        </div>
      </div>
    </Transition>
  </EmployeeLayout>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { usePage, Head, router } from '@inertiajs/vue3';
import EmployeeLayout from '@/Layouts/EmployeeLayout.vue';
import axios from 'axios';

const props = defineProps({
  isPunchedIn: Boolean
});

const flash = usePage().props.flash || {};
const isProcessing = ref(false);
const userLocation = ref(null);
const currentTime = ref('');

// popup
const popup = ref({ show: false, message: '', type: 'success' });
const showPopup = (msg, type = 'success') => {
  popup.value = { show: true, message: msg, type };
  setTimeout(() => (popup.value.show = false), 3000);
};

// get geolocation
const getLocation = () => {
  if (!('geolocation' in navigator)) {
    showPopup('Geolocation not supported by this browser.', 'error');
    return;
  }

  navigator.geolocation.getCurrentPosition(
    (pos) => {
      const lat = Number(pos.coords.latitude);
      const lng = Number(pos.coords.longitude);
      const formatted = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
      userLocation.value = { latitude: lat, longitude: lng, formatted };
    },
    (err) => {
      console.error('Geolocation error:', err);
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

// handle punch
const handlePunch = async () => {
  if (isProcessing.value) return;

  if (!userLocation.value || !userLocation.value.formatted) {
    showPopup('Fetching location... please allow permission.', 'error');
    getLocation();
    return;
  }

  isProcessing.value = true;

  const payload = {
    location: userLocation.value.formatted,
    latitude: userLocation.value.latitude,
    longitude: userLocation.value.longitude
  };

  try {
    const res = await axios.post(route('employee.punches.store'), payload);

    if (res.data && res.data.success) {
      showPopup(res.data.message || 'Punch successful ✅', 'success');
      await router.reload({ only: ['isPunchedIn', 'punches', 'flash'] });
    } else {
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

// Update current time
const updateTime = () => {
  const now = new Date();
  currentTime.value = now.toLocaleTimeString('en-US', { 
    hour: '2-digit', 
    minute: '2-digit',
    second: '2-digit',
    hour12: true 
  });
};

const getCurrentDate = () => {
  const options = { weekday: 'short', month: 'short', day: 'numeric', year: 'numeric' };
  return new Date().toLocaleDateString('en-US', options);
};

let timeInterval;

onMounted(() => {
  getLocation();
  updateTime();
  timeInterval = setInterval(updateTime, 1000);

  if (flash && flash.sunday_incremented) {
    showPopup('Sunday leave credited +1 ✅', 'success');
  }
});

onBeforeUnmount(() => {
  if (timeInterval) {
    clearInterval(timeInterval);
  }
});
</script>

<style scoped>
/* Container */
.punch-container {
  max-width: 800px;
  margin: 0 auto;
  padding: 0;
}

/* Page Header */
.page-header {
  margin-bottom: 2rem;
  text-align: center;
}

.page-title {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.75rem;
  font-size: 2rem;
  font-weight: 700;
  color: #111827;
  margin: 0 0 0.5rem 0;
}

.page-title svg {
  color: #1e3a8a;
}

.page-subtitle {
  color: #6b7280;
  font-size: 1rem;
}

/* Status Card */
.status-card {
  background: white;
  border-radius: 20px;
  padding: 2rem;
  margin-bottom: 2rem;
  display: flex;
  align-items: center;
  gap: 2rem;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  border: 2px solid transparent;
  transition: all 0.3s ease;
}

.status-active {
  background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
  border-color: #86efac;
}

.status-inactive {
  background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
  border-color: #fca5a5;
}

.status-icon-wrapper {
  position: relative;
  flex-shrink: 0;
}

.status-icon {
  width: 80px;
  height: 80px;
  border-radius: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: white;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.status-active .status-icon {
  color: #10b981;
}

.status-inactive .status-icon {
  color: #ef4444;
}

.pulse-ring {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 80px;
  height: 80px;
  border-radius: 20px;
  border: 3px solid #10b981;
  opacity: 0.5;
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
  0%, 100% {
    transform: translate(-50%, -50%) scale(1);
    opacity: 0.5;
  }
  50% {
    transform: translate(-50%, -50%) scale(1.1);
    opacity: 0;
  }
}

.status-content {
  flex: 1;
}

.status-title {
  font-size: 1.5rem;
  font-weight: 700;
  margin: 0 0 0.5rem 0;
}

.status-active .status-title {
  color: #065f46;
}

.status-inactive .status-title {
  color: #991b1b;
}

.status-subtitle {
  font-size: 0.9375rem;
  margin: 0;
}

.status-active .status-subtitle {
  color: #047857;
}

.status-inactive .status-subtitle {
  color: #b91c1c;
}

/* Action Card */
.action-card {
  background: white;
  border-radius: 20px;
  padding: 2rem;
  margin-bottom: 2rem;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
}

.action-header {
  margin-bottom: 2rem;
}

.location-info {
  display: flex;
  align-items: flex-start;
  gap: 1rem;
  padding: 1rem;
  background: #f9fafb;
  border-radius: 12px;
}

.location-info svg {
  color: #1e3a8a;
  flex-shrink: 0;
  margin-top: 2px;
}

.location-label {
  font-size: 0.8125rem;
  color: #6b7280;
  margin: 0 0 0.25rem 0;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.location-value {
  font-size: 0.9375rem;
  color: #111827;
  font-weight: 500;
  margin: 0;
}

.location-error {
  font-size: 0.875rem;
  color: #ef4444;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin: 0;
}

/* Punch Button */
.punch-button {
  width: 100%;
  padding: 1.25rem 2rem;
  border: none;
  border-radius: 16px;
  font-size: 1.125rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 1rem;
  position: relative;
  overflow: hidden;
}

.punch-button:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
}

.punch-button:active:not(:disabled) {
  transform: translateY(0);
}

.punch-button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.punch-in {
  background: linear-gradient(135deg, #059669 0%, #10b981 100%);
  color: white;
}

.punch-out {
  background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
  color: white;
}

.button-icon {
  display: flex;
  align-items: center;
  justify-content: center;
}

.button-text {
  flex: 1;
  text-align: center;
}

.button-arrow {
  display: flex;
  align-items: center;
  transition: transform 0.3s ease;
}

.punch-button:hover:not(:disabled) .button-arrow {
  transform: translateX(4px);
}

.animate-spin {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

/* Helper Text */
.helper-text {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  margin-top: 1rem;
  color: #6b7280;
  font-size: 0.875rem;
}

.helper-text svg {
  color: #1e3a8a;
}

/* Info Grid */
.info-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1rem;
}

.info-card {
  background: white;
  border-radius: 16px;
  padding: 1.5rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
  text-align: center;
}

.info-icon {
  width: 48px;
  height: 48px;
  margin: 0 auto 1rem;
  background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
}

.info-label {
  font-size: 0.8125rem;
  color: #6b7280;
  margin: 0 0 0.5rem 0;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.info-value {
  font-size: 1.125rem;
  font-weight: 600;
  color: #111827;
  margin: 0;
}

/* Popup */
.popup-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.5);
  backdrop-filter: blur(4px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
  padding: 1rem;
}

.popup-card {
  background: white;
  border-radius: 20px;
  padding: 2rem;
  max-width: 400px;
  width: 100%;
  text-align: center;
  box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
  animation: popupBounce 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

@keyframes popupBounce {
  0% {
    transform: scale(0.8);
    opacity: 0;
  }
  50% {
    transform: scale(1.05);
  }
  100% {
    transform: scale(1);
    opacity: 1;
  }
}

.popup-icon {
  width: 80px;
  height: 80px;
  margin: 0 auto 1.5rem;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.popup-success .popup-icon {
  background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
  color: #10b981;
}

.popup-error .popup-icon {
  background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
  color: #ef4444;
}

.popup-message {
  font-size: 1.125rem;
  font-weight: 600;
  color: #111827;
  margin: 0;
}

/* Popup Transitions */
.popup-enter-active,
.popup-leave-active {
  transition: opacity 0.3s ease;
}

.popup-enter-from,
.popup-leave-to {
  opacity: 0;
}

/* Responsive */
@media (max-width: 768px) {
  .punch-container {
    padding: 0;
  }

  .page-title {
    font-size: 1.5rem;
  }

  .status-card {
    flex-direction: column;
    text-align: center;
    gap: 1.5rem;
  }

  .status-icon {
    width: 64px;
    height: 64px;
  }

  .pulse-ring {
    width: 64px;
    height: 64px;
  }

  .status-title {
    font-size: 1.25rem;
  }

  .punch-button {
    padding: 1rem 1.5rem;
    font-size: 1rem;
  }

  .info-grid {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 640px) {
  .page-title {
    font-size: 1.25rem;
  }

  .action-card,
  .status-card {
    padding: 1.5rem;
  }
}
</style>