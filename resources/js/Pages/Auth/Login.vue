<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-orange">
    <div class="bg-white shadow-2xl rounded-2xl p-8 max-w-md w-full animate-fadeIn">
      <h2 class="text-3xl font-bold text-center text-orange-600 mb-6">Employee Management System</h2>

      <form @submit.prevent="submit">
        <div class="mb-4">
          <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
          <input
            id="email"
            type="email"
            v-model="form.email"
            class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm p-2 focus:ring-orange-500 focus:border-orange-500"
            required
            autofocus
          />
        </div>

        <div class="mb-4">
          <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
          <input
            id="password"
            type="password"
            v-model="form.password"
            class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm p-2 focus:ring-orange-500 focus:border-orange-500"
            required
            autocomplete="current-password"
          />
        </div>

        <div class="mb-4">
          <label for="role" class="block text-sm font-medium text-gray-700">Login as</label>
          <select
            id="role"
            v-model="form.role"
            class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm p-2 focus:ring-orange-500 focus:border-orange-500"
          >
            <option value="employee">Employee</option>
            <option value="manager">Manager</option>
            <option value="admin">Admin</option>
          </select>
        </div>

        <div v-if="form.errors.email" class="text-red-500 text-sm mb-2">
          {{ form.errors.email }}
        </div>

        <button
          type="submit"
          class="w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-4 rounded transition duration-300"
        >
          Login
        </button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'

const form = useForm({
  email: '',
  password: '',
  role: 'employee', // default role
})

const submit = () => {
  form.post(route('login'), {
    onFinish: () => form.reset('password'),
  })
}
</script>

<style>
@keyframes fadeIn {
  0% {
    opacity: 0;
    transform: translateY(-10px);
  }
  100% {
    opacity: 1;
    transform: translateY(0);
  }
}
.animate-fadeIn {
  animation: fadeIn 0.6s ease-out;
}
</style>
