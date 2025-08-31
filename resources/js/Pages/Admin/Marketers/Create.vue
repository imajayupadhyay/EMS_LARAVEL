<template>
  <AdminLayout>
    <div class="p-6">
      <h1 class="text-2xl font-bold mb-6">Register New Marketer</h1>

      <div class="bg-white shadow rounded-2xl p-6">
        <form @submit.prevent="submitForm" class="space-y-6">
          <div class="grid grid-cols-2 gap-6">
            <!-- First Name -->
            <div>
              <label class="block text-sm font-medium mb-1">First Name</label>
              <input v-model="form.first_name" type="text" class="input" required />
            </div>

            <!-- Last Name -->
            <div>
              <label class="block text-sm font-medium mb-1">Last Name</label>
              <input v-model="form.last_name" type="text" class="input" required />
            </div>

            <!-- Email -->
            <div>
              <label class="block text-sm font-medium mb-1">Email</label>
              <input v-model="form.email" type="email" class="input" required />
            </div>

            <!-- Username -->
            <div>
              <label class="block text-sm font-medium mb-1">Username</label>
              <input v-model="form.username" type="text" class="input" required />
            </div>

            <!-- Password -->
            <div>
              <label class="block text-sm font-medium mb-1">Password</label>
              <input v-model="form.password" type="password" class="input" required />
            </div>

            <!-- Confirm Password -->
            <div>
              <label class="block text-sm font-medium mb-1">Confirm Password</label>
              <input v-model="form.password_confirmation" type="password" class="input" required />
            </div>

            <!-- Contact -->
            <div>
              <label class="block text-sm font-medium mb-1">Contact</label>
              <input v-model="form.contact" type="text" class="input" required />
            </div>

            <!-- Gender -->
            <div>
              <label class="block text-sm font-medium mb-1">Gender</label>
              <select v-model="form.gender" class="input" required>
                <option value="">Select</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
              </select>
            </div>
          </div>

          <!-- Location Section -->
          <div class="mt-6 border-t pt-6">
            <h2 class="text-lg font-semibold mb-4">Initial Location</h2>
            <div class="grid grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-medium mb-1">Latitude</label>
                <input v-model="form.latitude" type="number" step="0.000001" class="input" required />
              </div>
              <div>
                <label class="block text-sm font-medium mb-1">Longitude</label>
                <input v-model="form.longitude" type="number" step="0.000001" class="input" required />
              </div>
            </div>
          </div>

          <!-- Submit -->
          <div class="flex justify-end mt-6">
            <button type="submit" class="btn-primary">Register</button>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from "vue";
import axios from "axios";
import AdminLayout from "@/Layouts/AdminLayout.vue";

const form = ref({
  first_name: "",
  last_name: "",
  email: "",
  username: "",
  password: "",
  password_confirmation: "",
  contact: "",
  gender: "",
  latitude: "",
  longitude: ""
});

const submitForm = async () => {
  try {
    const res = await axios.post("/admin/marketers", form.value);
    alert("Marketer registered successfully!");
    console.log(res.data);
  } catch (e) {
    if (e.response?.status === 422) {
      alert("Validation failed: " + JSON.stringify(e.response.data.errors));
    } else {
      alert("Something went wrong");
    }
  }
};
</script>

<style scoped>
.input {
  @apply w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500;
}
.btn-primary {
  @apply bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg shadow-md transition;
}
</style>
