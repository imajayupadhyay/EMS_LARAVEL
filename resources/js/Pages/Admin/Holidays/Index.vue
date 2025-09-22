<template>
  <AdminLayout>
    <Head title="Holidays Management" />
    
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
              <path d="M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01M16 18h.01"/>
            </svg>
          </div>
          <div class="title-content">
            <h1 class="page-title">Holidays</h1>
            <p class="page-subtitle">Manage company holidays and observances</p>
          </div>
        </div>
        <div class="header-actions">
          <button @click="toggleView" class="action-btn secondary">
            <svg v-if="currentView === 'list'" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
              <line x1="16" y1="2" x2="16" y2="6"/>
              <line x1="8" y1="2" x2="8" y2="6"/>
              <line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
            <svg v-else width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <line x1="8" y1="6" x2="21" y2="6"/>
              <line x1="8" y1="12" x2="21" y2="12"/>
              <line x1="8" y1="18" x2="21" y2="18"/>
              <line x1="3" y1="6" x2="3.01" y2="6"/>
              <line x1="3" y1="12" x2="3.01" y2="12"/>
              <line x1="3" y1="18" x2="3.01" y2="18"/>
            </svg>
            {{ currentView === 'list' ? 'Calendar View' : 'List View' }}
          </button>
          <button @click="showAddModal = true" class="action-btn primary">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="10"/>
              <line x1="12" y1="8" x2="12" y2="16"/>
              <line x1="8" y1="12" x2="16" y2="12"/>
            </svg>
            Add Holiday
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
          <div class="stat-value">{{ statistics.total_holidays }}</div>
          <div class="stat-label">Total Holidays</div>
        </div>
      </div>

      <div class="stat-card success">
        <div class="stat-icon">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M3.85 8.62a4 4 0 0 1 4.78-4.77 4 4 0 0 1 6.74 0 4 4 0 0 1 4.78 4.78 4 4 0 0 1 0 6.74 4 4 0 0 1-4.77 4.78 4 4 0 0 1-6.75 0 4 4 0 0 1-4.78-4.77 4 4 0 0 1 0-6.76Z"/>
            <path d="m9 12 2 2 4-4"/>
          </svg>
        </div>
        <div class="stat-content">
          <div class="stat-value">{{ statistics.public_holidays }}</div>
          <div class="stat-label">Public Holidays</div>
        </div>
      </div>

      <div class="stat-card warning">
        <div class="stat-icon">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"/>
            <path d="M12 6v6l4 2"/>
          </svg>
        </div>
        <div class="stat-content">
          <div class="stat-value">{{ statistics.restricted_holidays }}</div>
          <div class="stat-label">Restricted Holidays</div>
        </div>
      </div>

      <div class="stat-card info">
        <div class="stat-icon">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M8 2v4"/>
            <path d="M16 2v4"/>
            <rect x="3" y="4" width="18" height="18" rx="2"/>
            <path d="M3 10h18"/>
          </svg>
        </div>
        <div class="stat-content">
          <div class="stat-value">{{ statistics.upcoming_holidays }}</div>
          <div class="stat-label">Upcoming</div>
        </div>
      </div>
    </div>

    <!-- Filters Section -->
    <div class="filters-card">
      <div class="filters-header">
        <h3 class="filters-title">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polygon points="22,3 2,3 10,12.46 10,19 14,21 14,12.46"/>
          </svg>
          Filters & Search
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
            Search Holidays
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
              <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
              <circle cx="12" cy="7" r="4"/>
            </svg>
            Holiday Type
          </label>
          <select v-model="filters.type" @change="applyFilters" class="filter-input">
            <option value="">All Types</option>
            <option value="public">Public Holidays</option>
            <option value="restricted">Restricted Holidays</option>
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
            Month
          </label>
          <select v-model="filters.month" @change="applyFilters" class="filter-input">
            <option value="">All Months</option>
            <option v-for="(month, index) in months" :key="index" :value="index + 1">
              {{ month }}
            </option>
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
            Year
          </label>
          <select v-model="filters.year" @change="applyFilters" class="filter-input">
            <option value="">All Years</option>
            <option v-for="year in availableYears" :key="year" :value="year">
              {{ year }}
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
            <option value="date_asc">Date (Earliest First)</option>
            <option value="date_desc">Date (Latest First)</option>
            <option value="name_asc">Name (A-Z)</option>
            <option value="name_desc">Name (Z-A)</option>
            <option value="type">Type</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Content View -->
    <div v-if="currentView === 'list'" class="list-view">
      <!-- Holidays Grid -->
      <div class="holidays-grid">
        <div v-for="holiday in filteredHolidays" :key="holiday.id" class="holiday-card" :class="getHolidayCardClass(holiday)">
          <div class="card-header">
            <div class="holiday-info">
              <h3 class="holiday-name">{{ holiday.name }}</h3>
              <div class="holiday-meta">
                <span class="date-display">{{ formatDate(holiday.date) }}</span>
                <span class="type-badge" :class="getTypeBadgeClass(holiday.type)">
                  {{ holiday.type.charAt(0).toUpperCase() + holiday.type.slice(1) }}
                </span>
              </div>
            </div>
            <div class="card-actions">
              <button @click="editHoliday(holiday)" class="action-btn-small edit">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                  <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                </svg>
              </button>
              <button @click="deleteHoliday(holiday)" class="action-btn-small delete">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <polyline points="3,6 5,6 21,6"/>
                  <path d="M19,6V20a2,2,0,0,1-2,2H7a2,2,0,0,1-2-2V6M8,6V4a2,2,0,0,1,2-2h4a2,2,0,0,1,2,2V6"/>
                </svg>
              </button>
            </div>
          </div>

          <div class="card-body">
            <div class="holiday-details">
              <div class="detail-item">
                <span class="detail-label">Days Until:</span>
                <span class="detail-value" :class="getDaysUntilClass(holiday.date)">
                  {{ getDaysUntil(holiday.date) }}
                </span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Day of Week:</span>
                <span class="detail-value">{{ getDayOfWeek(holiday.date) }}</span>
              </div>
            </div>

            <div class="holiday-status">
              <div class="status-indicator" :class="getStatusClass(holiday.date)">
                <div class="status-dot"></div>
                <span class="status-text">{{ getStatusText(holiday.date) }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="filteredHolidays.length === 0" class="empty-state">
          <div class="empty-icon">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
              <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
              <line x1="16" y1="2" x2="16" y2="6"/>
              <line x1="8" y1="2" x2="8" y2="6"/>
              <line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
          </div>
          <h3 class="empty-title">No Holidays Found</h3>
          <p class="empty-description">
            {{ hasActiveFilters ? 'No holidays match your current filters. Try adjusting your search criteria.' : 'Start by creating your first holiday.' }}
          </p>
          <button v-if="!hasActiveFilters" @click="showAddModal = true" class="empty-action-btn">
            Add First Holiday
          </button>
        </div>
      </div>
    </div>

    <!-- Calendar View -->
    <div v-else class="calendar-view">
      <div class="calendar-header">
        <button @click="previousMonth" class="calendar-nav-btn">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="15,18 9,12 15,6"/>
          </svg>
        </button>
        <h3 class="calendar-title">
          {{ months[calendarDate.getMonth()] }} {{ calendarDate.getFullYear() }}
        </h3>
        <button @click="nextMonth" class="calendar-nav-btn">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="9,18 15,12 9,6"/>
          </svg>
        </button>
      </div>

      <div class="calendar-grid">
        <div class="calendar-weekdays">
          <div v-for="day in weekdays" :key="day" class="weekday">{{ day }}</div>
        </div>
        <div class="calendar-days">
          <div
            v-for="day in calendarDays"
            :key="day.date"
            :class="['calendar-day', {
              'other-month': !day.isCurrentMonth,
              'today': day.isToday,
              'has-holiday': day.holidays.length > 0
            }]"
          >
            <div class="day-number">{{ day.number }}</div>
            <div v-if="day.holidays.length > 0" class="day-holidays">
              <div
                v-for="holiday in day.holidays"
                :key="holiday.id"
                class="calendar-holiday"
                :class="getTypeBadgeClass(holiday.type)"
                :title="holiday.name"
                @click="editHoliday(holiday)"
              >
                {{ holiday.name.length > 12 ? holiday.name.substring(0, 12) + '...' : holiday.name }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Pagination (for list view) -->
    <div v-if="currentView === 'list' && holidays.data && holidays.data.length > 0" class="pagination-wrapper">
      <div class="pagination-info">
        Showing {{ holidays.from || 1 }} to {{ holidays.to || holidays.data.length }} of {{ holidays.total || holidays.data.length }} results
      </div>
      <div class="pagination-controls" v-if="holidays.last_page > 1">
        <button
          @click="goToPage(holidays.current_page - 1)"
          :disabled="!holidays.prev_page_url"
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
            :class="['page-btn', { 'active': page === holidays.current_page }]"
          >
            {{ page }}
          </button>
        </div>

        <button
          @click="goToPage(holidays.current_page + 1)"
          :disabled="!holidays.next_page_url"
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
              {{ editingHoliday ? 'Edit Holiday' : 'Add New Holiday' }}
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
              <div class="form-group full-width">
                <label class="form-label required">Holiday Name</label>
                <input
                  type="text"
                  v-model="form.name"
                  class="form-input"
                  placeholder="e.g., New Year's Day, Independence Day"
                  required
                />
                <span v-if="form.errors.name" class="form-error">
                  {{ form.errors.name }}
                </span>
              </div>

              <div class="form-group">
                <label class="form-label required">Date</label>
                <input
                  type="date"
                  v-model="form.date"
                  class="form-input"
                  required
                />
                <span v-if="form.errors.date" class="form-error">
                  {{ form.errors.date }}
                </span>
              </div>

              <div class="form-group">
                <label class="form-label required">Type</label>
                <select v-model="form.type" class="form-input" required>
                  <option value="public">Public Holiday</option>
                  <option value="restricted">Restricted Holiday</option>
                </select>
                <span v-if="form.errors.type" class="form-error">
                  {{ form.errors.type }}
                </span>
                <div class="form-help">
                  Public holidays are observed by all employees. Restricted holidays are optional.
                </div>
              </div>
            </div>

            <!-- Preview Section -->
            <div v-if="form.name && form.date" class="preview-section">
              <h4 class="preview-title">Preview</h4>
              <div class="preview-card">
                <div class="preview-header">
                  <span class="preview-name">{{ form.name }}</span>
                  <span class="preview-badge" :class="getTypeBadgeClass(form.type)">
                    {{ form.type.charAt(0).toUpperCase() + form.type.slice(1) }}
                  </span>
                </div>
                <div class="preview-details">
                  <div class="preview-date">{{ formatDate(form.date) }}</div>
                  <div class="preview-day">{{ getDayOfWeek(form.date) }}</div>
                </div>
              </div>
            </div>

            <div class="modal-actions">
              <button type="button" @click="closeModal" class="action-btn secondary">
                Cancel
              </button>
              <button type="submit" :disabled="form.processing" class="action-btn primary">
                {{ form.processing ? 'Saving...' : (editingHoliday ? 'Update Holiday' : 'Create Holiday') }}
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
  holidays: Object,
  filters: Object,
  flash: Object,
})

// Reactive state
const showAddModal = ref(false)
const editingHoliday = ref(null)
const currentView = ref('list') // 'list' or 'calendar'
const searchQuery = ref('')
const showToast = ref(false)
const toastMessage = ref('')
const calendarDate = ref(new Date())

// Filters
const filters = ref({
  type: props.filters?.type || '',
  month: props.filters?.month || '',
  year: props.filters?.year || '',
  sort_by: props.filters?.sort_by || 'date_asc',
})

// Form
const form = useForm({
  name: '',
  date: '',
  type: 'public',
})

// Constants
const months = [
  'January', 'February', 'March', 'April', 'May', 'June',
  'July', 'August', 'September', 'October', 'November', 'December'
]

const weekdays = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']

// Computed properties
const statistics = computed(() => {
  const allHolidays = props.holidays?.data || []
  const today = new Date()
  const upcoming = allHolidays.filter(h => new Date(h.date) > today)
  
  return {
    total_holidays: allHolidays.length,
    public_holidays: allHolidays.filter(h => h.type === 'public').length,
    restricted_holidays: allHolidays.filter(h => h.type === 'restricted').length,
    upcoming_holidays: upcoming.length,
  }
})

const filteredHolidays = computed(() => {
  let holidays = [...(props.holidays?.data || [])]

  // Search filter
  if (searchQuery.value) {
    holidays = holidays.filter(holiday =>
      holiday.name.toLowerCase().includes(searchQuery.value.toLowerCase())
    )
  }

  // Type filter
  if (filters.value.type) {
    holidays = holidays.filter(holiday => holiday.type === filters.value.type)
  }

  // Month filter
  if (filters.value.month) {
    holidays = holidays.filter(holiday => {
      const holidayMonth = new Date(holiday.date).getMonth() + 1
      return holidayMonth === parseInt(filters.value.month)
    })
  }

  // Year filter
  if (filters.value.year) {
    holidays = holidays.filter(holiday => {
      const holidayYear = new Date(holiday.date).getFullYear()
      return holidayYear === parseInt(filters.value.year)
    })
  }

  // Sorting
  holidays.sort((a, b) => {
    switch (filters.value.sort_by) {
      case 'date_asc':
        return new Date(a.date) - new Date(b.date)
      case 'date_desc':
        return new Date(b.date) - new Date(a.date)
      case 'name_asc':
        return a.name.localeCompare(b.name)
      case 'name_desc':
        return b.name.localeCompare(a.name)
      case 'type':
        return a.type.localeCompare(b.type)
      default:
        return 0
    }
  })

  return holidays
})

const hasActiveFilters = computed(() => {
  return searchQuery.value || 
         filters.value.type || 
         filters.value.month || 
         filters.value.year
})

const availableYears = computed(() => {
  const currentYear = new Date().getFullYear()
  return [currentYear - 1, currentYear, currentYear + 1, currentYear + 2]
})

const calendarDays = computed(() => {
  const year = calendarDate.value.getFullYear()
  const month = calendarDate.value.getMonth()
  
  const firstDay = new Date(year, month, 1)
  const lastDay = new Date(year, month + 1, 0)
  const startDate = new Date(firstDay)
  startDate.setDate(startDate.getDate() - firstDay.getDay())
  
  const days = []
  const today = new Date()
  
  for (let i = 0; i < 42; i++) {
    const date = new Date(startDate)
    date.setDate(startDate.getDate() + i)
    
    const dateStr = date.toISOString().split('T')[0]
    const dayHolidays = (props.holidays?.data || []).filter(h => h.date === dateStr)
    
    days.push({
      date: dateStr,
      number: date.getDate(),
      isCurrentMonth: date.getMonth() === month,
      isToday: date.toDateString() === today.toDateString(),
      holidays: dayHolidays
    })
  }
  
  return days
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

function toggleView() {
  currentView.value = currentView.value === 'list' ? 'calendar' : 'list'
}

function clearFilters() {
  searchQuery.value = ''
  filters.value = {
    type: '',
    month: '',
    year: '',
    sort_by: 'date_asc',
  }
  applyFilters()
}

function applyFilters() {
  router.get(route('admin.holidays.index'), {
    ...filters.value,
    search: searchQuery.value
  }, {
    preserveState: true,
    preserveScroll: true,
  })
}

function getHolidayCardClass(holiday) {
  const today = new Date()
  const holidayDate = new Date(holiday.date)
  
  if (holidayDate.toDateString() === today.toDateString()) {
    return 'holiday-today'
  } else if (holidayDate < today) {
    return 'holiday-past'
  } else {
    return 'holiday-upcoming'
  }
}

function getTypeBadgeClass(type) {
  return type === 'public' ? 'type-public' : 'type-restricted'
}

function getDaysUntilClass(date) {
  const days = getDaysUntil(date, true)
  if (days < 0) return 'days-past'
  if (days === 0) return 'days-today'
  if (days <= 7) return 'days-soon'
  return 'days-future'
}

function getStatusClass(date) {
  const today = new Date()
  const holidayDate = new Date(date)
  
  if (holidayDate.toDateString() === today.toDateString()) {
    return 'status-today'
  } else if (holidayDate < today) {
    return 'status-past'
  } else {
    return 'status-upcoming'
  }
}

function getStatusText(date) {
  const today = new Date()
  const holidayDate = new Date(date)
  
  if (holidayDate.toDateString() === today.toDateString()) {
    return 'Today'
  } else if (holidayDate < today) {
    return 'Past'
  } else {
    return 'Upcoming'
  }
}

function getDaysUntil(date, returnNumber = false) {
  const today = new Date()
  const holidayDate = new Date(date)
  const diffTime = holidayDate - today
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))
  
  if (returnNumber) return diffDays
  
  if (diffDays < 0) {
    return `${Math.abs(diffDays)} days ago`
  } else if (diffDays === 0) {
    return 'Today'
  } else if (diffDays === 1) {
    return 'Tomorrow'
  } else {
    return `${diffDays} days`
  }
}

function getDayOfWeek(date) {
  const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']
  return days[new Date(date).getDay()]
}

function formatDate(dateString) {
  if (!dateString) return 'N/A'
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', { 
    weekday: 'short', 
    year: 'numeric', 
    month: 'short', 
    day: 'numeric' 
  })
}

function previousMonth() {
  const newDate = new Date(calendarDate.value)
  newDate.setMonth(newDate.getMonth() - 1)
  calendarDate.value = newDate
}

function nextMonth() {
  const newDate = new Date(calendarDate.value)
  newDate.setMonth(newDate.getMonth() + 1)
  calendarDate.value = newDate
}

function editHoliday(holiday) {
  editingHoliday.value = holiday
  form.name = holiday.name
  form.date = holiday.date
  form.type = holiday.type
  showAddModal.value = true
}

function closeModal() {
  showAddModal.value = false
  editingHoliday.value = null
  form.reset()
  form.clearErrors()
}

function submit() {
  if (editingHoliday.value) {
    form.post(route('admin.holidays.update', editingHoliday.value.id), {
      preserveScroll: true,
      onSuccess: () => {
        closeModal()
        showToastMessage('Holiday updated successfully!')
      },
    })
  } else {
    form.post(route('admin.holidays.store'), {
      preserveScroll: true,
      onSuccess: () => {
        closeModal()
        showToastMessage('Holiday created successfully!')
      },
    })
  }
}

function deleteHoliday(holiday) {
  if (confirm(`Delete "${holiday.name}" holiday? This action cannot be undone.`)) {
    router.post(route('admin.holidays.destroy', holiday.id), {}, {
      preserveScroll: true,
      onSuccess: () => {
        showToastMessage('Holiday deleted successfully!')
      }
    })
  }
}

function goToPage(page) {
  if (page < 1 || page > props.holidays?.last_page) return
  
  router.get(route('admin.holidays.index'), {
    ...filters.value,
    search: searchQuery.value,
    page: page
  }, {
    preserveState: true,
    preserveScroll: true,
  })
}

function getPageNumbers() {
  const current = props.holidays?.current_page || 1
  const last = props.holidays?.last_page || 1
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
  @apply grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4;
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

/* Holidays Grid */
.holidays-grid {
  @apply grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6;
}

.holiday-card {
  @apply bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden transition-all duration-200 hover:shadow-md;
}

.holiday-card.holiday-today {
  @apply border-green-300 bg-green-50;
}

.holiday-card.holiday-past {
  @apply border-gray-300 bg-gray-50;
}

.holiday-card.holiday-upcoming {
  @apply border-purple-300 bg-purple-50;
}

.card-header {
  @apply p-6 border-b border-gray-100 flex items-start justify-between;
}

.holiday-info {
  @apply flex-1;
}

.holiday-name {
  @apply text-lg font-semibold text-gray-900 mb-2;
}

.holiday-meta {
  @apply flex items-center gap-3;
}

.date-display {
  @apply text-sm text-gray-600;
}

.type-badge {
  @apply inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium;
}

.type-badge.type-public {
  @apply bg-green-100 text-green-800;
}

.type-badge.type-restricted {
  @apply bg-yellow-100 text-yellow-800;
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

.holiday-details {
  @apply grid grid-cols-2 gap-4 mb-4;
}

.detail-item {
  @apply flex flex-col;
}

.detail-label {
  @apply text-xs text-gray-500 uppercase tracking-wide;
}

.detail-value {
  @apply text-sm font-semibold text-gray-900 mt-1;
}

.detail-value.days-past {
  @apply text-gray-500;
}

.detail-value.days-today {
  @apply text-green-600;
}

.detail-value.days-soon {
  @apply text-yellow-600;
}

.detail-value.days-future {
  @apply text-purple-600;
}

.holiday-status {
  @apply pt-4 border-t border-gray-100;
}

.status-indicator {
  @apply flex items-center gap-2;
}

.status-dot {
  @apply w-2 h-2 rounded-full;
}

.status-indicator.status-today .status-dot {
  @apply bg-green-500;
}

.status-indicator.status-past .status-dot {
  @apply bg-gray-400;
}

.status-indicator.status-upcoming .status-dot {
  @apply bg-purple-500;
}

.status-text {
  @apply text-sm font-medium;
}

.status-indicator.status-today .status-text {
  @apply text-green-700;
}

.status-indicator.status-past .status-text {
  @apply text-gray-600;
}

.status-indicator.status-upcoming .status-text {
  @apply text-purple-700;
}

/* Calendar View */
.calendar-view {
  @apply bg-white rounded-xl shadow-sm border border-gray-200 p-6;
}

.calendar-header {
  @apply flex items-center justify-between mb-6;
}

.calendar-nav-btn {
  @apply p-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-600 transition-colors;
}

.calendar-title {
  @apply text-xl font-semibold text-gray-900;
}

.calendar-grid {
  @apply space-y-2;
}

.calendar-weekdays {
  @apply grid grid-cols-7 gap-2;
}

.weekday {
  @apply p-3 text-center text-sm font-medium text-gray-500 uppercase tracking-wide;
}

.calendar-days {
  @apply grid grid-cols-7 gap-2;
}

.calendar-day {
  @apply min-h-24 p-2 border border-gray-200 rounded-lg bg-gray-50 transition-colors;
}

.calendar-day.other-month {
  @apply bg-gray-100 text-gray-400;
}

.calendar-day.today {
  @apply border-purple-500 bg-purple-100;
}

.calendar-day.has-holiday {
  @apply bg-green-50 border-green-200;
}

.day-number {
  @apply text-sm font-medium text-gray-900 mb-1;
}

.calendar-day.other-month .day-number {
  @apply text-gray-400;
}

.day-holidays {
  @apply space-y-1;
}

.calendar-holiday {
  @apply text-xs px-2 py-1 rounded text-center cursor-pointer truncate;
}

.calendar-holiday.type-public {
  @apply bg-green-200 text-green-800 hover:bg-green-300;
}

.calendar-holiday.type-restricted {
  @apply bg-yellow-200 text-yellow-800 hover:bg-yellow-300;
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

.form-group.full-width {
  @apply md:col-span-2;
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
  @apply grid grid-cols-2 gap-4;
}

.preview-date {
  @apply text-sm text-gray-600;
}

.preview-day {
  @apply text-sm font-medium text-gray-900;
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

  .holidays-grid {
    @apply grid-cols-1;
  }

  .calendar-days {
    @apply gap-1;
  }

  .calendar-day {
    @apply min-h-20 p-1;
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

  .holiday-details {
    @apply grid-cols-1;
  }
}
</style>