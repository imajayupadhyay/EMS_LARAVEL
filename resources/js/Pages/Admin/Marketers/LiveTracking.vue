<template>
  <AdminLayout>
    <div class="p-6 space-y-6">
      <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-800">📡 Marketers Live Tracking</h1>
        <button @click="refresh" class="btn-refresh">🔄 Refresh</button>
      </div>

      <!-- Map -->
      <div id="map" class="h-96 w-full rounded-2xl shadow"></div>

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
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="m in marketers" :key="m.id">
              <td class="px-4 py-2">{{ m.first_name }} {{ m.last_name }}</td>
              <td class="px-4 py-2">{{ m.email }}</td>
              <td class="px-4 py-2">{{ m.contact }}</td>
              <td class="px-4 py-2">
                <span v-if="m.locations.length > 0">
                  {{ m.locations[0].latitude }}, {{ m.locations[0].longitude }}
                </span>
                <span v-else class="text-gray-400">No location</span>
              </td>
              <td class="px-4 py-2">
                <span v-if="m.last_punch && m.last_punch.status === 'in'" class="status in">🟢 In</span>
                <span v-else class="status out">🔴 Out</span>
              </td>
              <td class="px-4 py-2">
                <span v-if="m.locations.length > 0">
                  {{ new Date(m.locations[0].recorded_at).toLocaleString() }}
                </span>
                <span v-else>—</span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { onMounted } from "vue";
import L from "leaflet";
import "leaflet/dist/leaflet.css";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { usePage } from "@inertiajs/vue3";

const { props } = usePage();
const marketers = props.marketers || [];

let map;

const initMap = () => {
  map = L.map("map").setView([28.7041, 77.1025], 12); // default Delhi

  L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
    maxZoom: 19,
    attribution: "&copy; OpenStreetMap",
  }).addTo(map);

  addMarkers();
};

const addMarkers = () => {
  marketers.forEach((m) => {
    if (m.locations.length > 0) {
      const { latitude, longitude } = m.locations[0];
      L.marker([latitude, longitude])
        .addTo(map)
        .bindPopup(`<b>${m.first_name} ${m.last_name}</b><br>${m.email}`);
    }
  });
};

const refresh = () => {
  window.location.reload();
};

onMounted(initMap);
</script>

<style scoped>
#map {
  @apply w-full h-96 rounded-xl shadow;
}
.btn-refresh {
  @apply bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg shadow-md transition;
}
.status.in {
  @apply text-green-600 font-semibold;
}
.status.out {
  @apply text-red-600 font-semibold;
}
</style>
