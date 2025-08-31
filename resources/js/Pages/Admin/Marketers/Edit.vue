<template>
  <AdminLayout>
    <div class="p-6">
      <h1 class="text-2xl font-bold mb-6">Edit Marketer</h1>

      <div class="bg-white shadow rounded-2xl p-6">
        <form @submit.prevent="updateForm" class="space-y-6">
          <div class="grid grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-medium mb-1">First Name</label>
              <input v-model="form.first_name" type="text" class="input" required />
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Last Name</label>
              <input v-model="form.last_name" type="text" class="input" required />
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Email</label>
              <input v-model="form.email" type="email" class="input" required />
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Username</label>
              <input v-model="form.username" type="text" class="input" required />
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Contact</label>
              <input v-model="form.contact" type="text" class="input" required />
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Gender</label>
              <select v-model="form.gender" class="input" required>
                <option value="">Select</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
              </select>
            </div>
          </div>

          <div class="flex justify-end mt-6">
            <button type="submit" class="btn-primary">Update</button>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import AdminLayout from "@/Layouts/AdminLayout.vue";

// Extract marketer id from URL (assuming inertia/vue-router handles it)
const id = window.location.pathname.split("/").pop();
const form = ref({});

const fetchMarketer = async () => {
  try {
    const res = await axios.get(`/admin/marketers/${id}`);
    form.value = res.data;
  } catch (e) {
    alert("Failed to load marketer");
  }
};

const updateForm = async () => {
  try {
    await axios.put(`/admin/marketers/${id}`, form.value);
    alert("Marketer updated successfully!");
    window.location.href = "/admin/marketers";
  } catch (e) {
    alert("Error updating marketer");
  }
};

onMounted(fetchMarketer);
</script>

<style scoped>
.input {
  @apply w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500;
}
.btn-primary {
  @apply bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg shadow-md transition;
}
</style>
