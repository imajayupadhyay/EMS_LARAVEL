<template>
  <AdminLayout>
    <div class="page-container">
      <!-- Page Header -->
      <div class="page-header">
        <div class="header-left">
          <div class="icon-wrapper">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
              <circle cx="9" cy="7" r="4"/>
              <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
              <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
          </div>
          <div>
            <h1 class="page-title">Employee Management</h1>
            <p class="page-subtitle">Manage and view all employee records</p>
          </div>
        </div>
        <div class="header-stats">
          <div class="stat-card">
            <span class="stat-value">{{ employees?.length || 0 }}</span>
            <span class="stat-label">Total Employees</span>
          </div>
        </div>
      </div>

      <!-- Filters Section -->
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
              Search by Name
            </label>
            <input
              v-model="filters.name"
              @input="applyFilters"
              placeholder="Enter employee name..."
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
              <option v-for="d in departments" :key="d.id" :value="d.id">{{ d.name }}</option>
            </select>
          </div>

          <div class="filter-group">
            <label class="filter-label">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                <circle cx="12" cy="7" r="4"/>
              </svg>
              Designation
            </label>
            <select v-model="filters.designation_id" @change="applyFilters" class="filter-input">
              <option value="">All Designations</option>
              <option v-for="d in designations" :key="d.id" :value="d.id">{{ d.name }}</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Employee Cards Grid -->
      <div v-if="employees && employees.length > 0" class="employees-grid">
        <div v-for="emp in employees" :key="emp.id" class="employee-card">
          <!-- Card Header -->
          <div class="card-header">
            <div class="employee-avatar">
              {{ getInitials(emp.first_name, emp.last_name) }}
            </div>
            <div class="employee-info">
              <h3 class="employee-name">
                {{ emp.first_name }} {{ emp.middle_name }} {{ emp.last_name }}
              </h3>
              <p class="employee-email">{{ emp.email }}</p>
            </div>
            <div class="status-badge">Active</div>
          </div>

          <!-- Card Body -->
          <div class="card-body">
            <div class="info-row">
              <div class="info-item">
                <span class="info-icon">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="7" height="7"/>
                    <rect x="14" y="3" width="7" height="7"/>
                    <rect x="14" y="14" width="7" height="7"/>
                    <rect x="3" y="14" width="7" height="7"/>
                  </svg>
                </span>
                <div>
                  <span class="info-label">Department</span>
                  <span class="info-value">{{ emp.department?.name || 'N/A' }}</span>
                </div>
              </div>

              <div class="info-item">
                <span class="info-icon">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                  </svg>
                </span>
                <div>
                  <span class="info-label">Designation</span>
                  <span class="info-value">{{ emp.designation?.name || 'N/A' }}</span>
                </div>
              </div>
            </div>

            <div class="info-row">
              <div class="info-item">
                <span class="info-icon">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12 6 12 12 16 14"/>
                  </svg>
                </span>
                <div>
                  <span class="info-label">Shift</span>
                  <span class="info-value">{{ emp.shift?.name || 'Not Assigned' }}</span>
                </div>
              </div>

              <div class="info-item" v-if="emp.shift">
                <span class="info-icon">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12 6 12 12 16 14"/>
                  </svg>
                </span>
                <div>
                  <span class="info-label">Timing</span>
                  <span class="info-value">
                    {{ emp.shift.time_from?.substring(0, 5) }} - {{ emp.shift.time_to?.substring(0, 5) }}
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- Card Actions -->
          <div class="card-actions">
            <button @click="openView(emp.id)" class="action-btn view-btn">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                <circle cx="12" cy="12" r="3"/>
              </svg>
              View
            </button>
            <button @click="startEdit(emp)" class="action-btn edit-btn">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
              </svg>
              Edit
            </button>
            <button @click="confirmDelete(emp.id)" class="action-btn delete-btn">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="3 6 5 6 21 6"/>
                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
              </svg>
              Delete
            </button>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="empty-state">
        <div class="empty-icon">
          <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
            <circle cx="9" cy="7" r="4"/>
            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
          </svg>
        </div>
        <h3 class="empty-title">No employees found</h3>
        <p class="empty-text">Try adjusting your filters or add a new employee</p>
      </div>

      <!-- View Modal -->
      <Transition name="modal">
        <div v-if="viewModal" class="modal-overlay" @click="closeView">
          <div class="modal-container" @click.stop>
            <div class="modal-header">
              <h2 class="modal-title">Employee Details</h2>
              <button @click="closeView" class="modal-close">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <line x1="18" y1="6" x2="6" y2="18"/>
                  <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
              </button>
            </div>

            <div class="modal-body">
              <div v-if="loadingView" class="loading-state">
                <div class="spinner"></div>
                <p>Loading employee details...</p>
              </div>

              <div v-else class="details-grid">
                <div class="detail-item">
                  <label>Full Name</label>
                  <p>{{ viewData.full_name }}</p>
                </div>
                <div class="detail-item">
                  <label>Email</label>
                  <p>{{ viewData.email }}</p>
                </div>
                <div class="detail-item">
                  <label>Contact</label>
                  <p>{{ viewData.contact }}</p>
                </div>
                <div class="detail-item">
                  <label>Emergency Contact</label>
                  <p>{{ viewData.emergency_contact || 'N/A' }}</p>
                </div>
                <div class="detail-item">
                  <label>Department</label>
                  <p>{{ viewData.department?.name || 'N/A' }}</p>
                </div>
                <div class="detail-item">
                  <label>Designation</label>
                  <p>{{ viewData.designation?.name || 'N/A' }}</p>
                </div>
                <div class="detail-item">
                  <label>Shift</label>
                  <p>{{ viewData.shift?.name || 'Not Assigned' }}</p>
                  <p v-if="viewData.shift" class="text-sm text-gray-500">
                    {{ viewData.shift.time_from?.substring(0, 5) }} - {{ viewData.shift.time_to?.substring(0, 5) }}
                  </p>
                </div>
                <div class="detail-item">
                  <label>Date of Birth</label>
                  <p>{{ formatDate(viewData.dob) }}</p>
                </div>
                <div class="detail-item">
                  <label>Date of Joining</label>
                  <p>{{ formatDate(viewData.doj) }}</p>
                </div>
                <div class="detail-item">
                  <label>Gender</label>
                  <p>{{ viewData.gender || 'N/A' }}</p>
                </div>
                <div class="detail-item">
                  <label>Marital Status</label>
                  <p>{{ viewData.marital_status || 'N/A' }}</p>
                </div>
                <div class="detail-item">
                  <label>Work Location</label>
                  <p>{{ viewData.work_location || 'N/A' }}</p>
                </div>
                <div class="detail-item full-width">
                  <label>Address</label>
                  <p>{{ viewData.address || 'N/A' }}</p>
                </div>
                <div class="detail-item">
                  <label>ZIP Code</label>
                  <p>{{ viewData.zip || 'N/A' }}</p>
                </div>
                <div class="detail-item">
                  <label>Pay Scale</label>
                  <p>{{ viewData.pay_scale || 'N/A' }}</p>
                </div>
                <div class="detail-item">
                  <label>Monthly Salary</label>
                  <p class="salary-value">
                    {{ formatMoney(viewData.monthly_salary) }} {{ viewData.salary_currency || 'INR' }}
                  </p>
                </div>
                <div class="detail-item">
                  <label>Salary Type</label>
                  <p class="capitalize">{{ viewData.salary_type || 'monthly' }}</p>
                </div>
              </div>
            </div>

            <div class="modal-footer">
              <button @click="closeView" class="btn-secondary">Close</button>
            </div>
          </div>
        </div>
      </Transition>

      <!-- Edit Modal -->
      <Transition name="modal">
        <div v-if="showModal" class="modal-overlay" @click="showModal = false">
          <div class="modal-container large" @click.stop>
            <div class="modal-header">
              <h2 class="modal-title">Edit Employee</h2>
              <button @click="showModal = false" class="modal-close">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <line x1="18" y1="6" x2="6" y2="18"/>
                  <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
              </button>
            </div>

            <form @submit.prevent="saveEdit" class="modal-body">
              <div class="form-grid">
                <div class="form-field">
                  <label>First Name *</label>
                  <input v-model="editForm.first_name" required class="input-field" />
                </div>
                <div class="form-field">
                  <label>Middle Name</label>
                  <input v-model="editForm.middle_name" class="input-field" />
                </div>
                <div class="form-field">
                  <label>Last Name *</label>
                  <input v-model="editForm.last_name" required class="input-field" />
                </div>
                <div class="form-field">
                  <label>Email *</label>
                  <input v-model="editForm.email" type="email" required class="input-field" />
                </div>
                <div class="form-field">
                  <label>Contact *</label>
                  <input v-model="editForm.contact" required class="input-field" />
                </div>
                <div class="form-field">
                  <label>Emergency Contact</label>
                  <input v-model="editForm.emergency_contact" class="input-field" />
                </div>
                <div class="form-field">
                  <label>Gender</label>
                  <select v-model="editForm.gender" class="input-field">
                    <option value="">Select Gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                  </select>
                </div>
                <div class="form-field">
                  <label>Date of Birth</label>
                  <input v-model="editForm.dob" type="date" class="input-field" />
                </div>
                <div class="form-field">
                  <label>Date of Joining</label>
                  <input v-model="editForm.doj" type="date" class="input-field" />
                </div>
                <div class="form-field">
                  <label>Marital Status</label>
                  <select v-model="editForm.marital_status" class="input-field">
                    <option value="">Select Status</option>
                    <option value="Single">Single</option>
                    <option value="Married">Married</option>
                    <option value="Divorced">Divorced</option>
                  </select>
                </div>
                <div class="form-field">
                  <label>Department *</label>
                  <select v-model="editForm.department_id" required class="input-field">
                    <option value="">Select Department</option>
                    <option v-for="d in departments" :key="d.id" :value="d.id">{{ d.name }}</option>
                  </select>
                </div>
                <div class="form-field">
                  <label>Designation *</label>
                  <select v-model="editForm.designation_id" required class="input-field">
                    <option value="">Select Designation</option>
                    <option v-for="d in designations" :key="d.id" :value="d.id">{{ d.name }}</option>
                  </select>
                </div>
                <div class="form-field">
                  <label>Shift</label>
                  <select v-model="editForm.shift_id" class="input-field">
                    <option :value="null">Select Shift (Optional)</option>
                    <option v-for="shift in (shifts || [])" :key="shift.id" :value="shift.id">
                      {{ shift.name }} ({{ shift.time_from?.substring(0, 5) || '00:00' }} - {{ shift.time_to?.substring(0, 5) || '00:00' }})
                    </option>
                  </select>
                </div>
                <div class="form-field">
                  <label>Monthly Salary</label>
                  <input v-model.number="editForm.monthly_salary" type="number" step="0.01" min="0" class="input-field" />
                </div>
                <div class="form-field">
                  <label>Work Location</label>
                  <input v-model="editForm.work_location" class="input-field" />
                </div>
                <div class="form-field full-width">
                  <label>Address</label>
                  <input v-model="editForm.address" class="input-field" />
                </div>
              </div>
            </form>

            <div class="modal-footer">
              <button type="button" @click="showModal = false" class="btn-secondary">Cancel</button>
              <button @click="saveEdit" class="btn-primary">Save Changes</button>
            </div>
          </div>
        </div>
      </Transition>
    </div>
  </AdminLayout>
</template>

<script setup>
import { reactive, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import { route } from 'ziggy-js';

const props = defineProps({
  employees: Array,
  departments: Array,
  designations: Array,
  shifts: Array,
  filters: Object,
});

const filters = reactive({ ...props.filters });
const showModal = ref(false);
const editForm = reactive({
  first_name: '',
  middle_name: '',
  last_name: '',
  email: '',
  contact: '',
  emergency_contact: '',
  gender: '',
  dob: '',
  doj: '',
  marital_status: '',
  address: '',
  zip: '',
  pay_scale: '',
  work_location: '',
  department_id: '',
  designation_id: '',
  shift_id: null,
  monthly_salary: null,
  salary_currency: 'INR',
  salary_type: 'monthly',
});
let currentId = null;

const viewModal = ref(false);
const viewData = reactive({});
const loadingView = ref(false);

const applyFilters = () => {
  router.get(route('admin.employees.manage'), filters, {
    preserveScroll: true,
    preserveState: true,
  });
};

const clearFilters = () => {
  filters.name = '';
  filters.department_id = '';
  filters.designation_id = '';
  applyFilters();
};

const getInitials = (firstName, lastName) => {
  const first = firstName?.charAt(0) || '';
  const last = lastName?.charAt(0) || '';
  return (first + last).toUpperCase();
};

const startEdit = (emp) => {
  showModal.value = true;
  currentId = emp.id;

  editForm.first_name = emp.first_name ?? '';
  editForm.middle_name = emp.middle_name ?? '';
  editForm.last_name = emp.last_name ?? '';
  editForm.email = emp.email ?? '';
  editForm.contact = emp.contact ?? '';
  editForm.emergency_contact = emp.emergency_contact ?? '';
  editForm.gender = emp.gender ?? '';
  editForm.dob = emp.dob ?? '';
  editForm.doj = emp.doj ?? '';
  editForm.marital_status = emp.marital_status ?? '';
  editForm.address = emp.address ?? '';
  editForm.zip = emp.zip ?? '';
  editForm.pay_scale = emp.pay_scale ?? '';
  editForm.work_location = emp.work_location ?? '';
  editForm.department_id = emp.department_id ?? emp.department?.id ?? '';
  editForm.designation_id = emp.designation_id ?? emp.designation?.id ?? '';
  editForm.shift_id = emp.shift_id ? Number(emp.shift_id) : (emp.shift?.id ? Number(emp.shift.id) : null);
  editForm.monthly_salary = emp.monthly_salary !== undefined && emp.monthly_salary !== null ? Number(emp.monthly_salary) : null;
  editForm.salary_currency = emp.salary_currency ?? 'INR';
  editForm.salary_type = emp.salary_type ?? 'monthly';
};

const saveEdit = () => {
  const payload = {
    ...editForm,
    _method: 'put'
  };

  router.post(route('admin.employees.manage.update', currentId), payload, {
    onSuccess: () => {
      showModal.value = false;
    },
    onError: (errors) => {
      console.error('Validation errors:', errors);
    }
  });
};

const confirmDelete = (id) => {
  if (confirm("Are you sure you want to delete this employee?")) {
    router.post(route('admin.employees.manage.destroy', id), {}, {
      preserveScroll: true,
    });
  }
};

const openView = async (id) => {
  viewModal.value = true;
  loadingView.value = true;
  Object.keys(viewData).forEach(k => delete viewData[k]);

  try {
    const res = await axios.get(route('admin.employees.manage.show', id));
    if (res.data && res.data.success) {
      Object.assign(viewData, res.data.data || {});
    }
  } catch (err) {
    console.error('Failed to load employee:', err);
  } finally {
    loadingView.value = false;
  }
};

const closeView = () => {
  viewModal.value = false;
  Object.keys(viewData).forEach(k => delete viewData[k]);
};

const formatDate = (d) => {
  if (!d) return 'N/A';
  const dt = (d || '').split('T')[0];
  const [y,m,day] = dt.split('-') || [];
  if (!y) return d;
  return `${day}-${m}-${y}`;
};

const formatMoney = (value) => {
  if (value === null || value === undefined || value === '') return '0.00';
  const n = Number(value) || 0;
  return n.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
};
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

.header-stats {
  display: flex;
  gap: 1rem;
}

.stat-card {
  background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
  padding: 1rem 1.5rem;
  border-radius: 12px;
  display: flex;
  flex-direction: column;
  align-items: center;
  color: white;
  box-shadow: 0 4px 12px rgba(139, 92, 246, 0.25);
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
  border-color: #8b5cf6;
  box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
}

/* Employee Cards Grid */
.employees-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
  gap: 1.5rem;
}

.employee-card {
  background: white;
  border-radius: 16px;
  border: 1px solid #f3f4f6;
  overflow: hidden;
  transition: all 0.3s ease;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.employee-card:hover {
  box-shadow: 0 10px 25px rgba(139, 92, 246, 0.1);
  border-color: #e9d5ff;
  transform: translateY(-2px);
}

.card-header {
  padding: 1.5rem;
  background: linear-gradient(135deg, #faf5ff 0%, #f3e8ff 100%);
  display: flex;
  align-items: center;
  gap: 1rem;
  border-bottom: 1px solid #f3f4f6;
}

.employee-avatar {
  width: 3rem;
  height: 3rem;
  background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-weight: 600;
  font-size: 1.125rem;
  flex-shrink: 0;
  box-shadow: 0 4px 12px rgba(139, 92, 246, 0.25);
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
  background: #d1fae5;
  color: #065f46;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 600;
  flex-shrink: 0;
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
  background: linear-gradient(135deg, #ede9fe 0%, #e9d5ff 100%);
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #7c3aed;
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

.card-actions {
  padding: 1rem 1.5rem;
  background: #fafafa;
  border-top: 1px solid #f3f4f6;
  display: flex;
  gap: 0.75rem;
}

.action-btn {
  flex: 1;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  padding: 0.625rem 1rem;
  border: none;
  border-radius: 8px;
  font-size: 0.875rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.view-btn {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  color: white;
}

.view-btn:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
}

.edit-btn {
  background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
  color: white;
}

.edit-btn:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.25);
}

.delete-btn {
  background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
  color: white;
}

.delete-btn:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(239, 68, 68, 0.25);
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

.modal-container {
  background: white;
  border-radius: 16px;
  width: 100%;
  max-width: 800px;
  max-height: 90vh;
  display: flex;
  flex-direction: column;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
}

.modal-container.large {
  max-width: 900px;
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

.loading-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 3rem;
  color: #6b7280;
  gap: 1rem;
}

.spinner {
  width: 3rem;
  height: 3rem;
  border: 3px solid #f3f4f6;
  border-top-color: #8b5cf6;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
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

.salary-value {
  font-size: 1.25rem;
  font-weight: 600;
  background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
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

.btn-primary {
  padding: 0.625rem 1.5rem;
  background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
  border: none;
  border-radius: 8px;
  color: white;
  font-size: 0.9375rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  box-shadow: 0 4px 12px rgba(139, 92, 246, 0.25);
}

.btn-primary:hover {
  transform: translateY(-1px);
  box-shadow: 0 6px 20px rgba(139, 92, 246, 0.35);
}

/* Form Grid */
.form-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1rem;
}

.form-field {
  display: flex;
  flex-direction: column;
}

.form-field.full-width {
  grid-column: 1 / -1;
}

.form-field label {
  font-size: 0.875rem;
  font-weight: 500;
  color: #374151;
  margin-bottom: 0.5rem;
}

.input-field {
  width: 100%;
  padding: 0.625rem 0.875rem;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 0.9375rem;
  color: #1f2937;
  transition: all 0.2s;
}

.input-field:focus {
  outline: none;
  border-color: #8b5cf6;
  box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
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

/* Responsive */
@media (max-width: 640px) {
  .page-container {
    padding: 1rem;
  }

  .page-header {
    flex-direction: column;
    align-items: flex-start;
  }

  .employees-grid {
    grid-template-columns: 1fr;
  }

  .details-grid,
  .form-grid {
    grid-template-columns: 1fr;
  }

  .info-row {
    grid-template-columns: 1fr;
  }

  .filters-grid {
    grid-template-columns: 1fr;
  }
}
</style>