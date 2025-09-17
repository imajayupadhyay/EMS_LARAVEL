<template>
  <div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Page Header -->
      <div class="mb-8">
        <div class="md:flex md:items-center md:justify-between">
          <div class="flex-1 min-w-0">
            <h2 class="text-3xl font-bold leading-7 text-gray-900 sm:truncate sm:text-4xl">
              Employee Management
            </h2>
            <p class="mt-1 text-sm text-gray-500">
              Manage your team members and their information
            </p>
          </div>
          <div class="mt-4 flex md:mt-0 md:ml-4 gap-3">
            <button
              @click="exportEmployees"
              :disabled="loading"
              class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50"
            >
              <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"/>
              </svg>
              {{ loading ? 'Exporting...' : 'Export CSV' }}
            </button>
            <button
              v-if="selectedEmployees.length > 0"
              @click="showBulkDeleteModal = true"
              class="inline-flex items-center px-4 py-2 border border-red-300 rounded-lg shadow-sm text-sm font-medium text-red-700 bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
            >
              <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
              </svg>
              Delete Selected ({{ selectedEmployees.length }})
            </button>
            <Link
              v-if="canCreate"
              :href="route('manager.employees.create')"
              class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200"
            >
              <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
              </svg>
              Add New Employee
            </Link>
          </div>
        </div>
      </div>

      <!-- Stats Cards -->
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

      <!-- Filters -->
      <div class="bg-white shadow-sm rounded-lg mb-6">
        <div class="px-6 py-4">
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Search Input -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
              <div class="relative">
                <input
                  v-model="filtersLocal.name"
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
                v-model="filtersLocal.department_id"
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
                v-model="filtersLocal.designation_id"
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

      <!-- Employee Table -->
      <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <!-- Desktop Table -->
        <div class="hidden lg:block overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col" class="px-6 py-3 text-left">
                  <input
                    type="checkbox"
                    :checked="selectedEmployees.length === employees.data.length && employees.data.length > 0"
                    @change="toggleSelectAll"
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                  />
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Designation</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">DOJ</th>
                <th scope="col" class="relative px-6 py-3">
                  <span class="sr-only">Actions</span>
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="(emp, idx) in employees.data" :key="emp.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">
                  <input
                    type="checkbox"
                    :value="emp.id"
                    v-model="selectedEmployees"
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                  />
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ (employees.current_page - 1) * employees.per_page + idx + 1 }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10">
                      <div class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center">
                        <span class="text-sm font-medium text-white">
                          {{ getInitials(emp.first_name, emp.last_name) }}
                        </span>
                      </div>
                    </div>
                    <div class="ml-4">
                      <div class="text-sm font-medium text-gray-900">
                        {{ emp.first_name }} {{ emp.last_name }}
                      </div>
                      <div class="text-sm text-gray-500">ID: {{ emp.id }}</div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-900">{{ emp.email }}</div>
                  <div class="text-sm text-gray-500">{{ emp.contact }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                    {{ emp.department?.name || '-' }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ emp.designation?.name || '-' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ formatDate(emp.doj) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <div class="flex items-center justify-end space-x-2">
                    <button
                      @click="view(emp.id)"
                      class="text-blue-600 hover:text-blue-900 p-1 rounded hover:bg-blue-50"
                      title="View Details"
                    >
                      <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                      </svg>
                    </button>
                    <Link
                      v-if="canEdit"
                      :href="route('manager.employees.edit', emp.id)"
                      class="text-indigo-600 hover:text-indigo-900 p-1 rounded hover:bg-indigo-50"
                      title="Edit Employee"
                    >
                      <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                      </svg>
                    </Link>
                    <button
                      v-if="canDelete"
                      @click="confirmDelete(emp)"
                      class="text-red-600 hover:text-red-900 p-1 rounded hover:bg-red-50"
                      title="Delete Employee"
                    >
                      <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>

              <!-- Empty State -->
              <tr v-if="employees.data.length === 0">
                <td colspan="8" class="px-6 py-12 text-center">
                  <div class="flex flex-col items-center justify-center">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No employees found</h3>
                    <p class="mt-1 text-sm text-gray-500">Get started by adding a new employee.</p>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Mobile Cards -->
        <div class="lg:hidden">
          <div v-if="employees.data.length === 0" class="p-8 text-center">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No employees found</h3>
            <p class="text-gray-500 mb-6">Get started by adding your first employee.</p>
          </div>

          <div v-else class="space-y-4 p-4">
            <div
              v-for="emp in employees.data"
              :key="emp.id"
              class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow"
            >
              <div class="p-4">
                <div class="flex items-center justify-between mb-3">
                  <div class="flex items-center space-x-3">
                    <input
                      type="checkbox"
                      :value="emp.id"
                      v-model="selectedEmployees"
                      class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                    />
                    <div class="flex-shrink-0 h-10 w-10">
                      <div class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center">
                        <span class="text-sm font-medium text-white">
                          {{ getInitials(emp.first_name, emp.last_name) }}
                        </span>
                      </div>
                    </div>
                    <div>
                      <h3 class="text-lg font-medium text-gray-900">
                        {{ emp.first_name }} {{ emp.last_name }}
                      </h3>
                      <p class="text-sm text-gray-500">ID: {{ emp.id }}</p>
                    </div>
                  </div>
                </div>

                <div class="grid grid-cols-1 gap-2 text-sm text-gray-700 mb-4">
                  <div><span class="font-medium">Email:</span> {{ emp.email }}</div>
                  <div><span class="font-medium">Contact:</span> {{ emp.contact }}</div>
                  <div><span class="font-medium">Department:</span> {{ emp.department?.name || '-' }}</div>
                  <div><span class="font-medium">Designation:</span> {{ emp.designation?.name || '-' }}</div>
                  <div><span class="font-medium">Joined:</span> {{ formatDate(emp.doj) }}</div>
                </div>

                <div class="flex justify-between items-center pt-3 border-t border-gray-100">
                  <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                    {{ emp.department?.name || 'No Department' }}
                  </span>
                  <div class="flex items-center space-x-3">
                    <button
                      @click="view(emp.id)"
                      class="text-blue-600 hover:text-blue-800 p-2 rounded-full hover:bg-blue-50"
                      title="View Details"
                    >
                      <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                      </svg>
                    </button>
                    <Link
                      v-if="canEdit"
                      :href="route('manager.employees.edit', emp.id)"
                      class="text-indigo-600 hover:text-indigo-800 p-2 rounded-full hover:bg-indigo-50"
                      title="Edit Employee"
                    >
                      <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                      </svg>
                    </Link>
                    <button
                      v-if="canDelete"
                      @click="confirmDelete(emp)"
                      class="text-red-600 hover:text-red-800 p-2 rounded-full hover:bg-red-50"
                      title="Delete Employee"
                    >
                      <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                      </svg>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="employees && employees.last_page > 1" class="mt-6">
        <Pagination :data="employees" @pagination-change-page="gotoPage" />
      </div>

      <!-- Employee Details Modal -->
      <Transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="opacity-0 scale-95"
        enter-to-class="opacity-100 scale-100"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100 scale-100"
        leave-to-class="opacity-0 scale-95"
      >
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
          <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
              <h3 class="text-lg font-semibold text-gray-900">Employee Details</h3>
              <button
                @click="closeModal"
                class="text-gray-400 hover:text-gray-600 p-1 rounded-full hover:bg-gray-100"
              >
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
              </button>
            </div>

            <div class="px-6 py-4 overflow-y-auto max-h-[calc(90vh-8rem)]">
              <div v-if="modalLoading" class="flex items-center justify-center py-12">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
              </div>

              <div v-else-if="detail && detail.id" class="space-y-6">
                <!-- Employee Header -->
                <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg">
                  <div class="flex-shrink-0 h-16 w-16">
                    <div class="h-16 w-16 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center">
                      <span class="text-xl font-medium text-white">
                        {{ getInitials(detail.first_name, detail.last_name) }}
                      </span>
                    </div>
                  </div>
                  <div>
                    <h4 class="text-xl font-semibold text-gray-900">{{ detail.full_name }}</h4>
                    <p class="text-gray-600">Employee ID: {{ detail.id }}</p>
                    <div class="flex items-center space-x-4 mt-2">
                      <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                        {{ detail.department?.name || 'No Department' }}
                      </span>
                      <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                        {{ detail.designation?.name || 'No Designation' }}
                      </span>
                    </div>
                  </div>
                </div>

                <!-- Personal & Work Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div>
                    <h5 class="text-lg font-medium text-gray-900 mb-3">Personal Information</h5>
                    <div class="space-y-3">
                      <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="font-medium text-gray-600">Email:</span>
                        <span class="text-gray-900">{{ detail.email }}</span>
                      </div>
                      <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="font-medium text-gray-600">Contact:</span>
                        <span class="text-gray-900">{{ detail.contact }}</span>
                      </div>
                      <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="font-medium text-gray-600">Emergency Contact:</span>
                        <span class="text-gray-900">{{ detail.emergency_contact || '-' }}</span>
                      </div>
                      <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="font-medium text-gray-600">Gender:</span>
                        <span class="text-gray-900 capitalize">{{ detail.gender || '-' }}</span>
                      </div>
                      <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="font-medium text-gray-600">Date of Birth:</span>
                        <span class="text-gray-900">{{ formatDate(detail.dob) }}</span>
                      </div>
                    </div>
                  </div>

                  <div>
                    <h5 class="text-lg font-medium text-gray-900 mb-3">Work Information</h5>
                    <div class="space-y-3">
                      <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="font-medium text-gray-600">Date of Joining:</span>
                        <span class="text-gray-900">{{ formatDate(detail.doj) }}</span>
                      </div>
                      <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="font-medium text-gray-600">Department:</span>
                        <span class="text-gray-900">{{ detail.department?.name || '-' }}</span>
                      </div>
                      <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="font-medium text-gray-600">Designation:</span>
                        <span class="text-gray-900">{{ detail.designation?.name || '-' }}</span>
                      </div>
                      <!-- <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="font-medium text-gray-600">Pay Scale:</span>
                        <span class="text-gray-900">{{ detail.pay_scale || '-' }}</span>
                      </div> -->
                      <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="font-medium text-gray-600">Work Location:</span>
                        <span class="text-gray-900">{{ detail.work_location || '-' }}</span>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Address Information -->
                <div>
                  <h5 class="text-lg font-medium text-gray-900 mb-3">Address Information</h5>
                  <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-gray-900">{{ detail.address || 'No address provided' }}</p>
                    <p v-if="detail.zip" class="text-gray-600 mt-1">ZIP: {{ detail.zip }}</p>
                  </div>
                </div>
              </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 flex justify-between items-center">
              <div class="flex space-x-3">
                <Link
                  v-if="canEdit && detail.id"
                  :href="route('manager.employees.edit', detail.id)"
                  class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                  Edit Employee
                </Link>
              </div>
              <button
                @click="closeModal"
                class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50"
              >
                Close
              </button>
            </div>
          </div>
        </div>
      </Transition>

      <!-- Delete Confirmation Modal -->
      <Transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="opacity-0 scale-95"
        enter-to-class="opacity-100 scale-100"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100 scale-100"
        leave-to-class="opacity-0 scale-95"
      >
        <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
          <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
            <div class="px-6 py-4">
              <div class="flex items-center mb-4">
                <div class="flex-shrink-0">
                  <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 15.5c-.77.833.192 2.5 1.732 2.5z"/>
                  </svg>
                </div>
                <div class="ml-3">
                  <h3 class="text-lg font-medium text-gray-900">Delete Employee</h3>
                </div>
              </div>
              <div class="mb-6">
                <p class="text-sm text-gray-600">
                  Are you sure you want to delete <strong>{{ deleteEmployee?.first_name }} {{ deleteEmployee?.last_name }}</strong>? 
                  This action cannot be undone.
                </p>
              </div>
              <div class="flex justify-end space-x-3">
                <button
                  @click="showDeleteModal = false"
                  class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50"
                >
                  Cancel
                </button>
                <button
                  @click="deleteEmployeeConfirmed"
                  :disabled="deleting"
                  class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-red-600 hover:bg-red-700 disabled:opacity-50"
                >
                  {{ deleting ? 'Deleting...' : 'Delete' }}
                </button>
              </div>
            </div>
          </div>
        </div>
      </Transition>

      <!-- Bulk Delete Modal -->
      <Transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="opacity-0 scale-95"
        enter-to-class="opacity-100 scale-100"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100 scale-100"
        leave-to-class="opacity-0 scale-95"
      >
        <div v-if="showBulkDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
          <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
            <div class="px-6 py-4">
              <div class="flex items-center mb-4">
                <div class="flex-shrink-0">
                  <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 15.5c-.77.833.192 2.5 1.732 2.5z"/>
                  </svg>
                </div>
                <div class="ml-3">
                  <h3 class="text-lg font-medium text-gray-900">Bulk Delete Employees</h3>
                </div>
              </div>
              <div class="mb-6">
                <p class="text-sm text-gray-600">
                  Are you sure you want to delete {{ selectedEmployees.length }} selected employees? 
                  This action cannot be undone.
                </p>
              </div>
              <div class="flex justify-end space-x-3">
                <button
                  @click="showBulkDeleteModal = false"
                  class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50"
                >
                  Cancel
                </button>
                <button
                  @click="bulkDeleteConfirmed"
                  :disabled="bulkDeleting"
                  class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-red-600 hover:bg-red-700 disabled:opacity-50"
                >
                  {{ bulkDeleting ? 'Deleting...' : `Delete ${selectedEmployees.length} Employees` }}
                </button>
              </div>
            </div>
          </div>
        </div>
      </Transition>
    </div>
  </div>
</template>

<script>
import ManagerLayout from '@/Layouts/ManagerLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import { Link } from '@inertiajs/vue3';

export default {
  layout: ManagerLayout,
  
  components: {
    Pagination,
    Link,
  },
  
  props: {
    employees: Object,
    departments: Array,
    designations: Array,
    filters: Object,
    canCreate: { type: Boolean, default: false },
    canEdit: { type: Boolean, default: false },
    canDelete: { type: Boolean, default: false },
  },
  
  data() {
    return {
      filtersLocal: {
        name: this.filters?.name || '',
        department_id: this.filters?.department_id || '',
        designation_id: this.filters?.designation_id || '',
      },
      selectedEmployees: [],
      detail: {},
      deleteEmployee: null,
      showModal: false,
      showDeleteModal: false,
      showBulkDeleteModal: false,
      modalLoading: false,
      deleting: false,
      bulkDeleting: false,
      loading: false,
      csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
    };
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
  },
  
  methods: {
    applyFilters() {
      const params = new URLSearchParams();
      Object.keys(this.filtersLocal).forEach(key => {
        if (this.filtersLocal[key]) params.append(key, this.filtersLocal[key]);
      });

      const url = route('manager.employees.index') + (params.toString() ? `?${params.toString()}` : '');
      window.location.href = url;
    },
    
    resetFilters() {
      this.filtersLocal = { name: '', department_id: '', designation_id: '' };
      this.applyFilters();
    },
    
    gotoPage(page) {
      const params = new URLSearchParams(window.location.search);
      params.set('page', page);
      window.location.href = route('manager.employees.index') + '?' + params.toString();
    },
    
    toggleSelectAll() {
      if (this.selectedEmployees.length === this.employees.data.length) {
        this.selectedEmployees = [];
      } else {
        this.selectedEmployees = this.employees.data.map(emp => emp.id);
      }
    },
    
    async view(id) {
      this.showModal = true;
      this.modalLoading = true;
      this.detail = {};
      
      try {
        const res = await fetch(route('manager.employees.show', id), { 
          headers: { Accept: 'application/json' } 
        });
        const json = await res.json();
        if (json?.success) {
          this.detail = json.data;
        }
      } catch (e) {
        console.error('Error fetching employee details:', e);
      } finally {
        this.modalLoading = false;
      }
    },
    
    closeModal() {
      this.showModal = false;
      this.detail = {};
      this.modalLoading = false;
    },
    
    confirmDelete(employee) {
      this.deleteEmployee = employee;
      this.showDeleteModal = true;
    },
    
    async deleteEmployeeConfirmed() {
      if (!this.deleteEmployee) return;
      
      this.deleting = true;
      try {
        const response = await fetch(route('manager.employees.destroy', this.deleteEmployee.id), {
          method: 'DELETE',
          headers: {
            'X-CSRF-TOKEN': this.csrfToken,
            'Accept': 'application/json',
            'Content-Type': 'application/json',
          },
        });
        
        if (response.ok) {
          window.location.reload();
        } else {
          throw new Error('Failed to delete employee');
        }
      } catch (error) {
        console.error('Error deleting employee:', error);
        alert('Failed to delete employee. Please try again.');
      } finally {
        this.deleting = false;
        this.showDeleteModal = false;
        this.deleteEmployee = null;
      }
    },
    
    async bulkDeleteConfirmed() {
      if (this.selectedEmployees.length === 0) return;
      
      this.bulkDeleting = true;
      try {
        const response = await fetch(route('manager.employees.bulk-delete'), {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': this.csrfToken,
            'Accept': 'application/json',
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            employee_ids: this.selectedEmployees
          }),
        });
        
        if (response.ok) {
          window.location.reload();
        } else {
          throw new Error('Failed to delete employees');
        }
      } catch (error) {
        console.error('Error deleting employees:', error);
        alert('Failed to delete employees.');
      } finally {
        this.bulkDeleting = false;
        this.showBulkDeleteModal = false;
        this.selectedEmployees = [];
      }
    },
    
    async exportEmployees() {
      this.loading = true;
      try {
        const params = new URLSearchParams(window.location.search);
        const url = route('manager.employees.export') + (params.toString() ? `?${params.toString()}` : '');
        window.open(url, '_blank');
      } catch (error) {
        console.error('Error exporting employees:', error);
        alert('Failed to export employees.');
      } finally {
        this.loading = false;
      }
    },
    
    getInitials(firstName, lastName) {
      const first = firstName ? firstName.charAt(0).toUpperCase() : '';
      const last = lastName ? lastName.charAt(0).toUpperCase() : '';
      return first + last;
    },
    
    formatDate(date) {
      if (!date) return '-';
      try {
        return new Date(date).toLocaleDateString('en-US', {
          year: 'numeric',
          month: 'short',
          day: 'numeric'
        });
      } catch (e) {
        return date;
      }
    },
  },
};
</script>

<style scoped>
.transition-all {
  transition-property: all;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 300ms;
}

.hover\:shadow-md:hover {
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}
</style>