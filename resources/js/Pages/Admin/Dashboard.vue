<template>
  <AdminLayout>
    <div class="p-6">
      <!-- Header -->
      <div class="header flex items-center justify-between mb-6">
        <div>
          <h1 class="welcome text-2xl font-bold text-orange-600">
            Welcome back, <span class="text-emerald-700">{{ adminName }}</span> 👋
          </h1>
          <p class="text-sm text-gray-500 mt-1">Quick access to admin sections</p>
        </div>

        <div class="flex items-center gap-3">
          <button @click="reloadPage" class="btn-refresh inline-flex items-center px-4 py-2 rounded shadow-sm bg-white border hover:bg-gray-50">
            Refresh
          </button>
        </div>
      </div>

      <!-- Quick Links header -->
      <div class="mb-4 flex items-center justify-between">
        <h2 class="text-lg font-semibold">Quick Links</h2>
        <p class="text-sm text-gray-500">Open a container to go to that section</p>
      </div>

      <!-- Quick Links grouped by section -->
      <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        <div v-for="section in navSections" :key="section.label" class="section-card rounded-lg bg-white shadow-sm border overflow-hidden">
          <div class="section-header px-5 py-4 bg-gray-50 flex items-center justify-between">
            <div class="flex items-center gap-3">
              <div class="section-badge w-10 h-10 rounded-md flex items-center justify-center bg-orange-50 text-orange-600 font-semibold">
                {{ section.label.charAt(0) }}
              </div>
              <div>
                <div class="text-sm font-semibold">{{ section.label }}</div>
                <div class="text-xs text-gray-500 mt-0.5">{{ section.links.length }} links</div>
              </div>
            </div>
          </div>

          <div class="section-body p-4 grid gap-3">
            <Link
              v-for="link in section.links"
              :key="link.name"
              :href="link.href"
              class="link-card"
              :class="{ 'link-card-disabled': !link.href }"
            >
              <div class="icon text-xl mr-3">{{ link.icon || '🔗' }}</div>
              <div class="flex-1 min-w-0">
                <div class="title text-sm font-medium text-gray-900 truncate">{{ link.name }}</div>
                <div class="subtitle text-xs text-gray-500 mt-0.5 truncate">
                  Go to {{ link.name.toLowerCase() }}
                </div>
              </div>
              <div class="chev text-gray-300 ml-3">›</div>
            </Link>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link } from '@inertiajs/vue3'
import { route } from 'ziggy-js'

// Admin name (could be passed via Inertia props; fallback to 'Admin')
const adminName = ref((typeof window !== 'undefined' && window.page && window.page.props && window.page.props.auth && window.page.props.auth.user)
  ? window.page.props.auth.user.name
  : 'Admin')

// Navigation sections (same set as your sidebar)
const navSections = [
  {
    label: 'Employee Management',
    links: [
      { name: 'Add Employee', href: route('admin.employees.create'), icon: '👤' },
      { name: 'Employee List', href: route('admin.employees.manage'), icon: '📋' },
    ]
  },
  {
    label: 'Marketer Management',
    links: [
      { name: 'Add Marketer', href: route('admin.marketers.create'), icon: '🧑‍💼' },
      { name: 'Marketer List', href: route('admin.marketers.index'), icon: '📊' },
      { name: 'Live Tracking', href: route('admin.marketers.live'), icon: '🛰️' },
    ]
  },
  {
    label: 'Attendance Management',
    links: [
      { name: 'Attendance', href: route('admin.attendance.index'), icon: '🕒' },
      { name: 'View Tasks', href: route('admin.tasks.index'), icon: '✅' },
    ]
  },
  {
    label: 'Leave Management',
    links: [
      { name: 'Leave Types', href: route('admin.leave-types.index'), icon: '📌' },
      { name: 'Holidays', href: route('admin.holidays.index'), icon: '🎊' },
      { name: 'Leave Assignments', href: route('admin.leave-assignments.index'), icon: '📝' },
      { name: 'Leave Requests', href: route('admin.leave-applications.index'), icon: '📝' },
    ]
  },
  {
    label: 'Organization Settings',
    links: [
      { name: 'Departments', href: route('admin.departments.index'), icon: '🏢' },
      { name: 'Designations', href: route('admin.designations.index'), icon: '🏷️' },
      { name: 'Locations', href: route('admin.locations.index'), icon: '📍' },
      { name: 'Admins / Managers', href: route('admin.users.index'), icon: '🛡️' },
    ]
  },
  {
    label: 'Reports',
    links: [
      { name: 'Salary Report', href: route('admin.salary-report.index'), icon: '💰' }
    ]
  }
]

function reloadPage() {
  // Simple refresh if needed
  window.location.reload()
}
</script>

<style scoped>
.section-card { border-radius: 10px; overflow: hidden; }
.section-header { display: flex; align-items: center; justify-content: space-between; }
.section-badge { font-size: 14px; }

/* link card inside a section */
.link-card {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 10px 12px;
  border-radius: 8px;
  transition: all .15s ease;
  background: white;
  border: 1px solid transparent;
  text-decoration: none;
  color: inherit;
}
.link-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 6px 18px rgba(15,23,42,0.06);
  border-color: rgba(14,165,233,0.06);
}
.link-card:focus {
  outline: 3px solid rgba(99,102,241,0.12);
}
.link-card .icon { min-width: 34px; display:flex; align-items:center; justify-content:center; font-size:18px }

/* disabled link style (if no href) */
.link-card-disabled { opacity: 0.6; pointer-events: none; }

/* smaller chev */
.chev { font-size: 18px; color: #cbd5e1 }

/* responsive spacing */
@media (max-width: 768px) {
  .section-badge { width: 38px; height: 38px; font-size: 12px; }
}
</style>
