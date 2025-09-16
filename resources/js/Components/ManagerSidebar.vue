<template>
  <!-- DESKTOP: fixed sidebar on left (hidden on mobile) -->
  <aside v-if="!isMobile" :class="['ms-aside', collapsed ? 'ms-collapsed':'']" :style="asideStyle">
    <div class="ms-header">
      <div class="ms-title" v-if="!collapsed">Manager</div>
      <div class="ms-title-c" v-else>M</div>
    </div>

    <nav class="ms-nav">
      <template v-for="(section, sidx) in sections" :key="sidx">
        <div class="ms-section-title" v-if="!collapsed">{{ section.title }}</div>

        <ul class="ms-list">
          <li v-for="item in section.items" :key="item.name" class="ms-item">
            <template v-if="safeRoute(item.route)">
              <Link :href="safeRoute(item.route)" :style="computeLinkStyle(item)" class="ms-link">
                <span class="ms-icon" v-html="item.svg"></span>
                <span v-if="!collapsed" class="ms-text">{{ item.name }}</span>
              </Link>
            </template>
            <template v-else>
              <div class="ms-disabled">
                <span class="ms-icon" v-html="item.svg"></span>
                <span v-if="!collapsed" class="ms-text">{{ item.name }}</span>
              </div>
            </template>
          </li>
        </ul>

        <div class="ms-divider" v-if="!collapsed"></div>
      </template>
    </nav>

    <div class="ms-bottom">
      <!-- <div class="ms-company">NuclearEdge</div> -->
      <button class="ms-toggle" @click="$emit('toggle')">☰ <span v-if="!collapsed" class="ms-text">Toggle</span></button>
    </div>
  </aside>

  <!-- MOBILE: sliding drawer from left, with backdrop -->
  <div v-if="isMobile && mobileOpen" class="ms-mobile-overlay" @click.self="closeMobile">
    <div class="ms-drawer" :class="{ open: mobileOpen }" role="dialog" aria-modal="true">
      <div class="ms-drawer-header">
        <div class="ms-drawer-title">Manager</div>
        <button class="ms-close" @click="closeMobile">✕</button>
      </div>

      <nav class="ms-drawer-nav">
        <template v-for="(section, sidx) in sections" :key="sidx">
          <div class="ms-section-title-mobile">{{ section.title }}</div>
          <ul class="ms-list-mobile">
            <li v-for="item in section.items" :key="item.name" class="ms-item-mobile">
              <template v-if="safeRoute(item.route)">
                <Link :href="safeRoute(item.route)" :style="computeMobileLinkStyle(item)" @click="closeAndNavigate">
                  <span class="ms-icon" v-html="item.svg"></span>
                  <span class="ms-text-mobile">{{ item.name }}</span>
                </Link>
              </template>
              <template v-else>
                <div class="ms-disabled-mobile">
                  <span class="ms-icon" v-html="item.svg"></span>
                  <span class="ms-text-mobile">{{ item.name }}</span>
                </div>
              </template>
            </li>
          </ul>
        </template>
      </nav>

      <div class="ms-drawer-footer">
        <form ref="logoutForm" :action="logoutUrl" method="POST" style="width:100%">
          <input type="hidden" name="_token" :value="csrfToken" />
          <button type="button" class="ms-logout-mobile" @click.prevent="doLogout">⎋ Logout</button>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { route as ziggyRoute } from 'ziggy-js';
import { ref } from 'vue';
import { usePage } from '@inertiajs/vue3';

const props = defineProps({
  collapsed: { type: Boolean, default: false },
  mobileOpen: { type: Boolean, default: false },
  isMobile: { type: Boolean, default: false },
  currentRouteName: { type: [String, null], default: null }, // optional
});

const emit = defineEmits(['toggle', 'close-mobile', 'open-mobile']);

const logoutForm = ref(null);
const csrfToken = typeof document !== 'undefined' ? document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '' : '';
let logoutUrl = '/logout';
try { logoutUrl = ziggyRoute('logout') || logoutUrl; } catch (e) {}

const page = usePage();
const pageProps = page.props || {};

// menu items
const sections = [
  {
    title: 'MAIN',
    items: [
      { name: 'Dashboard', route: 'manager.dashboard', svg: '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1.6"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>' },
      { name: 'Employees', route: 'manager.employees.index', svg: '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1.6"><path d="M5.121 17.804A13.937 13.937 0 0112 15c2.485 0 4.817.623 6.879 1.804"/><path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>' },
    ]
  },
  {
    title: 'HR',
    items: [
      { name: 'Leaves', route: 'manager.leaves.index', svg: '<svg width="18" height="18"></svg>' },
      { name: 'Attendance', route: 'manager.attendance.index', svg: '<svg width="18" height="18"></svg>' }
    ]
  }
];

// helper: resolve named route (safe)
function safeRoute(name) {
  if (!name) return null;
  try { return ziggyRoute(name); } catch (e) { return null; }
}

// active detection: prefer route-name matching (if provided by server), else fallback to path matching
function activeByName(itemRoute) {
  const current = props.currentRouteName ?? pageProps.routeName ?? pageProps.currentRouteName ?? null;
  if (!current || !itemRoute) return false;
  // if itemRoute is prefix of current (e.g., manager.employees.*) mark active
  return current === itemRoute || current.startsWith(itemRoute + '.');
}

function activeByPath(itemRoute) {
  const href = safeRoute(itemRoute);
  if (!href) return false;
  try {
    const t = new URL(href, window.location.origin).pathname.replace(/\/$/, '');
    const c = window.location.pathname.replace(/\/$/, '');
    return c === t || c.startsWith(t + '/');
  } catch (e) {
    return false;
  }
}

function isActive(item) {
  return activeByName(item.route) || activeByPath(item.route);
}

function computeLinkStyle(item) {
  const base = {
    display: 'flex', alignItems: 'center', gap: '10px',
    padding: '8px 10px', borderRadius: '8px', color:'#fff', textDecoration:'none',
    transition:'background .18s ease, transform .12s ease',
    transform: 'translateY(0)'
  };
  if (isActive(item)) {
    base.background = '#123a73';
    base.boxShadow = 'inset 0 0 0 4px rgba(255,255,255,0.02)';
    base.transform = 'translateY(-1px)';
  } else {
    base.background = 'transparent';
  }
  return base;
}
function computeMobileLinkStyle(item) {
  const base = {
    display: 'flex', alignItems: 'center', gap:'12px',
    padding:'10px 12px', borderRadius:'8px', color:'#fff', textDecoration:'none',
    transition:'background .18s ease',
  };
  if (isActive(item)) base.background = '#123a73';
  else base.background = 'transparent';
  return base;
}

function closeMobile() { emit('close-mobile'); }
function closeAndNavigate() { emit('close-mobile'); } // Link will navigate

function doLogout() {
  emit('close-mobile');
  setTimeout(() => {
    if (logoutForm.value && typeof logoutForm.value.submit === 'function') {
      logoutForm.value.submit();
    } else {
      const f = document.createElement('form');
      f.method = 'POST';
      f.action = logoutUrl;
      const token = document.createElement('input');
      token.type = 'hidden'; token.name = '_token'; token.value = csrfToken;
      f.appendChild(token); document.body.appendChild(f); f.submit();
    }
  }, 160);
}
</script>

<style scoped>
/* Desktop aside */
.ms-aside { width:240px; min-width:240px; height:100vh; background:#0b69a3; color:#fff; display:flex; flex-direction:column; transition:width .22s cubic-bezier(.2,.9,.3,1); box-shadow: 2px 0 10px rgba(0,0,0,0.04); }
.ms-aside.ms-collapsed { width:60px; min-width:60px; }

/* header */
.ms-header { padding:18px; border-bottom:1px solid rgba(255,255,255,0.03); }
.ms-title { font-size:20px; font-weight:700; letter-spacing: .3px; }
.ms-title-c { text-align:center; font-size:16px; font-weight:700; }

/* nav */
.ms-nav { padding:12px; overflow:auto; flex:1 1 auto; }
.ms-section-title { font-size:12px; color:rgba(255,255,255,0.85); margin:8px 0; letter-spacing:.06em; }
.ms-list { list-style:none; margin:0; padding:0; }
.ms-item { margin-bottom:8px; }
.ms-icon { width:18px; height:18px; display:inline-block; vertical-align:middle; }
.ms-text { font-size:15px; vertical-align:middle; }

/* link */
.ms-link:hover { transform: translateY(-2px); }
.ms-disabled { display:flex; align-items:center; padding:8px 10px; opacity:0.6; border-radius:8px; color:rgba(255,255,255,0.9); }

/* divider and bottom */
.ms-divider { height:1px; background:rgba(255,255,255,0.03); margin:12px 0; }
.ms-bottom { padding:12px; border-top:1px solid rgba(255,255,255,0.03); display:flex; flex-direction:column; gap:8px; }
.ms-company { font-size:12px; color:rgba(255,255,255,0.85); }
.ms-toggle { padding:8px; background:rgba(255,255,255,0.06); color:#fff; border:none; border-radius:8px; cursor:pointer; }

/* MOBILE drawer */
.ms-mobile-overlay { position:fixed; inset:0; z-index:1400; display:flex; align-items:stretch; background: rgba(0,0,0,0.25); }
.ms-drawer { width:320px; max-width:85%; background:#0b69a3; color:#fff; display:flex; flex-direction:column; padding:18px; box-sizing:border-box; transform: translateX(-14%); transition: transform .22s cubic-bezier(.2,.9,.3,1); }
.ms-drawer.open { transform: translateX(0); }

/* drawer header */
.ms-drawer-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:8px; }
.ms-drawer-title { font-size:20px; font-weight:700; }
.ms-close { background:transparent; color:#fff; border:none; font-size:18px; cursor:pointer; }

/* drawer nav */
.ms-section-title-mobile { font-size:12px; color:rgba(255,255,255,0.95); margin-bottom:8px; }
.ms-list-mobile { list-style:none; margin:0; padding:0; }
.ms-item-mobile { margin-bottom:8px; }

/* drawer footer logout */
.ms-drawer-footer { margin-top:12px; }
.ms-logout-mobile { width:100%; padding:10px; background:#fff; color:#0b69a3; border:none; border-radius:8px; cursor:pointer; font-weight:600; }

/* small utilities */
.ms-text-mobile { font-size:15px; }
.ms-disabled-mobile { opacity:0.6; display:flex; align-items:center; padding:10px 12px; border-radius:8px; }

/* make links not underlined */
a { text-decoration:none; }
</style>
