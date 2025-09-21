<template>
  <AdminLayout>
    <div class="dashboard-container">
      <!-- Header Section -->
      <div class="dashboard-header">
        <div class="header-content">
          <div class="welcome-section">
            <h1 class="welcome-title">
              Welcome back, <span class="admin-name">{{ adminName }}</span> 👋
            </h1>
            <p class="welcome-subtitle">Quick access to admin sections</p>
          </div>

          <div class="header-actions">
            <button @click="reloadPage" class="refresh-btn">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="23 4 23 10 17 10"/>
                <polyline points="1 20 1 14 7 14"/>
                <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/>
              </svg>
              Refresh
            </button>
          </div>
        </div>
      </div>

      <!-- Quick Links Section -->
      <div class="section-header">
        <h2 class="section-title">Quick Links</h2>
        <p class="section-subtitle">Click any card to navigate to that section</p>
      </div>

      <!-- Cards Grid -->
      <div class="cards-grid">
        <div v-for="section in navSections" :key="section.label" class="section-card">
          <!-- Card Header -->
          <div class="card-header">
            <div class="header-left">
              <div class="section-icon">
                <span class="icon-text">{{ section.label.charAt(0) }}</span>
              </div>
              <div class="header-info">
                <h3 class="section-name">{{ section.label }}</h3>
                <p class="section-count">{{ section.links.length }} {{ section.links.length === 1 ? 'link' : 'links' }}</p>
              </div>
            </div>
          </div>

          <!-- Card Body with Links -->
          <div class="card-body">
            <Link
              v-for="link in section.links"
              :key="link.name"
              :href="link.href"
              class="link-item"
              :class="{ 'link-disabled': !link.href }"
            >
              <div class="link-content">
                <div class="link-icon">{{ link.icon || '🔗' }}</div>
                <div class="link-info">
                  <span class="link-title">{{ link.name }}</span>
                  <span class="link-description">Go to {{ link.name.toLowerCase() }}</span>
                </div>
              </div>
              <div class="link-arrow">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <polyline points="9 18 15 12 9 6"/>
                </svg>
              </div>
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

// Admin name from Inertia props
const adminName = ref((typeof window !== 'undefined' && window.page && window.page.props && window.page.props.auth && window.page.props.auth.user)
  ? window.page.props.auth.user.name
  : 'Admin')

// Navigation sections matching sidebar
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
  window.location.reload()
}
</script>

<style scoped>
/* Container */
.dashboard-container {
  padding: 1.5rem;
  max-width: 1400px;
  margin: 0 auto;
}

/* Header Section */
.dashboard-header {
  margin-bottom: 2rem;
}

.header-content {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  flex-wrap: wrap;
}

.welcome-section {
  flex: 1;
}

.welcome-title {
  font-size: 1.875rem;
  font-weight: 700;
  color: #1f2937;
  margin: 0;
  line-height: 1.3;
}

.admin-name {
  background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.welcome-subtitle {
  font-size: 0.875rem;
  color: #6b7280;
  margin: 0.5rem 0 0;
}

.header-actions {
  display: flex;
  gap: 0.75rem;
}

.refresh-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.625rem 1.25rem;
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 10px;
  color: #6b7280;
  font-size: 0.875rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
}

.refresh-btn:hover {
  background: #f9fafb;
  border-color: #8b5cf6;
  color: #8b5cf6;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(139, 92, 246, 0.15);
}

.refresh-btn svg {
  flex-shrink: 0;
}

/* Section Header */
.section-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 1.5rem;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.section-title {
  font-size: 1.25rem;
  font-weight: 600;
  color: #1f2937;
  margin: 0;
}

.section-subtitle {
  font-size: 0.875rem;
  color: #6b7280;
  margin: 0;
}

/* Cards Grid */
.cards-grid {
  display: grid;
  gap: 1.5rem;
  grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
}

@media (max-width: 768px) {
  .cards-grid {
    grid-template-columns: 1fr;
  }
}

/* Section Card */
.section-card {
  background: white;
  border: 1px solid #f3f4f6;
  border-radius: 16px;
  overflow: hidden;
  transition: all 0.3s ease;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.section-card:hover {
  box-shadow: 0 10px 25px rgba(139, 92, 246, 0.1);
  border-color: #e9d5ff;
  transform: translateY(-2px);
}

/* Card Header */
.card-header {
  padding: 1.25rem 1.5rem;
  background: linear-gradient(135deg, #faf5ff 0%, #f3e8ff 100%);
  border-bottom: 1px solid #f3f4f6;
}

.header-left {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.section-icon {
  width: 3rem;
  height: 3rem;
  background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  box-shadow: 0 4px 12px rgba(139, 92, 246, 0.25);
}

.icon-text {
  font-size: 1.125rem;
  font-weight: 700;
  color: white;
}

.header-info {
  flex: 1;
}

.section-name {
  font-size: 1rem;
  font-weight: 600;
  color: #1f2937;
  margin: 0;
}

.section-count {
  font-size: 0.75rem;
  color: #8b5cf6;
  margin: 0.25rem 0 0;
  font-weight: 500;
}

/* Card Body */
.card-body {
  padding: 1rem;
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

/* Link Item */
.link-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0.875rem 1rem;
  border-radius: 10px;
  background: white;
  border: 1px solid transparent;
  text-decoration: none;
  color: inherit;
  transition: all 0.2s ease;
  cursor: pointer;
}

.link-item:hover {
  background: linear-gradient(135deg, #faf5ff 0%, #f9fafb 100%);
  border-color: #e9d5ff;
  transform: translateX(4px);
  box-shadow: 0 2px 8px rgba(139, 92, 246, 0.08);
}

.link-content {
  display: flex;
  align-items: center;
  gap: 0.875rem;
  flex: 1;
  min-width: 0;
}

.link-icon {
  font-size: 1.375rem;
  flex-shrink: 0;
  width: 2.25rem;
  height: 2.25rem;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f9fafb;
  border-radius: 8px;
  transition: all 0.2s;
}

.link-item:hover .link-icon {
  background: linear-gradient(135deg, #ede9fe 0%, #e9d5ff 100%);
  transform: scale(1.1);
}

.link-info {
  display: flex;
  flex-direction: column;
  gap: 0.125rem;
  min-width: 0;
}

.link-title {
  font-size: 0.9375rem;
  font-weight: 500;
  color: #1f2937;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.link-description {
  font-size: 0.75rem;
  color: #9ca3af;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.link-arrow {
  color: #d1d5db;
  flex-shrink: 0;
  transition: all 0.2s;
}

.link-item:hover .link-arrow {
  color: #8b5cf6;
  transform: translateX(2px);
}

/* Disabled Link */
.link-disabled {
  opacity: 0.5;
  pointer-events: none;
}

/* Responsive Design */
@media (max-width: 640px) {
  .dashboard-container {
    padding: 1rem;
  }

  .welcome-title {
    font-size: 1.5rem;
  }

  .header-content {
    flex-direction: column;
    align-items: flex-start;
  }

  .section-icon {
    width: 2.5rem;
    height: 2.5rem;
  }

  .icon-text {
    font-size: 1rem;
  }

  .link-icon {
    font-size: 1.125rem;
    width: 2rem;
    height: 2rem;
  }
}

/* Animation */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.section-card {
  animation: fadeInUp 0.5s ease-out;
}

.section-card:nth-child(1) { animation-delay: 0.05s; }
.section-card:nth-child(2) { animation-delay: 0.1s; }
.section-card:nth-child(3) { animation-delay: 0.15s; }
.section-card:nth-child(4) { animation-delay: 0.2s; }
.section-card:nth-child(5) { animation-delay: 0.25s; }
.section-card:nth-child(6) { animation-delay: 0.3s; }
</style>