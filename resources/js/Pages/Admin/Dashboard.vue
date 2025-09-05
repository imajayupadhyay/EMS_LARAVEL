<template>
  <AdminLayout>
    <div class="p-6">
      <!-- Header -->
      <div class="header flex items-center justify-between mb-6">
        <div>
          <h1 class="welcome text-2xl font-bold text-orange-600">
            Welcome back, <span class="text-emerald-700">{{ adminName }}</span> 👋
          </h1>
          <p class="text-sm text-gray-500 mt-1">Here’s a snapshot of today's attendance ({{ date }})</p>
        </div>
        <div>
          <button @click="load" class="btn-refresh inline-flex items-center px-4 py-2 rounded shadow-sm bg-white border hover:bg-gray-50">
            Refresh
          </button>
        </div>
      </div>

      <!-- Cards -->
      <div class="cards grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="card p-4 rounded-lg shadow-sm bg-gradient-to-br from-white to-orange-50 border border-orange-100">
          <div class="label text-sm text-gray-600">Total Employees</div>
          <div class="value text-3xl font-bold text-gray-800">{{ totals.total_employees }}</div>
        </div>

        <div class="card p-4 rounded-lg shadow-sm bg-gradient-to-br from-white to-green-50 border border-green-100">
          <div class="label text-sm text-gray-600">Present Today</div>
          <div class="value text-3xl font-bold text-green-700">{{ totals.present_today }}</div>
          <div class="text-xs text-gray-500 mt-2">Active / punched today</div>
        </div>

        <div class="card p-4 rounded-lg shadow-sm bg-gradient-to-br from-white to-red-50 border border-red-100">
          <div class="label text-sm text-gray-600">Absent Today</div>
          <div class="value text-3xl font-bold text-red-600">{{ totals.absent_today }}</div>
          <div class="text-xs text-gray-500 mt-2">Not punched today</div>
        </div>
      </div>

      <!-- Chart + list -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Chart -->
        <div class="col-span-1 lg:col-span-1 p-4 rounded-lg bg-white shadow-sm border">
          <h3 class="text-lg font-semibold mb-3">Present vs Absent</h3>
          <canvas id="presentChart" width="300" height="200"></canvas>
          <div class="legend mt-3 text-sm text-gray-600">
            <div><span class="dot inline-block w-3 h-3 mr-2 rounded-full" :style="{background: '#16a34a'}"></span> Present</div>
            <div class="mt-1"><span class="dot inline-block w-3 h-3 mr-2 rounded-full" :style="{background: '#ef4444'}"></span> Absent</div>
          </div>
        </div>

        <!-- Today's Punches -->
        <div class="col-span-1 lg:col-span-2 p-4 rounded-lg bg-white shadow-sm border overflow-auto">
          <div class="flex items-center justify-between mb-3">
            <h3 class="text-lg font-semibold">Employees who punched today</h3>
            <div class="text-sm text-gray-500">Showing {{ presentEmployees.length }} entries</div>
          </div>

          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">#</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Name</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Last In</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Last Out</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Duration</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Contact</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
              <tr v-for="(p, idx) in presentEmployees" :key="p.employee_id" class="hover:bg-gray-50">
                <td class="px-4 py-3 text-sm text-gray-700">{{ idx + 1 }}</td>
                <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ p.name }}</td>
                <td class="px-4 py-3 text-sm text-gray-700">{{ p.last_in || '-' }}</td>
                <td class="px-4 py-3 text-sm text-gray-700">{{ p.last_out || (p.last_in ? 'Still in' : '-') }}</td>
                <td class="px-4 py-3 text-sm text-gray-700">{{ p.duration_hhmm }}</td>
                <td class="px-4 py-3 text-sm text-gray-700">{{ p.contact || p.email || '-' }}</td>
              </tr>
              <tr v-if="presentEmployees.length === 0">
                <td colspan="6" class="px-4 py-6 text-center text-sm text-gray-400">No punches recorded for today.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'

// reactive state
const adminName = ref('Admin')
const totals = ref({
  total_employees: 0,
  present_today: 0,
  absent_today: 0
})
const presentEmployees = ref([])
const date = ref('')

// helper: load data
async function load() {
  try {
    const res = await fetch('/admin/api/dashboard', {
      credentials: 'same-origin',
      headers: { 'Accept': 'application/json' }
    })
    if (!res.ok) throw new Error('Failed to fetch dashboard data')
    const json = await res.json()

    adminName.value = json.admin?.name || 'Admin'
    totals.value = json.totals || totals.value
    presentEmployees.value = json.present_employees || []
    date.value = json.date || ''

    // update chart after data loaded
    renderChart()
  } catch (err) {
    console.error(err)
    // optional: show toast
  }
}

// Chart rendering — simple Chart.js usage via CDN
let chartInstance = null
function renderChart() {
  // ensure Chart global exists
  if (typeof Chart === 'undefined') {
    // try to load Chart.js from CDN
    const scriptId = 'chartjs-cdn'
    if (!document.getElementById(scriptId)) {
      const script = document.createElement('script')
      script.id = scriptId
      script.src = 'https://cdn.jsdelivr.net/npm/chart.js'
      script.onload = () => renderChart()
      document.head.appendChild(script)
    }
    return
  }

  const ctx = document.getElementById('presentChart').getContext('2d')

  const present = totals.value.present_today || 0
  const absent = Math.max(0, (totals.value.total_employees || 0) - present)

  const data = {
    labels: ['Present', 'Absent'],
    datasets: [{
      label: 'Employees',
      data: [present, absent],
      backgroundColor: [
        'rgba(22,163,74,0.9)', // green
        'rgba(239,68,68,0.9)'  // red
      ]
    }]
  }

  if (chartInstance) {
    chartInstance.data = data
    chartInstance.update()
    return
  }

  chartInstance = new Chart(ctx, {
    type: 'doughnut',
    data,
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false }
      }
    }
  })
}

onMounted(() => {
  load()
})
</script>

<style scoped>
/* Custom styling to beautify the admin dashboard */
.header .welcome { letter-spacing: 0.2px; }
.card { min-height: 96px; }
.value { line-height: 1; }
.btn-refresh { font-weight: 600; color: #374151; border-color: #e5e7eb; }
.card .label { color: #6b7280; }
.card .value { margin-top: 6px; }

/* make chart canvas responsive height */
#presentChart { width: 100% !important; height: 180px !important; }

/* table styles (light) */
table th { text-transform: uppercase; letter-spacing: 0.6px; font-size: 11px; }
table td { font-size: 13px; }

/* subtle hover */
tbody tr:hover { background: rgba(249, 250, 251, 0.9); }

/* small screens tweaks */
@media (max-width: 640px) {
  .cards { grid-template-columns: repeat(1, minmax(0, 1fr)); }
}
</style>
