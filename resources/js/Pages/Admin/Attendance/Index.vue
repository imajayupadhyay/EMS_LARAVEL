<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { reactive, ref, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'

const props = defineProps({
  attendance: Array,   // summary (employee, dept, desig, date, hours)
  employees: Array,
  filters: Object,
  totalWorkingDays: Number,
})

const filters = reactive({ ...props.filters })

// popup state
const showDetails = ref(false)
const details = ref({})

// watch filter changes
watch(filters, () => {
  router.get(route('admin.attendance.index'), filters, {
    preserveState: true,
    replace: true,
  })
})

// export excel
const exportExcel = () => {
  window.open(route('admin.attendance.export', filters), '_blank')
}

// open details
const openDetails = async (record) => {
  try {
    const response = await fetch(`/admin/attendance/${record.employee_id}/${record.date}`)
    const data = await response.json()
    details.value = data
    showDetails.value = true
  } catch (e) {
    console.error("Failed to fetch details", e)
  }
}

const closeDetails = () => {
  showDetails.value = false
}
</script>

<template>
  <AdminLayout>
    <div class="container">
      <h1 class="page-title">Attendance Summary</h1>

      <!-- Flash -->
      <div v-if="$page.props.flash.success" class="flash success">
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

      <!-- Total Working Days -->
      <div class="total-days">
        Total Working Days this Month: <strong>{{ totalWorkingDays }}</strong>
      </div>

      <!-- Attendance Container View -->
      <div v-if="attendance.length" class="cards">
        <div v-for="record in attendance" :key="record.employee + record.date" class="card">
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

      <div v-else class="no-records">No attendance records found.</div>
    </div>

    <!-- Details Popup -->
    <div v-if="showDetails" class="popup-overlay">
      <div class="popup">
        <h2>Attendance Details - {{ details.employee }} ({{ details.date }})</h2>

        <table class="details-table">
          <tr>
            <th>Punch In</th>
            <td>{{ details.first_in }}</td>
          </tr>
          <tr>
            <th>Punch Out</th>
            <td>{{ details.last_out }}</td>
          </tr>
          <!-- <tr>
            <th>Total Worked Hours</th>
            <td>{{ details.hours }}</td>
          </tr> -->
        </table>

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
.popup-overlay {
  position: fixed;
  top: 0; left: 0;
  width: 100%; height: 100%;
  background: rgba(0,0,0,0.6);
  display: flex;
  justify-content: center;
  align-items: center;
}
.popup {
  background: white;
  padding: 20px;
  border-radius: 8px;
  width: 400px;
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
