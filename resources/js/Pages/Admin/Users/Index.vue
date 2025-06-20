<template>
  <AdminLayout>
    <div class="p-4">
      <!-- HEADER -->
      <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-orange-600">Admin / Manager Users</h1>
        <button
          @click="openModal()"
          class="bg-gradient-to-r from-orange-400 to-orange-600 hover:from-orange-500 hover:to-orange-700 text-white px-5 py-2 rounded-lg shadow-lg transition"
        >
          + New User
        </button>
      </div>

      <!-- FLASH -->
      <div
        v-if="$page.props.flash.success"
        class="mb-4 p-4 border-l-4 border-green-600 bg-green-100 text-green-800 rounded"
      >
        {{ $page.props.flash.success }}
      </div>

      <!-- TABLE -->
      <div class="overflow-x-auto bg-white rounded-lg shadow-md">
        <table class="min-w-full text-sm text-left">
          <thead class="bg-gradient-to-r from-orange-100 to-orange-200 text-orange-800 uppercase tracking-wide">
            <tr>
              <th class="px-4 py-3">Name</th>
              <th class="px-4 py-3">Email</th>
              <th class="px-4 py-3">Role</th>
              <th class="px-4 py-3">Designation</th>
              <th class="px-4 py-3 text-center">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-orange-100">
            <tr
              v-for="user in users"
              :key="user.id"
              class="hover:bg-orange-50 transition"
            >
              <td class="px-4 py-2">{{ user.name }}</td>
              <td class="px-4 py-2">{{ user.email }}</td>
              <td class="px-4 py-2 capitalize">{{ user.role }}</td>
              <td class="px-4 py-2">{{ user.designation?.name || '—' }}</td>
              <td class="px-4 py-2 text-center space-x-2">
                <button
                  @click="openModal(user)"
                  class="text-blue-600 hover:underline"
                >Edit</button>
                <button
                  @click="destroy(user.id)"
                  class="text-red-600 hover:underline"
                >Delete</button>
              </td>
            </tr>
            <tr v-if="!users.length">
              <td colspan="5" class="text-center py-4 text-gray-400">No users found</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- MODAL -->
      <div
        v-if="showModal"
        class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50"
      >
        <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6">
          <h2 class="text-xl font-semibold mb-4 text-orange-600">
            {{ form.id ? 'Edit User' : 'Add New User' }}
          </h2>

          <form @submit.prevent="submit" class="space-y-4">
            <div>
              <label class="block mb-1 font-medium">Name</label>
              <input
                v-model="form.name"
                type="text"
                class="w-full rounded border-gray-300 focus:border-orange-500 focus:ring focus:ring-orange-200"
              />
              <p v-if="form.errors.name" class="text-sm text-red-600">{{ form.errors.name }}</p>
            </div>

            <div>
              <label class="block mb-1 font-medium">Email</label>
              <input
                v-model="form.email"
                type="email"
                class="w-full rounded border-gray-300 focus:border-orange-500 focus:ring focus:ring-orange-200"
              />
              <p v-if="form.errors.email" class="text-sm text-red-600">{{ form.errors.email }}</p>
            </div>

            <div>
              <label class="block mb-1 font-medium">Role</label>
              <select
                v-model="form.role"
                class="w-full rounded border-gray-300 focus:border-orange-500 focus:ring focus:ring-orange-200"
              >
                <option value="admin">Admin</option>
                <option value="manager">Manager</option>
              </select>
              <p v-if="form.errors.role" class="text-sm text-red-600">{{ form.errors.role }}</p>
            </div>

            <div>
              <label class="block mb-1 font-medium">Designation</label>
              <select
                v-model="form.designation_id"
                class="w-full rounded border-gray-300 focus:border-orange-500 focus:ring focus:ring-orange-200"
              >
                <option disabled value="">Select...</option>
                <option v-for="d in designations" :key="d.id" :value="d.id">{{ d.name }}</option>
              </select>
              <p v-if="form.errors.designation_id" class="text-sm text-red-600">{{ form.errors.designation_id }}</p>
            </div>

            <div>
              <label class="block mb-1 font-medium">Password</label>
              <input
                v-model="form.password"
                type="password"
                :placeholder="form.id ? 'Leave blank to keep current' : ''"
                class="w-full rounded border-gray-300 focus:border-orange-500 focus:ring focus:ring-orange-200"
              />
              <p v-if="form.errors.password" class="text-sm text-red-600">{{ form.errors.password }}</p>
            </div>

            <div v-if="!form.id">
              <label class="block mb-1 font-medium">Confirm Password</label>
              <input
                v-model="form.password_confirmation"
                type="password"
                class="w-full rounded border-gray-300 focus:border-orange-500 focus:ring focus:ring-orange-200"
              />
              <p v-if="form.errors.password_confirmation" class="text-sm text-red-600">{{ form.errors.password_confirmation }}</p>
            </div>

            <div class="flex justify-end space-x-2 mt-4">
              <button
                type="button"
                @click="closeModal"
                class="px-3 py-2 bg-gray-200 rounded hover:bg-gray-300"
              >
                Cancel
              </button>
              <button
                type="submit"
                class="px-4 py-2 bg-gradient-to-r from-orange-400 to-orange-600 text-white rounded hover:from-orange-500 hover:to-orange-700 shadow"
              >
                {{ form.id ? 'Update' : 'Create' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
  users: Array,
  designations: Array,
});

const showModal = ref(false);

const form = useForm({
  id: null,
  name: '',
  email: '',
  role: 'admin',
  designation_id: '',
  password: '',
  password_confirmation: '',
});

function openModal(user = null) {
  form.reset();
  if (user) {
    form.id = user.id;
    form.name = user.name;
    form.email = user.email;
    form.role = user.role;
    form.designation_id = user.designation_id;
    form.password = '';
    form.password_confirmation = '';
  }
  showModal.value = true;
}

function closeModal() {
  form.reset();
  showModal.value = false;
}

function submit() {
  if (form.id) {
    form._method = 'put';
    form.post(route('admin.users.update', form.id), {
      onSuccess: closeModal,
      preserveScroll: true,
    });
  } else {
    form.post(route('admin.users.store'), {
      onSuccess: closeModal,
      preserveScroll: true,
    });
  }
}

function destroy(id) {
  if (confirm('Are you sure?')) {
    const delForm = useForm({});
    delForm._method = 'delete';
    delForm.post(route('admin.users.destroy', id), {
      preserveScroll: true,
    });
  }
}
</script>
