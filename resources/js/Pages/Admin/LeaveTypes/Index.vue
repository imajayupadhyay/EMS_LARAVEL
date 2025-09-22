<template>
  <AdminLayout>
    <Head title="Leave Types Management" />
    
    <!-- Header Section -->
    <div class="page-header">
      <div class="header-content">
        <div class="title-section">
          <div class="icon-wrapper">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
              <line x1="16" y1="2" x2="16" y2="6"/>
              <line x1="8" y1="2" x2="8" y2="6"/>
              <line x1="3" y1="10" x2="21" y2="10"/>
              <path d="M9 14h6"/>
            </svg>
          </div>
          <div class="title-content">
            <h1 class="page-title">Leave Types</h1>
            <p class="page-subtitle">Configure leave types and their allowed days</p>
          </div>
        </div>
        <div class="header-actions">
          <button @click="showAddModal = true" class="action-btn primary">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="10"/>
              <line x1="12" y1="8" x2="12" y2="16"/>
              <line x1="8" y1="12" x2="16" y2="12"/>
            </svg>
            Add Leave Type
          </button>
        </div>
      </div>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-grid">
      <div class="stat-card primary">
        <div class="stat-icon">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
            <line x1="16" y1="2" x2="16" y2="6"/>
            <line x1="8" y1="2" x2="8" y2="6"/>
            <line x1="3" y1="10" x2="21" y2="10"/>
          </svg>
        </div>
        <div class="stat-content">
          <div class="stat-value">{{ statistics.total_types }}</div>
          <div class="stat-label">Total Leave Types</div>
        </div>
      </div>

      <div class="stat-card success">
        <div class="stat-icon">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
            <polyline points="22,4 12,14.01 9,11.01"/>
          </svg>
        </div>
        <div class="stat-content">
          <div class="stat-value">{{ statistics.total_days }}</div>
          <div class="stat-label">Total Allowed Days</div>
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
          <div class="stat-value">{{ statistics.avg_days }}</div>
          <div class="stat-label">Average Days</div>
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
          <div class="stat-value">{{ statistics.max_days }}</div>
          <div class="stat-label">Maximum Days</div>
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
            Search Leave Types
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
            <option value="name">Name (A-Z)</option>
            <option value="name_desc">Name (Z-A)</option>
            <option value="allowed_days">Allowed Days (Low-High)</option>
            <option value="allowed_days_desc">Allowed Days (High-Low)</option>
            <option value="created_at">Recently Added</option>
          </select>
        </div>

        <div class="filter-group">
          <label class="filter-label">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polygon points="22,3 2,3 10,12.46 10,19 14,21 14,12.46"/>
            </svg>
            Filter by Days
          </label>
          <select v-model="daysFilter" @change="applyFilters" class="filter-input">
            <option value="">All Types</option>
            <option value="low">Low (1-10 days)</option>
            <option value="medium">Medium (11-20 days)</option>
            <option value="high">High (20+ days)</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Leave Types Grid -->
    <div class="types-grid">
      <div v-for="type in filteredTypes" :key="type.id" class="type-card">
        <div class="card-header">
          <div class="type-info">
            <h3 class="type-name">{{ type.name }}</h3>
            <div class="type-meta">
              <span class="days-badge" :class="getDaysBadgeClass(type.allowed_days)">
                {{ type.allowed_days }} {{ type.allowed_days === 1 ? 'day' : 'days' }}
              </span>
            </div>
          </div>
          <div class="card-actions">
            <button @click="editType(type)" class="action-btn-small edit">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
              </svg>
            </button>
            <button @click="deleteType(type)" class="action-btn-small delete">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="3,6 5,6 21,6"/>
                <path d="M19,6V20a2,2,0,0,1-2,2H7a2,2,0,0,1-2-2V6M8,6V4a2,2,0,0,1,2-2h4a2,2,0,0,1,2,2V6"/>
              </svg>
            </button>
          </div>
        </div>

        <div class="card-body">
          <div class="progress-section">
            <div class="progress-label">
              <span class="progress-text">Usage Capacity</span>
              <span class="progress-value">{{ type.allowed_days }}/365 days</span>
            </div>
            <div class="progress-bar">
              <div 
                class="progress-fill" 
                :style="{ width: Math.min((type.allowed_days / 365) * 100, 100) + '%' }"
                :class="getProgressClass(type.allowed_days)"
              ></div>
            </div>
          </div>

          <div class="card-footer">
            <div class="type-stats">
              <div class="stat-item">
                <span class="stat-label">Created</span>
                <span class="stat-value">{{ formatDate(type.created_at) }}</span>
              </div>
              <div class="stat-item">
                <span class="stat-label">% of Year</span>
                <span class="stat-value">{{ Math.round((type.allowed_days / 365) * 100) }}%</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="filteredTypes.length === 0" class="empty-state">
        <div class="empty-icon">
          <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
            <line x1="16" y1="2" x2="16" y2="6"/>
            <line x1="8" y1="2" x2="8" y2="6"/>
            <line x1="3" y1="10" x2="21" y2="10"/>
          </svg>
        </div>
        <h3 class="empty-title">No Leave Types Found</h3>
        <p class="empty-description">
          {{ hasActiveSearch ? 'No leave types match your current search or filters.' : 'Start by creating your first leave type.' }}
        </p>
        <button v-if="!hasActiveSearch" @click="showAddModal = true" class="empty-action-btn">
          Add First Leave Type
        </button>
      </div>
    </div>

    <!-- Pagination -->
    <div v-if="leaveTypes.data && leaveTypes.data.length > 0" class="pagination-wrapper">
      <div class="pagination-info">
        Showing {{ leaveTypes.from || 1 }} to {{ leaveTypes.to || leaveTypes.data.length }} of {{ leaveTypes.total || leaveTypes.data.length }} results
      </div>
      <div class="pagination-controls" v-if="leaveTypes.last_page > 1">
        <button
          @click="goToPage(leaveTypes.current_page - 1)"
          :disabled="!leaveTypes.prev_page_url"
          class="pagination-btn"
        >
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="15,18 9,12 15,6"/>
          </svg>
          Previous
        </button>
        
        <div class="page-numbers">
          <button
            v-for="page in getPageNumbers()"
            :key="page"
            @click="goToPage(page)"
            :class="['page-btn', { 'active': page === leaveTypes.current_page }]"
          >
            {{ page }}
          </button>
        </div>

        <button
          @click="goToPage(leaveTypes.current_page + 1)"
          :disabled="!leaveTypes.next_page_url"
          class="pagination-btn"
        >
          Next
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="9,18 15,12 9,6"/>
          </svg>
        </button>
      </div>
    </div>

    <!-- Add/Edit Modal -->
    <Transition name="modal">
      <div v-if="showAddModal" class="modal-overlay" @click="closeModal">
        <div class="modal-container" @click.stop>
          <div class="modal-header">
            <h3 class="modal-title">
              {{ editingType ? 'Edit Leave Type' : 'Add New Leave Type' }}
            </h3>
            <button @click="closeModal" class="modal-close-btn">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18"/>
                <line x1="6" y1="6" x2="18" y2="18"/>
              </svg>
            </button>
          </div>

          <form @submit.prevent="submit" class="modal-form">
            <div class="form-grid">
              <div class="form-group">
                <label class="form-label required">Leave Type Name</label>
                <input
                  type="text"
                  v-model="form.name"
                  class="form-input"
                  placeholder="e.g., Annual Leave, Sick Leave"
                  required
                />
                <span v-if="form.errors.name" class="form-error">
                  {{ form.errors.name }}
                </span>
              </div>

              <div class="form-group">
                <label class="form-label required">Allowed Days</label>
                <input
                  type="number"
                  v-model="form.allowed_days"
                  class="form-input"
                  placeholder="Enter number of days"
                  min="1"
                  max="365"
                  required
                />
                <span v-if="form.errors.allowed_days" class="form-error">
                  {{ form.errors.allowed_days }}
                </span>
                <div class="form-help">
                  Maximum 365 days per year
                </div>
              </div>
            </div>

            <!-- Preview Section -->
            <div v-if="form.name && form.allowed_days" class="preview-section">
              <h4 class="preview-title">Preview</h4>
              <div class="preview-card">
                <div class="preview-header">
                  <span class="preview-name">{{ form.name }}</span>
                  <span class="preview-badge" :class="getDaysBadgeClass(form.allowed_days)">
                    {{ form.allowed_days }} {{ form.allowed_days == 1 ? 'day' : 'days' }}
                  </span>
                </div>
                <div class="preview-progress">
                  <div class="preview-progress-bar">
                    <div 
                      class="preview-progress-fill"
                      :style="{ width: Math.min((form.allowed_days / 365) * 100, 100) + '%' }"
                      :class="getProgressClass(form.allowed_days)"
                    ></div>
                  </div>
                  <span class="preview-percentage">{{ Math.round((form.allowed_days / 365) * 100) }}% of year</span>
                </div>
              </div>
            </div>

            <div class="modal-actions">
              <button type="button" @click="closeModal" class="action-btn secondary">
                Cancel
              </button>
              <button type="submit" :disabled="form.processing" class="action-btn primary">
                {{ form.processing ? 'Saving...' : (editingType ? 'Update Leave Type' : 'Create Leave Type') }}
              </button>
            </div>
          </form>
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
  leaveTypes: Object,
  flash: Object,
})

// Reactive state
const showAddModal = ref(false)
const editingType = ref(null)
const searchQuery = ref('')
const sortBy = ref('name')
const daysFilter = ref('')
const showToast = ref(false)
const toastMessage = ref('')

// Form
const form = useForm({
  name: '',
  allowed_days: '',
})

// Computed properties
const statistics = computed(() => {
  const types = props.leaveTypes?.data || []
  const totalDays = types.reduce((sum, type) => sum + parseInt(type.allowed_days), 0)
  
  return {
    total_types: types.length,
    total_days: totalDays,
    avg_days: types.length > 0 ? Math.round(totalDays / types.length) : 0,
    max_days: types.length > 0 ? Math.max(...types.map(t => parseInt(t.allowed_days))) : 0,
  }
})

const filteredTypes = computed(() => {
  let types = [...(props.leaveTypes?.data || [])]

  // Search filter
  if (searchQuery.value) {
    types = types.filter(type =>
      type.name.toLowerCase().includes(searchQuery.value.toLowerCase())
    )
  }

  // Days filter
  if (daysFilter.value) {
    types = types.filter(type => {
      const days = parseInt(type.allowed_days)
      switch (daysFilter.value) {
        case 'low': return days <= 10
        case 'medium': return days > 10 && days <= 20
        case 'high': return days > 20
        default: return true
      }
    })
  }

  // Sorting
  types.sort((a, b) => {
    switch (sortBy.value) {
      case 'name':
        return a.name.localeCompare(b.name)
      case 'name_desc':
        return b.name.localeCompare(a.name)
      case 'allowed_days':
        return parseInt(a.allowed_days) - parseInt(b.allowed_days)
      case 'allowed_days_desc':
        return parseInt(b.allowed_days) - parseInt(a.allowed_days)
      case 'created_at':
        return new Date(b.created_at) - new Date(a.created_at)
      default:
        return 0
    }
  })

  return types
})

const hasActiveSearch = computed(() => {
  return searchQuery.value || daysFilter.value
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
  daysFilter.value = ''
  sortBy.value = 'name'
}

function applySorting() {
  // Sorting is reactive via computed property
}

function applyFilters() {
  // Filtering is reactive via computed property
}

function getDaysBadgeClass(days) {
  const numDays = parseInt(days)
  if (numDays <= 10) return 'days-low'
  if (numDays <= 20) return 'days-medium'
  return 'days-high'
}

function getProgressClass(days) {
  const numDays = parseInt(days)
  if (numDays <= 10) return 'progress-low'
  if (numDays <= 20) return 'progress-medium'
  return 'progress-high'
}

function formatDate(dateString) {
  if (!dateString) return 'N/A'
  return new Date(dateString).toLocaleDateString()
}

function editType(type) {
  editingType.value = type
  form.name = type.name
  form.allowed_days = type.allowed_days
  showAddModal.value = true
}

function closeModal() {
  showAddModal.value = false
  editingType.value = null
  form.reset()
  form.clearErrors()
}

function submit() {
  if (editingType.value) {
    form.post(route('admin.leave-types.update', editingType.value.id), {
      preserveScroll: true,
      onSuccess: () => {
        closeModal()
        showToastMessage('Leave type updated successfully!')
      },
    })
  } else {
    form.post(route('admin.leave-types.store'), {
      preserveScroll: true,
      onSuccess: () => {
        closeModal()
        showToastMessage('Leave type created successfully!')
      },
    })
  }
}

function deleteType(type) {
  if (confirm(`Delete "${type.name}" leave type? This action cannot be undone.`)) {
    router.post(route('admin.leave-types.destroy', type.id), {}, {
      preserveScroll: true,
      onSuccess: () => {
        showToastMessage('Leave type deleted successfully!')
      }
    })
  }
}

function goToPage(page) {
  if (page < 1 || page > props.leaveTypes?.last_page) return
  
  router.get(route('admin.leave-types.index'), { page }, {
    preserveState: true,
    preserveScroll: true,
  })
}

function getPageNumbers() {
  const current = props.leaveTypes?.current_page || 1
  const last = props.leaveTypes?.last_page || 1
  const pages = []
  
  // Always show first page
  if (current > 3) pages.push(1)
  
  // Show pages around current
  for (let i = Math.max(1, current - 2); i <= Math.min(last, current + 2); i++) {
    pages.push(i)
  }
  
  // Always show last page
  if (current < last - 2) pages.push(last)
  
  return [...new Set(pages)].sort((a, b) => a - b)
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

/* Types Grid */
.types-grid {
  @apply grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6;
}

.type-card {
  @apply bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow;
}

.card-header {
  @apply p-6 border-b border-gray-100 flex items-start justify-between;
}

.type-info {
  @apply flex-1;
}

.type-name {
  @apply text-lg font-semibold text-gray-900 mb-2;
}

.type-meta {
  @apply flex items-center gap-2;
}

.days-badge {
  @apply inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium;
}

.days-badge.days-low {
  @apply bg-green-100 text-green-800;
}

.days-badge.days-medium {
  @apply bg-yellow-100 text-yellow-800;
}

.days-badge.days-high {
  @apply bg-red-100 text-red-800;
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

.progress-section {
  @apply mb-4;
}

.progress-label {
  @apply flex items-center justify-between mb-2;
}

.progress-text {
  @apply text-sm font-medium text-gray-700;
}

.progress-value {
  @apply text-sm text-gray-500;
}

.progress-bar {
  @apply w-full bg-gray-200 rounded-full h-2;
}

.progress-fill {
  @apply h-2 rounded-full transition-all duration-300;
}

.progress-fill.progress-low {
  @apply bg-gradient-to-r from-green-400 to-green-500;
}

.progress-fill.progress-medium {
  @apply bg-gradient-to-r from-yellow-400 to-yellow-500;
}

.progress-fill.progress-high {
  @apply bg-gradient-to-r from-red-400 to-red-500;
}

.card-footer {
  @apply pt-4 border-t border-gray-100;
}

.type-stats {
  @apply grid grid-cols-2 gap-4;
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

/* Pagination */
.pagination-wrapper {
  @apply flex items-center justify-between px-6 py-4 bg-white rounded-xl shadow-sm border border-gray-200;
}

.pagination-info {
  @apply text-sm text-gray-700;
}

.pagination-controls {
  @apply flex items-center gap-2;
}

.pagination-btn {
  @apply inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors;
}

.page-numbers {
  @apply flex items-center gap-1;
}

.page-btn {
  @apply w-8 h-8 flex items-center justify-center text-sm font-medium text-gray-700 rounded-md hover:bg-gray-100 transition-colors;
}

.page-btn.active {
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

.preview-progress {
  @apply space-y-2;
}

.preview-progress-bar {
  @apply w-full bg-gray-200 rounded-full h-2;
}

.preview-progress-fill {
  @apply h-2 rounded-full transition-all duration-300;
}

.preview-progress-fill.progress-low {
  @apply bg-gradient-to-r from-green-400 to-green-500;
}

.preview-progress-fill.progress-medium {
  @apply bg-gradient-to-r from-yellow-400 to-yellow-500;
}

.preview-progress-fill.progress-high {
  @apply bg-gradient-to-r from-red-400 to-red-500;
}

.preview-percentage {
  @apply text-sm text-gray-600;
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

  .types-grid {
    @apply grid-cols-1;
  }

  .modal-container {
    @apply mx-2 max-w-none;
  }

  .form-grid {
    @apply grid-cols-1;
  }

  .pagination-wrapper {
    @apply flex-col gap-4 items-start;
  }

  .type-stats {
    @apply grid-cols-1;
  }
}
</style>