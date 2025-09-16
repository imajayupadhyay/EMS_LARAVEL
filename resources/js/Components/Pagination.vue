<template>
  <nav class="flex items-center justify-between px-4 py-3 bg-white border-t">
    <!-- Large screens: full pagination info + page buttons -->
    <div v-if="hasPages" class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between w-full">
      <div>
        <p class="text-sm text-gray-700">
          Showing
          <span class="font-medium">{{ firstItem }}</span>
          to
          <span class="font-medium">{{ lastItem }}</span>
          of
          <span class="font-medium">{{ meta.total }}</span>
          results
        </p>
      </div>

      <div>
        <ul class="inline-flex -space-x-px">
          <li>
            <button
              :disabled="!meta.prev_page_url"
              @click="changePage(meta.current_page - 1)"
              class="px-3 py-1 border rounded-l disabled:opacity-50"
            >
              Prev
            </button>
          </li>

          <li v-for="p in pagesToShow" :key="p">
            <button
              @click="changePage(p)"
              :class="['px-3 py-1 border', { 'bg-blue-600 text-white': p === meta.current_page }]"
            >
              {{ p }}
            </button>
          </li>

          <li>
            <button
              :disabled="!meta.next_page_url"
              @click="changePage(meta.current_page + 1)"
              class="px-3 py-1 border rounded-r disabled:opacity-50"
            >
              Next
            </button>
          </li>
        </ul>
      </div>
    </div>

    <!-- Small screen: simple prev/next -->
    <div v-else class="flex justify-between w-full">
      <button :disabled="!meta.prev_page_url" @click="changePage(meta.current_page - 1)" class="px-3 py-1 border rounded disabled:opacity-50">Prev</button>
      <button :disabled="!meta.next_page_url" @click="changePage(meta.current_page + 1)" class="px-3 py-1 border rounded disabled:opacity-50">Next</button>
    </div>
  </nav>
</template>

<script>
export default {
  name: 'Pagination',
  props: {
    data: {
      type: Object,
      required: true,
    }
  },
  computed: {
    meta() {
      return {
        current_page: this.data.current_page ?? 1,
        last_page: this.data.last_page ?? (this.data.lastPage ?? 1),
        per_page: this.data.per_page ?? this.data.perPage ?? 15,
        total: this.data.total ?? 0,
        from: this.data.from ?? ((this.data.current_page - 1) * (this.data.per_page || 15) + 1),
        to: this.data.to ?? Math.min((this.data.current_page) * (this.data.per_page || 15), this.data.total ?? 0),
        prev_page_url: this.data.prev_page_url ?? null,
        next_page_url: this.data.next_page_url ?? null,
      };
    },
    hasPages() {
      return (this.meta.last_page && this.meta.last_page > 1);
    },
    firstItem() {
      return this.meta.from ?? 0;
    },
    lastItem() {
      return this.meta.to ?? 0;
    },
    pagesToShow() {
      const total = this.meta.last_page;
      const current = this.meta.current_page;
      const maxButtons = 7;
      if (!total || total <= 1) return [1];

      const half = Math.floor(maxButtons / 2);
      let start = Math.max(1, current - half);
      let end = Math.min(total, current + half);

      if (current - start < half) {
        end = Math.min(total, end + (half - (current - start)));
      }
      if (end - current < half) {
        start = Math.max(1, start - (half - (end - current)));
      }

      const pages = [];
      for (let i = start; i <= end; i++) pages.push(i);
      return pages;
    }
  },
  methods: {
    changePage(page) {
      if (!page || page === this.meta.current_page) return;
      this.$emit('pagination-change-page', page);
    }
  }
};
</script>

<style scoped>
button[disabled] { cursor: not-allowed; opacity: 0.5; }
</style>
