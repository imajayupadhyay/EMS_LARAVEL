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

.btn-export.excel {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  color: white;
}

.btn-export:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.btn-finalize {
  background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
  color: white;
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
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
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

/* Table Card */
.table-card {
  background: white;
  border-radius: 16px;
  overflow: hidden;
  border: 1px solid #f3f4f6;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
  margin-bottom: 2rem;
}

.salary-table {
  width: 100%;
  border-collapse: collapse;
}

.salary-table thead {
  background: #fafafa;
}

.salary-table th {
  padding: 1rem;
  text-align: left;
  font-size: 0.875rem;
  font-weight: 600;
  color: #374151;
  border-bottom: 1px solid #f3f4f6;
  white-space: nowrap;
}

.salary-table td {
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
  max-width: 700px;
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

.salary-detail {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1rem;
  margin-bottom: 1.5rem;
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

.detail-item p,
.detail-item input {
  font-size: 1rem;
  color: #1f2937;
  margin: 0;
  font-weight: 500;
}

.detail-item input {
  padding: 0.625rem;
  border: 1px solid #d1d5db;
  border-radius: 8px;
}

.detail-item input:focus {
  outline: none;
  border-color: #8b5cf6;
  box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
}

.section-title {
  font-size: 1rem;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 1rem;
}

.earnings-list,
.deductions-list {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.item-row {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.item-row input {
  flex: 1;
  padding: 0.625rem;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 0.875rem;
}

.item-row input:focus {
  outline: none;
  border-color: #8b5cf6;
  box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
}

.btn-remove {
  padding: 0.5rem;
  background: #fee2e2;
  color: #991b1b;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-remove:hover {
  background: #fecaca;
}

.btn-add {
  padding: 0.625rem 1rem;
  background: #f3f4f6;
  border: none;
  border-radius: 8px;
  color: #374151;
  font-size: 0.875rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
}

.btn-add:hover {
  background: #e5e7eb;
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

.btn-primary {
  padding: 0.625rem 1.5rem;
  background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
  border: none;
  border-radius: 8px;
  color: white;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-primary:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(139, 92, 246, 0.25);
}

.empty-state {
  text-align: center;
  padding: 3rem 2rem;
  color: #9ca3af;
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

  .salary-detail {
    grid-template-columns: 1fr;
  }
}
</style>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { reactive, ref, computed, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import axios from 'axios'

const props = defineProps({
  report: { type: Array, default: () => [] },
  employees: { type: Array, default: () => [] },
  month: { type: String, default: () => new Date().toISOString().slice(5,7) },
  year: { type: String, default: () => new Date().getFullYear().toString() },
  officeDays: { type: Number, default: 0 },
  selectedEmployee: { type: [String, Number], default: '' },
  flash: { type: Object, default: () => ({}) }
})

const filters = reactive({
  month: props.month,
  year: props.year,
  employee_id: props.selectedEmployee || ''
})

const showEditModal = ref(false)
const editingSalary = ref(null)
const editForm = reactive({
  employee_id: null,
  employee_name: '',
  present_days: 0,
  approved_leaves: 0,
  paid_leave_days: 0,
  unpaid_leave_days: 0,
  absent_days: 0,
  working_days: 0,
  gross_salary: 0,
  earnings: [],
  deductions: [],
  net_salary: 0
})

// Watch filters
watch(filters, () => {
  const indexUrl = typeof route === 'function' ? route('admin.salary-report.index') : '/admin/salary-report'
  router.get(indexUrl, filters, { preserveState: true, replace: true })
})

// Format money
const formatMoney = (value) => {
  const n = Number(value) || 0
  return '₹' + n.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
}

// Clear filters
const clearFilters = () => {
  filters.month = new Date().toISOString().slice(5,7)
  filters.year = new Date().getFullYear().toString()
  filters.employee_id = ''
}

// Export Excel
const exportExcel = () => {
  const url = typeof route === 'function'
    ? route('admin.salary-report.export', filters)
    : `/admin/salary-report/export?${new URLSearchParams(filters).toString()}`
  window.open(url, '_blank')
}

// Open edit modal
const openEditModal = (row) => {
  editingSalary.value = row
  editForm.employee_id = row.employee.id
  editForm.employee_name = `${row.employee.first_name} ${row.employee.last_name}`
  editForm.present_days = row.present_days
  editForm.approved_leaves = row.approved_leaves
  editForm.paid_leave_days = row.paid_leave_days
  editForm.unpaid_leave_days = row.unpaid_leave_days
  editForm.absent_days = row.absent_days
  editForm.working_days = row.working_days
  editForm.gross_salary = row.gross_salary

  // Convert earnings and deductions objects to arrays
  editForm.earnings = Object.entries(row.earnings || {}).map(([label, amount]) => ({ label, amount }))
  editForm.deductions = Object.entries(row.deductions || {}).map(([label, amount]) => ({ label, amount }))

  calculateNet()
  showEditModal.value = true
}

// Close edit modal
const closeEditModal = () => {
  showEditModal.value = false
  editingSalary.value = null
}

// Add earning
const addEarning = () => {
  editForm.earnings.push({ label: '', amount: 0 })
}

// Remove earning
const removeEarning = (index) => {
  editForm.earnings.splice(index, 1)
  calculateNet()
}

// Add deduction
const addDeduction = () => {
  editForm.deductions.push({ label: '', amount: 0 })
}

// Remove deduction
const removeDeduction = (index) => {
  editForm.deductions.splice(index, 1)
  calculateNet()
}

// Calculate net salary
const calculateNet = () => {
  const totalEarnings = editForm.earnings.reduce((sum, item) => sum + (parseFloat(item.amount) || 0), 0)
  const totalDeductions = editForm.deductions.reduce((sum, item) => sum + (parseFloat(item.amount) || 0), 0)
  editForm.net_salary = totalEarnings - totalDeductions
}

// Save salary
const saveSalary = async () => {
  try {
    await axios.post(route('admin.salary-report.update'), {
      employee_id: editForm.employee_id,
      month: filters.month,
      year: filters.year,
      gross_salary: editForm.gross_salary,
      earnings: editForm.earnings,
      deductions: editForm.deductions,
      meta: {
        present_days: editForm.present_days,
        approved_leaves: editForm.approved_leaves,
        paid_leave_days: editForm.paid_leave_days,
        unpaid_leave_days: editForm.unpaid_leave_days,
        absent_days: editForm.absent_days,
        working_days: editForm.working_days
      }
    })

    closeEditModal()
    router.reload({ preserveScroll: true })
  } catch (error) {
    console.error('Failed to save salary:', error)
    alert('Failed to save salary. Please try again.')
  }
}

// Computed metrics
const totalEmployees = computed(() => props.report.length)
const totalGrossSalary = computed(() => props.report.reduce((sum, row) => sum + (row.gross_salary || 0), 0))
const totalNetSalary = computed(() => props.report.reduce((sum, row) => sum + (row.net_salary || 0), 0))
const totalDeductions = computed(() => props.report.reduce((sum, row) => sum + (row.total_deductions || 0), 0))
</script>

<template>
  <AdminLayout>
    <div class="page-container">
      <!-- Page Header -->
      <div class="page-header">
        <div class="header-left">
          <div class="icon-wrapper">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="2" y="3" width="20" height="18" rx="2" ry="2"/>
              <line x1="8" y1="3" x2="8" y2="21"/>
              <line x1="16" y1="3" x2="16" y2="21"/>
              <line x1="2" y1="9" x2="22" y2="9"/>
              <line x1="2" y1="15" x2="22" y2="15"/>
            </svg>
          </div>
          <div>
            <h1 class="page-title">Salary Report</h1>
            <p class="page-subtitle">Generate and manage monthly payroll</p>
          </div>
        </div>
        <div class="header-actions">
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
        <div class="metric-card">
          <div class="metric-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
              <circle cx="9" cy="7" r="4"/>
              <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
              <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
          </div>
          <div class="metric-content">
            <div class="metric-value">{{ totalEmployees }}</div>
            <div class="metric-label">Employees</div>
          </div>
        </div>

        <div class="metric-card">
          <div class="metric-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
              <line x1="16" y1="2" x2="16" y2="6"/>
              <line x1="8" y1="2" x2="8" y2="6"/>
              <line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
          </div>
          <div class="metric-content">
            <div class="metric-value">{{ officeDays }}</div>
            <div class="metric-label">Office Days</div>
          </div>
        </div>

        <div class="metric-card">
          <div class="metric-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <line x1="12" y1="1" x2="12" y2="23"/>
              <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
            </svg>
          </div>
          <div class="metric-content">
            <div class="metric-value" style="font-size: 1.5rem;">{{ formatMoney(totalGrossSalary) }}</div>
            <div class="metric-label">Total Gross</div>
          </div>
        </div>

        <div class="metric-card">
          <div class="metric-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="10"/>
              <polyline points="12 6 12 12 16 14"/>
            </svg>
          </div>
          <div class="metric-content">
            <div class="metric-value" style="font-size: 1.5rem;">{{ formatMoney(totalNetSalary) }}</div>
            <div class="metric-label">Total Net</div>
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
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                <line x1="16" y1="2" x2="16" y2="6"/>
                <line x1="8" y1="2" x2="8" y2="6"/>
                <line x1="3" y1="10" x2="21" y2="10"/>
              </svg>
              Month
            </label>
            <input type="month" v-model="filters.month" class="filter-input" />
          </div>

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
                {{ e.name }}
              </option>
            </select>
          </div>
        </div>
      </div>

      <!-- Salary Table -->
      <div class="table-card">
        <table class="salary-table">
          <thead>
            <tr>
              <th>#</th>
              <th>Employee</th>
              <th>Department</th>
              <th>Designation</th>
              <th style="text-align: center;">Office Days</th>
              <th style="text-align: center;">Present</th>
              <th style="text-align: center;">Leaves</th>
              <th style="text-align: center;">Absent</th>
              <th style="text-align: right;">Gross Salary</th>
              <th style="text-align: right;">Deductions</th>
              <th style="text-align: right;">Net Salary</th>
              <th style="text-align: center;">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="!report || report.length === 0">
              <td colspan="12" class="empty-state">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                  <rect x="2" y="3" width="20" height="18" rx="2" ry="2"/>
                  <line x1="8" y1="3" x2="8" y2="21"/>
                  <line x1="16" y1="3" x2="16" y2="21"/>
                </svg>
                <p>No salary data found for the selected month</p>
              </td>
            </tr>

            <tr v-for="(row, idx) in report" :key="row.employee.id">
              <td>{{ idx + 1 }}</td>
              <td class="employee-cell">
                <div class="employee-avatar">{{ row.employee.first_name?.charAt(0) || 'E' }}</div>
                <span>{{ row.employee.first_name }} {{ row.employee.last_name }}</span>
              </td>
              <td>{{ row.employee.department?.name || '-' }}</td>
              <td>{{ row.employee.designation?.name || '-' }}</td>
              <td style="text-align: center;">{{ row.working_days }}</td>
              <td style="text-align: center;">{{ row.present_days }}</td>
              <td style="text-align: center;">{{ row.approved_leaves }}</td>
              <td style="text-align: center;">{{ row.absent_days }}</td>
              <td style="text-align: right;">{{ formatMoney(row.gross_salary) }}</td>
              <td style="text-align: right;">{{ formatMoney(row.total_deductions) }}</td>
              <td style="text-align: right; font-weight: 600;">{{ formatMoney(row.net_salary) }}</td>
              <td style="text-align: center;">
                <button @click="openEditModal(row)" class="action-btn">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                  </svg>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Edit Salary Modal -->
      <Transition name="modal">
        <div v-if="showEditModal" class="modal-overlay" @click="closeEditModal">
          <div class="modal-container" @click.stop>
            <div class="modal-header">
              <h2 class="modal-title">Edit Salary - {{ editForm.employee_name }}</h2>
              <button @click="closeEditModal" class="modal-close">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <line x1="18" y1="6" x2="6" y2="18"/>
                  <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
              </button>
            </div>

            <div class="modal-body">
              <!-- Attendance Details -->
              <div class="salary-detail">
                <div class="detail-item">
                  <label>Office Days</label>
                  <p>{{ editForm.working_days }}</p>
                </div>
                <div class="detail-item">
                  <label>Present Days</label>
                  <p>{{ editForm.present_days }}</p>
                </div>
                <div class="detail-item">
                  <label>Approved Leaves</label>
                  <p>{{ editForm.approved_leaves }}</p>
                </div>
                <div class="detail-item">
                  <label>Absent Days</label>
                  <p>{{ editForm.absent_days }}</p>
                </div>
              </div>

              <!-- Gross Salary -->
              <div class="detail-item" style="margin-bottom: 1.5rem;">
                <label>Gross Salary</label>
                <input type="number" v-model="editForm.gross_salary" @input="calculateNet" />
              </div>

              <!-- Earnings -->
              <h3 class="section-title">Earnings</h3>
              <div class="earnings-list">
                <div v-for="(earning, index) in editForm.earnings" :key="index" class="item-row">
                  <input type="text" v-model="earning.label" placeholder="Label" />
                  <input type="number" v-model="earning.amount" placeholder="Amount" @input="calculateNet" />
                  <button @click="removeEarning(index)" class="btn-remove">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <line x1="18" y1="6" x2="6" y2="18"/>
                      <line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                  </button>
                </div>
              </div>
              <button @click="addEarning" class="btn-add" style="margin-top: 0.75rem;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <line x1="12" y1="5" x2="12" y2="19"/>
                  <line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Add Earning
              </button>

              <!-- Deductions -->
              <h3 class="section-title" style="margin-top: 1.5rem;">Deductions</h3>
              <div class="deductions-list">
                <div v-for="(deduction, index) in editForm.deductions" :key="index" class="item-row">
                  <input type="text" v-model="deduction.label" placeholder="Label" />
                  <input type="number" v-model="deduction.amount" placeholder="Amount" @input="calculateNet" />
                  <button @click="removeDeduction(index)" class="btn-remove">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <line x1="18" y1="6" x2="6" y2="18"/>
                      <line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                  </button>
                </div>
              </div>
              <button @click="addDeduction" class="btn-add" style="margin-top: 0.75rem;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <line x1="12" y1="5" x2="12" y2="19"/>
                  <line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Add Deduction
              </button>

              <!-- Net Salary -->
              <div class="detail-item" style="margin-top: 1.5rem; padding: 1rem; background: #f3f4f6; border-radius: 8px;">
                <label>Net Salary</label>
                <p style="font-size: 1.5rem; color: #8b5cf6;">{{ formatMoney(editForm.net_salary) }}</p>
              </div>
            </div>

            <div class="modal-footer">
              <button @click="closeEditModal" class="btn-secondary">Cancel</button>
              <button @click="saveSalary" class="btn-primary">Save Changes</button>
            </div>
          </div>
        </div>
      </Transition>
    </div>
  </AdminLayout>
</template>
