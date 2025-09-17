<template>
  <div class="min-h-screen bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Header -->
      <div class="mb-8">
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-3xl font-bold text-gray-900">Add New Employee</h2>
            <p class="mt-1 text-sm text-gray-500">Fill in the information below to create a new employee.</p>
          </div>
          <Link 
            :href="route('manager.employees.index')"
            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
          >
            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/>
            </svg>
            Back to Employees
          </Link>
        </div>
      </div>

      <!-- Form -->
      <div class="bg-white shadow-sm rounded-lg">
        <form @submit.prevent="submit">
          <div class="px-6 py-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
              
              <!-- Personal Information Section -->
              <div class="lg:col-span-2">
                <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                  <svg class="w-5 h-5 mr-2 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                  </svg>
                  Personal Information
                </h3>
              </div>

              <!-- First Name -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  First Name <span class="text-red-500">*</span>
                </label>
                <input
                  v-model="form.first_name"
                  type="text"
                  required
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                  :class="{ 'border-red-300 focus:ring-red-500 focus:border-red-500': form.errors?.first_name }"
                  placeholder="Enter first name"
                />
                <p v-if="form.errors?.first_name" class="mt-1 text-sm text-red-600">{{ form.errors.first_name }}</p>
              </div>

              <!-- Middle Name -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Middle Name</label>
                <input
                  v-model="form.middle_name"
                  type="text"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                  placeholder="Enter middle name (optional)"
                />
              </div>

              <!-- Last Name -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Last Name <span class="text-red-500">*</span>
                </label>
                <input
                  v-model="form.last_name"
                  type="text"
                  required
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                  :class="{ 'border-red-300 focus:ring-red-500 focus:border-red-500': form.errors?.last_name }"
                  placeholder="Enter last name"
                />
                <p v-if="form.errors?.last_name" class="mt-1 text-sm text-red-600">{{ form.errors.last_name }}</p>
              </div>

              <!-- Email -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Email Address <span class="text-red-500">*</span>
                </label>
                <input
                  v-model="form.email"
                  type="email"
                  required
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                  :class="{ 'border-red-300 focus:ring-red-500 focus:border-red-500': form.errors?.email }"
                  placeholder="Enter email address"
                />
                <p v-if="form.errors?.email" class="mt-1 text-sm text-red-600">{{ form.errors.email }}</p>
              </div>

              <!-- Contact -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Contact Number <span class="text-red-500">*</span>
                </label>
                <input
                  v-model="form.contact"
                  type="tel"
                  required
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                  :class="{ 'border-red-300 focus:ring-red-500 focus:border-red-500': form.errors?.contact }"
                  placeholder="Enter contact number"
                />
                <p v-if="form.errors?.contact" class="mt-1 text-sm text-red-600">{{ form.errors.contact }}</p>
              </div>

              <!-- Emergency Contact -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Emergency Contact</label>
                <input
                  v-model="form.emergency_contact"
                  type="tel"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                  placeholder="Enter emergency contact"
                />
              </div>

              <!-- Gender -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Gender</label>
                <select
                  v-model="form.gender"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                >
                  <option value="">Select Gender</option>
                  <option value="male">Male</option>
                  <option value="female">Female</option>
                  <option value="other">Other</option>
                </select>
              </div>

              <!-- Date of Birth -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date of Birth</label>
                <input
                  v-model="form.dob"
                  type="date"
                  :max="maxBirthDate"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                />
              </div>

              <!-- Marital Status -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Marital Status</label>
                <select
                  v-model="form.marital_status"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                >
                  <option value="">Select Status</option>
                  <option value="single">Single</option>
                  <option value="married">Married</option>
                  <option value="divorced">Divorced</option>
                  <option value="widowed">Widowed</option>
                </select>
              </div>

              <!-- Work Information Section -->
              <div class="lg:col-span-2 pt-6 border-t border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                  <svg class="w-5 h-5 mr-2 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd"/>
                  </svg>
                  Work Information
                </h3>
              </div>

              <!-- Department -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Department <span class="text-red-500">*</span>
                </label>
                <select
                  v-model="form.department_id"
                  required
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                  :class="{ 'border-red-300 focus:ring-red-500 focus:border-red-500': form.errors?.department_id }"
                >
                  <option value="">Select Department</option>
                  <option v-for="dept in departments" :key="dept.id" :value="dept.id">
                    {{ dept.name }}
                  </option>
                </select>
                <p v-if="form.errors?.department_id" class="mt-1 text-sm text-red-600">{{ form.errors.department_id }}</p>
              </div>

              <!-- Designation -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Designation <span class="text-red-500">*</span>
                </label>
                <select
                  v-model="form.designation_id"
                  required
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                  :class="{ 'border-red-300 focus:ring-red-500 focus:border-red-500': form.errors?.designation_id }"
                >
                  <option value="">Select Designation</option>
                  <option v-for="desig in designations" :key="desig.id" :value="desig.id">
                    {{ desig.name }}
                  </option>
                </select>
                <p v-if="form.errors?.designation_id" class="mt-1 text-sm text-red-600">{{ form.errors.designation_id }}</p>
              </div>

              <!-- Date of Joining -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date of Joining</label>
                <input
                  v-model="form.doj"
                  type="date"
                  :max="today"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                />
              </div>

              <!-- Pay Scale -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Pay Scale</label>
                <input
                  v-model="form.pay_scale"
                  type="text"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                  placeholder="e.g., Grade A, Level 5"
                />
              </div>

              <!-- Work Location -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Work Location</label>
                <input
                  v-model="form.work_location"
                  type="text"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                  placeholder="e.g., Head Office, Branch 1"
                />
              </div>

              <!-- Security Section -->
              <div class="lg:col-span-2 pt-6 border-t border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                  <svg class="w-5 h-5 mr-2 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                  </svg>
                  Account Security
                </h3>
              </div>

              <!-- Password -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Password <span class="text-red-500">*</span>
                </label>
                <input
                  v-model="form.password"
                  type="password"
                  required
                  minlength="8"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                  :class="{ 'border-red-300 focus:ring-red-500 focus:border-red-500': form.errors?.password }"
                  placeholder="Enter password (minimum 8 characters)"
                />
                <p v-if="form.errors?.password" class="mt-1 text-sm text-red-600">{{ form.errors.password }}</p>
                <p class="mt-1 text-xs text-gray-500">Password must be at least 8 characters long</p>
              </div>

              <!-- Confirm Password -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Confirm Password <span class="text-red-500">*</span>
                </label>
                <input
                  v-model="form.password_confirmation"
                  type="password"
                  required
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                  :class="{ 'border-red-300 focus:ring-red-500 focus:border-red-500': passwordMismatch }"
                  placeholder="Confirm password"
                />
                <p v-if="passwordMismatch" class="mt-1 text-sm text-red-600">Passwords do not match</p>
              </div>

              <!-- Address Section -->
              <div class="lg:col-span-2 pt-6 border-t border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                  <svg class="w-5 h-5 mr-2 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                  </svg>
                  Address Information
                </h3>
              </div>

              <!-- Address -->
              <div class="lg:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                <textarea
                  v-model="form.address"
                  rows="3"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors resize-none"
                  placeholder="Enter full address"
                />
              </div>

              <!-- ZIP Code -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">ZIP/Postal Code</label>
                <input
                  v-model="form.zip"
                  type="text"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                  placeholder="Enter ZIP/postal code"
                />
              </div>
            </div>
          </div>

          <!-- Form Actions -->
          <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end space-x-3">
            <Link
              :href="route('manager.employees.index')"
              class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
            >
              Cancel
            </Link>
            <button
              type="submit"
              :disabled="processing || passwordMismatch"
              class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
            >
              <svg v-if="processing" class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
              </svg>
              {{ processing ? 'Creating Employee...' : 'Create Employee' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import ManagerLayout from '@/Layouts/ManagerLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';

export default {
  layout: ManagerLayout,
  
  components: {
    Link,
  },
  
  props: {
    departments: {
      type: Array,
      required: true
    },
    designations: {
      type: Array, 
      required: true
    }
  },
  
  data() {
    return {
      processing: false,
      form: useForm({
        first_name: '',
        middle_name: '',
        last_name: '',
        email: '',
        password: '',
        password_confirmation: '',
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
      }),
    };
  },
  
  computed: {
    today() {
      return new Date().toISOString().split('T')[0];
    },
    
    maxBirthDate() {
      const date = new Date();
      date.setFullYear(date.getFullYear() - 18); // Minimum 18 years old
      return date.toISOString().split('T')[0];
    },
    
    passwordMismatch() {
      return this.form.password && this.form.password_confirmation && 
             this.form.password !== this.form.password_confirmation;
    }
  },
  
  methods: {
    submit() {
      if (this.passwordMismatch) {
        return;
      }
      
      this.form.post(route('manager.employees.store'), {
        onStart: () => {
          this.processing = true;
        },
        onFinish: () => {
          this.processing = false;
        },
        onSuccess: () => {
          // Will redirect to index page automatically
        },
        onError: (errors) => {
          console.error('Validation errors:', errors);
        }
      });
    }
  },
  
  mounted() {
    // Set default DOJ to today if empty
    if (!this.form.doj) {
      this.form.doj = this.today;
    }
  }
};
</script>

<style scoped>
/* Custom focus styles */
input:focus,
select:focus,
textarea:focus {
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Loading animation */
@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.animate-spin {
  animation: spin 1s linear infinite;
}

/* Smooth transitions */
.transition-colors {
  transition-property: color, background-color, border-color, text-decoration-color, fill, stroke;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 150ms;
}
</style>