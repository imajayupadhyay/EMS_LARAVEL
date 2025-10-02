<style scoped>
/* Container */
.page-container {
  padding: 1.5rem;
  max-width: 1200px;
  margin: 0 auto;
}

/* Page Header */
.page-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 2rem;
  flex-wrap: wrap;
  gap: 1rem;
}

.header-left {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.icon-wrapper {
  width: 3rem;
  height: 3rem;
  background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  box-shadow: 0 4px 12px rgba(139, 92, 246, 0.25);
}

.page-title {
  font-size: 1.875rem;
  font-weight: 700;
  color: #1f2937;
  margin: 0;
}

.page-subtitle {
  font-size: 0.875rem;
  color: #6b7280;
  margin: 0.25rem 0 0;
}

/* Search Bar */
.search-bar {
  background: white;
  border-radius: 12px;
  padding: 1rem;
  margin-bottom: 2rem;
  border: 1px solid #f3f4f6;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.search-input-wrapper {
  position: relative;
  max-width: 500px;
}

.search-input {
  width: 100%;
  padding: 0.75rem 1rem 0.75rem 2.75rem;
  border: 1px solid #d1d5db;
  border-radius: 10px;
  font-size: 0.9375rem;
  color: #1f2937;
  transition: all 0.2s;
}

.search-input:focus {
  outline: none;
  border-color: #8b5cf6;
  box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
}

.search-icon {
  position: absolute;
  left: 1rem;
  top: 50%;
  transform: translateY(-50%);
  color: #9ca3af;
}

/* Policies Grid */
.policies-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.policy-card {
  background: white;
  border-radius: 16px;
  padding: 1.5rem;
  border: 1px solid #f3f4f6;
  transition: all 0.3s ease;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
  cursor: pointer;
}

.policy-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 10px 25px rgba(139, 92, 246, 0.15);
  border-color: #e9d5ff;
}

.policy-header {
  display: flex;
  align-items: flex-start;
  gap: 1rem;
  margin-bottom: 1rem;
}

.policy-icon {
  width: 2.5rem;
  height: 2.5rem;
  background: linear-gradient(135deg, #ddd6fe 0%, #c4b5fd 100%);
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #5b21b6;
  flex-shrink: 0;
}

.policy-title-section {
  flex: 1;
  min-width: 0;
}

.policy-title {
  font-size: 1.125rem;
  font-weight: 600;
  color: #1f2937;
  margin: 0 0 0.25rem;
  word-wrap: break-word;
}

.policy-date {
  font-size: 0.75rem;
  color: #6b7280;
  display: flex;
  align-items: center;
  gap: 0.25rem;
}

.policy-content-preview {
  font-size: 0.875rem;
  color: #6b7280;
  line-height: 1.6;
  margin-bottom: 1rem;
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.policy-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding-top: 1rem;
  border-top: 1px solid #f3f4f6;
}

.audience-badge {
  padding: 0.25rem 0.75rem;
  background: #f3f4f6;
  color: #6b7280;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 500;
  text-transform: capitalize;
}

.view-btn {
  padding: 0.5rem 1rem;
  background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 0.875rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
}

.view-btn:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(139, 92, 246, 0.25);
}

/* Empty State */
.empty-state {
  background: white;
  border-radius: 16px;
  padding: 4rem 2rem;
  text-align: center;
  border: 1px solid #f3f4f6;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.empty-state svg {
  margin: 0 auto 1.5rem;
  color: #d1d5db;
}

.empty-state h3 {
  font-size: 1.125rem;
  font-weight: 600;
  color: #1f2937;
  margin: 0 0 0.5rem;
}

.empty-state p {
  font-size: 0.875rem;
  color: #6b7280;
  margin: 0;
}

/* Modal */
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.5);
  backdrop-filter: blur(4px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 1rem;
}

.modal-container {
  background: white;
  border-radius: 16px;
  width: 100%;
  max-width: 800px;
  max-height: 90vh;
  display: flex;
  flex-direction: column;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
}

.modal-header {
  padding: 1.5rem;
  background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-radius: 16px 16px 0 0;
}

.modal-title {
  font-size: 1.25rem;
  font-weight: 700;
  color: white;
  margin: 0;
}

.modal-close {
  background: rgba(255, 255, 255, 0.2);
  border: none;
  color: white;
  width: 2.5rem;
  height: 2.5rem;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: background 0.2s;
}

.modal-close:hover {
  background: rgba(255, 255, 255, 0.3);
}

.modal-body {
  padding: 2rem;
  overflow-y: auto;
  flex: 1;
}

.modal-meta {
  display: flex;
  align-items: center;
  gap: 1.5rem;
  padding-bottom: 1.5rem;
  margin-bottom: 1.5rem;
  border-bottom: 1px solid #f3f4f6;
  font-size: 0.875rem;
  color: #6b7280;
}

.modal-content {
  font-size: 1rem;
  line-height: 1.75;
  color: #374151;
}

.modal-content :deep(h1),
.modal-content :deep(h2),
.modal-content :deep(h3) {
  font-weight: 600;
  color: #1f2937;
  margin-top: 1.5rem;
  margin-bottom: 0.75rem;
}

.modal-content :deep(p) {
  margin-bottom: 1rem;
}

.modal-content :deep(ul),
.modal-content :deep(ol) {
  margin-bottom: 1rem;
  padding-left: 1.5rem;
}

.modal-content :deep(li) {
  margin-bottom: 0.5rem;
}

/* Transitions */
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

/* Responsive */
@media (max-width: 768px) {
  .page-container {
    padding: 1rem;
  }

  .policies-grid {
    grid-template-columns: 1fr;
  }

  .modal-body {
    padding: 1.5rem;
  }
}
</style>

<script setup>
import EmployeeLayout from '@/Layouts/EmployeeLayout.vue'
import { ref, computed } from 'vue'

const props = defineProps({
  policies: { type: Array, default: () => [] }
})

const searchQuery = ref('')
const showModal = ref(false)
const selectedPolicy = ref(null)

// Filter policies based on search
const filteredPolicies = computed(() => {
  if (!searchQuery.value) return props.policies

  const query = searchQuery.value.toLowerCase()
  return props.policies.filter(policy =>
    policy.title.toLowerCase().includes(query) ||
    policy.content.toLowerCase().includes(query)
  )
})

// Open policy modal
const openPolicy = (policy) => {
  selectedPolicy.value = policy
  showModal.value = true
}

// Close modal
const closeModal = () => {
  showModal.value = false
  selectedPolicy.value = null
}

// Format date
const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

// Strip HTML for preview
const stripHtml = (html) => {
  const tmp = document.createElement('div')
  tmp.innerHTML = html
  return tmp.textContent || tmp.innerText || ''
}
</script>

<template>
  <EmployeeLayout>
    <div class="page-container">
      <!-- Page Header -->
      <div class="page-header">
        <div class="header-left">
          <div class="icon-wrapper">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
              <polyline points="14 2 14 8 20 8"/>
              <line x1="16" y1="13" x2="8" y2="13"/>
              <line x1="16" y1="17" x2="8" y2="17"/>
              <polyline points="10 9 9 9 8 9"/>
            </svg>
          </div>
          <div>
            <h1 class="page-title">Company Policies</h1>
            <p class="page-subtitle">View and understand company policies and guidelines</p>
          </div>
        </div>
      </div>

      <!-- Search Bar -->
      <div class="search-bar">
        <div class="search-input-wrapper">
          <svg class="search-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="11" cy="11" r="8"/>
            <path d="m21 21-4.35-4.35"/>
          </svg>
          <input
            type="text"
            v-model="searchQuery"
            placeholder="Search policies by title or content..."
            class="search-input"
          />
        </div>
      </div>

      <!-- Policies Grid -->
      <div v-if="filteredPolicies.length > 0" class="policies-grid">
        <div
          v-for="policy in filteredPolicies"
          :key="policy.id"
          class="policy-card"
          @click="openPolicy(policy)"
        >
          <div class="policy-header">
            <div class="policy-icon">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                <polyline points="14 2 14 8 20 8"/>
              </svg>
            </div>
            <div class="policy-title-section">
              <h2 class="policy-title">{{ policy.title }}</h2>
              <div class="policy-date">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <circle cx="12" cy="12" r="10"/>
                  <polyline points="12 6 12 12 16 14"/>
                </svg>
                {{ formatDate(policy.created_at) }}
              </div>
            </div>
          </div>

          <div class="policy-content-preview">
            {{ stripHtml(policy.content) }}
          </div>

          <div class="policy-footer">
            <span class="audience-badge">{{ policy.audience || 'All' }}</span>
            <button class="view-btn">
              View Policy
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="5" y1="12" x2="19" y2="12"/>
                <polyline points="12 5 19 12 12 19"/>
              </svg>
            </button>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="empty-state">
        <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
          <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
          <polyline points="14 2 14 8 20 8"/>
          <line x1="16" y1="13" x2="8" y2="13"/>
          <line x1="16" y1="17" x2="8" y2="17"/>
        </svg>
        <h3>No Policies Found</h3>
        <p>{{ searchQuery ? 'No policies match your search criteria.' : 'No company policies are currently available.' }}</p>
      </div>

      <!-- Policy Detail Modal -->
      <Transition name="modal">
        <div v-if="showModal && selectedPolicy" class="modal-overlay" @click="closeModal">
          <div class="modal-container" @click.stop>
            <div class="modal-header">
              <h2 class="modal-title">{{ selectedPolicy.title }}</h2>
              <button @click="closeModal" class="modal-close">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <line x1="18" y1="6" x2="6" y2="18"/>
                  <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
              </button>
            </div>

            <div class="modal-body">
              <div class="modal-meta">
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                    <line x1="16" y1="2" x2="16" y2="6"/>
                    <line x1="8" y1="2" x2="8" y2="6"/>
                    <line x1="3" y1="10" x2="21" y2="10"/>
                  </svg>
                  Published: {{ formatDate(selectedPolicy.created_at) }}
                </div>
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                  </svg>
                  Audience: {{ selectedPolicy.audience || 'All Employees' }}
                </div>
              </div>

              <div class="modal-content" v-html="selectedPolicy.content"></div>
            </div>
          </div>
        </div>
      </Transition>
    </div>
  </EmployeeLayout>
</template>
