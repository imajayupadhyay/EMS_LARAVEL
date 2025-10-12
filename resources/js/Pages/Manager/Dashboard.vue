<template>
  <div class="space-y-6">
    <!-- Welcome Section -->
    <div class="relative overflow-hidden bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 rounded-2xl shadow-xl">
      <div class="absolute inset-0 bg-black opacity-5"></div>
      <div class="absolute top-0 right-0 -mt-4 -mr-4 w-48 h-48 bg-white opacity-5 rounded-full"></div>
      <div class="absolute bottom-0 left-0 -mb-8 -ml-8 w-64 h-64 bg-white opacity-5 rounded-full"></div>
      <div class="relative p-8 md:p-10">
        <div class="flex items-center justify-between flex-wrap gap-4">
          <div>
            <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">
              Welcome back, {{ userName }}! 👋
            </h1>
            <p class="text-blue-100 text-lg">
              {{ greetingMessage }}
            </p>
          </div>
          <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20">
            <div class="text-white/80 text-sm">Today's Date</div>
            <div class="text-white font-semibold text-lg">{{ currentDate }}</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div class="group bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 border border-gray-100">
        <div class="p-6">
          <div class="flex items-center justify-between">
            <div>
              <div class="text-sm font-medium text-gray-500 mb-1">Total Employees</div>
              <div class="text-3xl font-bold text-gray-900">{{ stats.employees }}</div>
            </div>
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-4 group-hover:scale-110 transition-transform">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
              </svg>
            </div>
          </div>
        </div>
      </div>

      <div class="group bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 border border-gray-100">
        <div class="p-6">
          <div class="flex items-center justify-between">
            <div>
              <div class="text-sm font-medium text-gray-500 mb-1">Pending Leaves</div>
              <div class="text-3xl font-bold text-gray-900">{{ stats.pending_leaves }}</div>
            </div>
            <div class="bg-gradient-to-br from-amber-500 to-orange-600 rounded-xl p-4 group-hover:scale-110 transition-transform">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
          </div>
        </div>
      </div>

      <div class="group bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 border border-gray-100">
        <div class="p-6">
          <div class="flex items-center justify-between">
            <div>
              <div class="text-sm font-medium text-gray-500 mb-1">Today's Attendance</div>
              <div class="text-3xl font-bold text-gray-900">{{ stats.today_attendance }}</div>
            </div>
            <div class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl p-4 group-hover:scale-110 transition-transform">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Quick Links Section -->
    <div class="bg-white rounded-xl shadow-lg p-6 md:p-8 border border-gray-100">
      <div class="flex items-center gap-3 mb-6">
        <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg p-2">
          <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
          </svg>
        </div>
        <h2 class="text-2xl font-bold text-gray-900">Quick Actions</h2>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Employee Management -->
        <a :href="route('manager.employees.index')"
           class="group flex flex-col items-center p-6 bg-gradient-to-br from-blue-50 to-indigo-50 hover:from-blue-100 hover:to-indigo-100 rounded-xl border-2 border-blue-200 hover:border-blue-400 transition-all duration-300 hover:shadow-lg transform hover:-translate-y-1">
          <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-4 mb-3 group-hover:scale-110 transition-transform">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
            </svg>
          </div>
          <span class="font-semibold text-gray-900 text-center">Manage Employees</span>
          <span class="text-xs text-gray-600 mt-1">View & Edit</span>
        </a>

        <!-- Attendance Management -->
        <a :href="route('manager.attendance.index')"
           class="group flex flex-col items-center p-6 bg-gradient-to-br from-green-50 to-emerald-50 hover:from-green-100 hover:to-emerald-100 rounded-xl border-2 border-green-200 hover:border-green-400 transition-all duration-300 hover:shadow-lg transform hover:-translate-y-1">
          <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-4 mb-3 group-hover:scale-110 transition-transform">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
            </svg>
          </div>
          <span class="font-semibold text-gray-900 text-center">Attendance</span>
          <span class="text-xs text-gray-600 mt-1">Track & Monitor</span>
        </a>

        <!-- Leave Management -->
        <a :href="route('manager.leaves.index')"
           class="group flex flex-col items-center p-6 bg-gradient-to-br from-amber-50 to-orange-50 hover:from-amber-100 hover:to-orange-100 rounded-xl border-2 border-amber-200 hover:border-amber-400 transition-all duration-300 hover:shadow-lg transform hover:-translate-y-1">
          <div class="bg-gradient-to-br from-amber-500 to-orange-600 rounded-xl p-4 mb-3 group-hover:scale-110 transition-transform">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
          </div>
          <span class="font-semibold text-gray-900 text-center">Leave Requests</span>
          <span class="text-xs text-gray-600 mt-1">Approve & Review</span>
        </a>

        <!-- Shift Management -->
        <a :href="route('manager.shifts.index')"
           class="group flex flex-col items-center p-6 bg-gradient-to-br from-purple-50 to-violet-50 hover:from-purple-100 hover:to-violet-100 rounded-xl border-2 border-purple-200 hover:border-purple-400 transition-all duration-300 hover:shadow-lg transform hover:-translate-y-1">
          <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-4 mb-3 group-hover:scale-110 transition-transform">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
          <span class="font-semibold text-gray-900 text-center">Shift Management</span>
          <span class="text-xs text-gray-600 mt-1">Schedule & Assign</span>
        </a>

        <!-- Leave Assignments -->
        <a :href="route('manager.leave-assignments.index')"
           class="group flex flex-col items-center p-6 bg-gradient-to-br from-pink-50 to-rose-50 hover:from-pink-100 hover:to-rose-100 rounded-xl border-2 border-pink-200 hover:border-pink-400 transition-all duration-300 hover:shadow-lg transform hover:-translate-y-1">
          <div class="bg-gradient-to-br from-pink-500 to-rose-600 rounded-xl p-4 mb-3 group-hover:scale-110 transition-transform">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
          </div>
          <span class="font-semibold text-gray-900 text-center">Leave Assignments</span>
          <span class="text-xs text-gray-600 mt-1">Allocate Leaves</span>
        </a>

        <!-- Holidays -->
        <a :href="route('manager.holidays.index')"
           class="group flex flex-col items-center p-6 bg-gradient-to-br from-cyan-50 to-teal-50 hover:from-cyan-100 hover:to-teal-100 rounded-xl border-2 border-cyan-200 hover:border-cyan-400 transition-all duration-300 hover:shadow-lg transform hover:-translate-y-1">
          <div class="bg-gradient-to-br from-cyan-500 to-teal-600 rounded-xl p-4 mb-3 group-hover:scale-110 transition-transform">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
          <span class="font-semibold text-gray-900 text-center">Holidays</span>
          <span class="text-xs text-gray-600 mt-1">Manage Calendar</span>
        </a>

        <!-- Departments -->
        <a :href="route('manager.departments.index')"
           class="group flex flex-col items-center p-6 bg-gradient-to-br from-indigo-50 to-blue-50 hover:from-indigo-100 hover:to-blue-100 rounded-xl border-2 border-indigo-200 hover:border-indigo-400 transition-all duration-300 hover:shadow-lg transform hover:-translate-y-1">
          <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl p-4 mb-3 group-hover:scale-110 transition-transform">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
          </div>
          <span class="font-semibold text-gray-900 text-center">Departments</span>
          <span class="text-xs text-gray-600 mt-1">Organize Teams</span>
        </a>

        <!-- Designations -->
        <a :href="route('manager.designations.index')"
           class="group flex flex-col items-center p-6 bg-gradient-to-br from-yellow-50 to-amber-50 hover:from-yellow-100 hover:to-amber-100 rounded-xl border-2 border-yellow-200 hover:border-yellow-400 transition-all duration-300 hover:shadow-lg transform hover:-translate-y-1">
          <div class="bg-gradient-to-br from-yellow-500 to-amber-600 rounded-xl p-4 mb-3 group-hover:scale-110 transition-transform">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
            </svg>
          </div>
          <span class="font-semibold text-gray-900 text-center">Designations</span>
          <span class="text-xs text-gray-600 mt-1">Define Roles</span>
        </a>
      </div>
    </div>
  </div>
</template>

<script>
import ManagerLayout from '@/Layouts/ManagerLayout.vue';

export default {
  name: 'ManagerDashboard',
  layout: ManagerLayout,
  props: {
    stats: {
      type: Object,
      default: () => ({ employees: 0, pending_leaves: 0, today_attendance: 0 })
    },
    user: {
      type: Object,
      default: () => ({ name: 'Manager' })
    }
  },
  computed: {
    userName() {
      return this.user?.name || 'Manager';
    },
    greetingMessage() {
      const hour = new Date().getHours();
      if (hour < 12) {
        return "Good morning! Ready to make today productive? Let's manage your team effectively.";
      } else if (hour < 17) {
        return "Good afternoon! Keep up the great work managing your team.";
      } else {
        return "Good evening! Wrapping up the day? Review your team's progress here.";
      }
    },
    currentDate() {
      const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
      return new Date().toLocaleDateString('en-US', options);
    }
  }
};
</script>

<style scoped>
/* small page-level tweaks if required */
</style>
