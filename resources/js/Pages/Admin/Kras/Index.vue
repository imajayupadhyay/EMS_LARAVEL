<template>
  <AdminLayout>
    <div class="kra-management">
      <!-- Header Section -->
      <div class="page-header">
        <div class="header-content">
          <div>
            <h1 class="page-title">KRA Management</h1>
            <p class="page-description">Manage Key Responsibility Areas for departments</p>
          </div>
          <button 
            @click="showCreateModal = true"
            class="btn-primary"
          >
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Add New KRA
          </button>
        </div>
      </div>

      <!-- Statistics Cards -->
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-icon bg-blue-100">
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
          </div>
          <div>
            <p class="stat-label">Total KRAs</p>
            <p class="stat-value">{{ statistics.total }}</p>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon bg-green-100">
            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
          <div>
            <p class="stat-label">Active KRAs</p>
            <p class="stat-value">{{ statistics.active }}</p>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon bg-red-100">
            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
          </div>
          <div>
            <p class="stat-label">High Priority</p>
            <p class="stat-value">{{ statistics.high_priority }}</p>
          </div>
        </div>
      </div>

      <!-- Filters Section -->
      <div class="filters-section">
        <div class="filters-row">
          <div class="search-input">
            <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <input
              v-model="filters.search"
              @input="debouncedFilter"
              type="text"
              placeholder="Search KRAs..."
            />
          </div>
          
          <select v-model="filters.department_id" @change="applyFilters" class="filter-select">
            <option value="">All Departments</option>
            <option v-for="dept in departments" :key="dept.id" :value="dept.id">
              {{ dept.name }}
            </option>
          </select>

          <select v-model="filters.priority" @change="applyFilters" class="filter-select">
            <option value="">All Priorities</option>
            <option value="high">High</option>
            <option value="medium">Medium</option>
            <option value="low">Low</option>
          </select>

          <select v-model="filters.status" @change="applyFilters" class="filter-select">
            <option value="">All Status</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
          </select>
        </div>
      </div>

      <!-- KRAs List -->
      <div class="kras-list">
        <div v-if="kras.data.length === 0" class="empty-state">
          <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
          </svg>
          <h3 class="text-lg font-semibold text-gray-600 mb-2">No KRAs Found</h3>
          <p class="text-gray-500 mb-4">Start by creating your first KRA for a department.</p>
          <button @click="showCreateModal = true" class="btn-primary">
            Create First KRA
          </button>
        </div>

        <div v-else class="kra-grid">
          <div 
            v-for="kra in kras.data" 
            :key="kra.id" 
            class="kra-card"
            :class="{ 'inactive': !kra.is_active }"
          >
            <div class="kra-header">
              <div class="kra-title-section">
                <h3 class="kra-title">{{ kra.title }}</h3>
                <div class="kra-meta">
                  <span class="department-badge">
                    {{ kra.department.name }}
                  </span>
                  <span 
                    class="priority-badge" 
                    :class="`priority-${kra.priority}`"
                  >
                    {{ kra.priority.charAt(0).toUpperCase() + kra.priority.slice(1) }}
                  </span>
                </div>
              </div>
              
              <div class="kra-actions">
                <button 
                  @click="toggleStatus(kra)"
                  class="action-btn"
                  :class="kra.is_active ? 'deactivate' : 'activate'"
                  :title="kra.is_active ? 'Deactivate' : 'Activate'"
                >
                  <svg v-if="kra.is_active" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"></path>
                  </svg>
                  <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                </button>
                
                <button 
                  @click="editKra(kra)"
                  class="action-btn edit"
                  title="Edit"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                  </svg>
                </button>
                
                <button 
                  @click="deleteKra(kra)"
                  class="action-btn delete"
                  title="Delete"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                  </svg>
                </button>
              </div>
            </div>

            <div class="kra-description" v-if="kra.description">
              <p>{{ kra.description }}</p>
            </div>

            <div class="kra-footer">
              <span class="created-info">
                Created by {{ kra.creator ? kra.creator.name : 'Unknown' }}
              </span>
              <span class="created-date">
                {{ formatDate(kra.created_at) }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="kras.last_page > 1" class="pagination-wrapper">
        <nav class="pagination">
          <Link 
            v-for="link in kras.links" 
            :key="link.label" 
            :href="link.url" 
            :class="['pagination-link', { 'active': link.active, 'disabled': !link.url }]"
            v-html="link.label"
          />
        </nav>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <div v-if="showCreateModal || showEditModal" class="modal-overlay" @click="closeModals">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h2 class="modal-title">
            {{ showCreateModal ? 'Create New KRA' : 'Edit KRA' }}
          </h2>
          <button @click="closeModals" class="modal-close">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>

        <form @submit.prevent="submitForm" class="modal-form">
          <div class="form-group">
            <label for="title" class="form-label">KRA Title *</label>
            <input
              id="title"
              v-model="form.title"
              type="text"
              class="form-input"
              :class="{ 'error': errors.title }"
              placeholder="Enter KRA title..."
              required
            />
            <span v-if="errors.title" class="error-text">{{ errors.title }}</span>
          </div>

          <div class="form-group">
            <label for="department_id" class="form-label">Department *</label>
            <select
              id="department_id"
              v-model="form.department_id"
              class="form-select"
              :class="{ 'error': errors.department_id }"
              required
            >
              <option value="">Select Department</option>
              <option v-for="dept in departments" :key="dept.id" :value="dept.id">
                {{ dept.name }}
              </option>
            </select>
            <span v-if="errors.department_id" class="error-text">{{ errors.department_id }}</span>
          </div>

          <div class="form-group">
            <label for="priority" class="form-label">Priority *</label>
            <select
              id="priority"
              v-model="form.priority"
              class="form-select"
              :class="{ 'error': errors.priority }"
              required
            >
              <option value="">Select Priority</option>
              <option value="high">High</option>
              <option value="medium">Medium</option>
              <option value="low">Low</option>
            </select>
            <span v-if="errors.priority" class="error-text">{{ errors.priority }}</span>
          </div>

          <div class="form-group">
            <label for="description" class="form-label">Description</label>
            <textarea
              id="description"
              v-model="form.description"
              class="form-textarea"
              :class="{ 'error': errors.description }"
              placeholder="Enter KRA description..."
              rows="4"
            ></textarea>
            <span v-if="errors.description" class="error-text">{{ errors.description }}</span>
          </div>

          <div class="form-group">
            <label class="checkbox-label">
              <input
                v-model="form.is_active"
                type="checkbox"
                class="form-checkbox"
              />
              <span class="checkmark"></span>
              Active
            </label>
          </div>

          <div class="modal-footer">
            <button type="button" @click="closeModals" class="btn-secondary">
              Cancel
            </button>
            <button type="submit" class="btn-primary" :disabled="processing">
              <svg v-if="processing" class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              {{ showCreateModal ? 'Create KRA' : 'Update KRA' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { router, Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { debounce } from 'lodash'

// Props
const props = defineProps({
  kras: Object,
  departments: Array,
  filters: Object,
  statistics: Object,
})

// Reactive state
const showCreateModal = ref(false)
const showEditModal = ref(false)
const processing = ref(false)
const errors = ref({})

// Form data
const form = reactive({
  id: null,
  title: '',
  department_id: '',
  priority: 'medium',
  description: '',
  is_active: true,
})

// Filters
const filters = reactive({
  search: props.filters?.search || '',
  department_id: props.filters?.department_id || '',
  priority: props.filters?.priority || '',
  status: props.filters?.status || '',
  sort_by: props.filters?.sort_by || 'created_at',
  sort_order: props.filters?.sort_order || 'desc',
})

// Methods
const resetForm = () => {
  form.id = null
  form.title = ''
  form.department_id = ''
  form.priority = 'medium'
  form.description = ''
  form.is_active = true
  errors.value = {}
}

const closeModals = () => {
  showCreateModal.value = false
  showEditModal.value = false
  resetForm()
}

const editKra = (kra) => {
  form.id = kra.id
  form.title = kra.title
  form.department_id = kra.department_id
  form.priority = kra.priority
  form.description = kra.description || ''
  form.is_active = kra.is_active
  showEditModal.value = true
}

const submitForm = async () => {
  processing.value = true
  errors.value = {}

  const formData = {
    title: form.title,
    department_id: form.department_id,
    priority: form.priority,
    description: form.description,
    is_active: form.is_active,
  }

  if (showCreateModal.value) {
    // Creating new KRA
    router.post(route('admin.kras.store'), formData, {
      onSuccess: () => {
        closeModals()
      },
      onError: (serverErrors) => {
        errors.value = serverErrors
      },
      onFinish: () => {
        processing.value = false
      },
    })
  } else {
    // Updating existing KRA - using POST with method spoofing like your other routes
    router.post(route('admin.kras.update', form.id), formData, {
      onSuccess: () => {
        closeModals()
      },
      onError: (serverErrors) => {
        errors.value = serverErrors
      },
      onFinish: () => {
        processing.value = false
      },
    })
  }
}

const toggleStatus = (kra) => {
  router.post(route('admin.kras.toggle-status', kra.id), {}, {
    preserveScroll: true,
  })
}

const deleteKra = (kra) => {
  if (confirm(`Are you sure you want to delete "${kra.title}"? This action cannot be undone.`)) {
    router.post(route('admin.kras.destroy', kra.id), {}, {
      preserveScroll: true,
    })
  }
}

const applyFilters = () => {
  router.get(route('admin.kras.index'), filters, {
    preserveState: true,
    preserveScroll: true,
  })
}

const debouncedFilter = debounce(() => {
  applyFilters()
}, 300)

const formatDate = (dateString) => {
  const options = { 
    year: 'numeric', 
    month: 'short', 
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  }
  return new Date(dateString).toLocaleDateString('en-US', options)
}
</script>

<style scoped>
/* Main Layout */
.kra-management {
  padding: 2rem;
  min-height: 100vh;
  background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
}

/* Header */
.page-header {
  margin-bottom: 2rem;
}

.header-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: white;
  padding: 2rem;
  border-radius: 16px;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.page-title {
  font-size: 2rem;
  font-weight: 700;
  color: #1f2937;
  margin-bottom: 0.5rem;
}

.page-description {
  color: #6b7280;
  font-size: 1.1rem;
}

/* Statistics Cards */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.stat-card {
  background: white;
  padding: 1.5rem;
  border-radius: 12px;
  display: flex;
  align-items: center;
  gap: 1rem;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
  transition: transform 0.2s ease;
}

.stat-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.stat-icon {
  width: 3rem;
  height: 3rem;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.stat-label {
  color: #6b7280;
  font-size: 0.875rem;
  margin-bottom: 0.25rem;
}

.stat-value {
  color: #1f2937;
  font-size: 1.875rem;
  font-weight: 700;
}

/* Filters */
.filters-section {
  background: white;
  padding: 1.5rem;
  border-radius: 12px;
  margin-bottom: 2rem;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.filters-row {
  display: grid;
  grid-template-columns: 2fr 1fr 1fr 1fr;
  gap: 1rem;
  align-items: end;
}

.search-input {
  position: relative;
}

.search-icon {
  position: absolute;
  left: 1rem;
  top: 50%;
  transform: translateY(-50%);
  width: 1.25rem;
  height: 1.25rem;
  color: #9ca3af;
}

.search-input input {
  width: 100%;
  padding: 0.75rem 1rem 0.75rem 3rem;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 0.875rem;
  transition: border-color 0.2s ease;
}

.search-input input:focus {
  outline: none;
  border-color: #8b5cf6;
  box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
}

.filter-select {
  padding: 0.75rem;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 0.875rem;
  background: white;
  cursor: pointer;
  transition: border-color 0.2s ease;
}

.filter-select:focus {
  outline: none;
  border-color: #8b5cf6;
  box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
}

/* KRA Grid */
.kras-list {
  margin-bottom: 2rem;
}

.kra-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
  gap: 1.5rem;
}

.kra-card {
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
  transition: all 0.3s ease;
  border: 1px solid #e5e7eb;
}

.kra-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
  border-color: #8b5cf6;
}

.kra-card.inactive {
  opacity: 0.6;
  background: #f9fafb;
}

.kra-header {
  display: flex;
  justify-content: space-between;
  align-items: start;
  margin-bottom: 1rem;
}

.kra-title {
  font-size: 1.25rem;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 0.5rem;
  line-height: 1.4;
}

.kra-meta {
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
}

.department-badge {
  background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%);
  color: #4338ca;
  padding: 0.25rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 500;
}

.priority-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
}

.priority-high {
  background: #fee2e2;
  color: #991b1b;
}

.priority-medium {
  background: #fef3c7;
  color: #92400e;
}

.priority-low {
  background: #d1fae5;
  color: #065f46;
}

.kra-actions {
  display: flex;
  gap: 0.5rem;
}

.action-btn {
  width: 2rem;
  height: 2rem;
  border-radius: 6px;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 1px solid #e5e7eb;
  background: white;
  cursor: pointer;
  transition: all 0.2s ease;
}

.action-btn:hover {
  background: #f3f4f6;
}

.action-btn.edit {
  color: #2563eb;
  border-color: #bfdbfe;
}

.action-btn.edit:hover {
  background: #eff6ff;
}

.action-btn.delete {
  color: #dc2626;
  border-color: #fecaca;
}

.action-btn.delete:hover {
  background: #fef2f2;
}

.action-btn.activate {
  color: #059669;
  border-color: #a7f3d0;
}

.action-btn.activate:hover {
  background: #ecfdf5;
}

.action-btn.deactivate {
  color: #d97706;
  border-color: #fed7aa;
}

.action-btn.deactivate:hover {
  background: #fffbeb;
}

.kra-description {
  margin-bottom: 1rem;
  color: #4b5563;
  line-height: 1.6;
}

.kra-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-top: 1rem;
  border-top: 1px solid #f3f4f6;
  font-size: 0.875rem;
  color: #6b7280;
}

/* Empty State */
.empty-state {
  text-align: center;
  padding: 4rem 2rem;
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

/* Buttons */
.btn-primary {
  background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
  color: white;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-weight: 500;
  border: none;
  cursor: pointer;
  display: flex;
  align-items: center;
  transition: all 0.2s ease;
  box-shadow: 0 2px 4px rgba(139, 92, 246, 0.2);
}

.btn-primary:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
}

.btn-primary:disabled {
  opacity: 0.5;
  cursor: not-allowed;
  transform: none;
}

.btn-secondary {
  background: white;
  color: #4b5563;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-weight: 500;
  border: 1px solid #d1d5db;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-secondary:hover {
  background: #f9fafb;
  border-color: #9ca3af;
}

/* Modal Styles */
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

.modal-content {
  background: white;
  border-radius: 16px;
  width: 100%;
  max-width: 500px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem;
  border-bottom: 1px solid #e5e7eb;
}

.modal-title {
  font-size: 1.5rem;
  font-weight: 600;
  color: #1f2937;
}

.modal-close {
  width: 2.5rem;
  height: 2.5rem;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #6b7280;
  background: none;
  border: none;
  cursor: pointer;
  transition: all 0.2s ease;
}

.modal-close:hover {
  background: #f3f4f6;
  color: #1f2937;
}

.modal-form {
  padding: 1.5rem;
}

.modal-footer {
  display: flex;
  gap: 1rem;
  justify-content: end;
  padding-top: 1.5rem;
  border-top: 1px solid #e5e7eb;
}

/* Form Styles */
.form-group {
  margin-bottom: 1.5rem;
}

.form-label {
  display: block;
  font-size: 0.875rem;
  font-weight: 500;
  color: #374151;
  margin-bottom: 0.5rem;
}

.form-input, .form-select, .form-textarea {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 0.875rem;
  transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.form-input:focus, .form-select:focus, .form-textarea:focus {
  outline: none;
  border-color: #8b5cf6;
  box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
}

.form-input.error, .form-select.error, .form-textarea.error {
  border-color: #dc2626;
}

.form-textarea {
  resize: vertical;
  min-height: 100px;
}

.checkbox-label {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  cursor: pointer;
  font-size: 0.875rem;
  color: #374151;
}

.form-checkbox {
  width: 1.25rem;
  height: 1.25rem;
  accent-color: #8b5cf6;
}

.error-text {
  display: block;
  color: #dc2626;
  font-size: 0.75rem;
  margin-top: 0.25rem;
}

/* Pagination */
.pagination-wrapper {
  display: flex;
  justify-content: center;
  padding-top: 2rem;
}

.pagination {
  display: flex;
  gap: 0.25rem;
}

.pagination-link {
  padding: 0.5rem 0.75rem;
  border: 1px solid #e5e7eb;
  background: white;
  color: #6b7280;
  text-decoration: none;
  border-radius: 6px;
  font-size: 0.875rem;
  transition: all 0.2s ease;
}

.pagination-link:hover:not(.disabled) {
  background: #f3f4f6;
  border-color: #d1d5db;
  color: #374151;
}

.pagination-link.active {
  background: #8b5cf6;
  border-color: #8b5cf6;
  color: white;
}

.pagination-link.disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

/* Responsive Design */
@media (max-width: 768px) {
  .kra-management {
    padding: 1rem;
  }
  
  .header-content {
    flex-direction: column;
    gap: 1rem;
    text-align: center;
  }
  
  .filters-row {
    grid-template-columns: 1fr;
  }
  
  .kra-grid {
    grid-template-columns: 1fr;
  }
  
  .stats-grid {
    grid-template-columns: 1fr;
  }
  
  .modal-content {
    margin: 1rem;
    max-width: none;
  }
}
</style>