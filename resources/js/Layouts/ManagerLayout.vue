<template>
  <div class="ml-root">
    <Head />

    <!-- fixed sidebar (desktop) and mobile drawer handled in ManagerSidebar -->
    <ManagerSidebar
      :collapsed="collapsed"
      :mobile-open="mobileOpen"
      :is-mobile="isMobile"
      :current-route-name="currentRouteName"
      @toggle="collapsed = !collapsed"
      @close-mobile="mobileOpen = false"
      @open-mobile="mobileOpen = true"
    />

    <!-- main area -->
    <div class="ml-main">
      <header class="ml-topbar">
        <div class="ml-topbar-left">
          <!-- collapse (desktop only) -->
          <button v-if="!isMobile" class="ml-icon" @click="collapsed = !collapsed" :title="collapsed ? 'Expand sidebar' : 'Collapse sidebar'">
            <svg v-if="!collapsed" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h16" stroke-linecap="round" stroke-linejoin="round"/></svg>
            <svg v-else width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><path d="M6 18L18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </button>

          <!-- hamburger (mobile only) -->
          <button v-if="isMobile" class="ml-icon" @click="mobileOpen = true" title="Open menu">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h16" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </button>

          <div class="ml-title">{{ title || pageTitle }}</div>
        </div>

        <div class="ml-topbar-right">
          <template v-if="!isMobile">
            <Link v-if="profileRoute" :href="profileRoute" class="ml-top-link">Profile</Link>
            <form :action="logoutUrl" method="POST" class="ml-inline-form">
              <input type="hidden" name="_token" :value="csrfToken" />
              <button type="submit" class="ml-logout">Logout</button>
            </form>
          </template>
        </div>
      </header>

      <main class="ml-content">
        <slot />
      </main>

      <footer class="ml-footer">© {{ new Date().getFullYear() }} — NuclearEdge</footer>
    </div>
  </div>
</template>

<script setup>
import ManagerSidebar from '@/Components/ManagerSidebar.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
import { route as ziggyRoute } from 'ziggy-js';

const props = defineProps({ title: { type: String, default: null } });

const collapsed = ref(false);
const mobileOpen = ref(false);
const isMobile = ref(false);

function updateIsMobile() { isMobile.value = window.innerWidth <= 768; }
onMounted(() => { updateIsMobile(); window.addEventListener('resize', updateIsMobile); });
onBeforeUnmount(() => { window.removeEventListener('resize', updateIsMobile); });

const page = usePage();
const pageProps = page.props || {};
// prefer explicit route name provided by server: page.props.routeName or page.props.currentRouteName
const currentRouteName = computed(() => pageProps.routeName ?? pageProps.currentRouteName ?? null);

const csrfToken = typeof document !== 'undefined' ? document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '' : '';
let logoutUrl = '/logout';
try { logoutUrl = ziggyRoute('logout') || logoutUrl; } catch (e) { /* fallback */ }

let profileRoute = null;
try { profileRoute = ziggyRoute('manager.profile'); } catch (e) { profileRoute = null; }

const pageTitle = computed(() => {
  const p = page.props || {};
  return p.title || (p.page?.title ?? 'Manager Dashboard');
});
</script>

<style scoped>
/* Root layout */
.ml-root { display:flex; min-height:100vh; background:#f5f7fb; color:#0f172a; font-family: Inter, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial; }

/* Main area (to the right of sidebar) */
.ml-main { flex:1; display:flex; flex-direction:column; min-height:100vh; }

/* Topbar */
.ml-topbar { display:flex; justify-content:space-between; align-items:center; padding:12px 18px; background:#0b69a3; color:#fff; box-shadow:0 2px 8px rgba(11,105,163,0.08); }
.ml-topbar-left { display:flex; align-items:center; gap:12px; }
.ml-icon { display:inline-flex; align-items:center; justify-content:center; padding:6px; background:transparent; border:none; border-radius:6px; cursor:pointer; transition:background .12s ease; }
.ml-icon:hover { background: rgba(255,255,255,0.06); }
.ml-title { font-size:16px; font-weight:600; letter-spacing:0.2px; }
.ml-topbar-right { display:flex; align-items:center; gap:12px; }

/* Topbar links */
.ml-top-link { color:#fff; text-decoration:none; margin-right:8px; font-size:14px; }
.ml-inline-form { display:inline; }
.ml-logout { padding:6px 10px; border-radius:6px; background: rgba(255,255,255,0.12); color:#fff; border:none; cursor:pointer; }

/* Content area */
.ml-content { padding:18px; flex:1 1 auto; overflow:auto; }

/* Footer */
.ml-footer { padding:12px 18px; background:#fff; border-top:1px solid #eee; color:#475569; font-size:13px; }

/* Responsive tweaks */
@media (max-width: 768px) {
  .ml-root { flex-direction:column; }
  /* sidebar is handled in ManagerSidebar (not visible here) */
}
</style>
