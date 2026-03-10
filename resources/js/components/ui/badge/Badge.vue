<script setup lang="ts">
import { cn } from '@/lib/utils';
import { Primitive, type PrimitiveProps } from 'radix-vue';
import type { HTMLAttributes } from 'vue';
import { badgeVariants, type BadgeVariants } from '.';

interface Props extends PrimitiveProps {
    variant?: BadgeVariants['variant'];
    class?: HTMLAttributes['class'];
}

const props = withDefaults(defineProps<Props>(), {
    as: 'span',
});
</script>

<template>
    <Primitive
        :as="as"
        :as-child="asChild"
        :class="cn(badgeVariants({ variant }), props.class)"
    >
        <slot />
    </Primitive>
</template>
