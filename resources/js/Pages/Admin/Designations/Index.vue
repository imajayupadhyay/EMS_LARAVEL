<template>
  <AdminLayout>
    <Head title="Designations Management" />
    
    <!-- Header Section -->
    <div class="page-header">
      <div class="header-content">
        <div class="title-section">
          <div class="icon-wrapper">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
              <circle cx="12" cy="7" r="4"/>
            </svg>
          </div>
          <div class="title-content">
            <h1 class="page-title">Designations</h1>
            <p class="page-subtitle">Manage job roles and positions within departments</p>
          </div>
        </div>
        <div class="header-actions">
          <button @click="showDepartmentView = !showDepartmentView" class="action-btn secondary">
            <svg v-if="!showDepartmentView" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="3" y="3" width="7" height="7"/>
              <rect x="14" y="3" width="7" height="7"/>
              <rect x="14" y="14" width="7" height="7"/>
              <rect x="3" y="14" width="7" height="7"/>
            </svg>
            <svg v-else width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <line x1="8" y1="6" x2="21" y2="6"/>
              <line x1="8" y1="12" x2="21" y2="12"/>
              <line x1="8" y1="18" x2="21" y2="18"/>
              <line x1="3" y1="6" x2="3.01" y2="6"/>
              <line x1="3" y1="12" x2="3.01" y2="12"/>
              <line x1="3" y1="18" x2="3.01" y2="18"/>
            </svg>
            {{ showDepartmentView ? 'List View' : 'Department View' }}
          </button>
          <button @click="showBulkModal = true" class="action-btn secondary" :disabled="selectedDesignations.length === 0">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/>
              <rect x="8" y="2" width="8" height="4" rx="1" ry="1"/>
            </svg>
            Bulk Actions ({{ selectedDesignations.length }})
          </button>
          <button @click="showAddModal = true" class="action-btn primary">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="10"/>
              <line x1="12" y1="8" x2="12" y2="16"/>
              <line x1="8" y1="12" x2="16" y2="12"/>
            </svg>
            Add Designation
          </button>
        </div>
      </div>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-grid">
      <div class="stat-card primary">
        <div class="stat-icon">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
            <circle cx="12" cy="7" r="4"/>
          </svg>
        </div>
        <div class="stat-content">
          <div class="stat-value">{{ statistics.total_designations }}</div>
          <div class="stat-label">Total Designations</div>
        </div>
      </div>

      <div class="stat-card success">
        <div class="stat-icon">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="3" y="3" width="7" height="7"/>
            <rect x="14" y="3" width="7" height="7"/>
            <rect x="14" y="14" width="7" height="7"/>
            <rect x="3" y="14" width="7" height="7"/>
          </svg>
        </div>
        <div class="stat-content">
          <div class="stat-value">{{ statistics.departments_with_designations }}</div>
          <div class="stat-label">Departments</div>
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
          <div class="stat-value">{{ statistics.avg_per_department }}</div>
          <div class="stat-label">Avg per Dept</div>
        </div>
      </div>

      <div class="stat-card warning">
        <div class="stat-icon">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="16" x2="12" y2="12"/>
            <line x1="12" y1="8" x2="12.01" y2="8"/>
          </svg>
        </div>
        <div class="stat-content">
          <div class="stat-value">{{ statistics.unassigned_count }}</div>
          <div class="stat-label">Unassigned</div>
        </div>
      </div>
    </div>

    <!-- Search and Filters -->
    <div class="filters-card">
      <div class="filters-header">
        <h3 class="filters-title">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polygon points="22,3 2,3 10,12.46 10,19 14,21 14,12.46"/>
          </svg>
          Search & Filters
        </h3>
        <button @click="clearFilters" class="clear-filters-btn">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="18" y1="6" x2="6" y2="18"/>
            <line x1="6" y1="6" x2="18" y2="18"/>
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
            Search Designations
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
              <rect x="3" y="3" width="7" height="7"/>
              <rect x="14" y="3" width="7" height="7"/>
              <rect x="14" y="14" width="7" height="7"/>
              <rect x="3" y="14" width="7" height="7"/>
            </svg>
            Department
          </label>
          <select v-model="filters.department_id" @change="applyFilters" class="filter-input">
            <option value="">All Departments</option>
            <option v-for="dept in departments" :key="dept.id" :value="dept.id">
              {{ dept.name }}
            </option>
          </select>
        </div>

        <div class="filter-group">
          <label class="filter-label">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M3 6h18l-2 13H5L3 6Z"/>
              <path d="M8 10V7a4 4 0 0 1 8 0v3"/>
            </svg>
            Sort By
          </label>
          <select v-model="filters.sort_by" @change="applyFilters" class="filter-input">
            <option value="name_asc">Name (A-Z)</option>
            <option value="name_desc">Name (Z-A)</option>
            <option value="department_asc">Department (A-Z)</option>
            <option value="department_desc">Department (Z-A)</option>
            <option value="created_desc">Recently Added</option>
            <option value="created_asc">Oldest First</option>
          </select>
        </div>

        <div class="filter-group">
          <label class="filter-label">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="10"/>
              <line x1="12" y1="16" x2="12" y2="12"/>
              <line x1="12" y1="8" x2="12.01" y2="8"/>
            </svg>
            Assignment Status
          </label>
          <select v-model="filters.assignment_status" @change="applyFilters" class="filter-input">
            <option value="">All Status</option>
            <option value="assigned">Assigned to Department</option>
            <option value="unassigned">Unassigned</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Content Views -->
    <div v-if="!showDepartmentView" class="designations-grid">
      <!-- Regular Grid View -->
      <div v-for="designation in filteredDesignations" :key="designation.id" class="designation-card">
        <div class="card-header">
          <div class="designation-info">
            <div class="selection-area">
              <input
                type="checkbox"
                :value="designation.id"
                v-model="selectedDesignations"
                class="selection-checkbox"
              />
            </div>
            <div class="designation-details">
              <h3 class="designation-name">{{ designation.name }}</h3>
              <div class="designation-meta">
                <span class="department-badge" :class="getDepartmentBadgeClass(designation.department_id)">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="7" height="7"/>
                    <rect x="14" y="3" width="7" height="7"/>
                    <rect x="14" y="14" width="7" height="7"/>
                    <rect x="3" y="14" width="7" height="7"/>
                  </svg>
                  {{ designation.department?.name || 'Unassigned' }}
                </span>
                <span class="designation-id">ID: {{ designation.id }}</span>
              </div>
            </div>
          </div>
          <div class="card-actions">
            <button @click="editDesignation(designation)" class="action-btn-small edit">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
              </svg>
            </button>
            <button @click="deleteDesignation(designation)" class="action-btn-small delete">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="3,6 5,6 21,6"/>
                <path d="M19,6V20a2,2,0,0,1-2,2H7a2,2,0,0,1-2-2V6M8,6V4a2,2,0,0,1,2-2h4a2,2,0,0,1,2,2V6"/>
              </svg>
            </button>
          </div>
        </div>

        <div class="card-body">
          <div class="designation-stats">
            <div class="stat-item">
              <span class="stat-label">Created</span>
              <span class="stat-value">{{ formatDate(designation.created_at) }}</span>
            </div>
            <div class="stat-item">
              <span class="stat-label">Status</span>
              <span class="stat-value" :class="getStatusClass(designation.department_id)">
                {{ designation.department_id ? 'Assigned' : 'Unassigned' }}
              </span>
            </div>
          </div>

          <div v-if="designation.department_id" class="department-info">
            <div class="info-header">
              <span class="info-title">Department Details</span>
            </div>
            <div class="info-content">
              <span class="info-text">{{ designation.department?.name }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="filteredDesignations.length === 0" class="empty-state">
        <div class="empty-icon">
          <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
            <circle cx="12" cy="7" r="4"/>
          </svg>
        </div>
        <h3 class="empty-title">No Designations Found</h3>
        <p class="empty-description">
          {{ hasActiveFilters ? 'No designations match your current search or filters.' : 'Start by creating your first designation.' }}
        </p>
        <button v-if="!hasActiveFilters" @click="showAddModal = true" class="empty-action-btn">
          Add First Designation
        </button>
      </div>
    </div>

    <!-- Department View -->
    <div v-else class="department-view">
      <div v-for="department in departmentGroups" :key="department.id || 'unassigned'" class="department-group">
        <div class="group-header">
          <div class="group-info">
            <h3 class="group-title">{{ department.name || 'Unassigned Designations' }}</h3>
            <span class="group-count">{{ department.designations.length }} designation{{ department.designations.length !== 1 ? 's' : '' }}</span>
          </div>
          <div class="group-actions">
            <button v-if="department.id" @click="addDesignationToDepartment(department.id)" class="group-btn">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="8" x2="12" y2="16"/>
                <line x1="8" y1="12" x2="16" y2="12"/>
              </svg>
              Add to Department
            </button>
          </div>
        </div>

        <div class="group-content">
          <div v-if="department.designations.length === 0" class="group-empty">
            <p class="empty-text">No designations in this {{ department.id ? 'department' : 'category' }}</p>
          </div>
          <div v-else class="designation-list">
            <div v-for="designation in department.designations" :key="designation.id" class="designation-item">
              <div class="item-info">
                <input
                  type="checkbox"
                  :value="designation.id"
                  v-model="selectedDesignations"
                  class="selection-checkbox"
                />
                <span class="item-name">{{ designation.name }}</span>
                <span class="item-id">ID: {{ designation.id }}</span>
              </div>
              <div class="item-actions">
                <button @click="editDesignation(designation)" class="action-btn-small edit">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                  </svg>
                </button>
                <button @click="deleteDesignation(designation)" class="action-btn-small delete">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="3,6 5,6 21,6"/>
                    <path d="M19,6V20a2,2,0,0,1-2,2H7a2,2,0,0,1-2-2V6M8,6V4a2,2,0,0,1,2-2h4a2,2,0,0,1,2,2V6"/>
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Bulk Selection Bar -->
    <Transition name="slide-up">
      <div v-if="selectedDesignations.length > 0" class="bulk-selection-bar">
        <div class="bulk-info">
          <span class="selection-count">{{ selectedDesignations.length }} designation(s) selected</span>
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
              {{ editingDesignation ? 'Edit Designation' : 'Add New Designation' }}
            </h3>
            <button @click="closeModal" class="modal-close-btn">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18"/>
                <line x1="6" y1="6" x2="18" y2="18"/>
              </svg>
            </button>
          </div>

          <form @submit.prevent="submitForm" class="modal-form">
            <div class="form-grid">
              <div class="form-group">
                <label class="form-label required">Designation Name</label>
                <input
                  type="text"
                  v-model="form.name"
                  class="form-input"
                  placeholder="e.g., Software Engineer, HR Manager"
                  required
                />
                <span v-if="form.errors.name" class="form-error">
                  {{ form.errors.name }}
                </span>
              </div>

              <div class="form-group">
                <label class="form-label">Department</label>
                <select v-model="form.department_id" class="form-input">
                  <option value="">Select Department (Optional)</option>
                  <option v-for="dept in departments" :key="dept.id" :value="dept.id">
                    {{ dept.name }}
                  </option>
                </select>
                <span v-if="form.errors.department_id" class="form-error">
                  {{ form.errors.department_id }}
                </span>
                <div class="form-help">
                  You can assign the designation to a department later if needed
                </div>
              </div>
            </div>

            <!-- Preview Section -->
            <div v-if="form.name" class="preview-section">
              <h4 class="preview-title">Preview</h4>
              <div class="preview-card">
                <div class="preview-header">
                  <span class="preview-name">{{ form.name }}</span>
                  <span class="preview-badge" :class="getDepartmentBadgeClass(form.department_id)">
                    {{ getDepartmentName(form.department_id) || 'Unassigned' }}
                  </span>
                </div>
                <div class="preview-details">
                  <div class="preview-stat">
                    <span class="preview-label">Status:</span>
                    <span class="preview-value" :class="getStatusClass(form.department_id)">
                      {{ form.department_id ? 'Assigned' : 'Unassigned' }}
                    </span>
                  </div>
                </div>
              </div>
            </div>

            <div class="modal-actions">
              <button type="button" @click="closeModal" class="action-btn secondary">
                Cancel
              </button>
              <button type="submit" :disabled="form.processing" class="action-btn primary">
                {{ form.processing ? 'Saving...' : (editingDesignation ? 'Update Designation' : 'Create Designation') }}
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
                You have selected {{ selectedDesignations.length }} designation(s). Choose an action below:
              </p>
            </div>

            <div class="bulk-actions-section">
              <div class="bulk-action-group">
                <h4 class="action-group-title">Department Assignment</h4>
                <form @submit.prevent="bulkAssignDepartment" class="bulk-form">
                  <select v-model="bulkForm.department_id" class="form-input mb-3">
                    <option value="">Select Department</option>
                    <option v-for="dept in departments" :key="dept.id" :value="dept.id">
                      {{ dept.name }}
                    </option>
                  </select>
                  <button type="submit" class="bulk-action-btn secondary full-width">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <rect x="3" y="3" width="7" height="7"/>
                      <rect x="14" y="3" width="7" height="7"/>
                      <rect x="14" y="14" width="7" height="7"/>
                      <rect x="3" y="14" width="7" height="7"/>
                    </svg>
                    Assign to Department
                  </button>
                </form>
              </div>

              <div class="bulk-divider"></div>

              <div class="bulk-action-group">
                <h4 class="action-group-title">Dangerous Actions</h4>
                <button @click="confirmBulkDelete" class="bulk-action-btn danger full-width">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="3,6 5,6 21,6"/>
                    <path d="M19,6V20a2,2,0,0,1-2,2H7a2,2,0,0,1-2-2V6M8,6V4a2,2,0,0,1,2-2h4a2,2,0,0,1,2,2V6"/>
                  </svg>
                  Delete Selected Designations
                </button>
                <p class="action-warning">
                  ⚠️ This action cannot be undone. Designations assigned to employees cannot be deleted.
                </p>
              </div>
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
  designations: {
    type: [Array, Object],
    default: () => ([])
  },
  departments: {
    type: Array,
    default: () => ([])
  },
  employees: {
    type: Array,
    default: () => ([])
  },
  flash: Object,
})

// Reactive state
const showAddModal = ref(false)
const showBulkModal = ref(false)
const showDepartmentView = ref(false)
const editingDesignation = ref(null)
const selectedDesignations = ref([])
const searchQuery = ref('')
const showToast = ref(false)
const toastMessage = ref('')

// Filters
const filters = ref({
  department_id: '',
  sort_by: 'name_asc',
  assignment_status: '',
})

// Forms
const form = useForm({
  name: '',
  department_id: '',
})

const bulkForm = useForm({
  department_id: '',
})

// Computed properties
const statistics = computed(() => {
  // Safely extract designations array
  let designationsData = []
  
  if (Array.isArray(props.designations)) {
    designationsData = props.designations
  } else if (props.designations && Array.isArray(props.designations.data)) {
    designationsData = props.designations.data
  } else if (props.designations && props.designations.designations) {
    designationsData = props.designations.designations
  }

  const departments = props.departments || []
  const assignedCount = designationsData.filter(d => d && d.department_id).length
  const departmentsWithDesignations = new Set(designationsData.filter(d => d && d.department_id).map(d => d.department_id)).size
  
  return {
    total_designations: designationsData.length,
    departments_with_designations: departmentsWithDesignations,
    avg_per_department: departments.length > 0 ? Math.round(assignedCount / departments.length) : 0,
    unassigned_count: designationsData.length - assignedCount,
  }
})

const filteredDesignations = computed(() => {
  // Safely extract designations array
  let designationsData = []
  
  if (Array.isArray(props.designations)) {
    designationsData = props.designations
  } else if (props.designations && Array.isArray(props.designations.data)) {
    designationsData = props.designations.data
  } else if (props.designations && props.designations.designations) {
    designationsData = props.designations.designations
  }
  
  let designations = [...designationsData]

  // Search filter
  if (searchQuery.value) {
    designations = designations.filter(designation =>
      designation.name && designation.name.toLowerCase().includes(searchQuery.value.toLowerCase())
    )
  }

  // Department filter
  if (filters.value.department_id) {
    designations = designations.filter(designation => 
      designation.department_id == filters.value.department_id
    )
  }

  // Assignment status filter
  if (filters.value.assignment_status) {
    if (filters.value.assignment_status === 'assigned') {
      designations = designations.filter(designation => designation.department_id)
    } else if (filters.value.assignment_status === 'unassigned') {
      designations = designations.filter(designation => !designation.department_id)
    }
  }

  // Sorting
  designations.sort((a, b) => {
    switch (filters.value.sort_by) {
      case 'name_asc':
        return a.name.localeCompare(b.name)
      case 'name_desc':
        return b.name.localeCompare(a.name)
      case 'department_asc':
        const deptA = a.department?.name || 'zzz'
        const deptB = b.department?.name || 'zzz'
        return deptA.localeCompare(deptB)
      case 'department_desc':
        const deptA2 = a.department?.name || ''
        const deptB2 = b.department?.name || ''
        return deptB2.localeCompare(deptA2)
      case 'created_desc':
        return new Date(b.created_at) - new Date(a.created_at)
      case 'created_asc':
        return new Date(a.created_at) - new Date(b.created_at)
      default:
        return 0
    }
  })

  return designations
})

const departmentGroups = computed(() => {
  const groups = []
  const departments = props.departments || []
  
  // Safely extract designations array
  let designationsData = []
  if (Array.isArray(props.designations)) {
    designationsData = props.designations
  } else if (props.designations && Array.isArray(props.designations.data)) {
    designationsData = props.designations.data
  } else if (props.designations && props.designations.designations) {
    designationsData = props.designations.designations
  }

  // Create groups for each department
  departments.forEach(dept => {
    const deptDesignations = designationsData.filter(d => d && d.department_id === dept.id)
    groups.push({
      id: dept.id,
      name: dept.name,
      designations: deptDesignations
    })
  })

  // Add unassigned group
  const unassigned = designationsData.filter(d => d && !d.department_id)
  if (unassigned.length > 0) {
    groups.push({
      id: null,
      name: null,
      designations: unassigned
    })
  }

  return groups.filter(g => g.designations.length > 0)
})

const hasActiveFilters = computed(() => {
  return searchQuery.value || 
         filters.value.department_id || 
         filters.value.assignment_status
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

function clearFilters() {
  searchQuery.value = ''
  filters.value = {
    department_id: '',
    sort_by: 'name_asc',
    assignment_status: '',
  }
}

function applyFilters() {
  // Filtering is reactive via computed property
}

function getDepartmentBadgeClass(departmentId) {
  return departmentId ? 'badge-assigned' : 'badge-unassigned'
}

function getStatusClass(departmentId) {
  return departmentId ? 'status-assigned' : 'status-unassigned'
}

function getDepartmentName(departmentId) {
  if (!departmentId) return null
  const dept = props.departments.find(d => d.id == departmentId)
  return dept ? dept.name : null
}

function formatDate(dateString) {
  if (!dateString) return 'N/A'
  return new Date(dateString).toLocaleDateString()
}

function clearSelection() {
  selectedDesignations.value = []
}

function addDesignationToDepartment(departmentId) {
  form.department_id = departmentId
  showAddModal.value = true
}

function editDesignation(designation) {
  editingDesignation.value = designation
  form.name = designation.name
  form.department_id = designation.department_id || ''
  showAddModal.value = true
}

function closeModal() {
  showAddModal.value = false
  editingDesignation.value = null
  form.reset()
  form.clearErrors()
}

function closeBulkModal() {
  showBulkModal.value = false
  bulkForm.reset()
}

function submitForm() {
  if (editingDesignation.value) {
    // Update designation
    const updateForm = useForm({
      name: form.name,
      department_id: form.department_id,
      _method: 'put',
    })

    updateForm.post(route('admin.designations.update', editingDesignation.value.id), {
      preserveScroll: true,
      onSuccess: () => {
        closeModal()
        showToastMessage('Designation updated successfully!')
      },
    })
  } else {
    // Create designation
    form.post(route('admin.designations.store'), {
      preserveScroll: true,
      onSuccess: () => {
        closeModal()
        showToastMessage('Designation created successfully!')
      },
    })
  }
}

function deleteDesignation(designation) {
  // Check if designation has employees (you might need to pass this data from backend)
  // const hasEmployees = (props.employees || []).some(emp => emp.designation_id === designation.id)
  
  // if (hasEmployees) {
  //   showToastMessage(`Cannot delete "${designation.name}" - it has employees assigned`)
  //   return
  // }

  if (confirm(`Delete "${designation.name}" designation? This action cannot be undone.`)) {
    router.post(route('admin.designations.destroy', designation.id), {}, {
      preserveScroll: true,
      onSuccess: () => {
        showToastMessage('Designation deleted successfully!')
      }
    })
  }
}

function bulkAssignDepartment() {
  if (!bulkForm.department_id) {
    showToastMessage('Please select a department')
    return
  }

  selectedDesignations.value.forEach(designationId => {
    const updateForm = useForm({
      department_id: bulkForm.department_id,
      _method: 'put',
    })
    
    // You might need to modify this to handle bulk updates more efficiently
    updateForm.post(route('admin.designations.update', designationId), {
      preserveScroll: true,
    })
  })

  clearSelection()
  closeBulkModal()
  showToastMessage(`${selectedDesignations.value.length} designation(s) assigned to department successfully!`)
}

function confirmBulkDelete() {
  // Safely extract designations array
  let designationsData = []
  
  if (Array.isArray(props.designations)) {
    designationsData = props.designations
  } else if (props.designations && Array.isArray(props.designations.data)) {
    designationsData = props.designations.data
  } else if (props.designations && props.designations.designations) {
    designationsData = props.designations.designations
  }

  const designationNames = selectedDesignations.value.map(id => {
    const designation = designationsData.find(d => d && d.id === id)
    return designation ? designation.name : 'Unknown'
  })

  if (confirm(`Delete ${selectedDesignations.value.length} designation(s)? This action cannot be undone.\n\nDesignations: ${designationNames.join(', ')}`)) {
    selectedDesignations.value.forEach(id => {
      router.post(route('admin.designations.destroy', id), {}, {
        preserveScroll: true
      })
    })
    
    clearSelection()
    closeBulkModal()
    showToastMessage(`${selectedDesignations.value.length} designation(s) deleted successfully!`)
  }
}

function showToastMessage(message) {
  toastMessage.value = message
  showToast.value = true
  setTimeout(() => {
    showToast.value = false
  }, 3000)
}

// Debug helper (remove after fixing)
onMounted(() => {
  console.log('Designations data:', props.designations)
  console.log('Departments data:', props.departments)
})
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
  @apply grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4;
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

/* Designations Grid */
.designations-grid {
  @apply grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6;
}

.designation-card {
  @apply bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow;
}

.card-header {
  @apply p-6 border-b border-gray-100 flex items-start justify-between;
}

.designation-info {
  @apply flex items-start gap-3 flex-1;
}

.selection-area {
  @apply flex items-center;
}

.selection-checkbox {
  @apply rounded border-gray-300 text-purple-600 focus:ring-purple-500;
}

.designation-details {
  @apply flex-1;
}

.designation-name {
  @apply text-lg font-semibold text-gray-900 mb-2;
}

.designation-meta {
  @apply flex items-center gap-3;
}

.department-badge {
  @apply inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium;
}

.department-badge.badge-assigned {
  @apply bg-green-100 text-green-800;
}

.department-badge.badge-unassigned {
  @apply bg-gray-100 text-gray-800;
}

.designation-id {
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

.designation-stats {
  @apply grid grid-cols-2 gap-4 mb-4;
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

.stat-value.status-assigned {
  @apply text-green-600;
}

.stat-value.status-unassigned {
  @apply text-gray-500;
}

.department-info {
  @apply pt-4 border-t border-gray-100;
}

.info-header {
  @apply mb-2;
}

.info-title {
  @apply text-sm font-medium text-gray-700;
}

.info-content {
  @apply text-sm text-gray-600;
}

/* Department View */
.department-view {
  @apply space-y-6;
}

.department-group {
  @apply bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden;
}

.group-header {
  @apply bg-gray-50 px-6 py-4 border-b border-gray-200 flex items-center justify-between;
}

.group-info {
  @apply flex items-center gap-3;
}

.group-title {
  @apply text-lg font-semibold text-gray-900;
}

.group-count {
  @apply text-sm text-gray-500 bg-white px-2 py-1 rounded;
}

.group-actions {
  @apply flex items-center gap-2;
}

.group-btn {
  @apply inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium text-purple-600 bg-purple-100 rounded-lg hover:bg-purple-200 transition-colors;
}

.group-content {
  @apply p-6;
}

.group-empty {
  @apply text-center py-8;
}

.empty-text {
  @apply text-gray-500;
}

.designation-list {
  @apply space-y-3;
}

.designation-item {
  @apply flex items-center justify-between p-4 bg-gray-50 rounded-lg;
}

.item-info {
  @apply flex items-center gap-3;
}

.item-name {
  @apply font-medium text-gray-900;
}

.item-id {
  @apply text-xs text-gray-500 bg-white px-2 py-1 rounded;
}

.item-actions {
  @apply flex items-center gap-1;
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

.form-grid {
  @apply grid grid-cols-1 md:grid-cols-2 gap-4 mb-6;
}

.form-group {
  @apply flex flex-col;
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
  @apply inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium;
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

.preview-value.status-assigned {
  @apply text-green-600;
}

.preview-value.status-unassigned {
  @apply text-gray-500;
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

.bulk-actions-section {
  @apply space-y-6;
}

.bulk-action-group {
  @apply space-y-3;
}

.action-group-title {
  @apply text-lg font-semibold text-gray-900;
}

.bulk-form {
  @apply space-y-3;
}

.bulk-action-btn {
  @apply flex items-center justify-center gap-2 px-4 py-3 rounded-lg font-medium transition-colors;
}

.bulk-action-btn.secondary {
  @apply bg-gray-100 text-gray-700 hover:bg-gray-200;
}

.bulk-action-btn.danger {
  @apply bg-red-500 text-white hover:bg-red-600;
}

.full-width {
  @apply w-full;
}

.bulk-divider {
  @apply border-t border-gray-200;
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

  .designations-grid {
    @apply grid-cols-1;
  }

  .modal-container {
    @apply mx-2 max-w-none;
  }

  .form-grid {
    @apply grid-cols-1;
  }

  .designation-stats {
    @apply grid-cols-1;
  }

  .bulk-selection-bar {
    @apply left-4 right-4 transform-none translate-x-0;
  }

  .group-header {
    @apply flex-col items-start gap-3;
  }

  .designation-item {
    @apply flex-col items-start gap-3;
  }
}
</style>