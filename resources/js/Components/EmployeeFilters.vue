<template>
  <div class="bg-white shadow-sm rounded-lg mb-6">
    <div class="px-6 py-4">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Search Input -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
          <div class="relative">
            <input
              v-model="localFilters.name"
              @keyup.enter="applyFilters"
              type="text"
              placeholder="Name, email or employee code"
              class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
            />
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/>
              </svg>
            </div>
          </div>
        </div>

        <!-- Department Filter -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
          <select
            v-model="localFilters.department_id"
            @change="applyFilters"
            class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
          >
            <option value="">All Departments</option>
            <option v-for="dept in departments" :key="dept.id" :value="dept.id">
              {{ dept.name }}
            </option>
          </select>
        </div>

        <!-- Designation Filter -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Designation</label>
          <select
            v-model="localFilters.designation_id"
            @change="applyFilters"
            class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
          >
            <option value="">All Designations</option>
            <option v-for="desig in designations" :key="desig.id" :value="desig.id">
              {{ desig.name }}
            </option>
          </select>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-end gap-2">
          <button
            @click="applyFilters"
            class="flex-1 inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
          >
            Apply Filters
          </button>
          <button
            @click="resetFilters"
            class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
          >
            Reset
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'EmployeeFilters',
  
  props: {
    filters: {
      type: Object,
      default: () => ({})
    },
    departments: {
      type: Array,
      required: true
    },
    designations: {
      type: Array,
      required: true
    }
  },
  
  data() {
    return {
      localFilters: {
        name: this.filters?.name || '',
        department_id: this.filters?.department_id || '',
        designation_id: this.filters?.designation_id || '',
      }
    };
  },
  
  methods: {
    applyFilters() {
      this.$emit('apply-filters', this.localFilters);
    },
    
    resetFilters() {
      this.localFilters = {
        name: '',
        department_id: '',
        designation_id: '',
      };
      this.$emit('reset-filters');
    }
  }
};
</script>