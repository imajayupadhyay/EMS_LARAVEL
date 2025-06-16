<script setup>
import { ref } from 'vue';
import { router, Head } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
  tasks: Object,
  filters: Object
});

const selectedDate = ref(props.filters.date);
const employeeName = ref(props.filters.employee || '');
const selectedTask = ref(null);

const applyFilters = () => {
  router.get(route('admin.tasks.index'), {
    date: selectedDate.value,
    employee: employeeName.value
  }, { preserveScroll: true });
};

const openPopup = (task) => {
  selectedTask.value = task;
};

const closePopup = () => {
  selectedTask.value = null;
};
</script>

<template>
  <AdminLayout>
    <Head title="All Employee Tasks" />
    <div class="max-w-7xl mx-auto p-4">
      <h1 class="text-3xl font-extrabold text-orange-600 mb-6 animate-slide-in">All Employee Tasks</h1>

      <!-- Filters -->
      <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 shadow mb-6 animate-fade-in">
        <div class="flex flex-col md:flex-row gap-3">
          <div class="flex items-center gap-2">
            <label class="font-medium">Date:</label>
            <input type="date" v-model="selectedDate" @change="applyFilters"
              class="border rounded px-2 py-1 shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-400" />
          </div>
          <div class="flex items-center gap-2">
            <label class="font-medium">Employee:</label>
            <input type="text" v-model="employeeName" @input="applyFilters" placeholder="Search by name"
              class="border rounded px-2 py-1 shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-400" />
          </div>
        </div>
      </div>

      <!-- Task Cards -->
      <div v-if="props.tasks.data.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div v-for="task in props.tasks.data" :key="task.id" @click="openPopup(task)"
          class="cursor-pointer border rounded-lg p-4 bg-white shadow-lg hover:shadow-2xl transition transform hover:scale-105 animate-fade-in">
          <div class="inline-block bg-orange-100 text-orange-700 text-xs font-semibold px-2 py-0.5 rounded-full mb-1">
            {{ task.task_date }}
          </div>
          <div class="font-semibold text-lg text-gray-800 mb-1">
            {{ task.employee ? (task.employee.first_name + ' ' + task.employee.last_name) : 'N/A' }}
          </div>
          <div class="truncate text-gray-600 text-sm" v-html="task.task_content"></div>
        </div>
      </div>
      <p v-else class="text-gray-500 text-center mt-6">No tasks found for selected filters.</p>

      <!-- Pagination -->
      <div class="mt-6 flex justify-center gap-4 animate-fade-in">
        <button v-if="props.tasks.prev_page_url" @click="router.get(props.tasks.prev_page_url, {}, { preserveScroll: true })"
          class="px-4 py-2 bg-orange-500 text-white rounded shadow hover:bg-orange-600">Previous</button>
        <button v-if="props.tasks.next_page_url" @click="router.get(props.tasks.next_page_url, {}, { preserveScroll: true })"
          class="px-4 py-2 bg-orange-500 text-white rounded shadow hover:bg-orange-600">Next</button>
      </div>

      <!-- Popup -->
      <transition name="fade">
        <div v-if="selectedTask" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
          <div class="bg-white rounded-lg max-w-lg w-full p-6 shadow-2xl relative transform animate-scale-in">
            <button @click="closePopup"
              class="absolute top-2 right-2 bg-gray-200 hover:bg-gray-300 text-gray-700 hover:text-black rounded-full w-8 h-8 flex items-center justify-center shadow">✕</button>
            <h2 class="text-xl font-bold text-orange-600 mb-3">
              {{ selectedTask.employee ? (selectedTask.employee.first_name + ' ' + selectedTask.employee.last_name) : 'N/A' }}'s Task
            </h2>
            <div class="inline-block bg-orange-100 text-orange-700 text-xs font-semibold px-3 py-1 rounded-full mb-4">
              {{ selectedTask.task_date }}
            </div>
            <div v-html="selectedTask.task_content" class="prose max-w-none text-gray-700"></div>
          </div>
        </div>
      </transition>
    </div>
  </AdminLayout>
</template>

<style scoped>
@keyframes fade-in {
  from { opacity: 0; }
  to { opacity: 1; }
}
@keyframes slide-in {
  from { transform: translateY(-10px); opacity: 0; }
  to { transform: translateY(0); opacity: 1; }
}
@keyframes scale-in {
  from { transform: scale(0.95); opacity: 0; }
  to { transform: scale(1); opacity: 1; }
}
.animate-fade-in {
  animation: fade-in 0.5s ease;
}
.animate-slide-in {
  animation: slide-in 0.5s ease;
}
.animate-scale-in {
  animation: scale-in 0.3s ease;
}
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>
