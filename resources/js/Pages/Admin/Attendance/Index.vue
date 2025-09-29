<style scoped>
/* Container */
.page-container {
  padding: 1.5rem;
  max-width: 1400px;
  margin: 0 auto;
}

/* Page Header */
.page-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 2rem;
  flex-wrap: wrap;
  gap: 1rem;
}

.header-left {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.icon-wrapper {
  width: 3rem;
  height: 3rem;
  background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  box-shadow: 0 4px 12px rgba(139, 92, 246, 0.25);
}

.page-title {
  font-size: 1.875rem;
  font-weight: 700;
  color: #1f2937;
  margin: 0;
}

.page-subtitle {
  font-size: 0.875rem;
  color: #6b7280;
  margin: 0.25rem 0 0;
}

.header-actions {
  display: flex;
  gap: 0.75rem;
}

.btn-export {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.625rem 1.25rem;
  border: none;
  border-radius: 8px;
  font-size: 0.875rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-export.pdf {
  background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
  color: white;
}

.btn-export.excel {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  color: white;
}

.btn-export:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

/* Flash Message */
.flash-message {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
  border: 1px solid #6ee7b7;
  border-radius: 12px;
  padding: 1rem 1.25rem;
  margin-bottom: 1.5rem;
  color: #065f46;
  font-weight: 500;
}

/* Metrics Grid */
.metrics-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.metric-card {
  background: white;
  border-radius: 16px;
  padding: 1.5rem;
  display: flex;
  align-items: center;
  gap: 1rem;
  cursor: pointer;
  transition: all 0.3s ease;
  border: 1px solid #f3f4f6;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.metric-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 25px rgba(139, 92, 246, 0.1);
}

.metric-icon {
  width: 3.5rem;
  height: 3.5rem;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.metric-card.present .metric-icon {
  background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
  color: #065f46;
}

.metric-card.absent .metric-icon {
  background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
  color: #991b1b;
}

.metric-card.late .metric-icon {
  background: linear-gradient(135deg, #fed7aa 0%, #fdba74 100%);
  color: #9a3412;
}

.metric-card.overtime .metric-icon {
  background: linear-gradient(135deg, #ddd6fe 0%, #c4b5fd 100%);
  color: #5b21b6;
}

.metric-value {
  font-size: 2rem;
  font-weight: 700;
  color: #1f2937;
  line-height: 1;
}

.metric-label {
  font-size: 0.875rem;
  color: #6b7280;
  margin-top: 0.25rem;
}

/* Filters Card */
.filters-card {
  background: white;
  border-radius: 16px;
  padding: 1.5rem;
  margin-bottom: 2rem;
  border: 1px solid #f3f4f6;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.filters-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 1.25rem;
}

.filters-title {
  font-size: 1.125rem;
  font-weight: 600;
  color: #1f2937;
  margin: 0;
}

.clear-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1rem;
  background: #f3f4f6;
  border: none;
  border-radius: 8px;
  color: #6b7280;
  font-size: 0.875rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.clear-btn:hover {
  background: #e5e7eb;
}

.filters-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1rem;
}

.filter-group {
  display: flex;
  flex-direction: column;
}

.filter-label {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.875rem;
  font-weight: 500;
  color: #374151;
  margin-bottom: 0.5rem;
}

.filter-input {
  width: 100%;
  padding: 0.625rem 0.875rem;
  border: 1px solid #d1d5db;
  border-radius: 10px;
  font-size: 0.9375rem;
  color: #1f2937;
  transition: all 0.2s;
}

.filter-input:focus {
  outline: none;
  border-color: #8b5cf6;
  box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
}

/* Info Banner */
.info-banner {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
  border: 1px solid #93c5fd;
  border-radius: 12px;
  padding: 1rem 1.25rem;
  margin-bottom: 1.5rem;
  color: #1e40af;
  font-size: 0.9375rem;
}

/* Section */
.section {
  margin-bottom: 2.5rem;
}

.section-title {
  font-size: 1.25rem;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 1rem;
}

/* Table Card */
.table-card {
  background: white;
  border-radius: 16px;
  overflow: hidden;
  border: 1px solid #f3f4f6;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.attendance-table {
  width: 100%;
  border-collapse: collapse;
}

.attendance-table thead {
  background: #fafafa;
}

.attendance-table th {
  padding: 1rem;
  text-align: left;
  font-size: 0.875rem;
  font-weight: 600;
  color: #374151;
  border-bottom: 1px solid #f3f4f6;
}

.attendance-table td {
  padding: 1rem;
  border-bottom: 1px solid #f3f4f6;
  font-size: 0.9375rem;
  color: #1f2937;
}

.employee-cell {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.employee-avatar {
  width: 2.5rem;
  height: 2.5rem;
  background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-weight: 600;
  flex-shrink: 0;
}

.status-badge {
  padding: 0.375rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 600;
  display: inline-block;
}

.status-badge.present {
  background: #d1fae5;
  color: #065f46;
}

.status-badge.late {
  background: #fed7aa;
  color: #9a3412;
}

.status-badge.overtime {
  background: #ddd6fe;
  color: #5b21b6;
}

.status-badge.absent {
  background: #fee2e2;
  color: #991b1b;
}

.action-btn {
  background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
  border: none;
  color: white;
  width: 2rem;
  height: 2rem;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s;
}

.action-btn:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.25);
}

/* Records Grid */
.records-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  gap: 1.5rem;
}

.record-card {
  background: white;
  border-radius: 16px;
  padding: 1.5rem;
  border: 1px solid #f3f4f6;
  transition: all 0.3s ease;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.record-card:hover {
  box-shadow: 0 10px 25px rgba(139, 92, 246, 0.1);
  border-color: #e9d5ff;
  transform: translateY(-2px);
}

.record-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 1rem;
}

.record-employee {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.record-name {
  font-weight: 600;
  color: #1f2937;
}

.record-date {
  font-size: 0.875rem;
  color: #6b7280;
}

.record-body {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
  margin-bottom: 1rem;
}

.record-info {
  display: flex;
  justify-content: space-between;
  font-size: 0.9375rem;
}

.info-label {
  color: #6b7280;
}

.info-value {
  font-weight: 500;
  color: #1f2937;
}

.btn-view {
  width: 100%;
  padding: 0.625rem;
  background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
  color: white;
  border: none;
  border-radius: 8px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-view:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(139, 92, 246, 0.25);
}

/* Empty State */
.empty-state {
  text-align: center;
  padding: 3rem 2rem;
  color: #9ca3af;
}

.empty-state svg {
  margin: 0 auto 1rem;
}

.empty-state p {
  font-size: 1rem;
  margin: 0;
}

/* Pagination */
.pagination {
  display: flex;
  gap: 0.5rem;
  justify-content: center;
  margin-top: 2rem;
  flex-wrap: wrap;
}

.page-btn {
  padding: 0.5rem 1rem;
  border: 1px solid #d1d5db;
  background: white;
  border-radius: 8px;
  color: #6b7280;
  cursor: pointer;
  transition: all 0.2s;
}

.page-btn:hover:not(.disabled) {
  border-color: #8b5cf6;
  color: #8b5cf6;
}

.page-btn.active {
  background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
  color: white;
  border-color: #8b5cf6;
}

.page-btn.disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

/* Modal */
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.5);
  backdrop-filter: blur(4px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 1rem;
}

.modal-container {
  background: white;
  border-radius: 16px;
  width: 100%;
  max-width: 600px;
  max-height: 90vh;
  display: flex;
  flex-direction: column;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
}

.modal-header {
  padding: 1.5rem;
  background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-radius: 16px 16px 0 0;
}

.modal-title {
  font-size: 1.25rem;
  font-weight: 700;
  color: white;
  margin: 0;
}

.modal-close {
  background: rgba(255, 255, 255, 0.2);
  border: none;
  color: white;
  width: 2.5rem;
  height: 2.5rem;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: background 0.2s;
}

.modal-close:hover {
  background: rgba(255, 255, 255, 0.3);
}

.modal-body {
  padding: 1.5rem;
  overflow-y: auto;
  flex: 1;
}

.employee-list {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.employee-item {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem;
  background: #fafafa;
  border-radius: 12px;
  transition: background 0.2s;
}

.employee-item:hover {
  background: #f3f4f6;
}

.employee-details {
  flex: 1;
  min-width: 0;
}

.employee-name {
  font-weight: 600;
  color: #1f2937;
}

.employee-meta {
  font-size: 0.875rem;
  color: #6b7280;
  margin-top: 0.25rem;
}

.employee-extra {
  font-size: 0.875rem;
  color: #8b5cf6;
  margin-top: 0.25rem;
  font-weight: 500;
}

.btn-view-small {
  padding: 0.5rem 1rem;
  background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 0.875rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-view-small:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(139, 92, 246, 0.25);
}

.details-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1.5rem;
}

.detail-item {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.detail-item label {
  font-size: 0.875rem;
  font-weight: 500;
  color: #6b7280;
}

.detail-item p {
  font-size: 1rem;
  color: #1f2937;
  margin: 0;
  font-weight: 500;
}

.modal-footer {
  padding: 1.5rem;
  background: #fafafa;
  border-top: 1px solid #f3f4f6;
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  border-radius: 0 0 16px 16px;
}

.btn-secondary {
  padding: 0.625rem 1.5rem;
  background: white;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  color: #6b7280;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-secondary:hover {
  background: #f9fafb;
}

/* Transitions */
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

/* Responsive */
@media (max-width: 768px) {
  .page-container {
    padding: 1rem;
  }

  .metrics-grid {
    grid-template-columns: 1fr;
  }

  .filters-grid {
    grid-template-columns: 1fr;
  }

  .table-card {
    overflow-x: auto;
  }

  .attendance-table th:nth-child(3),
  .attendance-table td:nth-child(3),
  .attendance-table th:nth-child(4),
  .attendance-table td:nth-child(4) {
    display: none;
  }

  .records-grid {
    grid-template-columns: 1fr;
  }

  .details-grid {
    grid-template-columns: 1fr;
  }
}
</style><script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { reactive, ref, computed, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'

const props = defineProps({
  todayAttendance: { type: Array, default: () => [] },
  attendance: { type: Object, default: () => ({ data: [], links: [] }) },
  employees: { type: Array, default: () => [] },
  departments: { type: Array, default: () => [] },
  filters: { type: Object, default: () => ({}) },
  totalWorkingDays: { type: [Number, null], default: null },
})

const filters = reactive({ ...props.filters })
const showDetails = ref(false)
const details = ref({})
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

/* Export PDF */
const exportPDF = () => {
  const url = (typeof route === 'function')
    ? route('admin.attendance.export-pdf', filters)
    : `/admin/attendance/export-pdf?${new URLSearchParams(filters).toString()}`
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
    window.location.href = url
  }
}

/* Summary cards logic */
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
      name: emp ? `${(emp.first_name || '').trim()} ${(emp.last_name || '').trim()}`.trim() : (r.employee || `Employee ${r.employee_id}`),
      department: r.department || (emp && emp.department ? emp.department.name : ''),
      designation: r.designation || (emp && emp.designation ? emp.designation.name : ''),
      attendanceRecord: r
    }
  })
})

// Late employees (arrived after 9:30 AM)
const lateEmployees = computed(() => {
  return (props.todayAttendance || []).filter(r => {
    if (!r.first_in) return false
    const time = r.first_in.split(':')
    const hour = parseInt(time[0])
    const minute = parseInt(time[1])
    return hour > 9 || (hour === 9 && minute > 30)
  })
})

// Overtime employees (worked more than 9 hours)
const overtimeEmployees = computed(() => {
  return (props.todayAttendance || []).filter(r => {
    const hours = parseFloat(r.hours || 0)
    return hours > 9
  })
})

const openListModal = (type) => {
  if (type === 'punched') {
    listModalTitle.value = `Punched In Today (${punchedCount.value})`
    listModalEmployees.value = punchedList.value
  } else if (type === 'not-punched') {
    listModalTitle.value = `Not Yet Punched (${notPunchedList.value.length})`
    listModalEmployees.value = notPunchedList.value.map(e => ({
      id: e.id,
      name: `${(e.first_name || '').trim()} ${(e.last_name || '').trim()}`.trim(),
      department: (e.department && e.department.name) ? e.department.name : '',
      designation: (e.designation && e.designation.name) ? e.designation.name : '',
      attendanceRecord: null
    }))
  } else if (type === 'late') {
    listModalTitle.value = `Late Arrivals (${lateEmployees.value.length})`
    listModalEmployees.value = lateEmployees.value.map(r => {
      const emp = props.employees.find(e => Number(e.id) === Number(r.employee_id))
      return {
        id: r.employee_id,
        name: emp ? `${emp.first_name} ${emp.last_name}` : r.employee,
        department: r.department,
        designation: r.designation,
        attendanceRecord: r,
        extraInfo: `Arrived at ${r.first_in}`
      }
    })
  } else if (type === 'overtime') {
    listModalTitle.value = `Overtime (${overtimeEmployees.value.length})`
    listModalEmployees.value = overtimeEmployees.value.map(r => {
      const emp = props.employees.find(e => Number(e.id) === Number(r.employee_id))
      return {
        id: r.employee_id,
        name: emp ? `${emp.first_name} ${emp.last_name}` : r.employee,
        department: r.department,
        designation: r.designation,
        attendanceRecord: r,
        extraInfo: `${r.hours} hours worked`
      }
    })
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

const clearFilters = () => {
  filters.employee_id = ''
  filters.date = ''
  filters.month = ''
}

// Status helper functions (needed for template)
const getStatus = (record) => {
  if (!record.first_in) return 'Absent'
  const hours = parseFloat(record.hours || 0)
  if (hours > 9) return 'Overtime'
  if (record.first_in) {
    const time = record.first_in.split(':')
    const hour = parseInt(time[0])
    const minute = parseInt(time[1])
    if (hour > 9 || (hour === 9 && minute > 30)) return 'Late'
  }
  return 'Present'
}

const getStatusClass = (record) => {
  const status = getStatus(record)
  if (status === 'Present') return 'present'
  if (status === 'Late') return 'late'
  if (status === 'Overtime') return 'overtime'
  if (status === 'Absent') return 'absent'
  return ''
}

// Absent count for template
const absentCount = computed(() => notPunchedList.value.length)
</script>

<template>
  <AdminLayout>
    <div class="page-container">
    <!-- Page Header -->
    <div class="page-header">
      <div class="header-left">
        <div class="icon-wrapper">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
            <line x1="16" y1="2" x2="16" y2="6"/>
            <line x1="8" y1="2" x2="8" y2="6"/>
            <line x1="3" y1="10" x2="21" y2="10"/>
          </svg>
        </div>
        <div>
          <h1 class="page-title">Attendance Management</h1>
          <p class="page-subtitle">Track and manage employee attendance records</p>
        </div>
      </div>
      <div class="header-actions">
        <button @click="exportPDF" class="btn-export pdf">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
            <polyline points="14 2 14 8 20 8"/>
            <line x1="16" y1="13" x2="8" y2="13"/>
            <line x1="16" y1="17" x2="8" y2="17"/>
            <polyline points="10 9 9 9 8 9"/>
          </svg>
          Export PDF
        </button>
        <button @click="exportExcel" class="btn-export excel">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
            <polyline points="14 2 14 8 20 8"/>
            <line x1="12" y1="18" x2="12" y2="12"/>
            <line x1="9" y1="15" x2="15" y2="15"/>
          </svg>
          Export Excel
        </button>
      </div>
    </div>

    <!-- Flash Message -->
    <div v-if="$page.props.flash?.success" class="flash-message">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <polyline points="20 6 9 17 4 12"/>
      </svg>
      {{ $page.props.flash.success }}
    </div>

    <!-- Metrics Cards -->
    <div class="metrics-grid">
      <div class="metric-card present" @click="openListModal('punched')">
        <div class="metric-icon">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
            <polyline points="22 4 12 14.01 9 11.01"/>
          </svg>
        </div>
        <div class="metric-content">
          <div class="metric-value">{{ punchedCount }}</div>
          <div class="metric-label">Present Today</div>
        </div>
      </div>

      <div class="metric-card absent" @click="openListModal('not-punched')">
        <div class="metric-icon">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"/>
            <line x1="15" y1="9" x2="9" y2="15"/>
            <line x1="9" y1="9" x2="15" y2="15"/>
          </svg>
        </div>
        <div class="metric-content">
          <div class="metric-value">{{ absentCount }}</div>
          <div class="metric-label">Absent Today</div>
        </div>
      </div>

      <div class="metric-card late" @click="openListModal('late')">
        <div class="metric-icon">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"/>
            <polyline points="12 6 12 12 16 14"/>
          </svg>
        </div>
        <div class="metric-content">
          <div class="metric-value">{{ lateEmployees.length }}</div>
          <div class="metric-label">Late Arrivals</div>
        </div>
      </div>

      <div class="metric-card overtime" @click="openListModal('overtime')">
        <div class="metric-icon">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"/>
            <polyline points="12 6 12 12 16 10"/>
          </svg>
        </div>
        <div class="metric-content">
          <div class="metric-value">{{ overtimeEmployees.length }}</div>
          <div class="metric-label">Overtime</div>
        </div>
      </div>
    </div>

    <!-- Filters Card -->
    <div class="filters-card">
      <div class="filters-header">
        <div class="flex items-center gap-2">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
          </svg>
          <h2 class="filters-title">Filters</h2>
        </div>
        <button @click="clearFilters" class="clear-btn">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="1 4 1 10 7 10"/>
            <path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/>
          </svg>
          Clear All
        </button>
      </div>
      
      <div class="filters-grid">
        <div class="filter-group">
          <label class="filter-label">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="11" cy="11" r="8"/>
              <path d="m21 21-4.35-4.35"/>
            </svg>
            Employee
          </label>
          <select v-model="filters.employee_id" class="filter-input">
            <option value="">All Employees</option>
            <option v-for="e in employees" :key="e.id" :value="e.id">
              {{ `${e.first_name} ${e.last_name}`.trim() }}
            </option>
          </select>
        </div>

        <div class="filter-group">
          <label class="filter-label">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="3" y="3" width="7" height="7"/>
              <rect x="14" y="3" width="7" height="7"/>
              <rect x="14" y="14" width="7" height="7"/>
              <rect x="3" y="14" width="7" height="7"/>
            </svg>
            Department
          </label>
          <select v-model="filters.department_id" class="filter-input">
            <option value="">All Departments</option>
            <option v-for="d in departments" :key="d.id" :value="d.id">{{ d.name }}</option>
          </select>
        </div>

        <div class="filter-group">
          <label class="filter-label">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
              <line x1="16" y1="2" x2="16" y2="6"/>
              <line x1="8" y1="2" x2="8" y2="6"/>
              <line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
            Date
          </label>
          <input type="date" v-model="filters.date" class="filter-input" />
        </div>

        <div class="filter-group">
          <label class="filter-label">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
              <line x1="16" y1="2" x2="16" y2="6"/>
              <line x1="8" y1="2" x2="8" y2="6"/>
              <line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
            Month
          </label>
          <input type="month" v-model="filters.month" class="filter-input" />
        </div>
      </div>
    </div>

    <!-- Total Working Days -->
    <div v-if="filters.employee_id && typeof totalWorkingDays === 'number'" class="info-banner">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <circle cx="12" cy="12" r="10"/>
        <line x1="12" y1="16" x2="12" y2="12"/>
        <line x1="12" y1="8" x2="12.01" y2="8"/>
      </svg>
      <span>Total Working Days this Month: <strong>{{ totalWorkingDays }}</strong></span>
    </div>

    <!-- Today's Attendance Table -->
    <div v-if="todayAttendance && todayAttendance.length" class="section">
      <h2 class="section-title">Today's Attendance</h2>
      <div class="table-card">
        <table class="attendance-table">
          <thead>
            <tr>
              <th>#</th>
              <th>Employee</th>
              <th>Department</th>
              <th>Designation</th>
              <th>Check In</th>
              <th>Check Out</th>
              <th>Hours</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(record, idx) in todayAttendance" :key="'today-' + record.employee_id">
              <td>{{ idx + 1 }}</td>
              <td class="employee-cell">
                <div class="employee-avatar">{{ record.employee?.charAt(0) || 'E' }}</div>
                <span>{{ record.employee }}</span>
              </td>
              <td>{{ record.department || '-' }}</td>
              <td>{{ record.designation || '-' }}</td>
              <td>{{ record.first_in || '-' }}</td>
              <td>{{ record.last_out || '-' }}</td>
              <td>{{ record.hours || '0' }}</td>
              <td>
                <span :class="['status-badge', getStatusClass(record)]">
                  {{ getStatus(record) }}
                </span>
              </td>
              <td>
                <button @click="openDetails(record)" class="action-btn">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                    <circle cx="12" cy="12" r="3"/>
                  </svg>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Earlier Attendance -->
    <div class="section">
      <h2 class="section-title">Earlier Records</h2>
      
      <div v-if="attendance?.data?.length" class="records-grid">
        <div v-for="record in attendance.data" :key="'old-' + record.employee_id + record.date" class="record-card">
          <div class="record-header">
            <div class="record-employee">
              <div class="employee-avatar">{{ record.employee?.charAt(0) || 'E' }}</div>
              <div>
                <div class="record-name">{{ record.employee }}</div>
                <div class="record-date">{{ record.date }}</div>
              </div>
            </div>
            <span :class="['status-badge', getStatusClass(record)]">
              {{ getStatus(record) }}
            </span>
          </div>
          <div class="record-body">
            <div class="record-info">
              <span class="info-label">Department:</span>
              <span class="info-value">{{ record.department || '-' }}</span>
            </div>
            <div class="record-info">
              <span class="info-label">Worked Hours:</span>
              <span class="info-value">{{ record.hours || '0' }}</span>
            </div>
          </div>
          <button @click="openDetails(record)" class="btn-view">View Details</button>
        </div>
      </div>

      <div v-else class="empty-state">
        <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
          <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
          <line x1="16" y1="2" x2="16" y2="6"/>
          <line x1="8" y1="2" x2="8" y2="6"/>
          <line x1="3" y1="10" x2="21" y2="10"/>
        </svg>
        <p>No earlier records found</p>
      </div>

      <!-- Pagination -->
      <div v-if="attendance?.links" class="pagination">
        <button
          v-for="(link, idx) in attendance.links"
          :key="idx"
          :class="['page-btn', { active: link.active, disabled: !link.url }]"
          :disabled="!link.url"
          @click="goTo(link.url, $event)"
        >
          <span v-html="link.label"></span>
        </button>
      </div>
    </div>

    <!-- Employee List Modal -->
    <Transition name="modal">
      <div v-if="showListModal" class="modal-overlay" @click="closeListModal">
        <div class="modal-container" @click.stop>
          <div class="modal-header">
            <h2 class="modal-title">{{ listModalTitle }}</h2>
            <button @click="closeListModal" class="modal-close">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18"/>
                <line x1="6" y1="6" x2="18" y2="18"/>
              </svg>
            </button>
          </div>

          <div class="modal-body">
            <div v-if="!listModalEmployees.length" class="empty-state">
              <p>No records found</p>
            </div>

            <div v-else class="employee-list">
              <div v-for="item in listModalEmployees" :key="item.id" class="employee-item">
                <div class="employee-avatar">{{ item.name?.charAt(0) || 'E' }}</div>
                <div class="employee-details">
                  <div class="employee-name">{{ item.name }}</div>
                  <div class="employee-meta">
                    <span v-if="item.department">{{ item.department }}</span>
                    <span v-if="item.department && item.designation"> · </span>
                    <span v-if="item.designation">{{ item.designation }}</span>
                  </div>
                  <div v-if="item.extraInfo" class="employee-extra">{{ item.extraInfo }}</div>
                </div>
                <button @click="onListEmployeeClick(item)" class="btn-view-small">View</button>
              </div>
            </div>
          </div>

          <div class="modal-footer">
            <button @click="closeListModal" class="btn-secondary">Close</button>
          </div>
        </div>
      </div>
    </Transition>

    <!-- Details Modal -->
    <Transition name="modal">
      <div v-if="showDetails" class="modal-overlay" @click="closeDetails">
        <div class="modal-container" @click.stop>
          <div class="modal-header">
            <h2 class="modal-title">
              {{ details.not_found ? 'Attendance Details' : `${details.employee} - ${details.date}` }}
            </h2>
            <button @click="closeDetails" class="modal-close">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18"/>
                <line x1="6" y1="6" x2="18" y2="18"/>
              </svg>
            </button>
          </div>

          <div class="modal-body">
            <template v-if="!details.not_found">
              <div class="details-grid">
                <div class="detail-item">
                  <label>First Check In</label>
                  <p>{{ details.first_in || '-' }}</p>
                </div>
                <div class="detail-item">
                  <label>Last Check Out</label>
                  <p>{{ details.last_out || '-' }}</p>
                </div>
                <div class="detail-item">
                  <label>Total Hours</label>
                  <p>{{ details.hours || '0' }}</p>
                </div>
                <div class="detail-item">
                  <label>Status</label>
                  <p :class="['status-badge', getStatusClass(details)]">
                    {{ getStatus(details) }}
                  </p>
                </div>
              </div>
            </template>
            <template v-else>
              <div class="empty-state">
                <p>No attendance data found for this date</p>
              </div>
            </template>
          </div>

          <div class="modal-footer">
            <button @click="closeDetails" class="btn-secondary">Close</button>
          </div>
        </div>
      </div>
    </Transition>
      </div>
  </AdminLayout>
</template>

<style scoped>
/* Continue in next message due to length... */
</style>