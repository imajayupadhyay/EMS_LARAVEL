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
}

.metric-icon.total {
  background: linear-gradient(135deg, #ddd6fe 0%, #c4b5fd 100%);
  color: #5b21b6;
}

.metric-icon.high {
  background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
  color: #991b1b;
}

.metric-icon.medium {
  background: linear-gradient(135deg, #fed7aa 0%, #fdba74 100%);
  color: #9a3412;
}

.metric-icon.low {
  background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
  color: #065f46;
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

/* KRA Cards Grid */
.kra-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.kra-card {
  background: white;
  border-radius: 16px;
  padding: 1.5rem;
  border: 1px solid #f3f4f6;
  transition: all 0.3s ease;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.kra-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 10px 25px rgba(139, 92, 246, 0.15);
  border-color: #e9d5ff;
}

.kra-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  margin-bottom: 1rem;
  gap: 1rem;
}

.kra-title {
  font-size: 1.125rem;
  font-weight: 600;
  color: #1f2937;
  margin: 0;
  flex: 1;
}

.priority-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
  flex-shrink: 0;
}

.priority-badge.high {
  background: #fee2e2;
  color: #991b1b;
}

.priority-badge.medium {
  background: #fed7aa;
  color: #9a3412;
}

.priority-badge.low {
  background: #d1fae5;
  color: #065f46;
}

.kra-description {
  font-size: 0.9375rem;
  color: #6b7280;
  line-height: 1.6;
  margin-bottom: 1rem;
}

.kra-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding-top: 1rem;
  border-top: 1px solid #f3f4f6;
  font-size: 0.875rem;
  color: #6b7280;
}

/* Empty State */
.empty-state {
  background: white;
  border-radius: 16px;
  padding: 4rem 2rem;
  text-align: center;
  border: 1px solid #f3f4f6;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.empty-state svg {
  margin: 0 auto 1.5rem;
  color: #d1d5db;
}

.empty-state h3 {
  font-size: 1.125rem;
  font-weight: 600;
  color: #1f2937;
  margin: 0 0 0.5rem;
}

.empty-state p {
  font-size: 0.875rem;
  color: #6b7280;
  margin: 0;
}

/* Pagination */
.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 0.5rem;
  margin-top: 2rem;
}

.pagination button {
  padding: 0.5rem 1rem;
  border: 1px solid #d1d5db;
  background: white;
  color: #374151;
  border-radius: 8px;
  font-size: 0.875rem;
  cursor: pointer;
  transition: all 0.2s;
}

.pagination button:hover:not(:disabled) {
  background: #f3f4f6;
  border-color: #9ca3af;
}

.pagination button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.pagination button.active {
  background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
  color: white;
  border-color: #8b5cf6;
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

  .kra-grid {
    grid-template-columns: 1fr;
  }
}
</style>

<script setup>
import EmployeeLayout from '@/Layouts/EmployeeLayout.vue'
import { reactive, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'

const props = defineProps({
  kras: { type: Object, required: true },
  statistics: { type: Object, required: true },
  filters: { type: Object, default: () => ({}) },
  departmentName: { type: String, default: 'Your Department' }
})

const filters = reactive({
  search: props.filters.search || '',
  priority: props.filters.priority || '',
  sort_by: props.filters.sort_by || 'priority',
  sort_order: props.filters.sort_order || 'asc'
})

// Watch filters
watch(filters, () => {
  router.get(route('employee.kra.index'), filters, {
    preserveState: true,
    replace: true
  })
}, { deep: true })

// Clear filters
const clearFilters = () => {
  filters.search = ''
  filters.priority = ''
  filters.sort_by = 'priority'
  filters.sort_order = 'asc'
}

// Format date
const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}
</script>

<template>
  <EmployeeLayout>
    <div class="page-container">
      <!-- Page Header -->
      <div class="page-header">
        <div class="header-left">
          <div class="icon-wrapper">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
            </svg>
          </div>
          <div>
            <h1 class="page-title">Key Result Areas (KRA)</h1>
            <p class="page-subtitle">{{ departmentName }} - Performance objectives and goals</p>
          </div>
        </div>
      </div>

      <!-- Metrics Cards -->
      <div class="metrics-grid">
        <div class="metric-card">
          <div class="metric-icon total">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
          </div>
          <div class="metric-content">
            <div class="metric-value">{{ statistics.total }}</div>
            <div class="metric-label">Total KRAs</div>
          </div>
        </div>

        <div class="metric-card">
          <div class="metric-icon high">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="10"/>
              <line x1="12" y1="8" x2="12" y2="12"/>
              <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
          </div>
          <div class="metric-content">
            <div class="metric-value">{{ statistics.high_priority }}</div>
            <div class="metric-label">High Priority</div>
          </div>
        </div>

        <div class="metric-card">
          <div class="metric-icon medium">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
          </div>
          <div class="metric-content">
            <div class="metric-value">{{ statistics.medium_priority }}</div>
            <div class="metric-label">Medium Priority</div>
          </div>
        </div>

        <div class="metric-card">
          <div class="metric-icon low">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="20 6 9 17 4 12"/>
            </svg>
          </div>
          <div class="metric-content">
            <div class="metric-value">{{ statistics.low_priority }}</div>
            <div class="metric-label">Low Priority</div>
          </div>
        </div>
      </div>

      <!-- Filters Card -->
      <div class="filters-card">
        <div class="filters-grid">
          <div class="filter-group">
            <label class="filter-label">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"/>
                <path d="m21 21-4.35-4.35"/>
              </svg>
              Search
            </label>
            <input
              type="text"
              v-model="filters.search"
              placeholder="Search KRAs..."
              class="filter-input"
            />
          </div>

          <div class="filter-group">
            <label class="filter-label">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="8" y1="6" x2="21" y2="6"/>
                <line x1="8" y1="12" x2="21" y2="12"/>
                <line x1="8" y1="18" x2="21" y2="18"/>
                <line x1="3" y1="6" x2="3.01" y2="6"/>
                <line x1="3" y1="12" x2="3.01" y2="12"/>
                <line x1="3" y1="18" x2="3.01" y2="18"/>
              </svg>
              Priority
            </label>
            <select v-model="filters.priority" class="filter-input">
              <option value="">All Priorities</option>
              <option value="high">High</option>
              <option value="medium">Medium</option>
              <option value="low">Low</option>
            </select>
          </div>

          <div class="filter-group" style="display: flex; align-items: flex-end;">
            <button @click="clearFilters" class="filter-input" style="cursor: pointer; background: #f3f4f6; border-color: #d1d5db;">
              Clear Filters
            </button>
          </div>
        </div>
      </div>

      <!-- KRA Cards Grid -->
      <div v-if="kras.data && kras.data.length > 0" class="kra-grid">
        <div v-for="kra in kras.data" :key="kra.id" class="kra-card">
          <div class="kra-header">
            <h2 class="kra-title">{{ kra.title }}</h2>
            <span class="priority-badge" :class="kra.priority">
              {{ kra.priority }}
            </span>
          </div>

          <div class="kra-description">
            {{ kra.description }}
          </div>

          <div class="kra-footer">
            <div style="display: flex; align-items: center; gap: 0.5rem;">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                <line x1="16" y1="2" x2="16" y2="6"/>
                <line x1="8" y1="2" x2="8" y2="6"/>
                <line x1="3" y1="10" x2="21" y2="10"/>
              </svg>
              {{ formatDate(kra.created_at) }}
            </div>
            <div v-if="kra.creator" style="display: flex; align-items: center; gap: 0.5rem;">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                <circle cx="12" cy="7" r="4"/>
              </svg>
              {{ kra.creator.name }}
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="empty-state">
        <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
          <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
        <h3>No KRAs Found</h3>
        <p>{{ filters.search ? 'No KRAs match your search criteria.' : 'No KRAs are currently assigned to your department.' }}</p>
      </div>

      <!-- Pagination -->
      <div v-if="kras.data && kras.data.length > 0" class="pagination">
        <button
          v-for="link in kras.links"
          :key="link.label"
          @click="link.url && router.visit(link.url)"
          :disabled="!link.url || link.active"
          :class="{ active: link.active }"
          v-html="link.label"
        ></button>
      </div>
    </div>
  </EmployeeLayout>
</template>
