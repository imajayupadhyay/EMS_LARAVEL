<template>
  <AdminLayout>
    <Head title="Manage Leave Applications" />
    
    <div class="page-container">
      <!-- Page Header -->
      <div class="page-header">
        <div class="header-left">
          <div class="icon-wrapper">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
              <line x1="16" y1="2" x2="16" y2="6"></line>
              <line x1="8" y1="2" x2="8" y2="6"></line>
              <line x1="3" y1="10" x2="21" y2="10"></line>
            </svg>
          </div>
          <div>
            <h1 class="page-title">Leave Applications</h1>
            <p class="page-subtitle">Manage and review employee leave requests</p>
          </div>
        </div>
        <div class="header-stats">
          <div class="stat-card pending">
            <span class="stat-value">{{ pendingCount }}</span>
            <span class="stat-label">Pending</span>
          </div>
          <div class="stat-card approved">
            <span class="stat-value">{{ approvedCount }}</span>
            <span class="stat-label">Approved</span>
          </div>
          <div class="stat-card rejected">
            <span class="stat-value">{{ rejectedCount }}</span>
            <span class="stat-label">Rejected</span>
          </div>
        </div>
      </div>

      <!-- Flash Messages -->
      <transition name="slide-down">
        <div v-if="flash.success" class="alert alert-success">
          <svg class="alert-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="20 6 9 17 4 12"></polyline>
          </svg>
          {{ flash.success }}
        </div>
      </transition>
      
      <transition name="slide-down">
        <div v-if="flash.error" class="alert alert-error">
          <svg class="alert-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="12" y1="8" x2="12" y2="12"></line>
            <line x1="12" y1="16" x2="12.01" y2="16"></line>
          </svg>
          {{ flash.error }}
        </div>
      </transition>

      <!-- Filters Section -->
      <div class="filters-card">
        <div class="filters-header">
          <div class="flex items-center gap-2">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
            </svg>
            <h2 class="filters-title">Filters</h2>
          </div>
          <button v-if="hasActiveFilters" @click="clearFilters" class="clear-btn">
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
              Status
            </label>
            <select v-model="status" @change="applyFilters" class="filter-input">
              <option value="">All Status</option>
              <option value="pending">Pending</option>
              <option value="approved">Approved</option>
              <option value="rejected">Rejected</option>
            </select>
          </div>

          <div class="filter-group">
            <label class="filter-label">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                <circle cx="12" cy="7" r="4"/>
              </svg>
              Employee
            </label>
            <input 
              v-model="employee" 
              @input="applyFilters" 
              placeholder="Search by name..." 
              class="filter-input"
            >
          </div>

          <div class="filter-group">
            <label class="filter-label">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="16" y1="2" x2="16" y2="6"></line>
                <line x1="8" y1="2" x2="8" y2="6"></line>
                <line x1="3" y1="10" x2="21" y2="10"></line>
              </svg>
              Leave Type
            </label>
            <select v-model="leaveType" @change="applyFilters" class="filter-input">
              <option value="">All Types</option>
              <option v-for="type in leaveTypes" :key="type.id" :value="type.name">{{ type.name }}</option>
            </select>
          </div>

          <div class="filter-group">
            <label class="filter-label">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="16" y1="2" x2="16" y2="6"></line>
                <line x1="8" y1="2" x2="8" y2="6"></line>
                <line x1="3" y1="10" x2="21" y2="10"></line>
              </svg>
              Date
            </label>
            <input 
              type="date" 
              v-model="date" 
              @change="applyFilters" 
              class="filter-input"
            >
          </div>
        </div>
      </div>

      <!-- Applications Grid -->
      <div v-if="applications.data.length" class="applications-grid">
        <div
          v-for="app in applications.data"
          :key="app.id"
          @click="openPopup(app)"
          class="application-card"
        >
          <!-- Card Header -->
          <div class="card-header">
            <div class="employee-avatar">
              {{ getInitials(app.employee) }}
            </div>
            <div class="employee-info">
              <h3 class="employee-name">
                {{ app.employee.first_name }} {{ app.employee.last_name }}
              </h3>
              <p class="employee-email">{{ app.leave_type.name }}</p>
            </div>
            <div :class="['status-badge', getStatusClass(app.status)]">
              {{ app.status }}
            </div>
          </div>

          <!-- Card Body -->
          <div class="card-body">
            <div class="info-row">
              <div class="info-item">
                <span class="info-icon">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                  </svg>
                </span>
                <div>
                  <span class="info-label">Start Date</span>
                  <span class="info-value">{{ formatDate(app.start_date) }}</span>
                </div>
              </div>

              <div class="info-item">
                <span class="info-icon">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                  </svg>
                </span>
                <div>
                  <span class="info-label">End Date</span>
                  <span class="info-value">{{ formatDate(app.end_date) }}</span>
                </div>
              </div>
            </div>

            <div class="reason-section">
              <span class="info-label">Reason</span>
              <p class="reason-text">{{ app.reason }}</p>
            </div>

            <div class="timestamp-section">
              <span class="info-label">Request Time</span>
              <span class="timestamp-text">{{ formatDateTime(app.created_at) }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="empty-state">
        <div class="empty-icon">
          <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
            <line x1="16" y1="2" x2="16" y2="6"></line>
            <line x1="8" y1="2" x2="8" y2="6"></line>
            <line x1="3" y1="10" x2="21" y2="10"></line>
          </svg>
        </div>
        <h3 class="empty-title">No leave applications found</h3>
        <p class="empty-text">Try adjusting your filters to see more results</p>
      </div>

      <!-- Pagination -->
      <div v-if="applications.data.length" class="pagination">
        <button 
          v-if="applications.prev_page_url" 
          @click="router.get(applications.prev_page_url, {}, { preserveScroll: true })" 
          class="pagination-btn"
        >
          Previous
        </button>
        <button 
          v-if="applications.next_page_url" 
          @click="router.get(applications.next_page_url, {}, { preserveScroll: true })" 
          class="pagination-btn"
        >
          Next
        </button>
      </div>

      <!-- Detail Modal -->
      <Transition name="modal">
        <div v-if="selectedApp" class="modal-overlay" @click="closePopup">
          <div class="modal-container" @click.stop>
            <div class="modal-header">
              <h2 class="modal-title">Leave Application Details</h2>
              <button @click="closePopup" class="modal-close">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <line x1="18" y1="6" x2="6" y2="18"/>
                  <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
              </button>
            </div>

            <div class="modal-body">
              <div class="details-grid">
                <div class="detail-item">
                  <label>Employee Name</label>
                  <p>{{ selectedApp.employee.first_name }} {{ selectedApp.employee.last_name }}</p>
                </div>
                <div class="detail-item">
                  <label>Leave Type</label>
                  <p>{{ selectedApp.leave_type.name }}</p>
                </div>
                <div class="detail-item">
                  <label>Start Date</label>
                  <p>{{ formatDate(selectedApp.start_date) }}</p>
                </div>
                <div class="detail-item">
                  <label>End Date</label>
                  <p>{{ formatDate(selectedApp.end_date) }}</p>
                </div>
                <div class="detail-item">
                  <label>Duration</label>
                  <p>{{ calculateDuration(selectedApp.start_date, selectedApp.end_date) }} days</p>
                </div>
                <div class="detail-item">
                  <label>Status</label>
                  <div :class="['status-badge', getStatusClass(selectedApp.status)]">
                    {{ selectedApp.status }}
                  </div>
                </div>
                <div class="detail-item">
                  <label>Day Type</label>
                  <p>{{ selectedApp.day_type || 'full' }}</p>
                </div>
                <div class="detail-item">
                  <label>Request Time</label>
                  <p>{{ formatDateTime(selectedApp.created_at) }}</p>
                </div>
                <div class="detail-item">
                  <label>Last Updated</label>
                  <p>{{ formatDateTime(selectedApp.updated_at) }}</p>
                </div>
                <div class="detail-item full-width">
                  <label>Reason</label>
                  <p>{{ selectedApp.reason }}</p>
                </div>
              </div>
            </div>

            <div v-if="selectedApp.status === 'pending'" class="modal-footer">
              <button @click="closePopup" class="btn-secondary">Cancel</button>
              <button @click="updateStatus(selectedApp, 'approved')" class="btn-approve">
                Approve
              </button>
              <button @click="updateStatus(selectedApp, 'rejected')" class="btn-reject">
                Reject
              </button>
            </div>
            <div v-else class="modal-footer">
              <button @click="closePopup" class="btn-secondary">Close</button>
            </div>
          </div>
        </div>
      </Transition>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  applications: Object,
  leaveTypes: Array,
  filters: Object,
  flash: Object
})

const status = ref(props.filters.status || '')
const employee = ref(props.filters.employee || '')
const leaveType = ref(props.filters.leave_type || '')
const date = ref(props.filters.date || '')
const selectedApp = ref(null)

// Computed properties
const pendingCount = computed(() => {
  return props.applications.data.filter(app => app.status === 'pending').length
})

const approvedCount = computed(() => {
  return props.applications.data.filter(app => app.status === 'approved').length
})

const rejectedCount = computed(() => {
  return props.applications.data.filter(app => app.status === 'rejected').length
})

const hasActiveFilters = computed(() => {
  return status.value || employee.value || leaveType.value || date.value
})

// Methods
const applyFilters = () => {
  router.get(route('admin.leave-applications.index'), {
    status: status.value,
    employee: employee.value,
    leave_type: leaveType.value,
    date: date.value
  }, { preserveScroll: true })
}

const clearFilters = () => {
  status.value = ''
  employee.value = ''
  leaveType.value = ''
  date.value = ''
  applyFilters()
}

const openPopup = (app) => {
  selectedApp.value = app
}

const closePopup = () => {
  selectedApp.value = null
}

const updateStatus = (app, newStatus) => {
  router.post(route('admin.leave-applications.update', app.id), { status: newStatus }, {
    preserveScroll: true,
    onSuccess: () => closePopup()
  })
}

const getInitials = (employee) => {
  return `${employee.first_name.charAt(0)}${employee.last_name.charAt(0)}`.toUpperCase()
}

const getStatusClass = (status) => {
  if (status === 'approved') return 'status-approved'
  if (status === 'pending') return 'status-pending'
  if (status === 'rejected') return 'status-rejected'
  return ''
}

const formatDate = (dateStr) => {
  if (!dateStr) return 'N/A'
  const [y, m, d] = dateStr.split('-')
  return `${d}-${m}-${y}`
}

const formatDateTime = (dateTimeStr) => {
  if (!dateTimeStr) return 'N/A'
  const date = new Date(dateTimeStr)
  return date.toLocaleString('en-GB', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
    second: '2-digit',
    hour12: false
  })
}

const calculateDuration = (startDate, endDate) => {
  const start = new Date(startDate)
  const end = new Date(endDate)
  const diffTime = Math.abs(end - start)
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1
  return diffDays
}
</script>

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
  background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  box-shadow: 0 4px 12px rgba(249, 115, 22, 0.25);
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

.header-stats {
  display: flex;
  gap: 1rem;
}

.stat-card {
  padding: 1rem 1.5rem;
  border-radius: 12px;
  display: flex;
  flex-direction: column;
  align-items: center;
  color: white;
  min-width: 120px;
}

.stat-card.pending {
  background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
  box-shadow: 0 4px 12px rgba(245, 158, 11, 0.25);
}

.stat-card.approved {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
}

.stat-card.rejected {
  background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
  box-shadow: 0 4px 12px rgba(239, 68, 68, 0.25);
}

.stat-value {
  font-size: 1.875rem;
  font-weight: 700;
  line-height: 1;
}

.stat-label {
  font-size: 0.75rem;
  margin-top: 0.25rem;
  opacity: 0.9;
}

/* Alerts */
.alert {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 1rem;
  border-radius: 12px;
  margin-bottom: 1.5rem;
  font-size: 0.875rem;
}

.alert-success {
  background: #d1fae5;
  color: #065f46;
  border: 1px solid #a7f3d0;
}

.alert-error {
  background: #fee2e2;
  color: #991b1b;
  border: 1px solid #fecaca;
}

.alert-icon {
  flex-shrink: 0;
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
  color: #1f2937;
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
  border-color: #f97316;
  box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1);
}

/* Applications Grid */
.applications-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.application-card {
  background: white;
  border-radius: 16px;
  border: 1px solid #f3f4f6;
  overflow: hidden;
  transition: all 0.3s ease;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
  cursor: pointer;
}

.application-card:hover {
  box-shadow: 0 10px 25px rgba(249, 115, 22, 0.1);
  border-color: #fed7aa;
  transform: translateY(-2px);
}

.card-header {
  padding: 1.5rem;
  background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%);
  display: flex;
  align-items: center;
  gap: 1rem;
  border-bottom: 1px solid #f3f4f6;
}

.employee-avatar {
  width: 3rem;
  height: 3rem;
  background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-weight: 600;
  font-size: 1.125rem;
  flex-shrink: 0;
  box-shadow: 0 4px 12px rgba(249, 115, 22, 0.25);
}

.employee-info {
  flex: 1;
  min-width: 0;
}

.employee-name {
  font-size: 1.125rem;
  font-weight: 600;
  color: #1f2937;
  margin: 0 0 0.25rem;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.employee-email {
  font-size: 0.875rem;
  color: #6b7280;
  margin: 0;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.status-badge {
  padding: 0.375rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: capitalize;
  flex-shrink: 0;
}

.status-pending {
  background: #fef3c7;
  color: #92400e;
}

.status-approved {
  background: #d1fae5;
  color: #065f46;
}

.status-rejected {
  background: #fee2e2;
  color: #991b1b;
}

.card-body {
  padding: 1.5rem;
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.info-row {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1rem;
}

.info-item {
  display: flex;
  gap: 0.75rem;
}

.info-icon {
  width: 2rem;
  height: 2rem;
  background: linear-gradient(135deg, #ffedd5 0%, #fed7aa 100%);
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #ea580c;
  flex-shrink: 0;
}

.info-item > div {
  display: flex;
  flex-direction: column;
  min-width: 0;
}

.info-label {
  font-size: 0.75rem;
  color: #9ca3af;
  font-weight: 500;
  text-transform: uppercase;
  letter-spacing: 0.025em;
}

.info-value {
  font-size: 0.9375rem;
  font-weight: 500;
  color: #1f2937;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.reason-section {
  margin-top: 0.5rem;
}

.reason-text {
  font-size: 0.875rem;
  color: #4b5563;
  line-height: 1.5;
  margin: 0.5rem 0 0;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.timestamp-section {
  margin-top: 1rem;
  padding-top: 1rem;
  border-top: 1px solid #f3f4f6;
  display: flex;
  flex-direction: column;
}

.timestamp-text {
  font-size: 0.8125rem;
  color: #6b7280;
  font-weight: 500;
  margin-top: 0.25rem;
}

/* Empty State */
.empty-state {
  text-align: center;
  padding: 4rem 2rem;
}

.empty-icon {
  color: #d1d5db;
  margin-bottom: 1.5rem;
}

.empty-title {
  font-size: 1.25rem;
  font-weight: 600;
  color: #1f2937;
  margin: 0 0 0.5rem;
}

.empty-text {
  font-size: 0.9375rem;
  color: #6b7280;
  margin: 0;
}

/* Pagination */
.pagination {
  display: flex;
  justify-content: center;
  gap: 1rem;
  margin-top: 2rem;
}

.pagination-btn {
  padding: 0.625rem 1.5rem;
  background: white;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  color: #1f2937;
  font-size: 0.9375rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.pagination-btn:hover {
  background: #f97316;
  color: white;
  border-color: #f97316;
  transform: translateY(-1px);
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
  background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-radius: 16px 16px 0 0;
}

.modal-title {
  font-size: 1.5rem;
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

.details-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1.5rem;
}

@media (max-width: 480px) {
  .details-grid {
    grid-template-columns: 1fr;
  }
}

.detail-item {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.detail-item.full-width {
  grid-column: 1 / -1;
}

.detail-item label {
  font-size: 0.875rem;
  font-weight: 500;
  color: #6b7280;
  text-transform: uppercase;
  letter-spacing: 0.025em;
}

.detail-item p {
  font-size: 1rem;
  color: #1f2937;
  margin: 0;
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
  font-size: 0.9375rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-secondary:hover {
  background: #f9fafb;
  border-color: #9ca3af;
}

.btn-approve {
  padding: 0.625rem 1.5rem;
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  border: none;
  border-radius: 8px;
  color: white;
  font-size: 0.9375rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
}

.btn-approve:hover {
  transform: translateY(-1px);
  box-shadow: 0 6px 20px rgba(16, 185, 129, 0.35);
}

.btn-reject {
  padding: 0.625rem 1.5rem;
  background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
  border: none;
  border-radius: 8px;
  color: white;
  font-size: 0.9375rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  box-shadow: 0 4px 12px rgba(239, 68, 68, 0.25);
}

.btn-reject:hover {
  transform: translateY(-1px);
  box-shadow: 0 6px 20px rgba(239, 68, 68, 0.35);
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

.modal-enter-active .modal-container,
.modal-leave-active .modal-container {
  transition: transform 0.3s ease;
}

.modal-enter-from .modal-container,
.modal-leave-to .modal-container {
  transform: scale(0.95);
}

.slide-down-enter-active,
.slide-down-leave-active {
  transition: all 0.3s ease;
}

.slide-down-enter-from {
  transform: translateY(-10px);
  opacity: 0;
}

.slide-down-leave-to {
  opacity: 0;
}

/* Utility Classes */
.flex {
  display: flex;
}

.items-center {
  align-items: center;
}

.gap-2 {
  gap: 0.5rem;
}

.capitalize {
  text-transform: capitalize;
}

/* Responsive */
@media (max-width: 1024px) {
  .header-stats {
    width: 100%;
    justify-content: flex-start;
  }
}

@media (max-width: 768px) {
  .page-container {
    padding: 1rem;
  }

  .page-header {
    flex-direction: column;
    align-items: flex-start;
  }

  .header-left {
    width: 100%;
  }

  .header-stats {
    width: 100%;
    flex-direction: column;
  }

  .stat-card {
    width: 100%;
  }

  .applications-grid {
    grid-template-columns: 1fr;
  }

  .filters-grid {
    grid-template-columns: 1fr;
  }

  .details-grid {
    grid-template-columns: 1fr;
  }

  .info-row {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 640px) {
  .page-title {
    font-size: 1.5rem;
  }

  .modal-container {
    margin: 0;
    max-height: 100vh;
    border-radius: 0;
  }

  .modal-header {
    border-radius: 0;
  }

  .modal-footer {
    border-radius: 0;
  }
}
</style>