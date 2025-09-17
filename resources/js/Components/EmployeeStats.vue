<template>
  <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
    <!-- Total Employees -->
    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
      <div class="p-5">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
              <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
              </svg>
            </div>
          </div>
          <div class="ml-5 w-0 flex-1">
            <dl>
              <dt class="text-sm font-medium text-gray-500 truncate">Total Employees</dt>
              <dd class="text-2xl font-semibold text-gray-900">{{ employees.total || 0 }}</dd>
            </dl>
          </div>
        </div>
      </div>
    </div>

    <!-- Active Employees -->
    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
      <div class="p-5">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
              <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
              </svg>
            </div>
          </div>
          <div class="ml-5 w-0 flex-1">
            <dl>
              <dt class="text-sm font-medium text-gray-500 truncate">Active</dt>
              <dd class="text-2xl font-semibold text-gray-900">{{ activeEmployeesCount }}</dd>
            </dl>
          </div>
        </div>
      </div>
    </div>

    <!-- New This Month -->
    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
      <div class="p-5">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
              <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
              </svg>
            </div>
          </div>
          <div class="ml-5 w-0 flex-1">
            <dl>
              <dt class="text-sm font-medium text-gray-500 truncate">New This Month</dt>
              <dd class="text-2xl font-semibold text-gray-900">{{ newEmployeesCount }}</dd>
            </dl>
          </div>
        </div>
      </div>
    </div>

    <!-- Total Departments -->
    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
      <div class="p-5">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
              <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
          </div>
          <div class="ml-5 w-0 flex-1">
            <dl>
              <dt class="text-sm font-medium text-gray-500 truncate">Departments</dt>
              <dd class="text-2xl font-semibold text-gray-900">{{ departments.length }}</dd>
            </dl>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'EmployeeStats',
  
  props: {
    employees: {
      type: Object,
      required: true
    },
    departments: {
      type: Array,
      required: true
    }
  },
  
  computed: {
    activeEmployeesCount() {
      if (!this.employees.data) return 0;
      return this.employees.data.filter(emp => emp.status !== 'inactive').length;
    },
    
    newEmployeesCount() {
      if (!this.employees.data) return 0;
      const oneMonthAgo = new Date();
      oneMonthAgo.setMonth(oneMonthAgo.getMonth() - 1);
      
      return this.employees.data.filter(emp => {
        if (!emp.created_at) return false;
        const createdDate = new Date(emp.created_at);
        return createdDate >= oneMonthAgo;
      }).length;
    }
  }
};
</script>