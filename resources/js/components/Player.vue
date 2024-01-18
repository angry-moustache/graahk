<template>
  <div
    class="flex flex-col gap-4 p-4"
    v-bind:class="{ 'flex-col-reverse': reverse }"
  >
    <img
      src="/images/logo.jpg"
      class="w-full rounded-xl"
    >

    <h2
      class="w-full text-center font-bold text-6xl"
      v-text="player.power"
    ></h2>

    <div>
      <p v-text="player.deck.length"></p>
      <p v-text="player.hand.length"></p>
      <p v-text="player.energy"></p>
    </div>
  </div>
</template>

<script>
export default {
  name: 'player',
  props: {
    player: Object,
    reverse: {
      type: Boolean,
      default: false,
    }
  },
}
</script>
