<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { reactive, watch, ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'

const props = defineProps({
  attendance: Array,
  employees: Array,
  filters: Object,
  totalWorkingDays: Number,
})

const filters = reactive({ ...props.filters })
const selectedRecord = ref(null)

watch(filters, () => {
  router.get(route('admin.attendance.index'), filters, {
    preserveState: true,
    replace: true,
  })
})

const exportExcel = () => {
  window.open(route('admin.attendance.export', filters), '_blank')
}

const openDetails = (record) => {
  selectedRecord.value = record
}
const closeDetails = () => {
  selectedRecord.value = null
}
</script>

<template>
  <AdminLayout>
    <div class="container">
      <h1 class="page-title">Attendance Summary</h1>

      <!-- Flash -->
      <div v-if="$page.props.flash.success" class="flash-success">
        {{ $page.props.flash.success }}
      </div>

      <!-- Filters -->
      <div class="filters">
        <select v-model="filters.employee_id" class="filter-input">
          <option value="">All Employees</option>
          <option v-for="e in employees" :key="e.id" :value="e.id">
            {{ e.first_name }} {{ e.last_name }}
          </option>
        </select>

        <input type="date" v-model="filters.date" class="filter-input" />
        <input type="month" v-model="filters.month" class="filter-input" />

        <button @click="exportExcel" class="btn-export">
          Export to Excel
        </button>
      </div>

      <!-- Total Working Days -->
      <div class="working-days">
        <strong>Total Working Days this Month:</strong> {{ totalWorkingDays }}
      </div>

      <!-- Attendance Cards -->
      <div v-if="attendance.length" class="card-grid">
        <div v-for="record in attendance" :key="record.date + '-' + record.employee" class="card">
          <div class="card-header">
            <h2 class="employee-name">{{ record.employee }}</h2>
            <span class="date-tag">{{ record.date }}</span>
          </div>
          <p class="sub-info">Dept: {{ record.department }} | Desig: {{ record.designation }}</p>
          <p class="hours"><strong>Worked Hours:</strong> {{ record.hours }}</p>
          <button class="btn-details" @click="openDetails(record)">View Details</button>
        </div>
      </div>

      <div v-else class="no-records">No attendance records found.</div>
    </div>

    <!-- Modal -->
    <div v-if="selectedRecord" class="modal-overlay">
      <div class="modal">
        <h2 class="modal-title">
          Attendance Details - {{ selectedRecord.employee }} ({{ selectedRecord.date }})
        </h2>

        <table class="details-table">
  <tr>
    <th>Punch In</th>
    <td>{{ selectedRecord.first_in }}</td>
  </tr>
  <tr>
    <th>Punch Out</th>
    <td>{{ selectedRecord.last_out }}</td>
  </tr>
  <tr>
    <th>Total Worked Hours</th>
    <td>{{ selectedRecord.hours }}</td>
  </tr>
</table>


        <div class="modal-footer">
          <button class="btn-close" @click="closeDetails">Close</button>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<style scoped>
/* Container */
.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
}
.page-title {
  font-size: 28px;
  font-weight: bold;
  color: #d35400;
  margin-bottom: 20px;
}

/* Flash */
.flash-success {
  background: #d4edda;
  border: 1px solid #28a745;
  color: #155724;
  padding: 10px 15px;
  border-radius: 6px;
  margin-bottom: 15px;
}

/* Filters */
.filters {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
  margin-bottom: 20px;
}
.filter-input {
  padding: 8px 10px;
  border: 1px solid #ccc;
  border-radius: 6px;
  flex: 1;
}
.btn-export {
  background: linear-gradient(90deg, #f39c12, #e67e22);
  color: white;
  padding: 8px 15px;
  border-radius: 6px;
  border: none;
  font-weight: bold;
  cursor: pointer;
}
.btn-export:hover {
  opacity: 0.9;
}

/* Working Days */
.working-days {
  background: #fff3e0;
  border-left: 4px solid #f39c12;
  padding: 10px 15px;
  margin-bottom: 20px;
  font-size: 15px;
  border-radius: 6px;
}

/* Cards */
.card-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 15px;
}
.card {
  background: white;
  padding: 15px;
  border-radius: 10px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.1);
  transition: transform 0.2s;
}
.card:hover {
  transform: translateY(-3px);
}
.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.employee-name {
  font-size: 18px;
  font-weight: bold;
}
.date-tag {
  background: #f39c12;
  color: white;
  padding: 3px 10px;
  border-radius: 15px;
  font-size: 12px;
}
.sub-info {
  font-size: 14px;
  color: #555;
  margin: 5px 0;
}
.hours {
  margin: 5px 0 10px;
}
.btn-details {
  background: #3498db;
  color: white;
  padding: 6px 12px;
  border-radius: 6px;
  border: none;
  cursor: pointer;
  font-weight: bold;
}
.btn-details:hover {
  background: #2980b9;
}

/* Modal */
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.6);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 50;
}
.modal {
  background: white;
  border-radius: 10px;
  padding: 20px;
  width: 600px;
  max-width: 90%;
}
.modal-title {
  font-size: 20px;
  font-weight: bold;
  margin-bottom: 15px;
  color: #d35400;
}
.details-table {
  width: 100%;
  border-collapse: collapse;
  margin-bottom: 15px;
}
.details-table th,
.details-table td {
  border: 1px solid #ddd;
  padding: 8px;
  text-align: center;
}
.details-table th {
  background: #f9f9f9;
  font-weight: bold;
}
.modal-footer {
  text-align: right;
}
.btn-close {
  background: #e74c3c;
  color: white;
  padding: 6px 12px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
}
.btn-close:hover {
  background: #c0392b;
}

/* No Records */
.no-records {
  text-align: center;
  color: #777;
  margin-top: 30px;
}
</style>
