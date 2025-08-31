<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-r from-orange-500 to-pink-600 px-4">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl p-8 space-y-6">
      <!-- Logo -->
      <div class="text-center">
        <h1 class="text-3xl font-extrabold text-gray-800">Marketer Login</h1>
        <p class="mt-2 text-gray-500">Sign in to start your day</p>
      </div>

      <!-- Flash/Error -->
      <div v-if="error" class="bg-red-100 text-red-700 px-4 py-2 rounded-md text-sm">
        {{ error }}
      </div>

      <!-- Form -->
      <form @submit.prevent="submitLogin" class="space-y-5">
        <!-- Username -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
          <input v-model="form.username" type="text"
                 placeholder="Enter your username"
                 class="input" required />
        </div>

        <!-- Password -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
          <input v-model="form.password" type="password"
                 placeholder="Enter your password"
                 class="input" required />
        </div>

        <!-- Submit -->
        <div>
          <button type="submit"
                  class="w-full bg-orange-600 hover:bg-orange-700 text-white font-bold py-3 px-4 rounded-lg shadow-lg transition">
            🔑 Login
          </button>
        </div>
      </form>

      <!-- Footer -->
      <p class="text-xs text-center text-gray-400">
        © {{ new Date().getFullYear() }} EMS System. All rights reserved.
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";
import axios from "axios";
import { router } from "@inertiajs/vue3";

const form = ref({
  username: "",
  password: ""
});
const error = ref(null);

const submitLogin = async () => {
  try {
    error.value = null;
    await axios.post("/marketer/login", form.value);
    router.visit("/marketer/dashboard");
  } catch (e) {
    error.value = e.response?.data?.message || "Invalid login credentials";
  }
};
</script>

<style scoped>
.input {
  @apply w-full border-gray-300 rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500 px-3 py-2;
}
</style>
