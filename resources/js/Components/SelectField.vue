<template>
  <div>
    <label class="block mb-1 text-gray-700 font-semibold">{{ label }}</label>
    <select
      :value="modelValue"
      @input="$emit('update:modelValue', $event.target.value)"
      class="input-style"
    >
      <option value="">Select</option>
      <option
        v-for="option in options"
        :key="option.value || option"
        :value="option.value || option"
      >
        {{ option.label || option }}
      </option>
    </select>
  </div>
</template>

<script setup>
defineProps({
  label: String,
  modelValue: [String, Number],
  options: Array,
})

defineEmits(['update:modelValue'])
</script>

<style scoped>
.input-style {
  @apply w-full px-4 py-2 rounded-md border border-gray-300 shadow-sm focus:ring-2 focus:ring-orange-500 focus:outline-none;
}
</style>
