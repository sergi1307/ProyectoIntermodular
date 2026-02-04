<template>
  <div class="stars" :class="{ readonly }">
    <span
      v-for="star in maxStars"
      :key="star"
      class="star"
      :class="getStarClass(star)"
      @mousemove="!readonly && onHoverStar($event, star)"
      @mouseleave="!readonly && (hover = 0)"
      @click="!readonly && onClickStar($event, star)"
    >
      ★
    </span>
  </div>
</template>

<script setup>
import { ref, computed } from "vue";

const props = defineProps({
  modelValue: {
    type: Number,
    default: 0
  },
  maxStars: {
    type: Number,
    default: 5
  },
  readonly: {
    type: Boolean,
    default: false
  }
});


const emit = defineEmits(["update:modelValue"]);

const hover = ref(0);

function getStarClass(star) {
  const value = hover.value || props.modelValue;

  return {
    active: star <= Math.floor(value),
    half: star === Math.floor(value) + 1 && value % 1 >= 0.5
  };
}

function onHoverStar(event, star) {
  if (props.readonly) return;

  const { left, width } = event.currentTarget.getBoundingClientRect();
  const hoverX = event.clientX - left;

  hover.value = hoverX < width / 2 ? star - 0.5 : star;
}

function onClickStar(event, star) {
  if (props.readonly) return;

  const { left, width } = event.currentTarget.getBoundingClientRect();
  const clickX = event.clientX - left;

  emit(
    "update:modelValue",
    clickX < width / 2 ? star - 0.5 : star
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