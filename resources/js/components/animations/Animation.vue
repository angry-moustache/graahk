<template>
  <div v-if="animation._meta">
    <img
      class="absolute"
      :src="'/images/animations/draw-card.png?r=' + animation.uuid"
      :key="animation.uuid"
      :style="{
        top: animation._meta.y + 'px',
        left: animation._meta.x + 'px',
        width: animation._meta.width + 'px',
      }"
    >
  </div>
</template>

<script>
export default {
  name: 'animation',
  props: {
    animation: Object,
  },
}
</script>
