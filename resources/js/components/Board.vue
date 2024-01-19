<template>
  <div
    class="flex flex-col h-[40vh] w-full justify-center"
    v-bind:class="{ 'bg-surface': ! active, 'bg-background': active }"
  >
    <div class="flex flex-wrap h-[30vh] w-full gap-4 items-center justify-evenly">
      <slot></slot>
    </div>
  </div>
</template>

<script>
export default {
  name: 'board',
  props: {
    board: Object,
    active: Boolean,
  },
}
</script>
