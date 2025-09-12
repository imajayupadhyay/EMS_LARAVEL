<script setup>
import { usePage, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import InputField from '@/Components/InputField.vue'
import SelectField from '@/Components/SelectField.vue'
import { ref } from 'vue'

defineOptions({ layout: AdminLayout })

const page = usePage()

const props = defineProps({
  departments: Array,
  designations: Array,
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
  // salary fields
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
      // keep the form but clear sensitive fields
      form.reset('password', 'password_confirmation')
    },
    onFinish: () => {
      submitting.value = false
    }
  })
}
</script>

<template>
  <div class="min-h-screen bg-gray-100">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
      <h2 class="text-3xl font-bold text-orange-600 mb-8 text-center">Register New Employee</h2>

      <!-- Flash Success Message -->
      <div v-if="page.props.flash && page.props.flash.success" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6" role="alert">
        <strong class="font-bold">Success:</strong>
        <span class="ml-2">{{ page.props.flash.success }}</span>
      </div>

      <!-- Form -->
      <form @submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-white p-8 rounded-2xl shadow-xl">
        <InputField label="First Name" v-model="form.first_name" />
        <div>
          <InputField label="Middle Name" v-model="form.middle_name" />
        </div>
        <div>
          <InputField label="Last Name" v-model="form.last_name" />
        </div>

        <div>
          <InputField label="Email" type="email" v-model="form.email" />
          <div v-if="form.errors.email" class="text-red-600 text-sm mt-1">{{ form.errors.email }}</div>
        </div>

        <div>
          <InputField label="Contact Number" v-model="form.contact" />
          <div v-if="form.errors.contact" class="text-red-600 text-sm mt-1">{{ form.errors.contact }}</div>
        </div>

        <div>
          <InputField label="Emergency Contact" v-model="form.emergency_contact" />
        </div>

        <div>
          <InputField label="Date of Birth" type="date" v-model="form.dob" />
          <div v-if="form.errors.dob" class="text-red-600 text-sm mt-1">{{ form.errors.dob }}</div>
        </div>

        <div>
          <InputField label="Date of Joining" type="date" v-model="form.doj" />
          <div v-if="form.errors.doj" class="text-red-600 text-sm mt-1">{{ form.errors.doj }}</div>
        </div>

        <div>
          <InputField label="Password" type="password" v-model="form.password" />
          <div v-if="form.errors.password" class="text-red-600 text-sm mt-1">{{ form.errors.password }}</div>
        </div>

        <div>
          <InputField label="Confirm Password" type="password" v-model="form.password_confirmation" />
        </div>

        <div>
          <SelectField label="Gender" v-model="form.gender" :options="['Male', 'Female']" />
        </div>

        <div>
          <SelectField label="Marital Status" v-model="form.marital_status" :options="['Single', 'Married']" />
        </div>

        <div>
          <InputField label="Work Location" v-model="form.work_location" />
        </div>

        <div>
          <InputField label="Address" v-model="form.address" />
        </div>

        <div>
          <InputField label="ZIP Code" v-model="form.zip" />
        </div>

        <div>
          <InputField label="Pay Scale" v-model="form.pay_scale" />
        </div>

        <div>
          <SelectField label="Department" v-model="form.department_id" :options="departments.map(d => ({ label: d.name, value: d.id }))" />
          <div v-if="form.errors.department_id" class="text-red-600 text-sm mt-1">{{ form.errors.department_id }}</div>
        </div>

        <div>
          <SelectField label="Designation" v-model="form.designation_id" :options="designations.map(d => ({ label: d.name, value: d.id }))" />
          <div v-if="form.errors.designation_id" class="text-red-600 text-sm mt-1">{{ form.errors.designation_id }}</div>
        </div>

        <!-- Salary Fields -->
        <div>
          <InputField label="Monthly Salary" type="number" v-model.number="form.monthly_salary" :min="0" step="0.01" />
          <div v-if="form.errors.monthly_salary" class="text-red-600 text-sm mt-1">{{ form.errors.monthly_salary }}</div>
        </div>

        <div>
          <InputField label="Currency" v-model="form.salary_currency" />
          <div v-if="form.errors.salary_currency" class="text-red-600 text-sm mt-1">{{ form.errors.salary_currency }}</div>
        </div>

        <div class="md:col-span-2">
          <label class="block text-sm font-medium text-gray-700 mb-1">Salary Type</label>
          <select v-model="form.salary_type" class="input w-full">
            <option value="monthly">Monthly</option>
            <option value="daily">Daily</option>
            <option value="hourly">Hourly</option>
          </select>
          <div v-if="form.errors.salary_type" class="text-red-600 text-sm mt-1">{{ form.errors.salary_type }}</div>
        </div>

        <div class="md:col-span-2">
          <button
            :disabled="submitting"
            type="submit"
            class="w-full py-3 mt-6 rounded-xl text-white font-bold bg-orange-500 hover:bg-orange-600 transition duration-300 disabled:opacity-60"
          >
            <span v-if="!submitting">Register Employee</span>
            <span v-else>Registering…</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<style scoped>
.input-style {
  @apply w-full px-4 py-2 rounded-md border border-gray-300 shadow-sm focus:ring-2 focus:ring-orange-500 focus:outline-none;
}
.input {
  @apply border border-gray-300 p-2 rounded w-full focus:outline-none focus:ring-2 focus:ring-orange-500;
}
</style>
