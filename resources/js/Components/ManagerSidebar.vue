<template>
  <!-- DESKTOP: fixed sidebar on left (hidden on mobile) -->
  <aside v-if="!isMobile" :class="['ms-aside', collapsed ? 'ms-collapsed':'']" :style="asideStyle">
    <div class="ms-header" :style="headerStyle">
      <div class="ms-title" v-if="!collapsed" :style="headerTitleStyle">Manager</div>
      <div class="ms-title-c" v-else :style="headerTitleCollapsedStyle">M</div>
    </div>

    <nav class="ms-nav" :style="navStyle">
      <template v-for="(section, sidx) in sections" :key="sidx">
        <div class="ms-section-title" v-if="!collapsed" :style="sectionTitleStyle">{{ section.title }}</div>

        <ul class="ms-list" :style="ulStyle">
          <li v-for="item in section.items" :key="item.name" class="ms-item" :style="liStyle">
            <template v-if="safeRoute(item.route)">
              <Link
                :href="safeRoute(item.route)"
                :style="computeLinkStyle(item)"
                class="ms-link"
                @click="onLinkClick"
              >
                <span class="ms-icon" v-html="item.svg" :style="svgStyle"></span>
                <span v-if="!collapsed" class="ms-text" :style="linkTextStyle">{{ item.name }}</span>
              </Link>
            </template>
            <template v-else>
              <div class="ms-disabled" :style="disabledLinkStyle">
                <span class="ms-icon" v-html="item.svg" :style="svgStyle"></span>
                <span v-if="!collapsed" class="ms-text" :style="linkTextStyle">{{ item.name }}</span>
              </div>
            </template>
          </li>
        </ul>

        <div class="ms-divider" v-if="!collapsed" :style="dividerStyle"></div>
      </template>
    </nav>

    <div class="ms-bottom" :style="bottomStyle">
      <div class="ms-company" :style="companyTextStyle">Your Company</div>
      <button class="ms-toggle" :style="bottomButtonStyle" @click="$emit('toggle')">☰ <span v-if="!collapsed" class="ms-text" :style="linkTextStyle"> Toggle</span></button>
    </div>
  </aside>

  <!-- MOBILE: sliding drawer from left, with backdrop -->
  <div v-if="isMobile && mobileOpen" class="ms-mobile-overlay" :style="mobileOverlayStyle" @click.self="closeMobile">
    <div class="ms-drawer" :class="{ open: mobileOpen }" :style="mobileDrawerStyle" role="dialog" aria-modal="true">
      <div class="ms-drawer-header" :style="mobileHeaderStyle">
        <div class="ms-drawer-title" :style="mobileTitleStyle">Manager</div>
        <button class="ms-close" :style="mobileCloseBtnStyle" @click="closeMobile">✕</button>
      </div>

      <nav class="ms-drawer-nav" :style="mobileNavStyle">
        <template v-for="(section, sidx) in sections" :key="sidx">
          <div class="ms-section-title-mobile" :style="mobileSectionTitleStyle">{{ section.title }}</div>
          <ul class="ms-list-mobile" :style="ulStyle">
            <li v-for="item in section.items" :key="item.name" class="ms-item-mobile" :style="liStyle">
              <template v-if="safeRoute(item.route)">
                <Link
                  :href="safeRoute(item.route)"
                  :style="computeMobileLinkStyle(item)"
                  @click="closeAndNavigate"
                >
                  <span class="ms-icon" v-html="item.svg" :style="svgStyle"></span>
                  <span class="ms-text-mobile" :style="mobileLinkTextStyle">{{ item.name }}</span>
                </Link>
              </template>
              <template v-else>
                <div class="ms-disabled-mobile" :style="disabledLinkStyle">
                  <span class="ms-icon" v-html="item.svg" :style="svgStyle"></span>
                  <span class="ms-text-mobile" :style="mobileLinkTextStyle">{{ item.name }}</span>
                </div>
              </template>
            </li>
          </ul>
        </template>
      </nav>

      <div class="ms-drawer-footer" :style="mobileLogoutWrapStyle">
        <form ref="logoutForm" :action="logoutUrl" method="POST" style="width:100%">
          <input type="hidden" name="_token" :value="csrfToken" />
          <button type="button" class="ms-logout-mobile" :style="mobileLogoutBtnStyle" @click.prevent="doLogout">⎋ Logout</button>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { route as ziggyRoute } from 'ziggy-js';
import { ref, computed } from 'vue';

/* Props & events */
const props = defineProps({
  collapsed: { type: Boolean, default: false },
  mobileOpen: { type: Boolean, default: false },
  isMobile: { type: Boolean, default: false },
});
const emit = defineEmits(['toggle', 'close-mobile', 'open-mobile']);

/* Inertia page (reactive) */
const page = usePage();

/* CSRF and logout */
const csrfToken = (typeof document !== 'undefined') ? document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '' : '';
let logoutUrl = '/logout';
try { logoutUrl = ziggyRoute('logout') || logoutUrl; } catch (e) {}

/* logout form ref */
const logoutForm = ref(null);

/* menu sections */
const sections = [
  {
    title: 'MAIN',
    items: [
      { name: 'Dashboard', route: 'manager.dashboard', svg: '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1.6"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>' },
      { name: 'Employees', route: 'manager.employees.index', svg: '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1.6"><path d="M5.121 17.804A13.937 13.937 0 0112 15c2.485 0 4.817.623 6.879 1.804"/><path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>' }
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

/* reactive current route name (from Inertia shared props) */
const currentRouteName = computed(() => {
  // page.props is reactive; prefer routeName shared prop
  try {
    return page.props.value?.routeName ?? page.props.value?.currentRouteName ?? null;
  } catch (e) {
    return null;
  }
});

/* helper: resolve named route (safe) */
function safeRoute(name) {
  if (!name) return null;
  try { return ziggyRoute(name); } catch (e) { return null; }
}

/* Active detection - first try route name (reactive), fallback to path comparison */
function activeByName(itemRoute) {
  if (!itemRoute) return false;
  const current = currentRouteName.value;
  if (!current) return false;
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

/* compute styles dynamically — desktop and mobile */
function computeLinkStyle(item) {
  const base = {
    display: 'flex', alignItems: 'center', gap: '10px',
    padding: '8px 10px', borderRadius: '8px', color:'#fff', textDecoration:'none',
    transition:'background .18s ease, transform .12s ease', transform:'translateY(0)',
    background: 'transparent'
  };
  if (isActive(item)) {
    base.background = '#123a73';
    base.boxShadow = 'inset 0 0 0 3px rgba(255,255,255,0.02)';
    base.transform = 'translateY(-1px)';
  }
  return base;
}
function computeMobileLinkStyle(item) {
  const base = {
    display: 'flex', alignItems: 'center', gap: '12px',
    padding: '10px 12px', borderRadius: '8px', color:'#fff', textDecoration:'none',
    transition:'background .18s ease', background:'transparent'
  };
  if (isActive(item)) base.background = '#123a73';
  return base;
}

/* link click handlers */
function onLinkClick() {
  // on desktop we don't need to close drawer; this exists for uniformity
}
function closeAndNavigate() {
  // Inertia Link will navigate automatically — close drawer so UI updates
  emit('close-mobile');
}
function closeMobile() {
  emit('close-mobile');
}

/* logout: close drawer then submit form (works both mobile & desktop forms) */
function doLogout() {
  // close mobile drawer for better UX (mobile case)
  emit('close-mobile');
  setTimeout(() => {
    if (logoutForm.value && typeof logoutForm.value.submit === 'function') {
      logoutForm.value.submit();
    } else {
      const f = document.createElement('form');
      f.method = 'POST';
      f.action = logoutUrl;
      const token = document.createElement('input');
      token.type = 'hidden';
      token.name = '_token';
      token.value = csrfToken;
      f.appendChild(token);
      document.body.appendChild(f);
      f.submit();
    }
  }, 160);
}

/* Inline style objects (kept same as before) */
const asideStyle = {
  width: props.collapsed ? '60px' : '240px',
  minWidth: props.collapsed ? '60px' : '240px',
  height: '100vh',
  display: 'flex', flexDirection: 'column',
  background: '#0b69a3', color: '#fff', boxSizing: 'border-box', borderRight: '1px solid rgba(0,0,0,0.06)'
};
const headerStyle = { padding: '18px', borderBottom: '1px solid rgba(0,0,0,0.08)' };
const headerTitleStyle = { fontSize: '20px', fontWeight: 700 };
const headerTitleCollapsedStyle = { fontSize: '16px', fontWeight: 700, textAlign: 'center' };
const navStyle = { padding: '12px', overflowY: 'auto', flex: '1 1 auto' };
const sectionTitleStyle = { fontSize: '12px', color: 'rgba(255,255,255,0.85)', margin: '8px 0', letterSpacing: '0.06em' };
const ulStyle = { listStyle: 'none', padding: 0, margin: 0 };
const liStyle = { marginBottom: '6px' };
const svgStyle = { display: 'inline-block', verticalAlign: 'middle', marginRight: '10px' };
const linkTextStyle = { fontSize: '15px', color: '#fff', verticalAlign: 'middle' };
const dividerStyle = { height: '1px', background: 'rgba(0,0,0,0.08)', margin: '12px 0' };
const bottomStyle = { padding: '12px', borderTop: '1px solid rgba(0,0,0,0.06)', display: 'flex', flexDirection: 'column', gap: '8px' };
const companyTextStyle = { fontSize: '12px', color: 'rgba(255,255,255,0.85)' };
const bottomButtonStyle = { padding: '8px', background: 'rgba(0,0,0,0.08)', color: '#fff', border: 'none', borderRadius: '8px', cursor: 'pointer', textAlign: 'center' };
const disabledLinkStyle = { display: 'flex', alignItems: 'center', padding: '8px 10px', borderRadius: '8px', opacity: 0.6, color: 'rgba(255,255,255,0.85)' };

const mobileOverlayStyle = { position: 'fixed', inset: 0, zIndex: 1200, display: 'flex', alignItems: 'stretch', background: 'rgba(0,0,0,0.2)' };
const mobileDrawerStyle = { width: '320px', maxWidth: '80%', background: '#0b69a3', color: '#fff', padding: '18px', boxSizing: 'border-box', display: 'flex', flexDirection: 'column', justifyContent: 'space-between', transform: 'translateX(-14%)', transition: 'transform .22s cubic-bezier(.2,.9,.3,1)' };
const mobileHeaderStyle = { display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '8px' };
const mobileTitleStyle = { fontSize: '20px', fontWeight: 700 };
const mobileCloseBtnStyle = { border: 'none', background: 'transparent', color: '#fff', fontSize: '18px', cursor: 'pointer' };
const mobileNavStyle = { overflowY: 'auto', paddingBottom: '12px', flex: '1 1 auto' };
const mobileSectionTitleStyle = { fontSize: '12px', color: 'rgba(255,255,255,0.95)', marginBottom: '8px' };
const mobileLinkTextStyle = { fontSize: '15px', color: '#fff' };
const mobileLogoutWrapStyle = { borderTop: '1px solid rgba(255,255,255,0.06)', paddingTop: '12px' };
const mobileLogoutBtnStyle = { width: '100%', padding: '10px', background: '#fff', color: '#0b69a3', border: 'none', borderRadius: '8px', cursor: 'pointer', fontWeight: 600 };
</script>

<style scoped>
/* minimal hover/active animations */
a[style] { display:flex !important; align-items:center; text-decoration:none; }
a[style]:hover { filter: brightness(0.95); transform: translateY(-2px); transition: transform .12s; }
.ms-drawer.open { transform: translateX(0) !important; }
</style>
