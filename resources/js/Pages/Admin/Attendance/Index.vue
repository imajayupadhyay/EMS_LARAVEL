<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { reactive, ref, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'

const props = defineProps({
  todayAttendance: Array, // full, unpaginated "today" (IST)
  attendance: Object,     // paginated older entries: { data, links, meta }
  employees: Array,
  filters: Object,
  totalWorkingDays: [Number, null], // null unless an employee is selected
})

const filters = reactive({ ...props.filters })

// Popup state
const showDetails = ref(false)
const details = ref({})

// React on filter changes
watch(filters, () => {
  router.get(route('admin.attendance.index'), filters, {
    preserveState: true,
    replace: true,
  })
})

// Export Excel
const exportExcel = () => {
  window.open(route('admin.attendance.export', filters), '_blank')
}

// Open details (date is IST, backend resolves proper UTC window)
const openDetails = async (record) => {
  try {
    const url = typeof route === 'function' && route().has?.('admin.attendance.details')
      ? route('admin.attendance.details', { employeeId: record.employee_id, date: record.date })
      : `/admin/attendance/${record.employee_id}/${record.date}`

    const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    const data = await response.json()
    details.value = data || {}
    showDetails.value = true
  } catch (e) {
    console.error('Failed to fetch details', e)
    details.value = { not_found: true, date: record.date, intervals: [] }
    showDetails.value = true
  }
}

const closeDetails = () => {
  showDetails.value = false
}

// Pagination handler
const goTo = (url) => {
  if (!url) return
  const u = new URL(url, window.location.origin)
  const page = u.searchParams.get('page') || 1
  const per_page = u.searchParams.get('per_page') || undefined

  router.get(route('admin.attendance.index'), { ...filters, page, per_page }, {
    preserveState: true,
    replace: true,
  })
}
</script>

<template>
  <AdminLayout>
    <div class="container">
      <h1 class="page-title">Attendance Summary</h1>

      <!-- Flash -->
      <div v-if="$page.props.flash?.success" class="flash success">
        {{ $page.props.flash.success }}
      </div>

      <!-- Filters -->
      <div class="filters">
        <select v-model="filters.employee_id">
          <option value="">All Employees</option>
          <option v-for="e in employees" :key="e.id" :value="e.id">
            {{ e.first_name }} {{ e.last_name }}
          </option>
        </select>

        <input type="date" v-model="filters.date" />
        <input type="month" v-model="filters.month" />

        <button @click="exportExcel" class="btn export">Export Excel</button>
      </div>

      <!-- Total Working Days: ONLY when an employee is selected -->
      <div
        v-if="filters.employee_id && typeof totalWorkingDays === 'number'"
        class="total-days"
      >
        Total Working Days this Month (Selected Employee):
        <strong>{{ totalWorkingDays }}</strong>
      </div>

      <!-- TODAY (Full list) -->
      <div v-if="todayAttendance && todayAttendance.length" class="section">
        <h2 class="section-title">Today</h2>
        <div class="cards">
          <div
            v-for="record in todayAttendance"
            :key="'today-' + record.employee_id + record.date"
            class="card"
          >
            <div class="card-header">
              <h2>{{ record.employee }}</h2>
              <span class="date-tag">{{ record.date }}</span>
            </div>
            <p><strong>Department:</strong> {{ record.department }}</p>
            <p><strong>Designation:</strong> {{ record.designation }}</p>
            <p><strong>Worked Hours:</strong> {{ record.hours }}</p>
            <button class="btn view" @click="openDetails(record)">View Details</button>
          </div>
        </div>
      </div>

      <!-- EARLIER (Paginated) -->
      <div class="section">
        <h2 class="section-title">Earlier</h2>

        <div
          v-if="attendance && attendance.data && attendance.data.length"
          class="cards"
        >
          <div
            v-for="record in attendance.data"
            :key="'old-' + record.employee_id + record.date"
            class="card"
          >
            <div class="card-header">
              <h2>{{ record.employee }}</h2>
              <span class="date-tag">{{ record.date }}</span>
            </div>
            <p><strong>Department:</strong> {{ record.department }}</p>
            <p><strong>Designation:</strong> {{ record.designation }}</p>
            <p><strong>Worked Hours:</strong> {{ record.hours }}</p>
            <button class="btn view" @click="openDetails(record)">View Details</button>
          </div>
        </div>

        <div v-else class="no-records">No earlier records.</div>

        <!-- Pagination -->
        <div v-if="attendance && attendance.links" class="pagination">
          <button
            v-for="(link, idx) in attendance.links"
            :key="idx"
            class="page-link"
            :class="{ active: link.active, disabled: !link.url }"
            v-html="link.label"
            @click="goTo(link.url)"
          />
        </div>
      </div>
    </div>

    <!-- Details Popup -->
    <div v-if="showDetails" class="popup-overlay">
      <div class="popup">
        <h2 v-if="!details.not_found">
          Attendance Details - {{ details.employee }} ({{ details.date }})
        </h2>
        <h2 v-else>
          Attendance Details ({{ details.date }})
        </h2>

        <template v-if="!details.not_found">
          <table class="details-table">
            <tr>
              <th>First In</th>
              <td>{{ details.first_in || '' }}</td>
            </tr>
            <tr>
              <th>Punch Out</th>
              <td>{{ details.last_out || '' }}</td> <!-- blank if not punched out -->
            </tr>
            <tr>
              <th>Total Worked Hours</th>
              <td>{{ details.hours || '' }}</td>
            </tr>
          </table>

          <h3 style="margin-top:12px;">Punch Intervals</h3>
          <table class="details-table">
            <thead>
              <tr>
                <th>#</th>
                <th>Punch In</th>
                <th>Punch Out</th>
                <th>Duration</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="!details.intervals || !details.intervals.length">
                <td colspan="4" style="text-align:center;color:#777;">No intervals</td>
              </tr>
              <tr v-for="(row, i) in (details.intervals || [])" :key="i">
                <td>{{ i + 1 }}</td>
                <td>{{ row.in || '' }}</td>
                <td>{{ row.out || '' }}</td>
                <td>{{ row.hours || '' }}</td>
              </tr>
            </tbody>
          </table>
        </template>

        <template v-else>
          <div class="no-records" style="margin-top:10px;">
            No punches found for this date.
          </div>
        </template>

        <button class="btn close" @click="closeDetails">Close</button>
      </div>
    </div>
  </AdminLayout>
</template>

<style scoped>
.container {
  max-width: 1200px;
  margin: auto;
  padding: 20px;
}
.page-title {
  font-size: 26px;
  font-weight: bold;
  color: #e65c00;
  margin-bottom: 20px;
}
.flash.success {
  background: #d4edda;
  color: #155724;
  padding: 10px;
  border-radius: 6px;
  margin-bottom: 20px;
}
.filters {
  display: flex;
  gap: 10px;
  margin-bottom: 20px;
  flex-wrap: wrap;
}
.filters select,
.filters input {
  padding: 8px;
  border: 1px solid #ccc;
  border-radius: 6px;
}
.btn {
  padding: 8px 14px;
  border-radius: 6px;
  border: none;
  cursor: pointer;
}
.btn.export {
  background: #e65c00;
  color: white;
}
.btn.view {
  background: #007bff;
  color: white;
  margin-top: 10px;
}
.btn.close {
  background: #dc3545;
  color: white;
  margin-top: 15px;
}
.total-days {
  background: #fff3cd;
  padding: 12px;
  border-radius: 6px;
  margin-bottom: 20px;
  font-weight: bold;
}
.cards {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 15px;
}
.card {
  background: white;
  padding: 16px;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.date-tag {
  background: #ffebcc;
  padding: 3px 8px;
  border-radius: 4px;
  font-size: 12px;
}
.no-records {
  text-align: center;
  margin-top: 20px;
  color: #666;
}
.section { margin-top: 24px; }
.section-title { font-size: 18px; margin-bottom: 10px; color: #333; }

.pagination {
  margin-top: 16px;
  display: flex;
  gap: 6px;
  flex-wrap: wrap;
}
.page-link {
  padding: 6px 10px;
  border-radius: 6px;
  border: 1px solid #ddd;
  background: #fff;
  cursor: pointer;
}
.page-link.active { background: #e65c00; color: #fff; border-color: #e65c00; }
.page-link.disabled { opacity: 0.5; cursor: not-allowed; }

.popup-overlay {
  position: fixed;
  top: 0; left: 0;
  width: 100%; height: 100%;
  background: rgba(0,0,0,0.6);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 50;
}
.popup {
  background: white;
  padding: 20px;
  border-radius: 8px;
  width: 560px;
  max-width: 95vw;
}
.details-table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 15px;
}
.details-table th,
.details-table td {
  border: 1px solid #ddd;
  padding: 8px;
}
.details-table th {
  background: #f9f9f9;
  text-align: left;
}
</style>
