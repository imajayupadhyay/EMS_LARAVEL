<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { reactive, ref, computed, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
/* Props expected from the controller:
  - todayAttendance: Array
  - attendance: Object (paginated)
  - employees: Array
  - filters: Object
  - totalWorkingDays: Number|null
*/
const props = defineProps({
  todayAttendance: { type: Array, default: () => [] },
  attendance: { type: Object, default: () => ({ data: [], links: [] }) },
  employees: { type: Array, default: () => [] },
  filters: { type: Object, default: () => ({}) },
  totalWorkingDays: { type: [Number, null], default: null },
})

/* Reactive filters (local copy) */
const filters = reactive({ ...props.filters })

/* Details popup state */
const showDetails = ref(false)
const details = ref({})

/* Employee-list modal (punched/not punched) */
const showListModal = ref(false)
const listModalTitle = ref('')
const listModalEmployees = ref([])

/* Watch filters and reload index route with params */
watch(filters, () => {
  const indexUrl = (typeof route === 'function') ? route('admin.attendance.index') : '/admin/attendance'
  router.get(indexUrl, filters, { preserveState: true, replace: true })
})

/* Export Excel */
const exportExcel = () => {
  const url = (typeof route === 'function')
    ? route('admin.attendance.export', filters)
    : `/admin/attendance/export?${new URLSearchParams(filters).toString()}`
  window.open(url, '_blank')
}

/* Safe function to build details URL */
const detailsUrlFor = (employeeId, date) => {
  if (typeof route === 'function') {
    try {
      return route('admin.attendance.details', { employeeId, date })
    } catch (e) {
      // fallback
    }
  }
  return `/admin/attendance/${employeeId}/${date}`
}

/* Open details for a record */
const openDetails = async (record) => {
  try {
    const url = detailsUrlFor(record.employee_id, record.date)
    const response = await fetch(url, {
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
      credentials: 'same-origin',
    })
    const data = await response.json()
    details.value = data || {}
    showDetails.value = true
  } catch (e) {
    console.error('Failed to fetch details', e)
    details.value = { not_found: true, date: record.date, intervals: [], employee: record.employee || '' }
    showDetails.value = true
  }
}
const closeDetails = () => { showDetails.value = false }

/* Pagination handler */
const goTo = (url, ev) => {
  if (ev && ev.preventDefault) ev.preventDefault()
  if (!url) return
  try {
    const u = new URL(url, window.location.origin)
    const page = u.searchParams.get('page') || 1
    const per_page = u.searchParams.get('per_page') || undefined
    const indexUrl = (typeof route === 'function') ? route('admin.attendance.index') : '/admin/attendance'
    router.get(indexUrl, { ...filters, page, per_page }, { preserveState: true, replace: true })
  } catch (e) {
    // fallback: navigate to url directly
    window.location.href = url
  }
}

/* ---------- Summary cards logic ---------- */
const todayMap = computed(() => {
  const m = new Map()
  ;(props.todayAttendance || []).forEach(r => {
    if (r && r.employee_id != null) m.set(Number(r.employee_id), r)
  })
  return m
})

const punchedCount = computed(() => todayMap.value.size)

const notPunchedList = computed(() => {
  return (props.employees || []).filter(e => !todayMap.value.has(Number(e.id)))
})

const punchedList = computed(() => {
  return (props.todayAttendance || []).map(r => {
    const emp = (props.employees || []).find(e => Number(e.id) === Number(r.employee_id))
    return {
      id: r.employee_id,
      name: emp ? `${emp.first_name || ''} ${emp.last_name || ''}`.trim() : (r.employee || `Employee ${r.employee_id}`),
      department: r.department || (emp && emp.department ? emp.department.name : ''),
      designation: r.designation || (emp && emp.designation ? emp.designation.name : ''),
      attendanceRecord: r
    }
  })
})

const openListModal = (type) => {
  if (type === 'punched') {
    listModalTitle.value = `Punched In Today (${punchedCount.value})`
    listModalEmployees.value = punchedList.value
  } else {
    listModalTitle.value = `Not Yet Punched (${notPunchedList.value.length})`
    listModalEmployees.value = notPunchedList.value.map(e => ({
      id: e.id,
      name: `${e.first_name || ''} ${e.last_name || ''}`.trim(),
      department: e.department?.name || '',
      designation: e.designation?.name || '',
      attendanceRecord: null
    }))
  }
  showListModal.value = true
}
const closeListModal = () => { showListModal.value = false }

const onListEmployeeClick = (empItem) => {
  if (empItem.attendanceRecord) {
    openDetails({ employee_id: empItem.id, date: empItem.attendanceRecord.date, employee: empItem.name })
    showListModal.value = false
    return
  }
  const today = (new Date()).toISOString().slice(0,10)
  openDetails({ employee_id: empItem.id, date: filters.date || today, employee: empItem.name })
  showListModal.value = false
}
</script>

<template>
  <AdminLayout>
    <div class="container">
      <div class="page-header">
        <div>
          <h1 class="page-title">Attendance Summary</h1>
          <p class="muted">Overview of today's punches and earlier attendance</p>
        </div>

        <div class="header-actions">
          <button class="btn export" @click="exportExcel">Export Excel</button>
        </div>
      </div>

      <!-- Flash -->
      <div v-if="$page.props.flash?.success" class="flash success">
        {{ $page.props.flash.success }}
      </div>

      <!-- Summary row -->
      <div class="summary-row">
        <div class="summary-card punched" @click="openListModal('punched')" title="View employees who punched in today">
          <div class="summary-number">{{ punchedCount }}</div>
          <div class="summary-label">Punched In Today</div>
        </div>

        <div class="summary-card not-punched" @click="openListModal('not-punched')" title="View employees who haven't punched in yet">
          <div class="summary-number">{{ notPunchedList.length }}</div>
          <div class="summary-label">Not Yet Punched</div>
        </div>

        <div style="flex:1"></div>
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
      </div>

      <!-- Total Working Days (if employee selected) -->
      <div v-if="filters.employee_id && typeof totalWorkingDays === 'number'" class="total-days">
        Total Working Days this Month (Selected Employee):
        <strong>{{ totalWorkingDays }}</strong>
      </div>

      <!-- TODAY (table view with details) -->
      <div v-if="todayAttendance && todayAttendance.length" class="section">
        <h2 class="section-title">Today</h2>

        <div class="today-table-wrap">
          <table class="today-table">
            <thead>
              <tr>
                <th>#</th>
                <th>Employee</th>
                <th>Department</th>
                <th>Designation</th>
                <th>First In</th>
                <th>Last Out</th>
                <th>Worked Hours</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(record, idx) in todayAttendance" :key="'today-' + record.employee_id + record.date">
                <td>{{ idx + 1 }}</td>
                <td>{{ record.employee }}</td>
                <td>{{ record.department }}</td>
                <td>{{ record.designation }}</td>
                <td>{{ record.first_in || '-' }}</td>
                <td>{{ record.last_out || '-' }}</td>
                <td>{{ record.hours || '0' }}</td>
                <td>
                  <button class="btn view small" @click="openDetails(record)">View</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- EARLIER (paginated) -->
      <div class="section">
        <h2 class="section-title">Earlier</h2>

        <div v-if="attendance && attendance.data && attendance.data.length" class="cards">
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
            :disabled="!link.url"
            @click="goTo(link.url, $event)"
          >
            <span v-html="link.label"></span>
          </button>
        </div>
      </div>
    </div>

    <!-- Employee list modal (punched / not-punched) -->
    <div v-if="showListModal" class="modal-overlay" role="dialog" aria-modal="true">
      <div class="modal">
        <div class="modal-header">
          <h3>{{ listModalTitle }}</h3>
          <button class="modal-close" @click="closeListModal" aria-label="Close">✕</button>
        </div>

        <div class="modal-body">
          <div v-if="!listModalEmployees.length" class="no-records">No records.</div>

          <ul class="employee-list">
            <li v-for="item in listModalEmployees" :key="item.id" class="employee-item">
              <div class="employee-left">
                <div class="emp-name">{{ item.name }}</div>
                <div class="emp-meta">{{ item.department }} · {{ item.designation }}</div>
              </div>
              <div class="employee-right">
                <button class="btn view small" @click="onListEmployeeClick(item)">View</button>
              </div>
            </li>
          </ul>
        </div>

        <div class="modal-footer">
          <button class="btn close" @click="closeListModal">Close</button>
        </div>
      </div>
    </div>

    <!-- Details Popup -->
    <div v-if="showDetails" class="popup-overlay" role="dialog" aria-modal="true">
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
              <td>{{ details.last_out || '' }}</td>
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
.page-header {
  display:flex;
  align-items:center;
  justify-content:space-between;
  gap:12px;
  margin-bottom:12px;
}
.page-title {
  font-size: 26px;
  font-weight: bold;
  color: #e65c00;
  margin-bottom: 4px;
}
.muted { color: #6b7280; font-size: 14px; }

/* Flash */
.flash.success {
  background: #d4edda;
  color: #155724;
  padding: 10px;
  border-radius: 6px;
  margin-bottom: 20px;
}

/* Summary row */
.summary-row {
  display: flex;
  gap: 12px;
  align-items: center;
  margin-bottom: 18px;
  flex-wrap: wrap;
}
.summary-card {
  background: white;
  padding: 16px 20px;
  border-radius: 10px;
  box-shadow: 0 6px 16px rgba(15,23,42,0.06);
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  cursor: pointer;
  min-width: 220px;
}
.summary-card.punched { border-left: 4px solid #34d399; } /* green */
.summary-card.not-punched { border-left: 4px solid #f97316; } /* orange */
.summary-number {
  font-size: 28px;
  font-weight: 700;
  color: #111827;
}
.summary-label {
  font-size: 14px;
  color: #6b7280;
  margin-top: 6px;
}
.header-actions { display:flex; gap:8px; align-items:center; }

/* Filters */
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

/* Buttons */
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
.btn.view.small { padding: 6px 10px; font-size: 13px; }
.btn.close {
  background: #dc3545;
  color: white;
  margin-top: 15px;
}

/* Cards */
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

/* Pagination */
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

/* Details popup */
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

/* Employee list modal */
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.45);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 60;
}
.modal {
  width: 740px;
  max-width: 95%;
  background: white;
  border-radius: 10px;
  box-shadow: 0 10px 30px rgba(2,6,23,0.2);
  overflow: hidden;
}
.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 16px 20px;
  border-bottom: 1px solid #f3f4f6;
}
.modal-body { padding: 12px 20px; max-height: 60vh; overflow: auto; }
.modal-close { background: transparent; border: none; font-size: 20px; cursor: pointer; }
.employee-list { list-style: none; margin: 0; padding: 0; }
.employee-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px;
  border-bottom: 1px solid #f3f4f6;
}
.employee-left { display: flex; flex-direction: column; }
.emp-name { font-weight: 600; }
.emp-meta { color: #6b7280; font-size: 13px; margin-top: 3px; }
.modal-footer { padding: 12px 20px; border-top: 1px solid #f3f4f6; display:flex; justify-content:flex-end; gap:8px; }

/* Today table */
.today-table-wrap { overflow-x:auto; background:white; padding: 12px; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,0.03); }
.today-table { width:100%; border-collapse: collapse; }
.today-table th, .today-table td { padding:10px 8px; border-bottom:1px solid #f1f1f1; text-align:left; }
.today-table th { background:#fafafa; font-weight:600; font-size:13px; }

/* Responsive */
@media (max-width: 700px) {
  .summary-row { flex-direction: column; align-items: stretch; }
  .summary-actions { margin-left: 0; }
  .modal { width: 100%; margin: 10px; }
  .today-table th:nth-child(3), .today-table td:nth-child(3),
  .today-table th:nth-child(4), .today-table td:nth-child(4) {
    display:none; /* hide dept/designation on small */
  }
}
</style>
