<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

interface Props {
    className?: HTMLAttributes['class'];
}

defineProps<Props>();
</script>

<template>
    <img src="/logoweb.webp" alt="logo" class="w-10 h-10 object-cover" :class="className" v-bind="$attrs">
</template>
