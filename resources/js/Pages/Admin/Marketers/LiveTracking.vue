<template>
  <AdminLayout>
    <div class="p-6 space-y-6">
      <!-- Header -->
      <div class="flex justify-between items-center">
        <div>
          <h1 class="text-2xl font-bold text-gray-800">📡 Marketers Live Tracking</h1>
          <p class="text-sm text-gray-500 mt-1">Click any marketer to open their latest location in Google Maps.</p>
        </div>

        <div class="flex items-center gap-3">
          <input v-model="query" placeholder="Search name or email..." class="px-3 py-2 rounded-md border shadow-sm focus:outline-none focus:ring" />
          <select v-model="statusFilter" class="px-3 py-2 rounded-md border">
            <option value="all">All status</option>
            <option value="in">Punched In</option>
            <option value="out">Punched Out</option>
          </select>
          <button @click="refresh" class="btn-refresh">🔄 Refresh</button>
        </div>
      </div>

      <!-- Two Lists + Summary map placeholder -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white shadow rounded-2xl p-4">
          <h3 class="font-medium text-gray-700 mb-3">Punched In ({{ punchedIn.length }})</h3>
          <div class="space-y-2 max-h-64 overflow-auto">
            <template v-if="punchedIn.length">
              <button v-for="p in punchedIn" :key="p.id"
                @click="openInGoogleMaps(p.id)"
                class="w-full text-left px-3 py-2 rounded-md hover:bg-gray-50 flex justify-between items-center">
                <div>
                  <div class="font-semibold">{{ p.name }}</div>
                  <div class="text-xs text-gray-500">{{ p.email }}</div>
                </div>
                <div class="text-sm text-green-600 font-semibold">🟢</div>
              </button>
            </template>
            <div v-else class="text-gray-400 text-sm">No marketers currently punched in.</div>
          </div>
        </div>

        <div class="bg-white shadow rounded-2xl p-4">
          <h3 class="font-medium text-gray-700 mb-3">Punched Out ({{ punchedOut.length }})</h3>
          <div class="space-y-2 max-h-64 overflow-auto">
            <template v-if="punchedOut.length">
              <button v-for="p in punchedOut" :key="p.id"
                @click="openInGoogleMaps(p.id)"
                class="w-full text-left px-3 py-2 rounded-md hover:bg-gray-50 flex justify-between items-center">
                <div>
                  <div class="font-semibold">{{ p.name }}</div>
                  <div class="text-xs text-gray-500">{{ p.email }}</div>
                </div>
                <div class="text-sm text-red-600 font-semibold">🔴</div>
              </button>
            </template>
            <div v-else class="text-gray-400 text-sm">No marketers currently punched out.</div>
          </div>
        </div>

        <!-- small summary card -->
        <div class="col-span-1 bg-white shadow rounded-2xl p-4 flex flex-col justify-between">
          <div>
            <h3 class="font-medium text-gray-700 mb-3">Quick Notes</h3>
            <p class="text-sm text-gray-500">Click a marketer to open their location in Google Maps. If a marketer has no recorded location you'll be notified.</p>
          </div>
          <div class="mt-4 text-xs text-gray-400">Tip: You can also use the table below to open Google Maps for any marketer.</div>
        </div>
      </div>

      <!-- Table -->
      <div class="bg-white shadow rounded-2xl overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-100">
            <tr>
              <th class="px-4 py-2 text-left">Name</th>
              <th class="px-4 py-2 text-left">Email</th>
              <th class="px-4 py-2 text-left">Contact</th>
              <th class="px-4 py-2 text-left">Last Location</th>
              <th class="px-4 py-2 text-left">Status</th>
              <th class="px-4 py-2 text-left">Last Update</th>
              <th class="px-4 py-2 text-left">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="m in filteredMarketers" :key="m.id">
              <td class="px-4 py-2">{{ m.first_name }} {{ m.last_name }}</td>
              <td class="px-4 py-2">{{ m.email }}</td>
              <td class="px-4 py-2">{{ m.contact }}</td>
              <td class="px-4 py-2">
                <span v-if="m.locations && m.locations.length > 0">
                  {{ m.locations[0].latitude }}, {{ m.locations[0].longitude }}
                </span>
                <span v-else class="text-gray-400">No location</span>
              </td>
              <td class="px-4 py-2">
                <span v-if="m.last_punch && m.last_punch.status === 'in'" class="status in">🟢 In</span>
                <span v-else class="status out">🔴 Out</span>
              </td>
              <td class="px-4 py-2">
                <span v-if="m.locations && m.locations.length > 0">
                  {{ formatDate(m.locations[0].recorded_at) }}
                </span>
                <span v-else>—</span>
              </td>
              <td class="px-4 py-2">
                <button @click="openInGoogleMaps(m.id)" class="text-sm px-3 py-1 rounded-md border">Open in Google Maps</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { usePage } from '@inertiajs/vue3';
import axios from 'axios';

// props from controller
const { props } = usePage();
const marketers = ref(props.marketers || []);
const punchedIn = ref(props.punchedIn || []);
const punchedOut = ref(props.punchedOut || []);

const query = ref('');
const statusFilter = ref('all');

const refresh = () => window.location.reload();

const formatDate = (d) => {
  if (!d) return '—';
  try { return new Date(d).toLocaleString(); } catch { return d; }
};

const filteredMarketers = computed(() => {
  const q = query.value.trim().toLowerCase();
  return (marketers.value || []).filter(m => {
    if (statusFilter.value !== 'all') {
      const isIn = m.last_punch && m.last_punch.status === 'in';
      if (statusFilter.value === 'in' && !isIn) return false;
      if (statusFilter.value === 'out' && isIn) return false;
    }
    if (!q) return true;
    return (m.first_name + ' ' + m.last_name + ' ' + (m.email || '')).toLowerCase().includes(q);
  });
});

/**
 * Fetch latest location for marketer and open in Google Maps.
 * Uses Google Maps "search" URL which centers and pins the location.
 */
const openInGoogleMaps = async (marketerId) => {
  try {
    // optional: show a loader pattern (not implemented) or disable UI while fetching
    const resp = await axios.get(`/admin/marketers/${marketerId}/latest-location`);
    console.log('latest-location response:', resp?.data);

    if (!resp || resp.status !== 200 || !resp.data || resp.data.success !== true) {
      return alert('Could not fetch latest location for this marketer.');
    }

    const location = resp.data.data.location;
    if (!location || !location.latitude || !location.longitude) {
      return alert('No recorded location for this marketer.');
    }

    const lat = parseFloat(location.latitude);
    const lng = parseFloat(location.longitude);
    if (!isFinite(lat) || !isFinite(lng)) {
      return alert('Invalid coordinates received.');
    }

    // open google maps search with lat,lng (opens new tab)
    const gmapsUrl = `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(lat + ',' + lng)}`;
    window.open(gmapsUrl, '_blank', 'noopener');

  } catch (err) {
    console.error('Error fetching latest marketer location', err);
    alert('Error fetching latest marketer location.');
  }
};
</script>

<style scoped>
/* small UI styles; relies mostly on tailwind in your project */
.btn-refresh {
  @apply bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg shadow-md transition;
}
.status.in {
  @apply text-green-600 font-semibold;
}
.status.out {
  @apply text-red-600 font-semibold;
}

/* small scrollbar for lists */
::-webkit-scrollbar {
  height: 6px;
  width: 6px;
}
::-webkit-scrollbar-thumb {
  background: rgba(0,0,0,0.15);
  border-radius: 9999px;
}
</style>
