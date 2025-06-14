<script setup>
import { ref, onMounted, nextTick, watch } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import { Head } from '@inertiajs/vue3';
import EmployeeLayout from '@/Layouts/EmployeeLayout.vue';
import Quill from 'quill';
import 'quill/dist/quill.snow.css';

const props = defineProps({
  today: String,
  tasks: Object,
  filters: Object,
  flash: Object
});

// Form for create/update
const form = useForm({
  task_content: '',
  task_id: null
});

// Filter state
const filterDate = ref(props.filters?.date || '');
const filterKeyword = ref(props.filters?.keyword || '');

// Quill refs and instances
const mainEditorRef = ref(null);
const modalEditorRef = ref(null);
let mainQuill = null;
let modalQuill = null;

// Modal state
const showEditModal = ref(false);

// Init main Quill
const initMainQuill = () => {
  mainQuill = new Quill(mainEditorRef.value, {
    theme: 'snow',
    placeholder: 'Write your task...',
    modules: {
      toolbar: [
        ['bold', 'italic', 'underline'],
        [{ list: 'ordered' }, { list: 'bullet' }],
        ['link']
      ]
    }
  });

  mainQuill.on('text-change', () => {
    form.task_content = mainQuill.root.innerHTML;
  });
};

// Init modal Quill
const initModalQuill = (content = '') => {
  modalQuill = new Quill(modalEditorRef.value, {
    theme: 'snow',
    placeholder: 'Edit your task...',
    modules: {
      toolbar: [
        ['bold', 'italic', 'underline'],
        [{ list: 'ordered' }, { list: 'bullet' }],
        ['link']
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
  initMainQuill();
});

// Handle create/update
const submit = () => {
  form.post(route('employee.tasks.save'), {
    preserveScroll: true,
    onSuccess: () => {
      form.reset();
      mainQuill.setContents([]);
      if (modalQuill) {
        modalQuill = null;
      }
      showEditModal.value = false;
    }
  });
};

// Open edit modal
const openEdit = (task) => {
  form.task_id = task.id;
  nextTick(() => {
    showEditModal.value = true;
    nextTick(() => {
      initModalQuill(task.task_content);
    });
  });
};

// Close modal
const closeModal = () => {
  if (modalQuill) modalQuill = null;
  showEditModal.value = false;
};

// Delete task
const deleteTask = (id) => {
  if (confirm('Are you sure you want to delete today’s task?')) {
    router.post(route('employee.tasks.destroy', id), {
      preserveScroll: true,
      _method: 'delete'
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
</script>

<template>
  <EmployeeLayout>
    <Head title="Daily Task Update" />

    <div class="max-w-4xl mx-auto mt-6 p-4 bg-white shadow rounded-lg">
      <h1 class="text-2xl font-bold text-orange-600 mb-4">Daily Task Update</h1>

      <div v-if="flash.success" class="mb-4 p-2 bg-green-100 text-green-700 rounded">
        {{ flash.success }}
      </div>
      <div v-if="flash.error" class="mb-4 p-2 bg-red-100 text-red-700 rounded">
        {{ flash.error }}
      </div>

      <form @submit.prevent="submit">
        <div ref="mainEditorRef" class="bg-white border border-gray-300 rounded-md p-2 min-h-[150px] shadow-sm"></div>
        <button
          type="submit"
          class="mt-4 bg-orange-500 hover:bg-orange-600 text-white font-semibold px-4 py-2 rounded"
          :disabled="form.processing"
        >
          {{ form.task_id ? 'Update Task' : 'Save Task' }}
        </button>
      </form>
    </div>

    <div class="max-w-4xl mx-auto mt-6 bg-white p-4 shadow rounded-lg space-y-4">
      <div class="flex flex-col sm:flex-row gap-2">
        <input v-model="filterDate" type="date" class="border rounded px-2 py-1 text-sm" />
        <input v-model="filterKeyword" type="text" placeholder="Search keyword" class="border rounded px-2 py-1 text-sm flex-1" />
      </div>

      <div
        v-for="t in tasks.data"
        :key="t.id"
        class="relative p-4 bg-gray-50 border rounded hover:shadow"
      >
        <div v-html="t.task_content" class="prose max-w-none"></div>
        <div class="text-sm text-gray-500 mt-1 flex justify-between">
          <span>Date: {{ t.task_date }}</span>
          <div v-if="t.task_date === today" class="space-x-2">
            <button @click="openEdit(t)" class="px-2 py-1 bg-orange-500 text-white rounded text-sm">Edit</button>
            <button @click="deleteTask(t.id)" class="px-2 py-1 bg-red-500 text-white rounded text-sm">Delete</button>
          </div>
        </div>
      </div>

      <div class="flex justify-between">
        <button
          v-if="tasks.prev_page_url"
          @click="router.get(tasks.prev_page_url, {}, { preserveScroll: true })"
          class="px-3 py-1 bg-orange-500 text-white rounded"
        >Previous</button>
        <button
          v-if="tasks.next_page_url"
          @click="router.get(tasks.next_page_url, {}, { preserveScroll: true })"
          class="px-3 py-1 bg-orange-500 text-white rounded"
        >Next</button>
      </div>
    </div>

    <div v-if="showEditModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
      <div class="bg-white rounded p-4 w-full max-w-lg">
        <h2 class="text-lg font-bold text-orange-600 mb-2">Edit Task</h2>
        <form @submit.prevent="submit">
          <div ref="modalEditorRef" class="bg-white border border-gray-300 rounded-md p-2 min-h-[150px] shadow-sm"></div>
          <div class="flex justify-end mt-4 space-x-2">
            <button @click="closeModal" type="button" class="px-3 py-1 bg-gray-300 rounded">Cancel</button>
            <button
              type="submit"
              class="px-3 py-1 bg-orange-500 text-white rounded"
              :disabled="form.processing"
            >Update</button>
          </div>
        </form>
      </div>
    </div>
  </EmployeeLayout>
</template>

<style scoped>
.prose {
  word-break: break-word;
}
</style>
