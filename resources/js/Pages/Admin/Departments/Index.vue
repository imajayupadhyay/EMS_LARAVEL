<template>
  <AdminLayout>
    <Head title="Departments Management" />
    
    <!-- Header Section -->
    <div class="page-header">
      <div class="header-content">
        <div class="title-section">
          <div class="icon-wrapper">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="3" y="3" width="7" height="7"/>
              <rect x="14" y="3" width="7" height="7"/>
              <rect x="14" y="14" width="7" height="7"/>
              <rect x="3" y="14" width="7" height="7"/>
            </svg>
          </div>
          <div class="title-content">
            <h1 class="page-title">Departments</h1>
            <p class="page-subtitle">Organize your company structure and manage departments</p>
          </div>
        </div>
        <div class="header-actions">
          <button @click="showBulkModal = true" class="action-btn secondary" :disabled="selectedDepartments.length === 0">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/>
              <rect x="8" y="2" width="8" height="4" rx="1" ry="1"/>
              <path d="m9 14 2 2 4-4"/>
            </svg>
            Bulk Actions ({{ selectedDepartments.length }})
          </button>
          <button @click="showAddModal = true" class="action-btn primary">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="10"/>
              <line x1="12" y1="8" x2="12" y2="16"/>
              <line x1="8" y1="12" x2="16" y2="12"/>
            </svg>
            Add Department
          </button>
        </div>
      </div>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-grid">
      <div class="stat-card primary">
        <div class="stat-icon">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="3" y="3" width="7" height="7"/>
            <rect x="14" y="3" width="7" height="7"/>
            <rect x="14" y="14" width="7" height="7"/>
            <rect x="3" y="14" width="7" height="7"/>
          </svg>
        </div>
        <div class="stat-content">
          <div class="stat-value">{{ statistics.total_departments }}</div>
          <div class="stat-label">Total Departments</div>
        </div>
      </div>

      <div class="stat-card success">
        <div class="stat-icon">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
            <circle cx="12" cy="7" r="4"/>
          </svg>
        </div>
        <div class="stat-content">
          <div class="stat-value">{{ statistics.total_employees }}</div>
          <div class="stat-label">Total Employees</div>
        </div>
      </div>

      <div class="stat-card info">
        <div class="stat-icon">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M9 11H1l8-8 8 8"/>
            <path d="M9 11v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V11"/>
          </svg>
        </div>
        <div class="stat-content">
          <div class="stat-value">{{ statistics.avg_employees }}</div>
          <div class="stat-label">Avg per Dept</div>
        </div>
      </div>

      <div class="stat-card warning">
        <div class="stat-icon">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.29 1.51 4.04 3 5.5l7 7z"/>
          </svg>
        </div>
        <div class="stat-content">
          <div class="stat-value">{{ statistics.largest_dept }}</div>
          <div class="stat-label">Largest Dept</div>
        </div>
      </div>
    </div>

    <!-- Search and Filters -->
    <div class="filters-card">
      <div class="filters-header">
        <h3 class="filters-title">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="11" cy="11" r="8"/>
            <path d="m21 21-4.35-4.35"/>
          </svg>
          Search & Filters
        </h3>
        <button @click="clearSearch" class="clear-filters-btn">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="18" y1="6" x2="6" y2="18"/>
            <line x1="6" y1="6" x2="18" y2="18"/>
          </svg>
          Clear
        </button>
      </div>

      <div class="filters-grid">
        <div class="filter-group">
          <label class="filter-label">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="11" cy="11" r="8"/>
              <path d="m21 21-4.35-4.35"/>
            </svg>
            Search Departments
          </label>
          <input
            v-model="searchQuery"
            @input="debouncedSearch"
            type="text"
            placeholder="Search by name..."
            class="filter-input"
          />
        </div>

        <div class="filter-group">
          <label class="filter-label">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M3 6h18l-2 13H5L3 6Z"/>
              <path d="M8 10V7a4 4 0 0 1 8 0v3"/>
            </svg>
            Sort By
          </label>
          <select v-model="sortBy" @change="applySorting" class="filter-input">
            <option value="name_asc">Name (A-Z)</option>
            <option value="name_desc">Name (Z-A)</option>
            <option value="employees_desc">Most Employees</option>
            <option value="employees_asc">Least Employees</option>
            <option value="created_desc">Recently Added</option>
            <option value="created_asc">Oldest First</option>
          </select>
        </div>

        <div class="filter-group">
          <label class="filter-label">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
              <circle cx="12" cy="7" r="4"/>
            </svg>
            Filter by Size
          </label>
          <select v-model="sizeFilter" @change="applyFilters" class="filter-input">
            <option value="">All Sizes</option>
            <option value="small">Small (1-10 employees)</option>
            <option value="medium">Medium (11-50 employees)</option>
            <option value="large">Large (50+ employees)</option>
            <option value="empty">Empty (0 employees)</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Departments Grid -->
    <div class="departments-grid">
      <div v-for="department in filteredDepartments" :key="department.id" class="department-card">
        <div class="card-header">
          <div class="department-info">
            <div class="selection-area">
              <input
                type="checkbox"
                :value="department.id"
                v-model="selectedDepartments"
                class="selection-checkbox"
              />
            </div>
            <div class="dept-details">
              <h3 class="department-name">{{ department.name }}</h3>
              <div class="department-meta">
                <span class="employee-count">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                  </svg>
                  {{ getEmployeeCount(department.id) }} {{ getEmployeeCount(department.id) === 1 ? 'employee' : 'employees' }}
                </span>
                <span class="dept-id">ID: {{ department.id }}</span>
              </div>
            </div>
          </div>
          <div class="card-actions">
            <button @click="editDepartment(department)" class="action-btn-small edit">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
              </svg>
            </button>
            <button @click="deleteDepartment(department)" class="action-btn-small delete">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="3,6 5,6 21,6"/>
                <path d="M19,6V20a2,2,0,0,1-2,2H7a2,2,0,0,1-2-2V6M8,6V4a2,2,0,0,1,2-2h4a2,2,0,0,1,2,2V6"/>
              </svg>
            </button>
          </div>
        </div>

        <div class="card-body">
          <div class="employee-distribution">
            <div class="distribution-header">
              <span class="distribution-title">Employee Distribution</span>
              <span class="distribution-percentage">{{ getDepartmentPercentage(department.id) }}%</span>
            </div>
            <div class="distribution-bar">
              <div 
                class="distribution-fill" 
                :style="{ width: getDepartmentPercentage(department.id) + '%' }"
                :class="getDistributionClass(getEmployeeCount(department.id))"
              ></div>
            </div>
          </div>

          <div class="department-stats">
            <div class="stat-item">
              <span class="stat-label">Created</span>
              <span class="stat-value">{{ formatDate(department.created_at) }}</span>
            </div>
            <div class="stat-item">
              <span class="stat-label">Status</span>
              <span class="stat-value" :class="getStatusClass(getEmployeeCount(department.id))">
                {{ getStatusText(getEmployeeCount(department.id)) }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="filteredDepartments.length === 0" class="empty-state">
        <div class="empty-icon">
          <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
            <rect x="3" y="3" width="7" height="7"/>
            <rect x="14" y="3" width="7" height="7"/>
            <rect x="14" y="14" width="7" height="7"/>
            <rect x="3" y="14" width="7" height="7"/>
          </svg>
        </div>
        <h3 class="empty-title">No Departments Found</h3>
        <p class="empty-description">
          {{ hasActiveSearch ? 'No departments match your current search or filters.' : 'Start by creating your first department.' }}
        </p>
        <button v-if="!hasActiveSearch" @click="showAddModal = true" class="empty-action-btn">
          Add First Department
        </button>
      </div>
    </div>

    <!-- Bulk Selection Bar -->
    <Transition name="slide-up">
      <div v-if="selectedDepartments.length > 0" class="bulk-selection-bar">
        <div class="bulk-info">
          <span class="selection-count">{{ selectedDepartments.length }} department(s) selected</span>
        </div>
        <div class="bulk-actions">
          <button @click="clearSelection" class="bulk-btn secondary">
            Clear Selection
          </button>
          <button @click="showBulkModal = true" class="bulk-btn primary">
            Bulk Actions
          </button>
        </div>
      </div>
    </Transition>

    <!-- Add/Edit Modal -->
    <Transition name="modal">
      <div v-if="showAddModal" class="modal-overlay" @click="closeModal">
        <div class="modal-container" @click.stop>
          <div class="modal-header">
            <h3 class="modal-title">
              {{ editingDepartment ? 'Edit Department' : 'Add New Department' }}
            </h3>
            <button @click="closeModal" class="modal-close-btn">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18"/>
                <line x1="6" y1="6" x2="18" y2="18"/>
              </svg>
            </button>
          </div>

          <form @submit.prevent="submitForm" class="modal-form">
            <div class="form-group">
              <label class="form-label required">Department Name</label>
              <input
                type="text"
                v-model="form.name"
                class="form-input"
                placeholder="e.g., Human Resources, Engineering, Sales"
                required
              />
              <span v-if="form.errors.name" class="form-error">
                {{ form.errors.name }}
              </span>
              <div class="form-help">
                Choose a clear, descriptive name for the department
              </div>
            </div>

            <!-- Preview Section -->
            <div v-if="form.name" class="preview-section">
              <h4 class="preview-title">Preview</h4>
              <div class="preview-card">
                <div class="preview-header">
                  <span class="preview-name">{{ form.name }}</span>
                  <span class="preview-badge">New Department</span>
                </div>
                <div class="preview-details">
                  <div class="preview-stat">
                    <span class="preview-label">Employees:</span>
                    <span class="preview-value">0 (new)</span>
                  </div>
                </div>
              </div>
            </div>

            <div class="modal-actions">
              <button type="button" @click="closeModal" class="action-btn secondary">
                Cancel
              </button>
              <button type="submit" :disabled="form.processing" class="action-btn primary">
                {{ form.processing ? 'Saving...' : (editingDepartment ? 'Update Department' : 'Create Department') }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </Transition>

    <!-- Bulk Actions Modal -->
    <Transition name="modal">
      <div v-if="showBulkModal" class="modal-overlay" @click="closeBulkModal">
        <div class="modal-container" @click.stop>
          <div class="modal-header">
            <h3 class="modal-title">Bulk Actions</h3>
            <button @click="closeBulkModal" class="modal-close-btn">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18"/>
                <line x1="6" y1="6" x2="18" y2="18"/>
              </svg>
            </button>
          </div>

          <div class="bulk-content">
            <div class="bulk-info-section">
              <p class="bulk-description">
                You have selected {{ selectedDepartments.length }} department(s). Choose an action below:
              </p>
              <div class="selected-departments">
                <h4 class="selected-title">Selected Departments:</h4>
                <div class="selected-list">
                  <span v-for="deptId in selectedDepartments" :key="deptId" class="selected-item">
                    {{ getDepartmentName(deptId) }}
                  </span>
                </div>
              </div>
            </div>

            <div class="bulk-actions-section">
              <button @click="confirmBulkDelete" class="bulk-action-btn danger">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <polyline points="3,6 5,6 21,6"/>
                  <path d="M19,6V20a2,2,0,0,1-2,2H7a2,2,0,0,1-2-2V6M8,6V4a2,2,0,0,1,2-2h4a2,2,0,0,1,2,2V6"/>
                </svg>
                Delete Selected Departments
              </button>
              <p class="action-warning">
                ⚠️ This action cannot be undone. Departments with employees cannot be deleted.
              </p>
            </div>
          </div>
        </div>
      </div>
    </Transition>

    <!-- Success Toast -->
    <Transition name="toast">
      <div v-if="showToast" class="toast-container">
        <div class="toast success">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="20,6 9,17 4,12"/>
          </svg>
          {{ toastMessage }}
        </div>
      </div>
    </Transition>
  </AdminLayout>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useForm, router, Head } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

// Props
const props = defineProps({
  departments: Array,
  employees: Array, // Assuming employees data is passed for statistics
  flash: Object,
})

// Reactive state
const showAddModal = ref(false)
const showBulkModal = ref(false)
const editingDepartment = ref(null)
const selectedDepartments = ref([])
const searchQuery = ref('')
const sortBy = ref('name_asc')
const sizeFilter = ref('')
const showToast = ref(false)
const toastMessage = ref('')

// Form
const form = useForm({
  name: '',
})

// Computed properties
const statistics = computed(() => {
  const departments = props.departments || []
  const employees = props.employees || []
  const employeeCounts = departments.map(dept => getEmployeeCount(dept.id))
  
  return {
    total_departments: departments.length,
    total_employees: employees.length,
    avg_employees: departments.length > 0 ? Math.round(employees.length / departments.length) : 0,
    largest_dept: Math.max(...employeeCounts, 0),
  }
})

const filteredDepartments = computed(() => {
  let departments = [...(props.departments || [])]

  // Search filter
  if (searchQuery.value) {
    departments = departments.filter(dept =>
      dept.name.toLowerCase().includes(searchQuery.value.toLowerCase())
    )
  }

  // Size filter
  if (sizeFilter.value) {
    departments = departments.filter(dept => {
      const count = getEmployeeCount(dept.id)
      switch (sizeFilter.value) {
        case 'small': return count >= 1 && count <= 10
        case 'medium': return count > 10 && count <= 50
        case 'large': return count > 50
        case 'empty': return count === 0
        default: return true
      }
    })
  }

  // Sorting
  departments.sort((a, b) => {
    switch (sortBy.value) {
      case 'name_asc':
        return a.name.localeCompare(b.name)
      case 'name_desc':
        return b.name.localeCompare(a.name)
      case 'employees_desc':
        return getEmployeeCount(b.id) - getEmployeeCount(a.id)
      case 'employees_asc':
        return getEmployeeCount(a.id) - getEmployeeCount(b.id)
      case 'created_desc':
        return new Date(b.created_at) - new Date(a.created_at)
      case 'created_asc':
        return new Date(a.created_at) - new Date(b.created_at)
      default:
        return 0
    }
  })

  return departments
})

const hasActiveSearch = computed(() => {
  return searchQuery.value || sizeFilter.value
})

// Methods
const debouncedSearch = debounce(() => {
  // Search is reactive, no action needed
}, 300)

function debounce(func, wait) {
  let timeout
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout)
      func(...args)
    }
    clearTimeout(timeout)
    timeout = setTimeout(later, wait)
  }
}

function clearSearch() {
  searchQuery.value = ''
  sizeFilter.value = ''
  sortBy.value = 'name_asc'
}

function applySorting() {
  // Sorting is reactive via computed property
}

function applyFilters() {
  // Filtering is reactive via computed property
}

function getEmployeeCount(departmentId) {
  // Mock calculation - replace with actual employee count logic
  const employees = props.employees || []
  return employees.filter(emp => emp.department_id === departmentId).length
}

function getDepartmentPercentage(departmentId) {
  const count = getEmployeeCount(departmentId)
  const total = (props.employees || []).length
  return total > 0 ? Math.round((count / total) * 100) : 0
}

function getDistributionClass(employeeCount) {
  if (employeeCount === 0) return 'distribution-empty'
  if (employeeCount <= 10) return 'distribution-small'
  if (employeeCount <= 50) return 'distribution-medium'
  return 'distribution-large'
}

function getStatusClass(employeeCount) {
  if (employeeCount === 0) return 'status-empty'
  if (employeeCount <= 10) return 'status-small'
  if (employeeCount <= 50) return 'status-medium'
  return 'status-large'
}

function getStatusText(employeeCount) {
  if (employeeCount === 0) return 'Empty'
  if (employeeCount <= 10) return 'Small'
  if (employeeCount <= 50) return 'Medium'
  return 'Large'
}

function getDepartmentName(departmentId) {
  const dept = props.departments.find(d => d.id === departmentId)
  return dept ? dept.name : 'Unknown'
}

function formatDate(dateString) {
  if (!dateString) return 'N/A'
  return new Date(dateString).toLocaleDateString()
}

function clearSelection() {
  selectedDepartments.value = []
}

function editDepartment(department) {
  editingDepartment.value = department
  form.name = department.name
  showAddModal.value = true
}

function closeModal() {
  showAddModal.value = false
  editingDepartment.value = null
  form.reset()
  form.clearErrors()
}

function closeBulkModal() {
  showBulkModal.value = false
}

function submitForm() {
  if (editingDepartment.value) {
    // Update department
    const updateForm = useForm({
      name: form.name,
      _method: 'put',
    })

    updateForm.post(route('admin.departments.update', editingDepartment.value.id), {
      preserveScroll: true,
      onSuccess: () => {
        closeModal()
        showToastMessage('Department updated successfully!')
      },
    })
  } else {
    // Create department
    form.post(route('admin.departments.store'), {
      preserveScroll: true,
      onSuccess: () => {
        closeModal()
        showToastMessage('Department created successfully!')
      },
    })
  }
}

function deleteDepartment(department) {
  const employeeCount = getEmployeeCount(department.id)
  
  if (employeeCount > 0) {
    showToastMessage(`Cannot delete "${department.name}" - it has ${employeeCount} employee(s)`)
    return
  }

  if (confirm(`Delete "${department.name}" department? This action cannot be undone.`)) {
    const deleteForm = useForm({
      _method: 'delete',
    })

    deleteForm.post(route('admin.departments.destroy', department.id), {
      preserveScroll: true,
      onSuccess: () => {
        showToastMessage('Department deleted successfully!')
      }
    })
  }
}

function confirmBulkDelete() {
  const departmentNames = selectedDepartments.value.map(id => getDepartmentName(id))
  const departmentsWithEmployees = selectedDepartments.value.filter(id => getEmployeeCount(id) > 0)
  
  if (departmentsWithEmployees.length > 0) {
    showToastMessage('Cannot delete departments with employees')
    return
  }

  if (confirm(`Delete ${selectedDepartments.value.length} department(s)? This action cannot be undone.\n\nDepartments: ${departmentNames.join(', ')}`)) {
    // Process bulk deletion
    selectedDepartments.value.forEach(id => {
      const deleteForm = useForm({
        _method: 'delete',
      })
      deleteForm.post(route('admin.departments.destroy', id), { preserveScroll: true })
    })
    
    clearSelection()
    closeBulkModal()
    showToastMessage(`${selectedDepartments.value.length} department(s) deleted successfully!`)
  }
}

function showToastMessage(message) {
  toastMessage.value = message
  showToast.value = true
  setTimeout(() => {
    showToast.value = false
  }, 3000)
}

// Watch for flash messages
watch(() => props.flash, (flash) => {
  if (flash?.success) {
    showToastMessage(flash.success)
  }
}, { deep: true, immediate: true })
</script>

<style scoped>
/* Page Layout */
.page-header {
  @apply bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6;
}

.header-content {
  @apply flex items-center justify-between;
}

.title-section {
  @apply flex items-center gap-4;
}

.icon-wrapper {
  @apply w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center text-white;
}

.title-content {
  @apply flex flex-col;
}

.page-title {
  @apply text-2xl font-bold text-gray-900;
}

.page-subtitle {
  @apply text-gray-500 mt-1;
}

.header-actions {
  @apply flex items-center gap-3;
}

.action-btn {
  @apply inline-flex items-center gap-2 px-4 py-2 rounded-lg font-medium transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2;
}

.action-btn:disabled {
  @apply opacity-50 cursor-not-allowed;
}

.action-btn.primary {
  @apply bg-gradient-to-r from-purple-500 to-purple-600 text-white hover:from-purple-600 hover:to-purple-700 focus:ring-purple-500 shadow-lg hover:shadow-xl;
}

.action-btn.secondary {
  @apply bg-gray-100 text-gray-700 hover:bg-gray-200 focus:ring-gray-500;
}

/* Statistics Cards */
.stats-grid {
  @apply grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6;
}

.stat-card {
  @apply bg-white rounded-xl p-6 border-l-4 shadow-sm flex items-center gap-4;
}

.stat-card.primary {
  @apply border-l-purple-500;
}

.stat-card.success {
  @apply border-l-green-500;
}

.stat-card.warning {
  @apply border-l-yellow-500;
}

.stat-card.info {
  @apply border-l-blue-500;
}

.stat-icon {
  @apply w-12 h-12 rounded-lg flex items-center justify-center;
}

.stat-card.primary .stat-icon {
  @apply bg-purple-100 text-purple-600;
}

.stat-card.success .stat-icon {
  @apply bg-green-100 text-green-600;
}

.stat-card.warning .stat-icon {
  @apply bg-yellow-100 text-yellow-600;
}

.stat-card.info .stat-icon {
  @apply bg-blue-100 text-blue-600;
}

.stat-content {
  @apply flex flex-col;
}

.stat-value {
  @apply text-2xl font-bold text-gray-900;
}

.stat-label {
  @apply text-sm text-gray-500 mt-1;
}

/* Filters */
.filters-card {
  @apply bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6;
}

.filters-header {
  @apply flex items-center justify-between mb-4;
}

.filters-title {
  @apply text-lg font-semibold text-gray-900 flex items-center gap-2;
}

.clear-filters-btn {
  @apply text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1 px-3 py-1 rounded-md hover:bg-gray-100 transition-colors;
}

.filters-grid {
  @apply grid grid-cols-1 md:grid-cols-3 gap-4;
}

.filter-group {
  @apply flex flex-col;
}

.filter-label {
  @apply text-sm font-medium text-gray-700 mb-2 flex items-center gap-1;
}

.filter-input {
  @apply w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors;
}

/* Departments Grid */
.departments-grid {
  @apply grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6;
}

.department-card {
  @apply bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow;
}

.card-header {
  @apply p-6 border-b border-gray-100 flex items-start justify-between;
}

.department-info {
  @apply flex items-start gap-3 flex-1;
}

.selection-area {
  @apply flex items-center;
}

.selection-checkbox {
  @apply rounded border-gray-300 text-purple-600 focus:ring-purple-500;
}

.dept-details {
  @apply flex-1;
}

.department-name {
  @apply text-lg font-semibold text-gray-900 mb-2;
}

.department-meta {
  @apply flex items-center gap-3 text-sm text-gray-500;
}

.employee-count {
  @apply flex items-center gap-1;
}

.dept-id {
  @apply text-xs bg-gray-100 px-2 py-1 rounded;
}

.card-actions {
  @apply flex items-center gap-1;
}

.action-btn-small {
  @apply p-1.5 rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-offset-1;
}

.action-btn-small.edit {
  @apply text-purple-600 hover:bg-purple-100 focus:ring-purple-500;
}

.action-btn-small.delete {
  @apply text-red-600 hover:bg-red-100 focus:ring-red-500;
}

.card-body {
  @apply p-6;
}

.employee-distribution {
  @apply mb-4;
}

.distribution-header {
  @apply flex items-center justify-between mb-2;
}

.distribution-title {
  @apply text-sm font-medium text-gray-700;
}

.distribution-percentage {
  @apply text-sm text-gray-500;
}

.distribution-bar {
  @apply w-full bg-gray-200 rounded-full h-2;
}

.distribution-fill {
  @apply h-2 rounded-full transition-all duration-300;
}

.distribution-fill.distribution-empty {
  @apply bg-gradient-to-r from-gray-300 to-gray-400;
}

.distribution-fill.distribution-small {
  @apply bg-gradient-to-r from-green-400 to-green-500;
}

.distribution-fill.distribution-medium {
  @apply bg-gradient-to-r from-yellow-400 to-yellow-500;
}

.distribution-fill.distribution-large {
  @apply bg-gradient-to-r from-red-400 to-red-500;
}

.department-stats {
  @apply grid grid-cols-2 gap-4 pt-4 border-t border-gray-100;
}

.stat-item {
  @apply flex flex-col;
}

.stat-label {
  @apply text-xs text-gray-500 uppercase tracking-wide;
}

.stat-value {
  @apply text-sm font-semibold text-gray-900 mt-1;
}

.stat-value.status-empty {
  @apply text-gray-500;
}

.stat-value.status-small {
  @apply text-green-600;
}

.stat-value.status-medium {
  @apply text-yellow-600;
}

.stat-value.status-large {
  @apply text-red-600;
}

/* Empty State */
.empty-state {
  @apply col-span-full text-center py-12;
}

.empty-icon {
  @apply w-16 h-16 mx-auto text-gray-400 mb-4;
}

.empty-title {
  @apply text-lg font-semibold text-gray-900 mb-2;
}

.empty-description {
  @apply text-gray-500 mb-6 max-w-md mx-auto;
}

.empty-action-btn {
  @apply inline-flex items-center px-4 py-2 bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-lg hover:from-purple-600 hover:to-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl;
}

/* Bulk Selection Bar */
.bulk-selection-bar {
  @apply fixed bottom-6 left-1/2 transform -translate-x-1/2 bg-white rounded-xl shadow-lg border border-gray-200 px-6 py-4 flex items-center gap-4 z-40;
}

.bulk-info {
  @apply flex items-center gap-2;
}

.selection-count {
  @apply text-sm font-medium text-gray-700;
}

.bulk-actions {
  @apply flex items-center gap-2;
}

.bulk-btn {
  @apply px-3 py-1.5 rounded-lg text-sm font-medium transition-colors;
}

.bulk-btn.secondary {
  @apply bg-gray-100 text-gray-700 hover:bg-gray-200;
}

.bulk-btn.primary {
  @apply bg-gradient-to-r from-purple-500 to-purple-600 text-white hover:from-purple-600 hover:to-purple-700;
}

/* Modal Styles */
.modal-overlay {
  @apply fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4;
}

.modal-container {
  @apply bg-white rounded-xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto;
}

.modal-header {
  @apply flex items-center justify-between p-6 border-b border-gray-200;
}

.modal-title {
  @apply text-xl font-semibold text-gray-900;
}

.modal-close-btn {
  @apply text-gray-400 hover:text-gray-600 p-1;
}

.modal-form {
  @apply p-6;
}

.form-group {
  @apply flex flex-col mb-6;
}

.form-label {
  @apply text-sm font-medium text-gray-700 mb-2;
}

.form-label.required::after {
  content: '*';
  @apply text-red-500 ml-1;
}

.form-input {
  @apply w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors;
}

.form-error {
  @apply text-sm text-red-600 mt-1;
}

.form-help {
  @apply text-xs text-gray-500 mt-1;
}

.modal-actions {
  @apply flex items-center justify-end gap-3 pt-4 border-t border-gray-200;
}

/* Preview Section */
.preview-section {
  @apply mb-6 p-4 bg-gray-50 rounded-lg border;
}

.preview-title {
  @apply text-sm font-semibold text-gray-700 mb-3;
}

.preview-card {
  @apply bg-white rounded-lg p-4 border;
}

.preview-header {
  @apply flex items-center justify-between mb-3;
}

.preview-name {
  @apply font-semibold text-gray-900;
}

.preview-badge {
  @apply inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800;
}

.preview-details {
  @apply space-y-2;
}

.preview-stat {
  @apply flex justify-between text-sm;
}

.preview-label {
  @apply text-gray-600;
}

.preview-value {
  @apply font-medium text-gray-900;
}

/* Bulk Modal */
.bulk-content {
  @apply p-6;
}

.bulk-info-section {
  @apply mb-6;
}

.bulk-description {
  @apply text-gray-700 mb-4;
}

.selected-departments {
  @apply mb-4;
}

.selected-title {
  @apply text-sm font-semibold text-gray-700 mb-2;
}

.selected-list {
  @apply flex flex-wrap gap-2;
}

.selected-item {
  @apply inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800;
}

.bulk-actions-section {
  @apply pt-4 border-t border-gray-200;
}

.bulk-action-btn {
  @apply w-full flex items-center justify-center gap-2 px-4 py-3 rounded-lg font-medium transition-colors;
}

.bulk-action-btn.danger {
  @apply bg-red-500 text-white hover:bg-red-600;
}

.action-warning {
  @apply text-sm text-gray-500 mt-2 text-center;
}

/* Toast Notifications */
.toast-container {
  @apply fixed top-4 right-4 z-50;
}

.toast {
  @apply flex items-center gap-3 px-4 py-3 rounded-lg shadow-lg;
}

.toast.success {
  @apply bg-green-500 text-white;
}

/* Transitions */
.modal-enter-active, .modal-leave-active {
  transition: all 0.3s ease;
}

.modal-enter-from, .modal-leave-to {
  opacity: 0;
  transform: scale(0.95);
}

.slide-up-enter-active, .slide-up-leave-active {
  transition: all 0.3s ease;
}

.slide-up-enter-from, .slide-up-leave-to {
  opacity: 0;
  transform: translateY(100%);
}

.toast-enter-active, .toast-leave-active {
  transition: all 0.3s ease;
}

.toast-enter-from, .toast-leave-to {
  opacity: 0;
  transform: translateX(100%);
}

/* Responsive Design */
@media (max-width: 768px) {
  .header-content {
    @apply flex-col items-start gap-4;
  }

  .stats-grid {
    @apply grid-cols-1;
  }

  .filters-grid {
    @apply grid-cols-1;
  }

  .departments-grid {
    @apply grid-cols-1;
  }

  .modal-container {
    @apply mx-2 max-w-none;
  }

  .department-stats {
    @apply grid-cols-1;
  }

  .bulk-selection-bar {
    @apply left-4 right-4 transform-none translate-x-0;
  }
}
</style>