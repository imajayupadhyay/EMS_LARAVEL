<template>
  <div class="min-h-screen flex bg-gray-100 text-gray-900">
    <Head />

    <!-- Sidebar -->
    <ManagerSidebar :collapsed="collapsed" @toggle="collapsed = !collapsed" />

    <!-- Main area -->
    <div class="flex-1 flex flex-col">
      <!-- Top bar -->
      <header class="flex items-center justify-between px-6 py-3 bg-blue-600 text-white shadow">
        <div class="flex items-center gap-4">
          <button @click="collapsed = !collapsed" class="p-2 rounded hover:bg-blue-500/60">
            <svg v-if="!collapsed" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round"
                    d="M4 6h16M4 12h16M4 18h16" />
            </svg>
            <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round"
                    d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>

          <h1 class="text-lg font-semibold">{{ title || pageTitle }}</h1>
        </div>

        <div class="flex items-center gap-4">
          <div class="hidden sm:flex items-center gap-3">
            <!-- If you don't have manager.profile route, this is guarded in JS -->
            <template v-if="profileRoute">
              <Link :href="profileRoute" class="hover:underline">Profile</Link>
            </template>
            <template v-else>
              <span class="opacity-80">Profile</span>
            </template>

            <form :action="route('logout')" method="POST" class="inline">
              <input type="hidden" name="_token" :value="csrfToken" />
              <button class="text-sm px-3 py-1 rounded bg-white/20 hover:bg-white/25">Logout</button>
            </form>
          </div>
        </div>
      </header>

      <!-- Content -->
      <main class="p-6 grow overflow-auto">
        <slot />
      </main>

      <footer class="px-6 py-3 bg-white border-t text-sm text-gray-600">
        <div class="max-w-7xl mx-auto">© {{ new Date().getFullYear() }} — Manager Panel</div>
      </footer>
    </div>
  </div>
</template>

<script setup>
import ManagerSidebar from '@/Components/ManagerSidebar.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { route } from 'ziggy-js';

const props = defineProps({
  title: { type: String, default: null },
});

const collapsed = ref(false);
const page = usePage();
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

const pageTitle = computed(() => {
  const p = page.props || {};
  return p.title || (p.page?.title ?? 'Manager Dashboard');
});

// safe profile route (so page doesn't break if named route missing)
const profileRoute = computed(() => {
  try {
    return route('manager.profile');
  } catch (e) {
    return null;
  }
});
</script>

<style scoped>
/* Optional small tweaks to fit the blue theme */
</style>
