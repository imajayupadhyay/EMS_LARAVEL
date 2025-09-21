<template>
  <AdminLayout>
    <div class="page-container">
      <!-- Page Header -->
      <div class="page-header">
        <div class="header-content">
          <div class="header-left">
            <div class="icon-wrapper">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                <circle cx="8.5" cy="7" r="4"/>
                <line x1="20" y1="8" x2="20" y2="14"/>
                <line x1="23" y1="11" x2="17" y2="11"/>
              </svg>
            </div>
            <div>
              <h1 class="page-title">Add New Employee</h1>
              <p class="page-subtitle">Fill in the details to register a new employee</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Flash Success Message -->
      <Transition name="fade">
        <div v-if="page.props.flash && page.props.flash.success" class="success-alert">
          <div class="alert-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="20 6 9 17 4 12"/>
            </svg>
          </div>
          <div class="alert-content">
            <strong>Success!</strong>
            <span>{{ page.props.flash.success }}</span>
          </div>
        </div>
      </Transition>

      <!-- Form Card -->
      <div class="form-card">
        <form @submit.prevent="submit">
          <!-- Personal Information Section -->
          <div class="form-section">
            <div class="section-header">
              <div class="section-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                  <circle cx="12" cy="7" r="4"/>
                </svg>
              </div>
              <h2 class="section-title">Personal Information</h2>
            </div>

            <div class="form-grid">
              <div class="form-group">
                <InputField label="First Name" v-model="form.first_name" :error="form.errors.first_name" required />
              </div>

              <div class="form-group">
                <InputField label="Middle Name" v-model="form.middle_name" />
              </div>

              <div class="form-group">
                <InputField label="Last Name" v-model="form.last_name" :error="form.errors.last_name" required />
              </div>

              <div class="form-group">
                <InputField label="Email" type="email" v-model="form.email" :error="form.errors.email" required />
              </div>

              <div class="form-group">
                <SelectField label="Gender" v-model="form.gender" :options="['Male', 'Female']" :error="form.errors.gender" required />
              </div>

              <div class="form-group">
                <InputField label="Date of Birth" type="date" v-model="form.dob" :error="form.errors.dob" required />
              </div>

              <div class="form-group">
                <SelectField label="Marital Status" v-model="form.marital_status" :options="['Single', 'Married']" :error="form.errors.marital_status" />
              </div>
            </div>
          </div>

          <!-- Contact Information Section -->
          <div class="form-section">
            <div class="section-header">
              <div class="section-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                </svg>
              </div>
              <h2 class="section-title">Contact Information</h2>
            </div>

            <div class="form-grid">
              <div class="form-group">
                <InputField label="Contact Number" v-model="form.contact" :error="form.errors.contact" required />
              </div>

              <div class="form-group">
                <InputField label="Emergency Contact" v-model="form.emergency_contact" :error="form.errors.emergency_contact" />
              </div>

              <div class="form-group full-width">
                <InputField label="Address" v-model="form.address" :error="form.errors.address" />
              </div>

              <div class="form-group">
                <InputField label="ZIP Code" v-model="form.zip" :error="form.errors.zip" />
              </div>
            </div>
          </div>

          <!-- Employment Details Section -->
          <div class="form-section">
            <div class="section-header">
              <div class="section-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <rect x="2" y="7" width="20" height="14" rx="2" ry="2"/>
                  <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                </svg>
              </div>
              <h2 class="section-title">Employment Details</h2>
            </div>

            <div class="form-grid">
              <div class="form-group">
                <InputField label="Date of Joining" type="date" v-model="form.doj" :error="form.errors.doj" required />
              </div>

              <div class="form-group">
                <InputField label="Work Location" v-model="form.work_location" :error="form.errors.work_location" />
              </div>

              <div class="form-group">
                <SelectField label="Department" v-model="form.department_id" :options="departments.map(d => ({ label: d.name, value: d.id }))" :error="form.errors.department_id" required />
              </div>

              <div class="form-group">
                <SelectField label="Designation" v-model="form.designation_id" :options="designations.map(d => ({ label: d.name, value: d.id }))" :error="form.errors.designation_id" required />
              </div>

              <div class="form-group">
                <label class="field-label">Assign Shift</label>
                <select v-model="form.shift_id" class="field-input">
                  <option value="">Select Shift (Optional)</option>
                  <option v-for="shift in shifts" :key="shift.id" :value="shift.id">
                    {{ shift.name }} ({{ shift.time_from?.substring(0, 5) }} - {{ shift.time_to?.substring(0, 5) }})
                  </option>
                </select>
                <div v-if="form.errors.shift_id" class="field-error">{{ form.errors.shift_id }}</div>
              </div>

              <div class="form-group">
                <InputField label="Pay Scale" v-model="form.pay_scale" :error="form.errors.pay_scale" />
              </div>
            </div>
          </div>

          <!-- Salary Information Section -->
          <div class="form-section">
            <div class="section-header">
              <div class="section-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <line x1="12" y1="1" x2="12" y2="23"/>
                  <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                </svg>
              </div>
              <h2 class="section-title">Salary Information</h2>
            </div>

            <div class="form-grid">
              <div class="form-group">
                <InputField label="Monthly Salary" type="number" v-model.number="form.monthly_salary" :min="0" step="0.01" :error="form.errors.monthly_salary" />
              </div>

              <div class="form-group">
                <InputField label="Currency" v-model="form.salary_currency" :error="form.errors.salary_currency" />
              </div>

              <div class="form-group">
                <label class="field-label">Salary Type</label>
                <select v-model="form.salary_type" class="field-input">
                  <option value="monthly">Monthly</option>
                  <option value="daily">Daily</option>
                  <option value="hourly">Hourly</option>
                </select>
                <div v-if="form.errors.salary_type" class="field-error">{{ form.errors.salary_type }}</div>
              </div>
            </div>
          </div>

          <!-- Account Security Section -->
          <div class="form-section">
            <div class="section-header">
              <div class="section-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                  <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
              </div>
              <h2 class="section-title">Account Security</h2>
            </div>

            <div class="form-grid">
              <div class="form-group">
                <InputField label="Password" type="password" v-model="form.password" :error="form.errors.password" required />
              </div>

              <div class="form-group">
                <InputField label="Confirm Password" type="password" v-model="form.password_confirmation" :error="form.errors.password_confirmation" required />
              </div>
            </div>
          </div>

          <!-- Submit Button -->
          <div class="form-actions">
            <button type="button" @click="resetForm" class="btn-secondary">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="1 4 1 10 7 10"/>
                <path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/>
              </svg>
              Reset Form
            </button>
            <button type="submit" :disabled="submitting" class="btn-primary">
              <svg v-if="!submitting" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                <polyline points="17 21 17 13 7 13 7 21"/>
                <polyline points="7 3 7 8 15 8"/>
              </svg>
              <svg v-else width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="animate-spin">
                <circle cx="12" cy="12" r="10"/>
              </svg>
              <span v-if="!submitting">Register Employee</span>
              <span v-else>Registering...</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { usePage, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import InputField from '@/Components/InputField.vue'
import SelectField from '@/Components/SelectField.vue'
import { ref } from 'vue'

const page = usePage()

const props = defineProps({
  departments: Array,
  designations: Array,
  shifts: Array,
  defaults: Object,
})

const defaults = props.defaults || { salary_currency: 'INR', salary_type: 'monthly' }

const form = useForm({
  first_name: '',
  middle_name: '',
  last_name: '',
  email: '',
  password: '',
  password_confirmation: '',
  gender: '',
  dob: '',
  doj: '',
  marital_status: '',
  contact: '',
  emergency_contact: '',
  address: '',
  zip: '',
  pay_scale: '',
  work_location: '',
  department_id: '',
  designation_id: '',
  shift_id: '',
  monthly_salary: defaults.monthly_salary ?? 0.00,
  salary_currency: defaults.salary_currency ?? 'INR',
  salary_type: defaults.salary_type ?? 'monthly',
})

const submitting = ref(false)

const submit = () => {
  submitting.value = true
  form.post(route('admin.employees.store'), {
    preserveState: true,
    onSuccess: () => {
      form.reset('password', 'password_confirmation')
    },
    onFinish: () => {
      submitting.value = false
    }
  })
}

const resetForm = () => {
  form.reset()
}
</script>

<style scoped>
/* Container */
.page-container {
  padding: 1.5rem;
  max-width: 1200px;
  margin: 0 auto;
}

/* Page Header */
.page-header {
  margin-bottom: 2rem;
}

.header-content {
  display: flex;
  align-items: center;
  justify-content: space-between;
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

/* Success Alert */
.success-alert {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
  border: 1px solid #6ee7b7;
  border-radius: 12px;
  padding: 1rem 1.25rem;
  margin-bottom: 1.5rem;
  box-shadow: 0 2px 8px rgba(16, 185, 129, 0.15);
}

.alert-icon {
  flex-shrink: 0;
  width: 2.5rem;
  height: 2.5rem;
  background: #10b981;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
}

.alert-content {
  flex: 1;
  color: #065f46;
  font-size: 0.875rem;
}

.alert-content strong {
  font-weight: 600;
  margin-right: 0.5rem;
}

/* Form Card */
.form-card {
  background: white;
  border-radius: 16px;
  padding: 2rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
  border: 1px solid #f3f4f6;
}

/* Form Section */
.form-section {
  margin-bottom: 2.5rem;
}

.form-section:last-of-type {
  margin-bottom: 2rem;
}

.section-header {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin-bottom: 1.5rem;
  padding-bottom: 0.75rem;
  border-bottom: 2px solid #f3f4f6;
}

.section-icon {
  width: 2.5rem;
  height: 2.5rem;
  background: linear-gradient(135deg, #ede9fe 0%, #e9d5ff 100%);
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #7c3aed;
  flex-shrink: 0;
}

.section-title {
  font-size: 1.125rem;
  font-weight: 600;
  color: #1f2937;
  margin: 0;
}

/* Form Grid */
.form-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 1.5rem;
}

.form-group {
  display: flex;
  flex-direction: column;
}

.form-group.full-width {
  grid-column: 1 / -1;
}

.field-label {
  display: block;
  font-size: 0.875rem;
  font-weight: 500;
  color: #374151;
  margin-bottom: 0.5rem;
}

.field-input {
  width: 100%;
  padding: 0.625rem 0.875rem;
  border: 1px solid #d1d5db;
  border-radius: 10px;
  font-size: 0.9375rem;
  color: #1f2937;
  transition: all 0.2s ease;
}

.field-input:focus {
  outline: none;
  border-color: #8b5cf6;
  box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
}

.field-error {
  font-size: 0.8125rem;
  color: #dc2626;
  margin-top: 0.375rem;
}

/* Form Actions */
.form-actions {
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
  padding-top: 1.5rem;
  border-top: 1px solid #f3f4f6;
}

.btn-secondary {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1.5rem;
  background: white;
  border: 1px solid #d1d5db;
  border-radius: 10px;
  color: #6b7280;
  font-size: 0.9375rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-secondary:hover {
  background: #f9fafb;
  border-color: #9ca3af;
  color: #374151;
}

.btn-primary {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 2rem;
  background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
  border: none;
  border-radius: 10px;
  color: white;
  font-size: 0.9375rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
  box-shadow: 0 4px 12px rgba(139, 92, 246, 0.25);
}

.btn-primary:hover:not(:disabled) {
  transform: translateY(-1px);
  box-shadow: 0 6px 20px rgba(139, 92, 246, 0.35);
}

.btn-primary:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.animate-spin {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

/* Transitions */
.fade-enter-active, .fade-leave-active {
  transition: all 0.3s ease;
}

.fade-enter-from, .fade-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}

/* Responsive */
@media (max-width: 640px) {
  .page-container {
    padding: 1rem;
  }

  .form-card {
    padding: 1.5rem;
  }

  .page-title {
    font-size: 1.5rem;
  }

  .form-grid {
    grid-template-columns: 1fr;
  }

  .form-actions {
    flex-direction: column-reverse;
  }

  .btn-secondary,
  .btn-primary {
    width: 100%;
    justify-content: center;
  }
}
</style>