]<template>
  <div>
    <label class="form-label">{{ label }}</label>
    <input
      :type="type"
      :value="modelValue"
      @input="$emit('update:modelValue', $event.target.value)"
      class="form-input"
      :required="required"
    />
  </div>
</template>

<script setup>
defineProps({
  label: String,
  type: { type: String, default: 'text' },
  modelValue: [String, Number],
  required: { type: Boolean, default: false }
})

defineEmits(['update:modelValue'])
</script>
