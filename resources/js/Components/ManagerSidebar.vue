<template>
  <aside :class="['h-screen transition-all duration-200', collapsed ? 'w-20' : 'w-64']" style="background: #0b69a3;">
    <div class="flex flex-col h-full text-white">
      <div class="px-4 py-4 flex items-center gap-3 border-b border-blue-800">
        <div v-if="!collapsed" class="text-lg font-bold">Manager</div>
        <div v-else class="text-center w-full">M</div>
      </div>

      <nav class="flex-1 px-2 py-4 overflow-auto">
        <div v-for="(section, si) in sections" :key="si" class="mb-4">
          <div v-if="!collapsed" class="px-2 text-xs uppercase opacity-80 mb-2">{{ section.title }}</div>
          <ul>
            <li v-for="item in section.items" :key="item.name" class="mb-1">
              <template v-if="safeRoute(item.route || item.href)">
                <Link
                  :href="safeRoute(item.route || item.href)"
                  class="flex items-center gap-3 px-3 py-2 rounded hover:bg-blue-800/60 transition"
                  :class="isActive(item) ? 'bg-blue-800/90' : ''"
                >
                  <span class="w-5 text-center" v-html="item.svg"></span>
                  <span v-if="!collapsed" class="truncate">{{ item.name }}</span>
                </Link>
              </template>

              <template v-else>
                <!-- route missing: show disabled item so UI doesn't break -->
                <div
                  class="flex items-center gap-3 px-3 py-2 rounded opacity-60 cursor-not-allowed"
                  :title="item.name + ' (route missing)'"
                >
                  <span class="w-5 text-center" v-html="item.svg"></span>
                  <span v-if="!collapsed" class="truncate">{{ item.name }}</span>
                </div>
              </template>
            </li>
          </ul>
        </div>
      </nav>

      <div class="px-3 py-4 border-t border-blue-800">
        <button @click="$emit('toggle')" class="w-full flex items-center gap-2 justify-center px-2 py-2 rounded bg-blue-800/30 hover:bg-blue-800/40">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
          </svg>
          <span v-if="!collapsed">Toggle</span>
        </button>
      </div>
    </div>
  </aside>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { route as ziggyRoute } from 'ziggy-js';
import { defineProps } from 'vue';

const props = defineProps({
  collapsed: { type: Boolean, default: false },
});

// Sidebar sections — keep these as you like
const sections = [
  {
    title: 'Main',
    items: [
      { name: 'Dashboard', route: 'manager.dashboard', svg: '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12h6v9H3zM3 3h6v6H3zM15 3h6v9h-6zM15 15h6v6h-6z"/></svg>' },
      { name: 'Employees', route: 'manager.employees.index', svg: '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5.121 17.804A13.937 13.937 0 0112 15c2.485 0 4.817.623 6.879 1.804M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>' },
    ],
  },
  {
    title: 'HR',
    items: [
      // These might not exist in routes yet; safeRoute will return null and the item will be disabled.
      { name: 'Leaves', route: 'manager.leaves.index', svg: '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"></svg>' },
      { name: 'Attendance', route: 'manager.attendance.index', svg: '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"></svg>' },
    ],
  },
];

// Helper: try to resolve a named route; return null if not present
function safeRoute(nameOrHref) {
  if (!nameOrHref) return null;
  try {
    // If it's a full href (starts with /), just return it
    if (typeof nameOrHref === 'string' && nameOrHref.startsWith('/')) {
      return nameOrHref;
    }
    // ziggyRoute will throw if name not found — catch that
    return ziggyRoute(nameOrHref);
  } catch (e) {
    return null;
  }
}

// isActive: compares paths safely, only when safeRoute resolved
function isActive(item) {
  try {
    const target = safeRoute(item.route || item.href);
    if (!target) return false;
    const curr = window.location.pathname;
    return new URL(target, window.location.origin).pathname === new URL(curr, window.location.origin).pathname;
  } catch (e) {
    return false;
  }
}
</script>

<style scoped>
/* keep visuals clean — colors chosen inline for clarity */
</style>
