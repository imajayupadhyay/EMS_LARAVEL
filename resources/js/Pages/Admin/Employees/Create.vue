<script setup>
import { usePage, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import InputField from '@/Components/InputField.vue'
import SelectField from '@/Components/SelectField.vue'

defineOptions({ layout: AdminLayout })

const page = usePage()

const props = defineProps({
  departments: Array,
  designations: Array,
})

const form = useForm({
  first_name: '',
  middle_name: '',
  last_name: '',
  email: '',
  password: '',
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
})

const submit = () => {
  form.post(route('admin.employees.store'), {
    onSuccess: () => form.reset('password'),
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
        <InputField label="Middle Name" v-model="form.middle_name" />
        <InputField label="Last Name" v-model="form.last_name" />
        <InputField label="Email" type="email" v-model="form.email" />
        <InputField label="Contact Number" v-model="form.contact" />
        <InputField label="Emergency Contact" v-model="form.emergency_contact" />
        <InputField label="Date of Birth" type="date" v-model="form.dob" />
        <InputField label="Date of Joining" type="date" v-model="form.doj" />
        <InputField label="Password" type="password" v-model="form.password" />
        <SelectField label="Gender" v-model="form.gender" :options="['Male', 'Female']" />
        <SelectField label="Marital Status" v-model="form.marital_status" :options="['Single', 'Married']" />
        <InputField label="Work Location" v-model="form.work_location" />
        <InputField label="Address" v-model="form.address" />
        <InputField label="ZIP Code" v-model="form.zip" />
        <InputField label="Pay Scale" v-model="form.pay_scale" />
        <SelectField label="Department" v-model="form.department_id" :options="departments.map(d => ({ label: d.name, value: d.id }))" />
        <SelectField label="Designation" v-model="form.designation_id" :options="designations.map(d => ({ label: d.name, value: d.id }))" />

        <div class="md:col-span-2">
          <button type="submit" class="w-full py-3 mt-6 rounded-xl text-white font-bold bg-orange-500 hover:bg-orange-600 transition duration-300">
            Register Employee
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
</style>
