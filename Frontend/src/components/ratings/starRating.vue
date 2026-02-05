<template>
  <div class="stars" :class="{ readonly }">
    <span
  v-for="star in readonly ? [1] : Array.from({ length: maxStars }, (_, i) => i + 1)"
  :key="star"
  class="star"
  :class="getStarClass(star, readonly)"
  @mousemove="!readonly && onHoverStar($event, star)"
  @mouseleave="!readonly && (hover = 0)"
  @click="!readonly && onClickStar($event, star)"
>
  ★
</span>

  </div>
</template>

<script setup>
import { ref } from "vue";

const props = defineProps({
  modelValue: Number,
  maxStars: { type: Number, default: 5 },
  readonly: { type: Boolean, default: false }
});

const emit = defineEmits(["update:modelValue"]);
const hover = ref(0);

function getStarClass(star, readonly = false) {
  const value = hover.value || props.modelValue;
  if (readonly) {
    // En readonly, mostramos solo una estrella "proporcional"
    if (value >= 1) return { active: true };
    if (value >= 0.5) return { half: true };
    return {};
  }
  return {
    active: star <= Math.floor(value),
    half: star === Math.floor(value) + 1 && value % 1 >= 0.5
  };
}

function onHoverStar(e, star) {
  const { left, width } = e.currentTarget.getBoundingClientRect();
  hover.value = e.clientX - left < width / 2 ? star - 0.5 : star;
}

function onClickStar(e, star) {
  const { left, width } = e.currentTarget.getBoundingClientRect();
  emit(
    "update:modelValue",
    e.clientX - left < width / 2 ? star - 0.5 : star
  );
}
</script>

<style scoped>
.stars {
  font-size: 2rem;
}

.star {
  position: relative;
  display: inline-block;
  cursor: pointer;
  color: #d1d5db;
}

.star.active {
  color: gold;
}

.star.half {
  color: #d1d5db;
}

.star.half::before {
  content: "★";
  position: absolute;
  top: 0;
  left: 0;
  width: 50%;
  overflow: hidden;
  color: gold;
  transform: translateX(0.02em);
}

.stars.readonly .star {
  cursor: default;
}
</style>
