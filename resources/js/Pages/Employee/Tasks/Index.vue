<script setup>
import { ref, onMounted, nextTick, watch, computed } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import { Head } from '@inertiajs/vue3';
import EmployeeLayout from '@/Layouts/EmployeeLayout.vue';
import Quill from 'quill';
import 'quill/dist/quill.snow.css';

const props = defineProps({
  today: String,
  tasks: Object,
  filters: Object,
  flash: Object,
  statistics: Object
});

// Form for create/update
const form = useForm({
  task_content: '',
  task_id: null
});

// Filter state
const filterDate = ref(props.filters?.date || '');
const filterKeyword = ref(props.filters?.keyword || '');
const sortOrder = ref('desc'); // 'asc' or 'desc'

// UI State
const showCreateForm = ref(false);
const showEditModal = ref(false);
const viewMode = ref('cards'); // 'cards' or 'timeline'

// Quill refs and instances
const mainEditorRef = ref(null);
const modalEditorRef = ref(null);
let mainQuill = null;
let modalQuill = null;

// Computed
const sortedTasks = computed(() => {
  if (!props.tasks?.data) return [];
  const tasks = [...props.tasks.data];
  return sortOrder.value === 'asc' ? tasks.reverse() : tasks;
});

// Init main Quill
const initMainQuill = () => {
  if (!mainEditorRef.value) return;
  
  mainQuill = new Quill(mainEditorRef.value, {
    theme: 'snow',
    placeholder: 'Describe your tasks for today...',
    modules: {
      toolbar: [
        [{ header: [1, 2, false] }],
        ['bold', 'italic', 'underline', 'strike'],
        [{ list: 'ordered' }, { list: 'bullet' }],
        [{ color: [] }, { background: [] }],
        ['link'],
        ['clean']
      ]
    }
  });

  mainQuill.on('text-change', () => {
    form.task_content = mainQuill.root.innerHTML;
  });
};

// Init modal Quill
const initModalQuill = (content = '') => {
  if (!modalEditorRef.value) return;
  
  modalQuill = new Quill(modalEditorRef.value, {
    theme: 'snow',
    placeholder: 'Edit your task...',
    modules: {
      toolbar: [
        [{ header: [1, 2, false] }],
        ['bold', 'italic', 'underline', 'strike'],
        [{ list: 'ordered' }, { list: 'bullet' }],
        [{ color: [] }, { background: [] }],
        ['link'],
        ['clean']
      ]
    }
  });

  modalQuill.root.innerHTML = content;
  form.task_content = content;

  modalQuill.on('text-change', () => {
    form.task_content = modalQuill.root.innerHTML;
  });
};

onMounted(() => {
  if (showCreateForm.value) {
    nextTick(() => initMainQuill());
  }
});

// Handle create/update
const submit = () => {
  if (!form.task_content || form.task_content.trim() === '<p><br></p>') {
    alert('Please enter task content');
    return;
  }

  form.post(route('employee.tasks.save'), {
    preserveScroll: true,
    onSuccess: () => {
      form.reset();
      if (mainQuill) {
        mainQuill.setContents([]);
      }
      if (modalQuill) {
        modalQuill = null;
      }
      showEditModal.value = false;
      showCreateForm.value = false;
    }
  });
};

// Toggle create form
const toggleCreateForm = () => {
  showCreateForm.value = !showCreateForm.value;
  if (showCreateForm.value) {
    nextTick(() => initMainQuill());
  } else {
    form.reset();
    if (mainQuill) {
      mainQuill.setContents([]);
    }
  }
};

// Open edit modal
const openEdit = (task) => {
  form.task_id = task.id;
  form.task_content = task.task_content;
  nextTick(() => {
    showEditModal.value = true;
    nextTick(() => {
      initModalQuill(task.task_content);
    });
  });
};

// Close modal
const closeModal = () => {
  if (modalQuill) {
    modalQuill = null;
  }
  form.reset();
  showEditModal.value = false;
};

// Delete task
const deleteTask = (id) => {
  if (confirm('Are you sure you want to delete this task?')) {
    router.delete(route('employee.tasks.destroy', id), {
      preserveScroll: true
    });
  }
};

// Watch filters
watch([filterDate, filterKeyword], () => {
  router.get(route('employee.tasks.index'), {
    date: filterDate.value,
    keyword: filterKeyword.value
  }, {
    preserveState: true,
    replace: true
  });
});

// Reset filters
const resetFilters = () => {
  filterDate.value = '';
  filterKeyword.value = '';
};

// Helper functions
const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    weekday: 'short',
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  });
};

const isToday = (date) => {
  return date === props.today;
};

const getWordCount = (html) => {
  const text = html.replace(/<[^>]*>/g, '');
  return text.trim().split(/\s+/).length;
};
</script>

<template>
  <EmployeeLayout>
    <Head title="Daily Task Update" />

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
      <!-- Header -->
      <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">Daily Task Updates</h1>
            <p class="mt-1 text-sm text-gray-500">Track and manage your daily work tasks</p>
          </div>
          
          <div class="mt-4 sm:mt-0 flex items-center space-x-3">
            <!-- View Mode Toggle -->
            <div class="inline-flex rounded-lg border border-gray-200 p-1">
              <button
                @click="viewMode = 'cards'"
                :class="[
                  'px-3 py-1.5 rounded-md text-sm font-medium transition-colors',
                  viewMode === 'cards' 
                    ? 'bg-orange-500 text-white' 
                    : 'text-gray-600 hover:text-gray-900'
                ]"
              >
                Cards
              </button>
              <button
                @click="viewMode = 'timeline'"
                :class="[
                  'px-3 py-1.5 rounded-md text-sm font-medium transition-colors',
                  viewMode === 'timeline' 
                    ? 'bg-orange-500 text-white' 
                    : 'text-gray-600 hover:text-gray-900'
                ]"
              >
                Timeline
              </button>
            </div>

            <!-- Add Task Button -->
            <button
              @click="toggleCreateForm"
              class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-colors"
            >
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
              </svg>
              {{ showCreateForm ? 'Cancel' : 'Add Task' }}
            </button>
          </div>
        </div>
      </div>

      <!-- Flash Messages -->
      <transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="opacity-0 transform translate-y-2"
        enter-to-class="opacity-100 transform translate-y-0"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100 transform translate-y-0"
        leave-to-class="opacity-0 transform translate-y-2"
      >
        <div v-if="flash?.success" class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center">
          <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
          </svg>
          {{ flash.success }}
        </div>
      </transition>

      <transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="opacity-0 transform translate-y-2"
        enter-to-class="opacity-100 transform translate-y-0"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100 transform translate-y-0"
        leave-to-class="opacity-0 transform translate-y-2"
      >
        <div v-if="flash?.error" class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex items-center">
          <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
          </svg>
          {{ flash.error }}
        </div>
      </transition>

      <!-- Statistics -->
      <div v-if="statistics" class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-6">
        <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="flex items-center justify-center h-12 w-12 rounded-lg bg-blue-500 text-white">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                  </svg>
                </div>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Total Tasks</dt>
                  <dd class="text-2xl font-bold text-gray-900">{{ statistics.total || 0 }}</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="flex items-center justify-center h-12 w-12 rounded-lg bg-green-500 text-white">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                </div>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">This Week</dt>
                  <dd class="text-2xl font-bold text-gray-900">{{ statistics.this_week || 0 }}</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="flex items-center justify-center h-12 w-12 rounded-lg bg-purple-500 text-white">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                  </svg>
                </div>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">This Month</dt>
                  <dd class="text-2xl font-bold text-gray-900">{{ statistics.this_month || 0 }}</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="flex items-center justify-center h-12 w-12 rounded-lg bg-orange-500 text-white">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                  </svg>
                </div>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Streak</dt>
                  <dd class="text-2xl font-bold text-gray-900">{{ statistics.streak || 0 }} days</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Create Task Form -->
      <transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="opacity-0 transform -translate-y-4"
        enter-to-class="opacity-100 transform translate-y-0"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100 transform translate-y-0"
        leave-to-class="opacity-0 transform -translate-y-4"
      >
        <div v-if="showCreateForm" class="bg-white shadow-sm rounded-lg border border-gray-200 p-6 mb-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Add Today's Task</h3>
          <form @submit.prevent="submit">
            <div ref="mainEditorRef" class="bg-white border border-gray-300 rounded-lg min-h-[200px] shadow-sm mb-4"></div>
            <div class="flex items-center justify-between">
              <p class="text-sm text-gray-500">
                <span class="font-medium">Tip:</span> Use formatting tools to organize your tasks better
              </p>
              <div class="flex space-x-3">
                <button
                  type="button"
                  @click="toggleCreateForm"
                  class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-colors"
                >
                  Cancel
                </button>
                <button
                  type="submit"
                  :disabled="form.processing"
                  class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  Save Task
                </button>
              </div>
            </div>
          </form>
        </div>
      </transition>

      <!-- Filters Section -->
      <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-4 mb-6">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
          <!-- Date Filter -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Filter by Date</label>
            <input
              type="date"
              v-model="filterDate"
              class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-1 focus:ring-orange-500 focus:border-orange-500 sm:text-sm"
            />
          </div>

          <!-- Keyword Filter -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Search Keywords</label>
            <div class="relative">
              <input
                type="text"
                v-model="filterKeyword"
                placeholder="Search in tasks..."
                class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-orange-500 focus:border-orange-500 sm:text-sm"
              />
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/>
                </svg>
              </div>
            </div>
          </div>

          <!-- Sort Order -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
            <select
              v-model="sortOrder"
              class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-1 focus:ring-orange-500 focus:border-orange-500 sm:text-sm"
            >
              <option value="desc">Newest First</option>
              <option value="asc">Oldest First</option>
            </select>
          </div>
        </div>

        <!-- Filter Actions -->
        <div class="mt-4 flex justify-end">
          <button
            @click="resetFilters"
            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-colors"
          >
            Reset Filters
          </button>
        </div>
      </div>

      <!-- Tasks Display -->
      <div v-if="viewMode === 'cards'">
        <div v-if="sortedTasks.length" class="grid grid-cols-1 gap-6 lg:grid-cols-2">
          <div
            v-for="task in sortedTasks"
            :key="task.id"
            :class="[
              'bg-white rounded-lg shadow-sm border-2 p-6 hover:shadow-md transition-shadow',
              isToday(task.task_date) ? 'border-orange-500 ring-2 ring-orange-200' : 'border-gray-200'
            ]"
          >
            <!-- Header -->
            <div class="flex items-center justify-between mb-4">
              <div class="flex items-center space-x-3">
                <div class="flex-shrink-0">
                  <div :class="[
                    'h-10 w-10 rounded-full flex items-center justify-center',
                    isToday(task.task_date) ? 'bg-orange-100' : 'bg-gray-100'
                  ]">
                    <svg :class="[
                      'h-5 w-5',
                      isToday(task.task_date) ? 'text-orange-600' : 'text-gray-600'
                    ]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                  </div>
                </div>
                <div>
                  <p class="text-sm font-medium text-gray-900">{{ formatDate(task.task_date) }}</p>
                  <p class="text-xs text-gray-500">{{ getWordCount(task.task_content) }} words</p>
                </div>
              </div>
              
              <!-- Today Badge -->
              <span v-if="isToday(task.task_date)" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800 border border-orange-200">
                Today
              </span>
            </div>

            <!-- Task Content -->
            <div v-html="task.task_content" class="prose max-w-none text-gray-700 mb-4"></div>

            <!-- Actions -->
            <div v-if="isToday(task.task_date)" class="flex items-center space-x-3 pt-4 border-t border-gray-200">
              <button
                @click="openEdit(task)"
                class="inline-flex items-center text-orange-600 hover:text-orange-800 text-sm font-medium"
              >
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit
              </button>
              <button
                @click="deleteTask(task.id)"
                class="inline-flex items-center text-red-600 hover:text-red-800 text-sm font-medium"
              >
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                Delete
              </button>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="bg-white shadow-sm rounded-lg border border-gray-200 p-12 text-center">
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
          </svg>
          <h3 class="mt-2 text-sm font-medium text-gray-900">No tasks found</h3>
          <p class="mt-1 text-sm text-gray-500">Get started by adding your first task.</p>
          <div class="mt-6">
            <button
              @click="toggleCreateForm"
              class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500"
            >
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
              </svg>
              Add Task
            </button>
          </div>
        </div>
      </div>

      <!-- Timeline View -->
      <div v-if="viewMode === 'timeline'" class="relative">
        <!-- Timeline Line -->
        <div class="absolute left-8 top-0 bottom-0 w-0.5 bg-gray-200"></div>
        
        <div class="space-y-6">
          <div
            v-for="(task, index) in sortedTasks"
            :key="task.id"
            class="relative pl-20"
          >
            <!-- Timeline Dot -->
            <div :class="[
              'absolute left-5 w-6 h-6 rounded-full border-4',
              isToday(task.task_date) ? 'bg-orange-500 border-orange-200' : 'bg-white border-gray-300'
            ]"></div>

            <!-- Task Card -->
            <div :class="[
              'bg-white rounded-lg shadow-sm border p-6',
              isToday(task.task_date) ? 'border-orange-200' : 'border-gray-200'
            ]">
              <div class="flex items-center justify-between mb-3">
                <p class="text-sm font-medium text-gray-900">{{ formatDate(task.task_date) }}</p>
                <span v-if="isToday(task.task_date)" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                  Today
                </span>
              </div>
              
              <div v-html="task.task_content" class="prose max-w-none text-gray-700"></div>
              
              <!-- Actions for today's task -->
              <div v-if="isToday(task.task_date)" class="flex items-center space-x-3 mt-4 pt-4 border-t border-gray-200">
                <button
                  @click="openEdit(task)"
                  class="inline-flex items-center text-orange-600 hover:text-orange-800 text-sm font-medium"
                >
                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                  </svg>
                  Edit
                </button>
                <button
                  @click="deleteTask(task.id)"
                  class="inline-flex items-center text-red-600 hover:text-red-800 text-sm font-medium"
                >
                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                  </svg>
                  Delete
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="tasks.data?.length > 0 && (tasks.next_page_url || tasks.prev_page_url)" class="mt-6">
        <div class="flex items-center justify-between">
          <div class="flex-1 flex justify-between sm:hidden">
            <button
              v-if="tasks.prev_page_url"
              @click="router.get(tasks.prev_page_url, {}, { preserveScroll: true })"
              class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
            >
              Previous
            </button>
            <button
              v-if="tasks.next_page_url"
              @click="router.get(tasks.next_page_url, {}, { preserveScroll: true })"
              class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
            >
              Next
            </button>
          </div>
          <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
              <p class="text-sm text-gray-700">
                Showing
                <span class="font-medium">{{ tasks.from || 0 }}</span>
                to
                <span class="font-medium">{{ tasks.to || 0 }}</span>
                of
                <span class="font-medium">{{ tasks.total || 0 }}</span>
                results
              </p>
            </div>
            <div>
              <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                <button
                  v-if="tasks.prev_page_url"
                  @click="router.get(tasks.prev_page_url, {}, { preserveScroll: true })"
                  class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                >
                  <span class="sr-only">Previous</span>
                  <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/>
                  </svg>
                </button>
                <button
                  v-if="tasks.next_page_url"
                  @click="router.get(tasks.next_page_url, {}, { preserveScroll: true })"
                  class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                >
                  <span class="sr-only">Next</span>
                  <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                  </svg>
                </button>
              </nav>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Edit Modal -->
    <transition
      enter-active-class="transition ease-out duration-300"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition ease-in duration-200"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div v-if="showEditModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50 px-4">
        <transition
          enter-active-class="transition ease-out duration-300"
          enter-from-class="opacity-0 transform scale-95"
          enter-to-class="opacity-100 transform scale-100"
          leave-active-class="transition ease-in duration-200"
          leave-from-class="opacity-100 transform scale-100"
          leave-to-class="opacity-0 transform scale-95"
        >
          <div v-if="showEditModal" class="bg-white rounded-lg p-6 w-full max-w-3xl max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-4">
              <h2 class="text-lg font-bold text-gray-900">Edit Task</h2>
              <button @click="closeModal" class="text-gray-400 hover:text-gray-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
              </button>
            </div>
            
            <form @submit.prevent="submit">
              <div ref="modalEditorRef" class="bg-white border border-gray-300 rounded-lg min-h-[200px] shadow-sm mb-4"></div>
              
              <div class="flex justify-end space-x-3">
                <button
                  @click="closeModal"
                  type="button"
                  class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500"
                >
                  Cancel
                </button>
                <button
                  type="submit"
                  :disabled="form.processing"
                  class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  Update Task
                </button>
              </div>
            </form>
          </div>
        </transition>
      </div>
    </transition>
  </EmployeeLayout>
</template>

<style scoped>
.prose {
  word-break: break-word;
}

.prose :deep(h1) {
  @apply text-xl font-bold mb-2;
}

.prose :deep(h2) {
  @apply text-lg font-bold mb-2;
}

.prose :deep(ul) {
  @apply list-disc pl-5 mb-2;
}

.prose :deep(ol) {
  @apply list-decimal pl-5 mb-2;
}

.prose :deep(a) {
  @apply text-orange-600 hover:text-orange-800 underline;
}

@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

.animate-spin {
  animation: spin 1s linear infinite;
}
</style>