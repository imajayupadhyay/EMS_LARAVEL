<template>
  <AdminLayout>
    <Head title="Leave Assignments Management" />
    
    <!-- Header Section -->
    <div class="page-header">
      <div class="header-content">
        <div class="title-section">
          <div class="icon-wrapper">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
              <line x1="16" y1="2" x2="16" y2="6"/>
              <line x1="8" y1="2" x2="8" y2="6"/>
              <line x1="3" y1="10" x2="21" y2="10"/>
              <path d="m9 16 2 2 4-4"/>
            </svg>
          </div>
          <div class="title-content">
            <h1 class="page-title">Leave Assignments</h1>
            <p class="page-subtitle">Manage employee leave allocations and balances</p>
          </div>
        </div>
        <div class="header-actions">
          <button @click="showBulkModal = true" class="action-btn secondary">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/>
              <rect x="8" y="2" width="8" height="4" rx="1" ry="1"/>
              <path d="m9 14 2 2 4-4"/>
            </svg>
            Bulk Actions
          </button>
          <button @click="showAddModal = true" class="action-btn primary">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="10"/>
              <line x1="12" y1="8" x2="12" y2="16"/>
              <line x1="8" y1="12" x2="16" y2="12"/>
            </svg>
            Add Assignment
          </button>
        </div>
      </div>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-grid">
      <div class="stat-card primary">
        <div class="stat-icon">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
            <line x1="16" y1="2" x2="16" y2="6"/>
            <line x1="8" y1="2" x2="8" y2="6"/>
            <line x1="3" y1="10" x2="21" y2="10"/>
          </svg>
        </div>
        <div class="stat-content">
          <div class="stat-value">{{ statistics.total_assignments }}</div>
          <div class="stat-label">Total Assignments</div>
        </div>
      </div>

      <div class="stat-card success">
        <div class="stat-icon">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
            <polyline points="22,4 12,14.01 9,11.01"/>
          </svg>
        </div>
        <div class="stat-content">
          <div class="stat-value">{{ statistics.total_allocated }}</div>
          <div class="stat-label">Days Allocated</div>
        </div>
      </div>

      <div class="stat-card warning">
        <div class="stat-icon">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"/>
            <path d="m15 9-6 6"/>
            <path d="m9 9 6 6"/>
          </svg>
        </div>
        <div class="stat-content">
          <div class="stat-value">{{ statistics.total_used }}</div>
          <div class="stat-label">Days Used</div>
        </div>
      </div>

      <div class="stat-card info">
        <div class="stat-icon">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="16" x2="12" y2="12"/>
            <line x1="12" y1="8" x2="12.01" y2="8"/>
          </svg>
        </div>
        <div class="stat-content">
          <div class="stat-value">{{ statistics.total_remaining }}</div>
          <div class="stat-label">Days Remaining</div>
        </div>
      </div>
    </div>

    <!-- Filters Section -->
    <div class="filters-card">
      <div class="filters-header">
        <h3 class="filters-title">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polygon points="22,3 2,3 10,12.46 10,19 14,21 14,12.46"/>
          </svg>
          Filters & Search
        </h3>
        <button @click="clearFilters" class="clear-filters-btn">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="18" y1="6" x2="6" y2="18"/>
            <line x1="6" y1="6" x2="18" y2="18"/>
          </svg>
          Clear All
        </button>
      </div>

      <div class="filters-grid">
        <div class="filter-group">
          <label class="filter-label">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="11" cy="11" r="8"/>
              <path d="m21 21-4.35-4.35"/>
            </svg>
            Search Employee
          </label>
          <input
            v-model="filters.search"
            @input="debouncedFilter"
            type="text"
            placeholder="Name, email..."
            class="filter-input"
          />
        </div>

        <div class="filter-group">
          <label class="filter-label">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="3" y="3" width="7" height="7"/>
              <rect x="14" y="3" width="7" height="7"/>
              <rect x="14" y="14" width="7" height="7"/>
              <rect x="3" y="14" width="7" height="7"/>
            </svg>
            Department
          </label>
          <select v-model="filters.department_id" @change="applyFilters" class="filter-input">
            <option value="">All Departments</option>
            <option v-for="dept in departments" :key="dept.id" :value="dept.id">
              {{ dept.name }}
            </option>
          </select>
        </div>

        <div class="filter-group">
          <label class="filter-label">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
              <circle cx="12" cy="7" r="4"/>
            </svg>
            Designation
          </label>
          <select v-model="filters.designation_id" @change="applyFilters" class="filter-input">
            <option value="">All Designations</option>
            <option v-for="designation in designations" :key="designation.id" :value="designation.id">
              {{ designation.name }}
            </option>
          </select>
        </div>

        <div class="filter-group">
          <label class="filter-label">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
              <line x1="16" y1="2" x2="16" y2="6"/>
              <line x1="8" y1="2" x2="8" y2="6"/>
              <line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
            Leave Type
          </label>
          <select v-model="filters.leave_type_id" @change="applyFilters" class="filter-input">
            <option value="">All Leave Types</option>
            <option v-for="type in leaveTypes" :key="type.id" :value="type.id">
              {{ type.name }} ({{ type.allowed_days }} days)
            </option>
          </select>
        </div>

        <div class="filter-group">
          <label class="filter-label">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M9 11H1l8-8 8 8"/>
              <path d="M9 11v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V11"/>
              <path d="M9 11h8l8-8-8 8"/>
            </svg>
            Balance Status
          </label>
          <select v-model="filters.status" @change="applyFilters" class="filter-input">
            <option value="">All Statuses</option>
            <option value="no_balance">No Balance</option>
            <option value="low_balance">Low Balance</option>
            <option value="full_balance">Full Balance</option>
          </select>
        </div>

        <div class="filter-group">
          <label class="filter-label">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M3 6h18l-2 13H5L3 6Z"/>
              <path d="M8 10V7a4 4 0 0 1 8 0v3"/>
            </svg>
            Sort By
          </label>
          <select v-model="filters.sort_by" @change="applyFilters" class="filter-input">
            <option value="created_at">Date Created</option>
            <option value="employee_name">Employee Name</option>
            <option value="total_assigned">Total Assigned</option>
            <option value="balance">Balance</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Assignments Table -->
    <div class="table-card">
      <div class="table-header">
        <h3 class="table-title">
          Leave Assignments 
          <span class="record-count">({{ assignments.total }} records)</span>
        </h3>
        <div class="table-actions">
          <button @click="toggleSelectAll" class="select-all-btn">
            {{ selectedAssignments.length === assignments.data.length ? 'Deselect All' : 'Select All' }}
          </button>
          <span v-if="selectedAssignments.length > 0" class="selected-count">
            {{ selectedAssignments.length }} selected
          </span>
        </div>
      </div>

      <div class="table-container">
        <table class="assignments-table">
          <thead>
            <tr>
              <th class="checkbox-column">
                <input
                  type="checkbox"
                  :checked="selectedAssignments.length === assignments.data.length && assignments.data.length > 0"
                  @change="toggleSelectAll"
                  class="table-checkbox"
                />
              </th>
              <th class="sortable" @click="sortBy('employee_name')">
                Employee
                <svg v-if="filters.sort_by === 'employee_name'" class="sort-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path :d="filters.sort_order === 'asc' ? 'M18 15l-6-6-6 6' : 'M6 9l6 6 6-6'"/>
                </svg>
              </th>
              <th>Department</th>
              <th>Designation</th>
              <th>Leave Type</th>
              <th class="align-right" @click="sortBy('total_assigned')">
                Allocated
                <svg v-if="filters.sort_by === 'total_assigned'" class="sort-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path :d="filters.sort_order === 'asc' ? 'M18 15l-6-6-6 6' : 'M6 9l6 6 6-6'"/>
                </svg>
              </th>
              <th class="sortable align-right" @click="sortBy('balance')">
                Balance
                <svg v-if="filters.sort_by === 'balance'" class="sort-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path :d="filters.sort_order === 'asc' ? 'M18 15l-6-6-6 6' : 'M6 9l6 6 6-6'"/>
                </svg>
              </th>
              <th class="align-right">Used</th>
              <th class="align-center">Status</th>
              <th class="align-center">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="assignment in assignments.data" :key="assignment.id" class="table-row">
              <td class="checkbox-column">
                <input
                  type="checkbox"
                  :value="assignment.id"
                  v-model="selectedAssignments"
                  class="table-checkbox"
                />
              </td>
              <td class="employee-cell">
                <div class="employee-info">
                  <div class="employee-avatar">
                    {{ getEmployeeInitials(assignment.employee) }}
                  </div>
                  <div class="employee-details">
                    <div class="employee-name">
                      {{ assignment.employee?.first_name || 'N/A' }} {{ assignment.employee?.last_name || '' }}
                    </div>
                    <div class="employee-email">{{ assignment.employee?.email || 'No email' }}</div>
                  </div>
                </div>
              </td>
              <td>
                <span class="department-badge">
                  {{ assignment.employee?.department?.name || 'Unassigned' }}
                </span>
              </td>
              <td>
                <span class="designation-badge">
                  {{ assignment.employee?.designation?.name || 'Unassigned' }}
                </span>
              </td>
              <td>
                <span class="leave-type-badge">
                  {{ assignment.leave_type?.name || 'N/A' }}
                </span>
              </td>
              <td class="align-right">
                <span class="allocated-days">{{ assignment.total_assigned }}</span>
              </td>
              <td class="align-right">
                <span class="balance-days" :class="getBalanceClass(assignment)">
                  {{ assignment.balance }}
                </span>
              </td>
              <td class="align-right">
                <span class="used-days">{{ assignment.total_assigned - assignment.balance }}</span>
              </td>
              <td class="align-center">
                <span class="status-badge" :class="getStatusBadgeClass(assignment)">
                  {{ getStatusText(assignment) }}
                </span>
              </td>
              <td class="align-center">
                <div class="action-buttons">
                  <button @click="editAssignment(assignment)" class="action-btn-small edit">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                      <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>
                  </button>
                  <button @click="deleteAssignment(assignment)" class="action-btn-small delete">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <polyline points="3,6 5,6 21,6"/>
                      <path d="M19,6V20a2,2,0,0,1-2,2H7a2,2,0,0,1-2-2V6M8,6V4a2,2,0,0,1,2-2h4a2,2,0,0,1,2,2V6"/>
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>

        <!-- Empty State -->
        <div v-if="assignments.data.length === 0" class="empty-state">
          <div class="empty-icon">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
              <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
              <line x1="16" y1="2" x2="16" y2="6"/>
              <line x1="8" y1="2" x2="8" y2="6"/>
              <line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
          </div>
          <h3 class="empty-title">No Leave Assignments Found</h3>
          <p class="empty-description">
            {{ hasActiveFilters ? 'No assignments match your current filters. Try adjusting your search criteria.' : 'Start by creating your first leave assignment for an employee.' }}
          </p>
          <button v-if="!hasActiveFilters" @click="showAddModal = true" class="empty-action-btn">
            Add First Assignment
          </button>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="assignments.data.length > 0" class="pagination-wrapper">
        <div class="pagination-info">
          Showing {{ assignments.from }} to {{ assignments.to }} of {{ assignments.total }} results
        </div>
        <div class="pagination-controls">
          <button
            @click="goToPage(assignments.current_page - 1)"
            :disabled="!assignments.prev_page_url"
            class="pagination-btn"
          >
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="15,18 9,12 15,6"/>
            </svg>
            Previous
          </button>
          
          <div class="page-numbers">
            <button
              v-for="page in getPageNumbers()"
              :key="page"
              @click="goToPage(page)"
              :class="['page-btn', { 'active': page === assignments.current_page }]"
            >
              {{ page }}
            </button>
          </div>

          <button
            @click="goToPage(assignments.current_page + 1)"
            :disabled="!assignments.next_page_url"
            class="pagination-btn"
          >
            Next
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="9,18 15,12 9,6"/>
            </svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Add Assignment Modal -->
    <Transition name="modal">
      <div v-if="showAddModal" class="modal-overlay" @click="closeAddModal">
        <div class="modal-container" @click.stop>
          <div class="modal-header">
            <h3 class="modal-title">
              {{ editingAssignment ? 'Edit Leave Assignment' : 'Add New Leave Assignment' }}
            </h3>
            <button @click="closeAddModal" class="modal-close-btn">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18"/>
                <line x1="6" y1="6" x2="18" y2="18"/>
              </svg>
            </button>
          </div>

          <form @submit.prevent="submitAssignment" class="modal-form">
            <div class="form-grid">
              <div class="form-group" v-if="!editingAssignment">
                <label class="form-label required">Employee</label>
                <select v-model="assignmentForm.employee_id" class="form-input" required>
                  <option value="">Select Employee</option>
                  <option v-for="employee in employees" :key="employee.id" :value="employee.id">
                    {{ employee.first_name }} {{ employee.last_name }} - {{ employee.department?.name || 'No Dept' }}
                  </option>
                </select>
                <span v-if="assignmentForm.errors.employee_id" class="form-error">
                  {{ assignmentForm.errors.employee_id }}
                </span>
              </div>

              <div class="form-group" v-if="!editingAssignment">
                <label class="form-label required">Leave Type</label>
                <select v-model="assignmentForm.leave_type_id" class="form-input" required>
                  <option value="">Select Leave Type</option>
                  <option v-for="type in leaveTypes" :key="type.id" :value="type.id">
                    {{ type.name }} ({{ type.allowed_days }} days max)
                  </option>
                </select>
                <span v-if="assignmentForm.errors.leave_type_id" class="form-error">
                  {{ assignmentForm.errors.leave_type_id }}
                </span>
              </div>

              <div class="form-group">
                <label class="form-label required">Total Assigned Days</label>
                <input
                  type="number"
                  v-model="assignmentForm.total_assigned"
                  class="form-input"
                  min="0"
                  max="365"
                  required
                  placeholder="Enter total days"
                />
                <span v-if="assignmentForm.errors.total_assigned" class="form-error">
                  {{ assignmentForm.errors.total_assigned }}
                </span>
              </div>

              <div class="form-group">
                <label class="form-label required">Current Balance</label>
                <input
                  type="number"
                  v-model="assignmentForm.balance"
                  class="form-input"
                  min="0"
                  :max="assignmentForm.total_assigned || 365"
                  required
                  placeholder="Enter current balance"
                />
                <span v-if="assignmentForm.errors.balance" class="form-error">
                  {{ assignmentForm.errors.balance }}
                </span>
                <div class="form-help">
                  Balance cannot exceed total assigned days
                </div>
              </div>
            </div>

            <div class="modal-actions">
              <button type="button" @click="closeAddModal" class="action-btn secondary">
                Cancel
              </button>
              <button type="submit" :disabled="assignmentForm.processing" class="action-btn primary">
                {{ assignmentForm.processing ? 'Saving...' : (editingAssignment ? 'Update Assignment' : 'Create Assignment') }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </Transition>

    <!-- Bulk Actions Modal -->
    <Transition name="modal">
      <div v-if="showBulkModal" class="modal-overlay" @click="closeBulkModal">
        <div class="modal-container" @click.stop>
          <div class="modal-header">
            <h3 class="modal-title">Bulk Actions</h3>
            <button @click="closeBulkModal" class="modal-close-btn">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18"/>
                <line x1="6" y1="6" x2="18" y2="18"/>
              </svg>
            </button>
          </div>

          <div class="bulk-actions-container">
            <div class="bulk-action-section">
              <h4 class="bulk-section-title">Bulk Assignment</h4>
              <p class="bulk-section-description">Assign leave to multiple employees at once</p>
              
              <form @submit.prevent="submitBulkAssignment" class="bulk-form">
                <div class="form-group">
                  <label class="form-label required">Select Employees</label>
                  <div class="employee-selection">
                    <div class="employee-filters">
                      <select v-model="bulkFilters.department" class="form-input-sm">
                        <option value="">All Departments</option>
                        <option v-for="dept in departments" :key="dept.id" :value="dept.id">
                          {{ dept.name }}
                        </option>
                      </select>
                      <select v-model="bulkFilters.designation" class="form-input-sm">
                        <option value="">All Designations</option>
                        <option v-for="des in designations" :key="des.id" :value="des.id">
                          {{ des.name }}
                        </option>
                      </select>
                    </div>
                    <div class="employee-list">
                      <div
                        v-for="employee in filteredEmployeesForBulk"
                        :key="employee.id"
                        class="employee-checkbox-item"
                      >
                        <input
                          type="checkbox"
                          :value="employee.id"
                          v-model="bulkForm.employee_ids"
                          class="checkbox-input"
                        />
                        <label class="checkbox-label">
                          {{ employee.first_name }} {{ employee.last_name }}
                          <span class="employee-meta">{{ employee.department?.name || 'No Dept' }}</span>
                        </label>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="form-row">
                  <div class="form-group">
                    <label class="form-label required">Leave Type</label>
                    <select v-model="bulkForm.leave_type_id" class="form-input" required>
                      <option value="">Select Leave Type</option>
                      <option v-for="type in leaveTypes" :key="type.id" :value="type.id">
                        {{ type.name }} ({{ type.allowed_days }} days)
                      </option>
                    </select>
                  </div>

                  <div class="form-group">
                    <label class="form-label required">Total Assigned</label>
                    <input
                      type="number"
                      v-model="bulkForm.total_assigned"
                      class="form-input"
                      min="0"
                      max="365"
                      required
                    />
                  </div>

                  <div class="form-group">
                    <label class="form-label required">Balance</label>
                    <input
                      type="number"
                      v-model="bulkForm.balance"
                      class="form-input"
                      min="0"
                      :max="bulkForm.total_assigned || 365"
                      required
                    />
                  </div>
                </div>

                <button type="submit" :disabled="bulkForm.processing || bulkForm.employee_ids.length === 0" class="action-btn primary full-width">
                  {{ bulkForm.processing ? 'Creating...' : `Assign to ${bulkForm.employee_ids.length} Employee(s)` }}
                </button>
              </form>
            </div>

            <div class="bulk-divider"></div>

            <div class="bulk-action-section">
              <h4 class="bulk-section-title">Bulk Update</h4>
              <p class="bulk-section-description">Update selected assignments</p>
              
              <form @submit.prevent="submitBulkUpdate" class="bulk-form">
                <div class="form-group">
                  <label class="form-label">Selected Assignments: {{ selectedAssignments.length }}</label>
                  <p class="form-help">{{ selectedAssignments.length === 0 ? 'Select assignments from the table first' : `${selectedAssignments.length} assignments selected` }}</p>
                </div>

                <div class="form-group">
                  <label class="form-label required">Action</label>
                  <select v-model="bulkUpdateForm.action" class="form-input" required>
                    <option value="">Select Action</option>
                    <option value="adjust_balance">Adjust Balance</option>
                    <option value="reset_balance">Reset to Full Balance</option>
                  </select>
                </div>

                <div v-if="bulkUpdateForm.action === 'adjust_balance'" class="form-group">
                  <label class="form-label required">Adjustment Value</label>
                  <input
                    type="number"
                    v-model="bulkUpdateForm.adjustment_value"
                    class="form-input"
                    placeholder="e.g., +5 or -3"
                    required
                  />
                  <div class="form-help">Use positive numbers to add days, negative to subtract</div>
                </div>

                <button
                  type="submit"
                  :disabled="bulkUpdateForm.processing || selectedAssignments.length === 0"
                  class="action-btn warning full-width"
                >
                  {{ bulkUpdateForm.processing ? 'Updating...' : 'Update Selected' }}
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </Transition>

    <!-- Success Toast -->
    <Transition name="toast">
      <div v-if="showToast" class="toast-container">
        <div class="toast success">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="20,6 9,17 4,12"/>
          </svg>
          {{ toastMessage }}
        </div>
      </div>
    </Transition>
  </AdminLayout>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useForm, router, Head } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

// Props
const props = defineProps({
  assignments: Object,
  employees: Array,
  leaveTypes: Array,
  departments: Array,
  designations: Array,
  filters: Object,
  statistics: Object,
})

// Reactive state
const showAddModal = ref(false)
const showBulkModal = ref(false)
const editingAssignment = ref(null)
const selectedAssignments = ref([])
const showToast = ref(false)
const toastMessage = ref('')

// Filters
const filters = ref({
  search: props.filters.search || '',
  department_id: props.filters.department_id || '',
  designation_id: props.filters.designation_id || '',
  leave_type_id: props.filters.leave_type_id || '',
  status: props.filters.status || '',
  sort_by: props.filters.sort_by || 'created_at',
  sort_order: props.filters.sort_order || 'desc',
})

// Forms
const assignmentForm = useForm({
  employee_id: '',
  leave_type_id: '',
  total_assigned: '',
  balance: '',
})

const bulkForm = useForm({
  employee_ids: [],
  leave_type_id: '',
  total_assigned: '',
  balance: '',
})

const bulkUpdateForm = useForm({
  assignment_ids: [],
  action: '',
  adjustment_value: '',
})

const bulkFilters = ref({
  department: '',
  designation: '',
})

// Computed properties
const hasActiveFilters = computed(() => {
  return filters.value.search || 
         filters.value.department_id || 
         filters.value.designation_id || 
         filters.value.leave_type_id || 
         filters.value.status
})

const filteredEmployeesForBulk = computed(() => {
  let employees = props.employees

  if (bulkFilters.value.department) {
    employees = employees.filter(emp => emp.department?.id == bulkFilters.value.department)
  }

  if (bulkFilters.value.designation) {
    employees = employees.filter(emp => emp.designation?.id == bulkFilters.value.designation)
  }

  return employees
})

// Methods
const debouncedFilter = debounce(() => {
  applyFilters()
}, 300)

function debounce(func, wait) {
  let timeout
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout)
      func(...args)
    }
    clearTimeout(timeout)
    timeout = setTimeout(later, wait)
  }
}

function applyFilters() {
  router.get(route('admin.leave-assignments.index'), filters.value, {
    preserveState: true,
    preserveScroll: true,
  })
}

function clearFilters() {
  filters.value = {
    search: '',
    department_id: '',
    designation_id: '',
    leave_type_id: '',
    status: '',
    sort_by: 'created_at',
    sort_order: 'desc',
  }
  applyFilters()
}

function sortBy(column) {
  if (filters.value.sort_by === column) {
    filters.value.sort_order = filters.value.sort_order === 'asc' ? 'desc' : 'asc'
  } else {
    filters.value.sort_by = column
    filters.value.sort_order = 'asc'
  }
  applyFilters()
}

function getEmployeeInitials(employee) {
  if (!employee) return 'NA'
  const firstName = employee.first_name || ''
  const lastName = employee.last_name || ''
  return (firstName.charAt(0) + lastName.charAt(0)).toUpperCase() || 'NA'
}

function getBalanceClass(assignment) {
  const balance = assignment.balance
  const total = assignment.total_assigned
  
  if (balance <= 0) return 'balance-critical'
  if (balance <= total * 0.2) return 'balance-low'
  return 'balance-good'
}

function getStatusText(assignment) {
  const balance = assignment.balance
  const total = assignment.total_assigned
  
  if (balance <= 0) return 'No Balance'
  if (balance <= total * 0.2) return 'Low Balance'
  if (balance === total) return 'Full Balance'
  return 'Available'
}

function getStatusBadgeClass(assignment) {
  const balance = assignment.balance
  const total = assignment.total_assigned
  
  if (balance <= 0) return 'error'
  if (balance <= total * 0.2) return 'warning'
  if (balance === total) return 'success'
  return 'info'
}

function toggleSelectAll() {
  if (selectedAssignments.value.length === props.assignments.data.length) {
    selectedAssignments.value = []
  } else {
    selectedAssignments.value = props.assignments.data.map(a => a.id)
  }
}

function editAssignment(assignment) {
  editingAssignment.value = assignment
  assignmentForm.total_assigned = assignment.total_assigned
  assignmentForm.balance = assignment.balance
  showAddModal.value = true
}

function closeAddModal() {
  showAddModal.value = false
  editingAssignment.value = null
  assignmentForm.reset()
  assignmentForm.clearErrors()
}

function closeBulkModal() {
  showBulkModal.value = false
  bulkForm.reset()
  bulkUpdateForm.reset()
  bulkFilters.value = { department: '', designation: '' }
}

function submitAssignment() {
  if (editingAssignment.value) {
    assignmentForm.post(route('admin.leave-assignments.update', editingAssignment.value.id), {
      preserveScroll: true,
      onSuccess: () => {
        closeAddModal()
        showToastMessage('Leave assignment updated successfully!')
      },
    })
  } else {
    assignmentForm.post(route('admin.leave-assignments.store'), {
      preserveScroll: true,
      onSuccess: () => {
        closeAddModal()
        showToastMessage('Leave assignment created successfully!')
      },
    })
  }
}

function submitBulkAssignment() {
  bulkForm.post(route('admin.leave-assignments.bulk-assign'), {
    preserveScroll: true,
    onSuccess: () => {
      closeBulkModal()
      showToastMessage('Bulk assignment completed successfully!')
    },
  })
}

function submitBulkUpdate() {
  bulkUpdateForm.assignment_ids = selectedAssignments.value
  bulkUpdateForm.post(route('admin.leave-assignments.bulk-update'), {
    preserveScroll: true,
    onSuccess: () => {
      closeBulkModal()
      selectedAssignments.value = []
      showToastMessage('Bulk update completed successfully!')
    },
  })
}

function deleteAssignment(assignment) {
  const employeeName = `${assignment.employee?.first_name || 'N/A'} ${assignment.employee?.last_name || ''}`
  const leaveTypeName = assignment.leave_type?.name || 'N/A'
  
  if (confirm(`Delete leave assignment for ${employeeName} (${leaveTypeName})?`)) {
    router.post(route('admin.leave-assignments.destroy', assignment.id), {}, {
      preserveScroll: true,
      onSuccess: () => {
        showToastMessage('Leave assignment deleted successfully!')
      }
    })
  }
}

function goToPage(page) {
  if (page < 1 || page > props.assignments.last_page) return
  
  router.get(route('admin.leave-assignments.index'), {
    ...filters.value,
    page: page
  }, {
    preserveState: true,
    preserveScroll: true,
  })
}

function getPageNumbers() {
  const current = props.assignments.current_page
  const last = props.assignments.last_page
  const pages = []
  
  // Always show first page
  if (current > 3) pages.push(1)
  
  // Show pages around current
  for (let i = Math.max(1, current - 2); i <= Math.min(last, current + 2); i++) {
    pages.push(i)
  }
  
  // Always show last page
  if (current < last - 2) pages.push(last)
  
  return [...new Set(pages)].sort((a, b) => a - b)
}

function showToastMessage(message) {
  toastMessage.value = message
  showToast.value = true
  setTimeout(() => {
    showToast.value = false
  }, 3000)
}

// Watch for flash messages
watch(() => props.flash, (flash) => {
  if (flash?.success) {
    showToastMessage(flash.success)
  }
}, { deep: true, immediate: true })
</script>

<style scoped>
/* Page Layout */
.page-header {
  @apply bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6;
}

.header-content {
  @apply flex items-center justify-between;
}

.title-section {
  @apply flex items-center gap-4;
}

.icon-wrapper {
  @apply w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center text-white;
}

.title-content {
  @apply flex flex-col;
}

.page-title {
  @apply text-2xl font-bold text-gray-900;
}

.page-subtitle {
  @apply text-gray-500 mt-1;
}

.header-actions {
  @apply flex items-center gap-3;
}

.action-btn {
  @apply inline-flex items-center gap-2 px-4 py-2 rounded-lg font-medium transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2;
}

.action-btn.primary {
  @apply bg-gradient-to-r from-purple-500 to-purple-600 text-white hover:from-purple-600 hover:to-purple-700 focus:ring-purple-500 shadow-lg hover:shadow-xl;
}

.action-btn.secondary {
  @apply bg-gray-100 text-gray-700 hover:bg-gray-200 focus:ring-gray-500;
}

.action-btn.warning {
  @apply bg-yellow-500 text-white hover:bg-yellow-600 focus:ring-yellow-500;
}

/* Statistics Cards */
.stats-grid {
  @apply grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6;
}

.stat-card {
  @apply bg-white rounded-xl p-6 border-l-4 shadow-sm;
}

.stat-card.primary {
  @apply border-l-purple-500;
}

.stat-card.success {
  @apply border-l-green-500;
}

.stat-card.warning {
  @apply border-l-yellow-500;
}

.stat-card.info {
  @apply border-l-blue-500;
}

.stat-card {
  @apply flex items-center gap-4;
}

.stat-icon {
  @apply w-12 h-12 rounded-lg flex items-center justify-center;
}

.stat-card.primary .stat-icon {
  @apply bg-purple-100 text-purple-600;
}

.stat-card.success .stat-icon {
  @apply bg-green-100 text-green-600;
}

.stat-card.warning .stat-icon {
  @apply bg-yellow-100 text-yellow-600;
}

.stat-card.info .stat-icon {
  @apply bg-blue-100 text-blue-600;
}

.stat-content {
  @apply flex flex-col;
}

.stat-value {
  @apply text-2xl font-bold text-gray-900;
}

.stat-label {
  @apply text-sm text-gray-500 mt-1;
}

/* Filters */
.filters-card {
  @apply bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6;
}

.filters-header {
  @apply flex items-center justify-between mb-4;
}

.filters-title {
  @apply text-lg font-semibold text-gray-900 flex items-center gap-2;
}

.clear-filters-btn {
  @apply text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1 px-3 py-1 rounded-md hover:bg-gray-100 transition-colors;
}

.filters-grid {
  @apply grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4;
}

.filter-group {
  @apply flex flex-col;
}

.filter-label {
  @apply text-sm font-medium text-gray-700 mb-2 flex items-center gap-1;
}

.filter-input {
  @apply w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors;
}

/* Table */
.table-card {
  @apply bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden;
}

.table-header {
  @apply flex items-center justify-between p-6 border-b border-gray-200;
}

.table-title {
  @apply text-lg font-semibold text-gray-900;
}

.record-count {
  @apply text-sm font-normal text-gray-500;
}

.table-actions {
  @apply flex items-center gap-4;
}

.select-all-btn {
  @apply text-sm text-purple-600 hover:text-purple-700 font-medium;
}

.selected-count {
  @apply text-sm text-gray-500 px-2 py-1 bg-gray-100 rounded-md;
}

.table-container {
  @apply overflow-x-auto;
}

.assignments-table {
  @apply w-full;
}

.assignments-table th {
  @apply px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50 border-b border-gray-200;
}

.assignments-table th.sortable {
  @apply cursor-pointer hover:bg-gray-100 select-none;
}

.assignments-table th.align-right {
  text-align: right;
}

.assignments-table th.align-center {
  text-align: center;
}

.assignments-table th.checkbox-column {
  @apply w-12;
}

.sort-icon {
  @apply inline-block ml-1;
}

.table-row {
  @apply border-b border-gray-200 hover:bg-gray-50 transition-colors;
}

.table-row td {
  @apply px-6 py-4 whitespace-nowrap;
}

.table-checkbox {
  @apply rounded border-gray-300 text-purple-600 focus:ring-purple-500;
}

.employee-cell {
  @apply py-4;
}

.employee-info {
  @apply flex items-center gap-3;
}

.employee-avatar {
  @apply w-10 h-10 rounded-full bg-gradient-to-br from-purple-400 to-purple-600 text-white font-semibold flex items-center justify-center text-sm;
}

.employee-details {
  @apply flex flex-col;
}

.employee-name {
  @apply font-medium text-gray-900;
}

.employee-email {
  @apply text-sm text-gray-500;
}

.department-badge, .designation-badge, .leave-type-badge {
  @apply inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium;
}

.department-badge {
  @apply bg-blue-100 text-blue-800;
}

.designation-badge {
  @apply bg-purple-100 text-purple-800;
}

.leave-type-badge {
  @apply bg-green-100 text-green-800;
}

.allocated-days, .balance-days, .used-days {
  @apply font-semibold;
}

.status-badge {
  @apply inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium;
}

.status-badge.success {
  @apply bg-green-100 text-green-800;
}

.status-badge.warning {
  @apply bg-yellow-100 text-yellow-800;
}

.status-badge.error {
  @apply bg-red-100 text-red-800;
}

.status-badge.info {
  @apply bg-blue-100 text-blue-800;
}

.action-buttons {
  @apply flex items-center gap-1;
}

.action-btn-small {
  @apply p-1.5 rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-offset-1;
}

.action-btn-small.edit {
  @apply text-purple-600 hover:bg-purple-100 focus:ring-purple-500;
}

.action-btn-small.delete {
  @apply text-red-600 hover:bg-red-100 focus:ring-red-500;
}

/* Empty State */
.empty-state {
  @apply text-center py-12;
}

.empty-icon {
  @apply w-16 h-16 mx-auto text-gray-400 mb-4;
}

.empty-title {
  @apply text-lg font-semibold text-gray-900 mb-2;
}

.empty-description {
  @apply text-gray-500 mb-6 max-w-md mx-auto;
}

.empty-action-btn {
  @apply inline-flex items-center px-4 py-2 bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-lg hover:from-purple-600 hover:to-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl;
}

/* Pagination */
.pagination-wrapper {
  @apply flex items-center justify-between px-6 py-4 border-t border-gray-200;
}

.pagination-info {
  @apply text-sm text-gray-700;
}

.pagination-controls {
  @apply flex items-center gap-2;
}

.pagination-btn {
  @apply inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors;
}

.page-numbers {
  @apply flex items-center gap-1;
}

.page-btn {
  @apply w-8 h-8 flex items-center justify-center text-sm font-medium text-gray-700 rounded-md hover:bg-gray-100 transition-colors;
}

.page-btn.active {
  @apply bg-gradient-to-r from-purple-500 to-purple-600 text-white hover:from-purple-600 hover:to-purple-700;
}

/* Modal Styles */
.modal-overlay {
  @apply fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4;
}

.modal-container {
  @apply bg-white rounded-xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto;
}

.modal-header {
  @apply flex items-center justify-between p-6 border-b border-gray-200;
}

.modal-title {
  @apply text-xl font-semibold text-gray-900;
}

.modal-close-btn {
  @apply text-gray-400 hover:text-gray-600 p-1;
}

.modal-form {
  @apply p-6;
}

.form-grid {
  @apply grid grid-cols-1 md:grid-cols-2 gap-4 mb-6;
}

.form-row {
  @apply grid grid-cols-1 md:grid-cols-3 gap-4;
}

.form-group {
  @apply flex flex-col;
}

.form-label {
  @apply text-sm font-medium text-gray-700 mb-2;
}

.form-label.required::after {
  content: '*';
  @apply text-red-500 ml-1;
}

.form-input {
  @apply w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors;
}

.form-input-sm {
  @apply w-full px-2 py-1 text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-purple-500 focus:border-purple-500;
}

.form-error {
  @apply text-sm text-red-600 mt-1;
}

.form-help {
  @apply text-xs text-gray-500 mt-1;
}

.modal-actions {
  @apply flex items-center justify-end gap-3 pt-4 border-t border-gray-200;
}

.full-width {
  @apply w-full;
}

/* Bulk Actions */
.bulk-actions-container {
  @apply p-6;
}

.bulk-action-section {
  @apply mb-6;
}

.bulk-section-title {
  @apply text-lg font-semibold text-gray-900 mb-2;
}

.bulk-section-description {
  @apply text-gray-500 mb-4;
}

.bulk-form {
  @apply space-y-4;
}

.bulk-divider {
  @apply border-t border-gray-200 my-6;
}

.employee-selection {
  @apply border border-gray-200 rounded-lg p-4;
}

.employee-filters {
  @apply flex gap-2 mb-4;
}

.employee-list {
  @apply max-h-40 overflow-y-auto space-y-2;
}

.employee-checkbox-item {
  @apply flex items-center gap-2;
}

.checkbox-input {
  @apply rounded border-gray-300 text-purple-600 focus:ring-purple-500;
}

.checkbox-label {
  @apply text-sm text-gray-700 flex flex-col;
}

.employee-meta {
  @apply text-xs text-gray-500;
}

/* Toast Notifications */
.toast-container {
  @apply fixed top-4 right-4 z-50;
}

.toast {
  @apply flex items-center gap-3 px-4 py-3 rounded-lg shadow-lg;
}

.toast.success {
  @apply bg-green-500 text-white;
}

/* Transitions */
.modal-enter-active, .modal-leave-active {
  transition: all 0.3s ease;
}

.modal-enter-from, .modal-leave-to {
  opacity: 0;
  transform: scale(0.95);
}

.toast-enter-active, .toast-leave-active {
  transition: all 0.3s ease;
}

.toast-enter-from, .toast-leave-to {
  opacity: 0;
  transform: translateX(100%);
}

/* Responsive Design */
@media (max-width: 768px) {
  .header-content {
    @apply flex-col items-start gap-4;
  }

  .stats-grid {
    @apply grid-cols-1;
  }

  .filters-grid {
    @apply grid-cols-1;
  }

  .table-container {
    @apply -mx-6;
  }

  .assignments-table th,
  .assignments-table td {
    @apply px-3;
  }

  .pagination-wrapper {
    @apply flex-col gap-4 items-start;
  }

  .modal-container {
    @apply mx-2 max-w-none;
  }

  .form-grid {
    @apply grid-cols-1;
  }

  .form-row {
    @apply grid-cols-1;
  }
}
</style>