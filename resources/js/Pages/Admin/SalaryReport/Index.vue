<template>
  <AdminLayout>
    <div class="p-6">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <h1 class="text-2xl font-bold text-orange-600">💼 Salary Report</h1>

        <div class="flex flex-col md:flex-row gap-2 items-stretch">
          <!-- Month picker -->
          <!-- <input
            type="month"
            :value="monthYearValue"
            @change="onMonthChange"
            class="border rounded px-3 py-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
            aria-label="Select month"
          /> -->

          <!-- Employee select -->
          <!-- <select v-model="selectedEmployee" @change="applyFilters"
            class="border rounded px-3 py-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
            <option value="">All Employees</option>
            <option v-for="emp in employees" :key="emp.id" :value="emp.id">{{ emp.name }}</option>
          </select> -->

          <!-- Preview -->
          <!-- <button
            @click="preview"
            :disabled="loadingPreview"
            class="bg-white border px-4 py-2 rounded shadow hover:bg-orange-50 disabled:opacity-60"
          >
            <span v-if="!loadingPreview">Preview Salaries</span>
            <span v-else>Previewing…</span>
          </button> -->

          <!-- Export -->
          <!-- <form :action="route('admin.salary-report.export')" method="GET" class="inline">
            <input type="hidden" name="month" :value="month" />
            <input type="hidden" name="year" :value="year" />
            <button type="submit"
              class="bg-orange-600 text-white px-4 py-2 rounded shadow hover:bg-orange-700 transition">
              Export Excel
            </button>
          </form> -->

          <!-- Finalize bulk -->
          <!-- <button
            @click="finalizeAll"
            :disabled="loadingFinalize"
            class="bg-green-600 text-white px-4 py-2 rounded shadow hover:bg-green-700 disabled:opacity-60"
          >
            <span v-if="!loadingFinalize">Finalize (Bulk)</span>
            <span v-else>Finalizing…</span>
          </button> -->
        </div>
      </div>

      <!-- Summary bar -->
      <div class="mb-4 flex flex-wrap items-center gap-4 text-sm text-gray-700">
        <div><strong>Office Days:</strong> {{ officeDays }}</div>
        <div v-if="report && report.length"><strong>Employees:</strong> {{ report.length }}</div>
        <div v-if="flashMessage" class="text-green-700">{{ flashMessage }}</div>
      </div>

      <!-- Table -->
      <div class="overflow-x-auto bg-white rounded-lg shadow">
        <table class="min-w-full text-sm">
          <thead class="bg-orange-100 text-gray-700">
            <tr>
              <th class="p-3 border">#</th>
              <th class="p-3 border">Name</th>
              <th class="p-3 border">Dept</th>
              <th class="p-3 border">Desg.</th>
              <th class="p-3 border">Office Days</th>
              <th class="p-3 border">Present</th>
              <th class="p-3 border">Approved</th>
              <th class="p-3 border">Paid Leave</th>
              <th class="p-3 border">Unpaid Leave</th>
              <th class="p-3 border">Absent</th>
              <th class="p-3 border">Gross</th>
              <th class="p-3 border">Deductions</th>
              <th class="p-3 border">Net</th>
              <!-- <th class="p-3 border">Actions</th> -->
            </tr>
          </thead>
          <tbody>
            <tr v-if="!report || report.length === 0">
              <td class="p-3 border text-center" :colspan="14">No data. Click <strong>Preview Salaries</strong>.</td>
            </tr>

            <tr v-for="(row, idx) in report" :key="row.employee.id" class="hover:bg-orange-50">
              <td class="p-3 border text-xs text-gray-600">{{ idx + 1 }}</td>
              <td class="p-3 border font-medium">
                {{ row.employee.first_name }} {{ row.employee.last_name }}
              </td>
              <td class="p-3 border">{{ row.employee.department?.name ?? '-' }}</td>
              <td class="p-3 border">{{ row.employee.designation?.name ?? '-' }}</td>
              <td class="p-3 border text-center">{{ row.working_days }}</td>
              <td class="p-3 border text-center">{{ row.present_days }}</td>
              <td class="p-3 border text-center">{{ row.approved_leaves }}</td>
              <td class="p-3 border text-center">{{ row.paid_leave_days }}</td>
              <td class="p-3 border text-center">{{ row.unpaid_leave_days }}</td>
              <td class="p-3 border text-center">{{ row.absent_days }}</td>
              <td class="p-3 border text-right">{{ formatMoney(row.gross_salary) }}</td>

              <td class="p-3 border text-sm">
                <div v-for="(amt, label) in row.deductions" :key="label" class="flex justify-between">
                  <span class="truncate pr-2">{{ label }}</span>
                  <span class="font-semibold">{{ formatMoney(amt) }}</span>
                </div>
              </td>

              <td class="p-3 border text-right font-semibold">{{ formatMoney(row.net_salary) }}</td>

              <!-- <td class="p-3 border">
                <div class="flex gap-2"> -->
                  <!-- <button
                    @click="finalizeEmployee(row.employee.id)"
                    class="px-2 py-1 rounded bg-blue-600 text-white text-xs"
                    :disabled="loadingEmployee[row.employee.id]"
                  >
                    <span v-if="!loadingEmployee[row.employee.id]">Finalize</span>
                    <span v-else>Working…</span>
                  </button> -->

                  <!-- <button
                    v-if="row.salary_id"
                    @click="downloadPayslip(row.salary_id)"
                    class="px-2 py-1 rounded bg-gray-800 text-white text-xs"
                  >
                    Payslip
                  </button> -->
                <!-- </div>
              </td> -->
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Small footer / hint -->
      <div class="mt-4 text-xs text-gray-500">
        Note: Paid leaves are deducted from leave balance. Unpaid leaves and absences are deducted pro-rata.
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
/*
  Usage:
   - Place in resources/js/Pages/Admin/SalaryReport/Index.vue
   - Route name expectations:
       admin.salary-report.index  -> GET (Inertia props: report, officeDays, month, year, employees, selectedEmployee)
       admin.salary-report.finalize -> POST (month, year, optional employee_id, overwrite)
       admin.salary-report.export -> GET (month, year)
       (Optional) a route serving payslip download: admin.salary-report.payslip (GET salary_id)
   - This component uses Inertia and Axios. Axios is required for POST finalize calls.
*/

import AdminLayout from '@/Layouts/AdminLayout.vue'
import { router } from '@inertiajs/vue3'
import { ref, reactive, computed, watch } from 'vue'
import axios from 'axios'
import { route } from 'ziggy-js'

const props = defineProps({
  report: { type: Array, default: () => [] },
  officeDays: { type: Number, default: 0 },
  month: { type: String, default: () => (new Date()).toISOString().slice(5,7) },
  year: { type: String, default: () => (new Date()).getFullYear().toString() },
  employees: { type: Array, default: () => [] },
  selectedEmployee: { type: [String, Number], default: '' },
  // Inertia flash message (if controller sets one)
  flash: { type: Object, default: () => ({}) }
})

const report = ref(props.report || [])
const officeDays = ref(props.officeDays || 0)
const employees = ref(props.employees || [])
const selectedEmployee = ref(props.selectedEmployee || '')
const month = ref(String(props.month).padStart(2,'0'))
const year = ref(String(props.year))
const loadingPreview = ref(false)
const loadingFinalize = ref(false)
const loadingEmployee = reactive({})
const flashMessage = computed(() => props.flash?.success ?? props.flash?.message ?? '')

// build value for <input type="month">
const monthYearValue = computed(() => `${year.value}-${month.value}`)

// helpers
const formatMoney = (value) => {
  const n = Number(value) || 0
  return n.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })
}

function onMonthChange(e) {
  if (!e || !e.target) return
  const [y, m] = e.target.value.split('-')
  if (!y || !m) return
  month.value = String(m).padStart(2,'0')
  year.value = String(y)
  // update route with current filters (preserve scroll)
  router.get(route('admin.salary-report.index'), { month: month.value, year: year.value, employee_id: selectedEmployee.value }, { preserveState: true, preserveScroll: true })
}

function applyFilters() {
  router.get(route('admin.salary-report.index'), { month: month.value, year: year.value, employee_id: selectedEmployee.value }, { preserveState: true, preserveScroll: true })
}

async function preview() {
  loadingPreview.value = true
  try {
    // hitting index is sufficient — it will pass computed report as Inertia props
    await router.get(route('admin.salary-report.index'), { month: month.value, year: year.value, employee_id: selectedEmployee.value }, { preserveState: false })
    // Inertia will rehydrate the page; no need to merge response here
  } catch (err) {
    console.error(err)
    alert('Failed to preview salaries. Check console.')
  } finally {
    loadingPreview.value = false
  }
}

async function finalizeAll() {
  if (!confirm('Finalize and store salaries for this payroll period? This will create salary records for employees.')) return
  loadingFinalize.value = true
  try {
    const form = new FormData()
    form.append('month', month.value)
    form.append('year', year.value)
    if (selectedEmployee.value) form.append('employee_id', selectedEmployee.value)
    form.append('overwrite', 'false')

    await axios.post(route('admin.salary-report.finalize'), form)
    // refresh page (Inertia)
    await router.get(route('admin.salary-report.index'), { month: month.value, year: year.value, employee_id: selectedEmployee.value }, { preserveState: false })
    alert('Salaries finalized (check flash messages).')
  } catch (err) {
    console.error(err)
    alert('Failed to finalize salaries. See console for details.')
  } finally {
    loadingFinalize.value = false
  }
}

async function finalizeEmployee(empId) {
  if (!confirm('Finalize salary for this employee?')) return
  loadingEmployee[empId] = true
  try {
    const form = new FormData()
    form.append('month', month.value)
    form.append('year', year.value)
    form.append('employee_id', empId)
    form.append('overwrite', 'false')

    await axios.post(route('admin.salary-report.finalize'), form)
    await router.get(route('admin.salary-report.index'), { month: month.value, year: year.value, employee_id: selectedEmployee.value }, { preserveState: false })
    alert('Employee salary finalized.')
  } catch (err) {
    console.error(err)
    alert('Failed to finalize employee salary.')
  } finally {
    loadingEmployee[empId] = false
  }
}

/**
 * Optional: download payslip for a finalized salary_id
 * You should implement route admin.salary-report.payslip that returns a PDF response:
 * Route::get('salary-report/payslip/{salary}', [SalaryReportController::class, 'payslip'])->name('admin.salary-report.payslip');
 */
function downloadPayslip(salaryId) {
  // open new tab to download PDF
  const url = route('admin.salary-report.payslip', { salary: salaryId })
  window.open(url, '_blank')
}
</script>

<style scoped>
table th, table td { vertical-align: middle; }
</style>
