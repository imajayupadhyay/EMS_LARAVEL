<template>
  <Head title="Upcoming Holidays" />
  <div class="holidays-wrapper">
    <div class="holidays-container">
      <!-- Header -->
      <h1 class="holidays-title">🎉 Upcoming Holidays</h1>

      <!-- Filters -->
      <div class="filters">
        <select v-model="type" class="filter-select">
          <option value="">All Types</option>
          <option v-for="t in types" :key="t" :value="t">
            {{ t.charAt(0).toUpperCase() + t.slice(1) }}
          </option>
        </select>

        <select v-model="month" class="filter-select">
          <option value="">All Months</option>
          <option v-for="m in 12" :key="m" :value="m">
            {{ new Date(0, m - 1).toLocaleString("default", { month: "long" }) }}
          </option>
        </select>
      </div>

      <!-- Holiday Cards -->
      <div class="holiday-grid">
        <div v-for="h in holidays.data" :key="h.id" class="holiday-card">
          <span
            class="holiday-badge"
            :class="h.type === 'public' ? 'badge-green' : 'badge-blue'"
          >
            {{ h.type.toUpperCase() }}
          </span>

          <h2 class="holiday-name">{{ h.name }}</h2>
          <p class="holiday-date">📅 {{ formatDate(h.date) }}</p>
          <p class="holiday-day">🗓 {{ formatDay(h.date) }}</p>
        </div>
      </div>

      <!-- Pagination -->
      <div class="pagination">
        <button
          v-if="holidays.prev_page_url"
          @click="router.get(holidays.prev_page_url)"
          class="btn-pagination"
        >
          ⬅ Previous
        </button>
        <button
          v-if="holidays.next_page_url"
          @click="router.get(holidays.next_page_url)"
          class="btn-pagination"
        >
          Next ➡
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from "vue";
import { router, Head } from "@inertiajs/vue3";
import EmployeeLayout from "@/Layouts/EmployeeLayout.vue";

const props = defineProps({
  holidays: Object,
  filters: Object,
  types: Array,
});

defineOptions({ layout: EmployeeLayout });

const type = ref(props.filters?.type || "");
const month = ref(props.filters?.month || "");

watch([type, month], () => {
  router.get(
    route("employee.holidays.index"),
    { type: type.value, month: month.value },
    { preserveState: true, replace: true }
  );
});

const formatDate = (d) =>
  new Date(d).toLocaleDateString("en-GB", {
    day: "2-digit",
    month: "long",
    year: "numeric",
  });

const formatDay = (d) =>
  new Date(d).toLocaleDateString("en-GB", { weekday: "long" });
</script>

<style scoped>
/* Page wrapper */
.holidays-wrapper {
  max-width: 1200px;
  margin: 0 auto;
  padding: 24px;
}

/* Main container */
.holidays-container {
  background: #fff;
  border-radius: 12px;
  padding: 32px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
  border: 1px solid #eee;
}

/* Title */
.holidays-title {
  font-size: 28px;
  font-weight: 800;
  color: #f97316;
  margin-bottom: 24px;
}

/* Filters */
.filters {
  display: flex;
  flex-wrap: wrap;
  gap: 16px;
  margin-bottom: 32px;
  background: #fff7ed;
  padding: 16px;
  border-radius: 8px;
  border: 1px solid #fde7d2;
}

.filter-select {
  padding: 8px 12px;
  border-radius: 6px;
  border: 1px solid #ccc;
  font-size: 14px;
}

/* Grid */
.holiday-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
  gap: 20px;
}

/* Holiday card */
.holiday-card {
  position: relative;
  background: #fff;
  border: 1px solid #eee;
  border-radius: 10px;
  padding: 20px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
  transition: all 0.2s ease;
}

.holiday-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
}

/* Badge */
.holiday-badge {
  position: absolute;
  top: 12px;
  right: 12px;
  padding: 4px 10px;
  border-radius: 999px;
  font-size: 11px;
  font-weight: bold;
  color: #fff;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.badge-green {
  background: #16a34a;
}

.badge-blue {
  background: #2563eb;
}

/* Holiday Info */
.holiday-name {
  font-size: 18px;
  font-weight: 700;
  margin-bottom: 12px;
  color: #333;
}

.holiday-date {
  font-size: 14px;
  color: #444;
  margin-bottom: 6px;
}

.holiday-day {
  font-size: 13px;
  color: #777;
}

/* Pagination */
.pagination {
  display: flex;
  justify-content: space-between;
  margin-top: 24px;
}

.btn-pagination {
  background: #f97316;
  color: white;
  font-weight: 600;
  padding: 10px 16px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  transition: background 0.2s;
}

.btn-pagination:hover {
  background: #ea580c;
}
</style>
