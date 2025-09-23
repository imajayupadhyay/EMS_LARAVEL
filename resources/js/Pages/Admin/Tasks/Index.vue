<script setup>
import { ref, computed, onMounted } from 'vue'
import { router, Head } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  tasks: Object,
  departments: Array,
  stats: Object,
  filters: Object
})

// Filter state
const filters = ref({
  date_from: props.filters.date_from || '',
  date_to: props.filters.date_to || '',
  employee: props.filters.employee || '',
  department_id: props.filters.department_id || '',
  per_page: props.filters.per_page || 15,
  sort_by: props.filters.sort_by || 'task_date',
  sort_order: props.filters.sort_order || 'desc'
})

// UI state
const selectedTask = ref(null)
const isFilterPanelOpen = ref(false)
const isLoading = ref(false)

// Computed properties
const hasActiveFilters = computed(() => {
  return filters.value.employee || 
         filters.value.department_id || 
         (filters.value.date_from && filters.value.date_to)
})

const sortOptions = [
  { value: 'task_date', label: 'Date' },
  { value: 'employee_name', label: 'Employee Name' },
  { value: 'department', label: 'Department' }
]

const perPageOptions = [10, 15, 25, 50]

// Methods
const applyFilters = () => {
  isLoading.value = true
  router.get(route('admin.tasks.index'), filters.value, { 
    preserveScroll: true,
    onFinish: () => isLoading.value = false
  })
}

const clearFilters = () => {
  filters.value = {
    date_from: '',
    date_to: '',
    employee: '',
    department_id: '',
    per_page: 15,
    sort_by: 'task_date',
    sort_order: 'desc'
  }
  applyFilters()
}

const toggleSort = (column) => {
  if (filters.value.sort_by === column) {
    filters.value.sort_order = filters.value.sort_order === 'desc' ? 'asc' : 'desc'
  } else {
    filters.value.sort_by = column
    filters.value.sort_order = 'desc'
  }
  applyFilters()
}

const openTaskModal = (task) => {
  selectedTask.value = task
}

const closeTaskModal = () => {
  selectedTask.value = null
}

const exportTasks = () => {
  // Export functionality
  router.post(route('admin.tasks.export'), filters.value, {
    onSuccess: () => {
      // Handle successful export
    }
  })
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-GB', {
    day: '2-digit',
    month: 'short',
    year: 'numeric'
  })
}

const getEmployeeName = (employee) => {
  if (!employee) return 'N/A'
  return `${employee.first_name} ${employee.last_name}`.trim()
}

const getDepartmentName = (employee) => {
  return employee?.department?.name || 'N/A'
}

const truncateContent = (content, length = 100) => {
  if (!content) return ''
  const text = content.replace(/<[^>]*>/g, '') // Remove HTML tags
  return text.length > length ? text.substring(0, length) + '...' : text
}

// Auto-apply filters on change (debounced)
let filterTimeout
const debouncedApplyFilters = () => {
  clearTimeout(filterTimeout)
  filterTimeout = setTimeout(() => {
    applyFilters()
  }, 500)
}

onMounted(() => {
  // Set default date range if not set
  if (!filters.value.date_from && !filters.value.date_to) {
    const today = new Date()
    const thirtyDaysAgo = new Date(today.getTime() - (30 * 24 * 60 * 60 * 1000))
    
    filters.value.date_to = today.toISOString().split('T')[0]
    filters.value.date_from = thirtyDaysAgo.toISOString().split('T')[0]
  }
})
</script>

<template>
  <AdminLayout>
    <Head title="Employee Tasks Management" />
    
    <div class="page-container">
      <!-- Page Header -->
      <div class="page-header">
        <div class="header-left">
          <div class="icon-wrapper">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M9 11H7l3-10 3 10h-2l.5 3.5h-3L9 11z"/>
              <path d="M20 12l-1.5-1.5L17 12l1.5 1.5L20 12z"/>
              <circle cx="12" cy="12" r="10"/>
            </svg>
          </div>
          <div>
            <h1 class="page-title">Tasks Management</h1>
            <p class="page-subtitle">View and manage all employee daily tasks</p>
          </div>
        </div>
        
        <!-- Header Actions -->
        <div class="header-actions">
          <button @click="exportTasks" class="btn btn-secondary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
              <polyline points="7,10 12,15 17,10"/>
              <line x1="12" y1="15" x2="12" y2="3"/>
            </svg>
            Export Tasks
          </button>
          
          <button @click="isFilterPanelOpen = !isFilterPanelOpen" 
                  class="btn btn-primary"
                  :class="{ 'active': hasActiveFilters }">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
            </svg>
            Filters
            <span v-if="hasActiveFilters" class="badge">Active</span>
          </button>
        </div>
      </div>

      <!-- Statistics Cards -->
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-icon purple">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M9 11H7l3-10 3 10h-2l.5 3.5h-3L9 11z"/>
            </svg>
          </div>
          <div class="stat-content">
            <div class="stat-value">{{ stats.total_tasks }}</div>
            <div class="stat-label">Total Tasks</div>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon blue">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
              <circle cx="12" cy="7" r="4"/>
            </svg>
          </div>
          <div class="stat-content">
            <div class="stat-value">{{ stats.unique_employees }}</div>
            <div class="stat-label">Active Employees</div>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon green">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
              <line x1="16" y1="2" x2="16" y2="6"/>
              <line x1="8" y1="2" x2="8" y2="6"/>
              <line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
          </div>
          <div class="stat-content">
            <div class="stat-value">{{ stats.today_tasks }}</div>
            <div class="stat-label">Today's Tasks</div>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon orange">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
            </svg>
          </div>
          <div class="stat-content">
            <div class="stat-value">{{ stats.avg_tasks_per_day }}</div>
            <div class="stat-label">Avg. per Day</div>
          </div>
        </div>
      </div>

      <!-- Filters Panel -->
      <transition name="slide-down">
        <div v-show="isFilterPanelOpen" class="filters-panel">
          <div class="filters-header">
            <h3 class="filters-title">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
              </svg>
              Advanced Filters
            </h3>
            <button @click="clearFilters" class="btn btn-ghost btn-sm">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="1 4 1 10 7 10"/>
                <path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/>
              </svg>
              Clear All
            </button>
          </div>

          <div class="filters-grid">
            <div class="filter-group">
              <label class="filter-label">Date From</label>
              <input 
                type="date" 
                v-model="filters.date_from" 
                @change="applyFilters"
                class="filter-input"
              />
            </div>

            <div class="filter-group">
              <label class="filter-label">Date To</label>
              <input 
                type="date" 
                v-model="filters.date_to" 
                @change="applyFilters"
                class="filter-input"
              />
            </div>

            <div class="filter-group">
              <label class="filter-label">Employee Name</label>
              <input 
                type="text" 
                v-model="filters.employee" 
                @input="debouncedApplyFilters"
                placeholder="Search employee..."
                class="filter-input"
              />
            </div>

            <div class="filter-group">
              <label class="filter-label">Department</label>
              <select 
                v-model="filters.department_id" 
                @change="applyFilters"
                class="filter-input"
              >
                <option value="">All Departments</option>
                <option v-for="dept in departments" :key="dept.id" :value="dept.id">
                  {{ dept.name }}
                </option>
              </select>
            </div>

            <div class="filter-group">
              <label class="filter-label">Sort By</label>
              <select 
                v-model="filters.sort_by" 
                @change="applyFilters"
                class="filter-input"
              >
                <option v-for="option in sortOptions" :key="option.value" :value="option.value">
                  {{ option.label }}
                </option>
              </select>
            </div>

            <div class="filter-group">
              <label class="filter-label">Per Page</label>
              <select 
                v-model="filters.per_page" 
                @change="applyFilters"
                class="filter-input"
              >
                <option v-for="option in perPageOptions" :key="option" :value="option">
                  {{ option }}
                </option>
              </select>
            </div>
          </div>
        </div>
      </transition>

      <!-- Tasks Table -->
      <div class="content-card">
        <div class="table-container">
          <div v-if="isLoading" class="loading-overlay">
            <div class="loading-spinner"></div>
          </div>

          <table v-if="tasks.data.length > 0" class="modern-table">
            <thead>
              <tr>
                <th>
                  <button @click="toggleSort('task_date')" class="sort-button">
                    Date
                    <svg v-if="filters.sort_by === 'task_date'" 
                         :class="{ 'rotate-180': filters.sort_order === 'asc' }"
                         width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <polyline points="6 9 12 15 18 9"/>
                    </svg>
                  </button>
                </th>
                <th>
                  <button @click="toggleSort('employee_name')" class="sort-button">
                    Employee
                    <svg v-if="filters.sort_by === 'employee_name'" 
                         :class="{ 'rotate-180': filters.sort_order === 'asc' }"
                         width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <polyline points="6 9 12 15 18 9"/>
                    </svg>
                  </button>
                </th>
                <th>Department</th>
                <th>Task Content</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="task in tasks.data" :key="task.id" class="table-row">
                <td class="font-medium">
                  <div class="date-badge">
                    {{ formatDate(task.task_date) }}
                  </div>
                </td>
                <td>
                  <div class="employee-cell">
                    <div class="employee-avatar">
                      {{ getEmployeeName(task.employee).split(' ').map(n => n[0]).join('').toUpperCase() }}
                    </div>
                    <div class="employee-info">
                      <div class="employee-name">{{ getEmployeeName(task.employee) }}</div>
                      <div class="employee-designation">{{ task.employee?.designation?.name || 'N/A' }}</div>
                    </div>
                  </div>
                </td>
                <td>
                  <div class="department-badge">
                    {{ getDepartmentName(task.employee) }}
                  </div>
                </td>
                <td class="task-content-cell">
                  <div class="task-preview" v-html="truncateContent(task.task_content)"></div>
                </td>
                <td>
                  <button @click="openTaskModal(task)" class="action-btn view-btn">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                      <circle cx="12" cy="12" r="3"/>
                    </svg>
                    View
                  </button>
                </td>
              </tr>
            </tbody>
          </table>

          <!-- Empty State -->
          <div v-else class="empty-state">
            <div class="empty-icon">
              <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                <path d="M9 11H7l3-10 3 10h-2l.5 3.5h-3L9 11z"/>
                <path d="M20 12l-1.5-1.5L17 12l1.5 1.5L20 12z"/>
                <circle cx="12" cy="12" r="10"/>
              </svg>
            </div>
            <h3>No Tasks Found</h3>
            <p>No tasks match your current filters. Try adjusting your search criteria.</p>
            <button @click="clearFilters" class="btn btn-primary">Clear Filters</button>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="tasks.data.length > 0" class="pagination-wrapper">
          <div class="pagination-info">
            Showing {{ tasks.from }} to {{ tasks.to }} of {{ tasks.total }} tasks
          </div>
          
          <div class="pagination-controls">
            <button 
              v-if="tasks.prev_page_url" 
              @click="router.get(tasks.prev_page_url, {}, { preserveScroll: true })"
              class="pagination-btn"
            >
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="15 18 9 12 15 6"/>
              </svg>
              Previous
            </button>
            
            <div class="pagination-numbers">
              <span v-for="page in tasks.links.slice(1, -1)" :key="page.label">
                <button 
                  v-if="page.url" 
                  @click="router.get(page.url, {}, { preserveScroll: true })"
                  class="pagination-number"
                  :class="{ 'active': page.active }"
                >
                  {{ page.label }}
                </button>
                <span v-else class="pagination-dots">{{ page.label }}</span>
              </span>
            </div>
            
            <button 
              v-if="tasks.next_page_url" 
              @click="router.get(tasks.next_page_url, {}, { preserveScroll: true })"
              class="pagination-btn"
            >
              Next
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="9 18 15 12 9 6"/>
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Task Details Modal -->
    <transition name="modal">
      <div v-if="selectedTask" class="modal-overlay" @click="closeTaskModal">
        <div class="modal-content" @click.stop>
          <div class="modal-header">
            <div class="modal-title">
              <div class="employee-avatar">
                {{ getEmployeeName(selectedTask.employee).split(' ').map(n => n[0]).join('').toUpperCase() }}
              </div>
              <div>
                <h3>{{ getEmployeeName(selectedTask.employee) }}'s Task</h3>
                <p class="modal-subtitle">{{ formatDate(selectedTask.task_date) }}</p>
              </div>
            </div>
            <button @click="closeTaskModal" class="modal-close">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18"/>
                <line x1="6" y1="6" x2="18" y2="18"/>
              </svg>
            </button>
          </div>
          
          <div class="modal-body">
            <div class="task-meta">
              <div class="meta-item">
                <span class="meta-label">Department:</span>
                <span class="meta-value">{{ getDepartmentName(selectedTask.employee) }}</span>
              </div>
              <div class="meta-item">
                <span class="meta-label">Designation:</span>
                <span class="meta-value">{{ selectedTask.employee?.designation?.name || 'N/A' }}</span>
              </div>
              <div class="meta-item">
                <span class="meta-label">Task Date:</span>
                <span class="meta-value">{{ formatDate(selectedTask.task_date) }}</span>
              </div>
            </div>
            
            <div class="task-content">
              <h4>Task Details</h4>
              <div class="content-box" v-html="selectedTask.task_content"></div>
            </div>
          </div>
        </div>
      </div>
    </transition>
  </AdminLayout>
</template>

<style scoped>
/* Page Layout */
.page-container {
  max-width: 100%;
  padding: 1.5rem;
  space-y: 1.5rem;
}

/* Page Header */
.page-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 1rem;
  margin-bottom: 2rem;
}

.header-left {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.icon-wrapper {
  width: 48px;
  height: 48px;
  background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  flex-shrink: 0;
}

.page-title {
  font-size: 2rem;
  font-weight: 700;
  color: #111827;
  margin: 0;
  line-height: 1.2;
}

.page-subtitle {
  color: #6b7280;
  font-size: 0.875rem;
  margin: 0.25rem 0 0 0;
}

.header-actions {
  display: flex;
  gap: 0.75rem;
  align-items: center;
}

/* Statistics Grid */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.stat-card {
  background: white;
  padding: 1.5rem;
  border-radius: 16px;
  border: 1px solid #f3f4f6;
  display: flex;
  align-items: center;
  gap: 1rem;
  transition: all 0.2s ease;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.stat-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.stat-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  flex-shrink: 0;
}

.stat-icon.purple { background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); }
.stat-icon.blue { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); }
.stat-icon.green { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
.stat-icon.orange { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }

.stat-content {
  flex: 1;
}

.stat-value {
  font-size: 2rem;
  font-weight: 700;
  color: #111827;
  line-height: 1;
}

.stat-label {
  color: #6b7280;
  font-size: 0.875rem;
  margin-top: 0.25rem;
}

/* Filters Panel */
.filters-panel {
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 16px;
  padding: 1.5rem;
  margin-bottom: 1.5rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.filters-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
}

.filters-title {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 1.125rem;
  font-weight: 600;
  color: #111827;
  margin: 0;
}

.filters-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
}

.filter-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.filter-label {
  font-weight: 500;
  color: #374151;
  font-size: 0.875rem;
}

.filter-input {
  padding: 0.75rem;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 0.875rem;
  transition: all 0.2s ease;
  background: white;
}

.filter-input:focus {
  outline: none;
  border-color: #8b5cf6;
  box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
}

/* Buttons */
.btn {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1rem;
  border-radius: 8px;
  font-weight: 500;
  text-decoration: none;
  border: none;
  cursor: pointer;
  transition: all 0.2s ease;
  font-size: 0.875rem;
  position: relative;
}

.btn-primary {
  background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
  color: white;
}

.btn-primary:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(139, 92, 246, 0.4);
}

.btn-primary.active {
  background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
}

.btn-secondary {
  background: white;
  color: #374151;
  border: 1px solid #d1d5db;
}

.btn-secondary:hover {
  background: #f9fafb;
  border-color: #8b5cf6;
  color: #8b5cf6;
}

.btn-ghost {
  background: transparent;
  color: #6b7280;
}

.btn-ghost:hover {
  background: #f3f4f6;
  color: #374151;
}

.btn-sm {
  padding: 0.5rem 0.75rem;
  font-size: 0.8125rem;
}

.badge {
  position: absolute;
  top: -0.25rem;
  right: -0.25rem;
  background: #ef4444;
  color: white;
  font-size: 0.625rem;
  padding: 0.125rem 0.375rem;
  border-radius: 9999px;
  font-weight: 600;
}

/* Content Card */
.content-card {
  background: white;
  border-radius: 16px;
  border: 1px solid #e5e7eb;
  overflow: hidden;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.table-container {
  position: relative;
  overflow-x: auto;
}

.loading-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(255, 255, 255, 0.8);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 10;
}

.loading-spinner {
  width: 32px;
  height: 32px;
  border: 3px solid #f3f4f6;
  border-top: 3px solid #8b5cf6;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Table Styles */
.modern-table {
  width: 100%;
  border-collapse: collapse;
}

.modern-table th {
  background: #f9fafb;
  padding: 1rem 1.5rem;
  text-align: left;
  font-weight: 600;
  color: #374151;
  border-bottom: 1px solid #e5e7eb;
}

.modern-table th:first-child {
  padding-left: 1.5rem;
}

.modern-table th:last-child {
  padding-right: 1.5rem;
}

.sort-button {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  background: none;
  border: none;
  font-weight: 600;
  color: #374151;
  cursor: pointer;
  padding: 0;
}

.sort-button:hover {
  color: #8b5cf6;
}

.sort-button svg {
  transition: transform 0.2s ease;
}

.rotate-180 {
  transform: rotate(180deg);
}

.table-row {
  border-bottom: 1px solid #f3f4f6;
  transition: background 0.15s ease;
}

.table-row:hover {
  background: #f9fafb;
}

.modern-table td {
  padding: 1rem 1.5rem;
  vertical-align: middle;
}

.modern-table td:first-child {
  padding-left: 1.5rem;
}

.modern-table td:last-child {
  padding-right: 1.5rem;
}

/* Table Cell Components */
.date-badge {
  display: inline-block;
  background: linear-gradient(135deg, #ede9fe 0%, #e9d5ff 100%);
  color: #7c3aed;
  padding: 0.375rem 0.75rem;
  border-radius: 6px;
  font-size: 0.8125rem;
  font-weight: 500;
}

.employee-cell {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.employee-avatar {
  width: 40px;
  height: 40px;
  background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 0.875rem;
  font-weight: 600;
  flex-shrink: 0;
}

.employee-info {
  min-width: 0;
}

.employee-name {
  font-weight: 500;
  color: #111827;
  line-height: 1.25;
}

.employee-designation {
  font-size: 0.75rem;
  color: #6b7280;
  line-height: 1.25;
}

.department-badge {
  display: inline-block;
  background: #f3f4f6;
  color: #374151;
  padding: 0.25rem 0.75rem;
  border-radius: 6px;
  font-size: 0.75rem;
  font-weight: 500;
}

.task-content-cell {
  max-width: 300px;
}

.task-preview {
  color: #6b7280;
  line-height: 1.4;
  font-size: 0.875rem;
}

.action-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.375rem;
  padding: 0.5rem 0.75rem;
  border-radius: 6px;
  font-size: 0.8125rem;
  font-weight: 500;
  border: none;
  cursor: pointer;
  transition: all 0.15s ease;
}

.view-btn {
  background: #ede9fe;
  color: #7c3aed;
}

.view-btn:hover {
  background: #ddd6fe;
  transform: translateY(-1px);
}

/* Empty State */
.empty-state {
  text-align: center;
  padding: 4rem 2rem;
  color: #6b7280;
}

.empty-icon {
  margin: 0 auto 1.5rem;
  color: #d1d5db;
}

.empty-state h3 {
  font-size: 1.125rem;
  font-weight: 600;
  color: #374151;
  margin: 0 0 0.5rem 0;
}

.empty-state p {
  margin: 0 0 1.5rem 0;
  font-size: 0.875rem;
}

/* Pagination */
.pagination-wrapper {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem;
  border-top: 1px solid #e5e7eb;
  background: #f9fafb;
}

.pagination-info {
  color: #6b7280;
  font-size: 0.875rem;
}

.pagination-controls {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.pagination-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1rem;
  border: 1px solid #d1d5db;
  background: white;
  color: #374151;
  border-radius: 6px;
  font-size: 0.875rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.15s ease;
}

.pagination-btn:hover {
  border-color: #8b5cf6;
  color: #8b5cf6;
}

.pagination-numbers {
  display: flex;
  gap: 0.25rem;
}

.pagination-number {
  width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 1px solid #d1d5db;
  background: white;
  color: #374151;
  border-radius: 6px;
  font-size: 0.875rem;
  cursor: pointer;
  transition: all 0.15s ease;
}

.pagination-number:hover {
  border-color: #8b5cf6;
  color: #8b5cf6;
}

.pagination-number.active {
  background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
  border-color: #8b5cf6;
  color: white;
}

.pagination-dots {
  width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #9ca3af;
  font-size: 0.875rem;
}

/* Modal Styles */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 100;
  padding: 1rem;
}

.modal-content {
  background: white;
  border-radius: 16px;
  box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);
  max-width: 600px;
  width: 100%;
  max-height: 90vh;
  overflow: hidden;
  display: flex;
  flex-direction: column;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem;
  border-bottom: 1px solid #e5e7eb;
  flex-shrink: 0;
}

.modal-title {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.modal-title h3 {
  font-size: 1.25rem;
  font-weight: 600;
  color: #111827;
  margin: 0;
}

.modal-subtitle {
  color: #6b7280;
  font-size: 0.875rem;
  margin: 0.25rem 0 0 0;
}

.modal-close {
  width: 40px;
  height: 40px;
  border-radius: 8px;
  border: none;
  background: #f3f4f6;
  color: #6b7280;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.15s ease;
}

.modal-close:hover {
  background: #e5e7eb;
  color: #374151;
}

.modal-body {
  padding: 1.5rem;
  overflow-y: auto;
  flex: 1;
}

.task-meta {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1rem;
  margin-bottom: 1.5rem;
  padding: 1rem;
  background: #f9fafb;
  border-radius: 8px;
}

.meta-item {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.meta-label {
  font-size: 0.75rem;
  font-weight: 500;
  color: #6b7280;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.meta-value {
  font-weight: 500;
  color: #111827;
}

.task-content h4 {
  font-size: 1rem;
  font-weight: 600;
  color: #111827;
  margin: 0 0 1rem 0;
}

.content-box {
  background: #f9fafb;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  padding: 1rem;
  line-height: 1.6;
  color: #374151;
}

/* Responsive Design */
@media (max-width: 768px) {
  .page-container {
    padding: 1rem;
  }
  
  .page-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
  }
  
  .header-actions {
    width: 100%;
    justify-content: flex-end;
  }
  
  .stats-grid {
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
  }
  
  .filters-grid {
    grid-template-columns: 1fr;
    gap: 1rem;
  }
  
  .modern-table {
    font-size: 0.8125rem;
  }
  
  .modern-table th,
  .modern-table td {
    padding: 0.75rem 1rem;
  }
  
  .pagination-wrapper {
    flex-direction: column;
    gap: 1rem;
    align-items: stretch;
  }
  
  .pagination-controls {
    justify-content: center;
  }
  
  .modal-content {
    margin: 1rem;
    max-height: calc(100vh - 2rem);
  }
}

/* Animations */
.slide-down-enter-active,
.slide-down-leave-active {
  transition: all 0.3s ease;
}

.slide-down-enter-from {
  opacity: 0;
  transform: translateY(-10px);
}

.slide-down-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}

.modal-enter-active,
.modal-leave-active {
  transition: all 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

.modal-enter-from .modal-content,
.modal-leave-to .modal-content {
  transform: scale(0.95);
}
</style>