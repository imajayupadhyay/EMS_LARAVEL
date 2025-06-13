<template>
  <EmployeeLayout>
    <div class="max-w-4xl mx-auto py-10 px-4">
      <h1 class="text-2xl font-bold text-orange-600 mb-6">Punch In / Punch Out</h1>

      <!-- Flash Messages -->
      <div v-if="flash.success" class="mb-4 text-green-600">{{ flash.success }}</div>
      <div v-if="flash.error" class="mb-4 text-red-600">{{ flash.error }}</div>

      <!-- Date Filter -->
      <div class="mb-6 flex items-center gap-3">
        <label class="font-medium">Select Date:</label>
        <input type="date" v-model="selectedDate" @change="fetchPunches" class="border px-3 py-1 rounded" />
      </div>

      <!-- Punch Button -->
      <div class="mb-6">
        <button
          :disabled="!isWithinRange || loading"
          @click="handlePunch"
          class="px-6 py-3 bg-orange-500 text-white font-semibold rounded shadow hover:bg-orange-600 disabled:opacity-50 transition duration-200"
        >
          {{ isPunchedIn ? 'Punch Out' : 'Punch In' }}
        </button>
        <p v-if="!isWithinRange" class="text-sm text-red-600 mt-2">
          You must be at the designated location to punch.
        </p>
      </div>

      <!-- Total Hours -->
      <div class="mb-6">
        <p class="text-lg font-medium text-gray-700">
          ⏱ Total Hours: <span class="font-bold">{{ totalHours }}</span>
        </p>
      </div>

      <!-- Punch Logs -->
      <div>
        <h2 class="text-lg font-semibold text-gray-700 mb-3">Punch Log</h2>
        <div v-if="punches.length" class="bg-white shadow rounded">
          <table class="w-full table-auto text-sm">
            <thead class="bg-gray-100 text-left">
              <tr>
                <th class="p-2">#</th>
                <th class="p-2">Punch In</th>
                <th class="p-2">Punch Out</th>
                <th class="p-2">Duration</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(pair, index) in pairedPunches" :key="index" class="border-t">
                <td class="p-2">{{ index + 1 }}</td>
                <td class="p-2">{{ formatTime(pair.in) }}</td>
                <td class="p-2">{{ pair.out ? formatTime(pair.out) : '—' }}</td>
                <td class="p-2">{{ pair.out ? calculateDuration(pair.in, pair.out) : '—' }}</td>
              </tr>
            </tbody>
          </table>
        </div>
        <p v-else class="text-gray-500 text-sm mt-4">No punches found for selected date.</p>
      </div>
    </div>
  </EmployeeLayout>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import EmployeeLayout from '@/Layouts/EmployeeLayout.vue'

const props = defineProps({
  punches: Array,
  isPunchedIn: Boolean,
  allowedLocation: Object,
  date: String
})

const flash = usePage().props.flash
const punches = ref(props.punches)
const selectedDate = ref(props.date)
const loading = ref(false)
const isWithinRange = ref(false)
const userLocation = ref(null)

onMounted(() => {
  navigator.geolocation.getCurrentPosition(
    (pos) => {
      userLocation.value = {
        lat: pos.coords.latitude,
        lng: pos.coords.longitude,
      }
      checkProximity()
    },
    () => {
      isWithinRange.value = false
    }
  )
})

// Punch In/Out Action
function handlePunch() {
  if (!userLocation.value) return
  loading.value = true
  router.post(route('employee.punches.store'), {
    location: `${userLocation.value.lat},${userLocation.value.lng}`,
    date: selectedDate.value
  }, {
    preserveScroll: true,
    onFinish: () => loading.value = false,
  })
}

// Location Proximity Checker
function checkProximity() {
  const toRad = (val) => (val * Math.PI) / 180
  const earthRadius = 6371
  const dLat = toRad(props.allowedLocation.lat - userLocation.value.lat)
  const dLng = toRad(props.allowedLocation.lng - userLocation.value.lng)
  const a =
    Math.sin(dLat / 2) ** 2 +
    Math.cos(toRad(userLocation.value.lat)) *
    Math.cos(toRad(props.allowedLocation.lat)) *
    Math.sin(dLng / 2) ** 2
  const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a))
  const distance = earthRadius * c
  isWithinRange.value = distance <= 0.5
}

// Format Time to HH:MM
function formatTime(time) {
  return new Date(time).toLocaleTimeString([], {
    hour: '2-digit',
    minute: '2-digit',
  })
}

// Duration Calculator
function calculateDuration(start, end) {
  const diff = (new Date(end) - new Date(start)) / 1000
  const hours = Math.floor(diff / 3600)
  const minutes = Math.floor((diff % 3600) / 60)
  return `${hours}h ${minutes}m`
}

// Pair Punches (in/out)
const pairedPunches = computed(() => {
  const pairs = []
  for (let i = 0; i < punches.value.length; i++) {
    if (punches.value[i].punched_in_at) {
      pairs.push({
        in: punches.value[i].punched_in_at,
        out: punches.value[i].punched_out_at || null
      })
    }
  }
  return pairs
})

// Total Hours
const totalHours = computed(() => {
  let totalMinutes = 0
  pairedPunches.value.forEach(pair => {
    if (pair.out) {
      const diff = (new Date(pair.out) - new Date(pair.in)) / 1000
      totalMinutes += Math.floor(diff / 60)
    }
  })
  const hrs = Math.floor(totalMinutes / 60)
  const mins = totalMinutes % 60
  return `${hrs}h ${mins}m`
})

// Fetch punches on date change
function fetchPunches() {
  router.get(route('employee.punches.index'), {
    date: selectedDate.value
  }, {
    preserveScroll: true
  })
}
</script>
