<template>
  <div class="dashboard-container">
    <!-- Welcome Header -->
    <div class="welcome-header">
      <div class="welcome-content">
        <h1 class="welcome-title">
          Welcome back, <span class="user-name">{{ auth?.user?.first_name || auth?.user?.name || 'Employee' }}</span> 
          <span class="wave-emoji">👋</span>
        </h1>
        <p class="welcome-subtitle">Here's your activity summary for today</p>
      </div>
      <div class="date-badge">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
          <line x1="16" y1="2" x2="16" y2="6"></line>
          <line x1="8" y1="2" x2="8" y2="6"></line>
          <line x1="3" y1="10" x2="21" y2="10"></line>
        </svg>
        <span>{{ getCurrentDate() }}</span>
      </div>
    </div>

    <!-- Punch Status Banner -->
    <div :class="isPunchedIn ? 'status-banner status-active' : 'status-banner status-inactive'">
      <div class="status-icon">
        <svg v-if="isPunchedIn" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <path d="M22 11.08V12a10 10 0 11-5.93-9.14"></path>
          <polyline points="22 4 12 14.01 9 11.01"></polyline>
        </svg>
        <svg v-else width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <circle cx="12" cy="12" r="10"></circle>
          <line x1="15" y1="9" x2="9" y2="15"></line>
          <line x1="9" y1="9" x2="15" y2="15"></line>
        </svg>
      </div>
      <div class="status-content">
        <h3 class="status-title">{{ isPunchedIn ? 'You are Punched In' : 'You are Punched Out' }}</h3>
        <p class="status-subtitle">{{ isPunchedIn ? 'Your attendance is being tracked' : 'Please punch in to start tracking' }}</p>
      </div>
      <div class="status-time" v-if="isPunchedIn">
        <span class="time-label">Since</span>
        <span class="time-value">09:30 AM</span>
      </div>
    </div>

    <!-- Stats Cards Grid -->
    

    <!-- Quick Actions -->
    <div class="quick-actions-section">
      <h2 class="section-title">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="12" cy="12" r="1"></circle>
          <circle cx="12" cy="5" r="1"></circle>
          <circle cx="12" cy="19" r="1"></circle>
        </svg>
        Quick Actions
      </h2>
      
      <div class="actions-grid">
        <!-- Punch In/Out Action -->
        <button @click="goTo('employee.punches.index')" class="action-card action-primary">
          <div class="action-icon">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="10"></circle>
              <polyline points="12 6 12 12 16 14"></polyline>
            </svg>
          </div>
          <div class="action-content">
            <h3 class="action-title">Punch In / Out</h3>
            <p class="action-description">Track your attendance</p>
          </div>
          <svg class="action-arrow" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="5" y1="12" x2="19" y2="12"></line>
            <polyline points="12 5 19 12 12 19"></polyline>
          </svg>
        </button>

        <!-- Apply for Leave Action -->
        <button @click="goTo('employee.leave-applications.index')" class="action-card action-secondary">
          <div class="action-icon">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"></path>
              <polyline points="14 2 14 8 20 8"></polyline>
              <line x1="12" y1="18" x2="12" y2="12"></line>
              <line x1="9" y1="15" x2="15" y2="15"></line>
            </svg>
          </div>
          <div class="action-content">
            <h3 class="action-title">Apply for Leave</h3>
            <p class="action-description">Submit leave request</p>
          </div>
          <svg class="action-arrow" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="5" y1="12" x2="19" y2="12"></line>
            <polyline points="12 5 19 12 12 19"></polyline>
          </svg>
        </button>

        <!-- Daily Tasks Action -->
        <button @click="goTo('employee.tasks.index')" class="action-card action-accent">
          <div class="action-icon">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M9 11l3 3L22 4"></path>
              <path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"></path>
            </svg>
          </div>
          <div class="action-content">
            <h3 class="action-title">Daily Tasks</h3>
            <p class="action-description">View your tasks</p>
          </div>
          <svg class="action-arrow" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="5" y1="12" x2="19" y2="12"></line>
            <polyline points="12 5 19 12 12 19"></polyline>
          </svg>
        </button>

        <!-- Attendance Action -->
        <button @click="goTo('employee.attendance.index')" class="action-card action-info">
          <div class="action-icon">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
              <line x1="16" y1="2" x2="16" y2="6"></line>
              <line x1="8" y1="2" x2="8" y2="6"></line>
              <line x1="3" y1="10" x2="21" y2="10"></line>
            </svg>
          </div>
          <div class="action-content">
            <h3 class="action-title">Attendance</h3>
            <p class="action-description">Check attendance records</p>
          </div>
          <svg class="action-arrow" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="5" y1="12" x2="19" y2="12"></line>
            <polyline points="12 5 19 12 12 19"></polyline>
          </svg>
        </button>
      </div>
    </div>

    <!-- Recent Activity -->
    
  </div>
</template>

<script setup>
import { usePage, router } from '@inertiajs/vue3'
import EmployeeLayout from '@/Layouts/EmployeeLayout.vue'

const page = usePage()
const auth = page.props.auth || {}
const workingDays = page.props.workingDays
const totalHours = page.props.totalHours
const remainingLeaves = page.props.remainingLeaves
const isPunchedIn = page.props.isPunchedIn || false

defineOptions({ layout: EmployeeLayout })

const goTo = (routeName) => {
  router.get(route(routeName))
}

const getCurrentDate = () => {
  const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }
  return new Date().toLocaleDateString('en-US', options)
}
</script>

<style scoped>
/* Dashboard Container */
.dashboard-container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 1.5rem;
}

/* Welcome Header */
.welcome-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 2rem;
  gap: 1rem;
  flex-wrap: wrap;
}

.welcome-content {
  flex: 1;
}

.welcome-title {
  font-size: 2rem;
  font-weight: 700;
  color: #111827;
  margin: 0 0 0.5rem 0;
  line-height: 1.2;
}

.user-name {
  background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.wave-emoji {
  display: inline-block;
  animation: wave 2s infinite;
  transform-origin: 70% 70%;
}

@keyframes wave {
  0%, 100% { transform: rotate(0deg); }
  10%, 30% { transform: rotate(14deg); }
  20% { transform: rotate(-8deg); }
  40% { transform: rotate(-4deg); }
  50% { transform: rotate(10deg); }
}

.welcome-subtitle {
  color: #6b7280;
  font-size: 1rem;
}

.date-badge {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1.25rem;
  background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
  color: white;
  border-radius: 12px;
  font-weight: 500;
  font-size: 0.875rem;
}

/* Status Banner */
.status-banner {
  display: flex;
  align-items: center;
  gap: 1.5rem;
  padding: 1.5rem;
  border-radius: 16px;
  margin-bottom: 2rem;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.status-active {
  background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
  border: 2px solid #86efac;
}

.status-inactive {
  background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
  border: 2px solid #fca5a5;
}

.status-icon {
  width: 56px;
  height: 56px;
  border-radius: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.status-active .status-icon {
  background: white;
  color: #10b981;
}

.status-inactive .status-icon {
  background: white;
  color: #ef4444;
}

.status-content {
  flex: 1;
}

.status-title {
  font-size: 1.25rem;
  font-weight: 700;
  margin: 0 0 0.25rem 0;
}

.status-active .status-title {
  color: #065f46;
}

.status-inactive .status-title {
  color: #991b1b;
}

.status-subtitle {
  font-size: 0.875rem;
  margin: 0;
}

.status-active .status-subtitle {
  color: #047857;
}

.status-inactive .status-subtitle {
  color: #b91c1c;
}

.status-time {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
}

.time-label {
  font-size: 0.75rem;
  opacity: 0.7;
}

.time-value {
  font-size: 1.125rem;
  font-weight: 600;
}

/* Stats Grid */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.stat-card {
  background: white;
  border-radius: 16px;
  padding: 1.5rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
  transition: all 0.3s ease;
  border: 1px solid #f3f4f6;
}

.stat-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
}

.stat-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 1rem;
}

.stat-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.stat-icon-primary {
  background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
  color: white;
}

.stat-icon-success {
  background: linear-gradient(135deg, #059669 0%, #10b981 100%);
  color: white;
}

.stat-icon-warning {
  background: linear-gradient(135deg, #d97706 0%, #f59e0b 100%);
  color: white;
}

.stat-icon-info {
  background: linear-gradient(135deg, #0891b2 0%, #06b6d4 100%);
  color: white;
}

.stat-badge {
  padding: 0.25rem 0.75rem;
  background: #f3f4f6;
  border-radius: 6px;
  font-size: 0.75rem;
  font-weight: 600;
  color: #6b7280;
}

.stat-content {
  margin-bottom: 1rem;
}

.stat-value {
  font-size: 2.5rem;
  font-weight: 700;
  color: #111827;
  margin: 0;
  line-height: 1;
}

.stat-label {
  font-size: 0.875rem;
  color: #6b7280;
  margin: 0.5rem 0 0 0;
}

.stat-footer {
  margin-top: 1rem;
}

.progress-bar {
  height: 6px;
  background: #f3f4f6;
  border-radius: 3px;
  overflow: hidden;
  margin-bottom: 0.5rem;
}

.progress-fill {
  height: 100%;
  background: linear-gradient(90deg, #1e3a8a 0%, #3b82f6 100%);
  border-radius: 3px;
  transition: width 0.6s ease;
}

.progress-text {
  font-size: 0.75rem;
  color: #6b7280;
}

.stat-detail {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: #6b7280;
  font-size: 0.875rem;
}

.stat-detail svg {
  color: #10b981;
}

/* Section Title */
.section-title {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 1.25rem;
  font-weight: 700;
  color: #111827;
  margin-bottom: 1.5rem;
}

/* Quick Actions */
.quick-actions-section {
  margin-bottom: 2rem;
}

.actions-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 1rem;
}

.action-card {
  background: white;
  border-radius: 16px;
  padding: 1.5rem;
  display: flex;
  align-items: center;
  gap: 1rem;
  border: 2px solid transparent;
  cursor: pointer;
  transition: all 0.3s ease;
  text-align: left;
}

.action-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
}

.action-primary {
  border-color: #1e3a8a;
}

.action-primary:hover {
  background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
  color: white;
}

.action-secondary {
  border-color: #059669;
}

.action-secondary:hover {
  background: linear-gradient(135deg, #059669 0%, #10b981 100%);
  color: white;
}

.action-accent {
  border-color: #d97706;
}

.action-accent:hover {
  background: linear-gradient(135deg, #d97706 0%, #f59e0b 100%);
  color: white;
}

.action-info {
  border-color: #0891b2;
}

.action-info:hover {
  background: linear-gradient(135deg, #0891b2 0%, #06b6d4 100%);
  color: white;
}

.action-icon {
  width: 56px;
  height: 56px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f3f4f6;
  flex-shrink: 0;
  transition: all 0.3s ease;
}

.action-card:hover .action-icon {
  background: rgba(255, 255, 255, 0.2);
}

.action-content {
  flex: 1;
}

.action-title {
  font-size: 1.125rem;
  font-weight: 600;
  margin: 0 0 0.25rem 0;
}

.action-description {
  font-size: 0.875rem;
  opacity: 0.8;
  margin: 0;
}

.action-arrow {
  transition: transform 0.3s ease;
}

.action-card:hover .action-arrow {
  transform: translateX(4px);
}

/* Recent Activity */
.recent-activity-section {
  margin-bottom: 2rem;
}

.activity-list {
  background: white;
  border-radius: 16px;
  padding: 1rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.activity-item {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem;
  border-radius: 12px;
  transition: background 0.2s;
}

.activity-item:hover {
  background: #f9fafb;
}

.activity-icon {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.activity-icon-success {
  background: #dcfce7;
  color: #059669;
}

.activity-icon-info {
  background: #dbeafe;
  color: #1e40af;
}

.activity-icon-warning {
  background: #fed7aa;
  color: #d97706;
}

.activity-content {
  flex: 1;
}

.activity-title {
  font-weight: 600;
  color: #111827;
  margin: 0 0 0.25rem 0;
}

.activity-time {
  font-size: 0.875rem;
  color: #6b7280;
  margin: 0;
}

/* Responsive */
@media (max-width: 768px) {
  .dashboard-container {
    padding: 1rem;
  }

  .welcome-title {
    font-size: 1.5rem;
  }

  .welcome-header {
    flex-direction: column;
    align-items: flex-start;
  }

  .date-badge {
    width: 100%;
    justify-content: center;
  }

  .status-banner {
    flex-direction: column;
    text-align: center;
    gap: 1rem;
  }

  .status-time {
    align-items: center;
  }

  .stats-grid {
    grid-template-columns: 1fr;
  }

  .actions-grid {
    grid-template-columns: 1fr;
  }

  .action-card {
    padding: 1rem;
  }

  .stat-value {
    font-size: 2rem;
  }
}

@media (max-width: 640px) {
  .welcome-title {
    font-size: 1.25rem;
  }

  .stat-card {
    padding: 1.25rem;
  }

  .action-icon {
    width: 48px;
    height: 48px;
  }

  .action-title {
    font-size: 1rem;
  }
}

/* Animations */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.stat-card,
.action-card,
.activity-item {
  animation: fadeInUp 0.5s ease-out;
}

.stat-card:nth-child(1) { animation-delay: 0.1s; }
.stat-card:nth-child(2) { animation-delay: 0.2s; }
.stat-card:nth-child(3) { animation-delay: 0.3s; }
.stat-card:nth-child(4) { animation-delay: 0.4s; }

.action-card:nth-child(1) { animation-delay: 0.1s; }
.action-card:nth-child(2) { animation-delay: 0.2s; }
.action-card:nth-child(3) { animation-delay: 0.3s; }
.action-card:nth-child(4) { animation-delay: 0.4s; }

/* Focus States */
.action-card:focus {
  outline: none;
  box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.2);
}

/* Loading States */
@keyframes shimmer {
  0% {
    background-position: -1000px 0;
  }
  100% {
    background-position: 1000px 0;
  }
}

.loading {
  background: linear-gradient(90deg, #f3f4f6 25%, #e5e7eb 50%, #f3f4f6 75%);
  background-size: 1000px 100%;
  animation: shimmer 2s infinite;
}
</style>