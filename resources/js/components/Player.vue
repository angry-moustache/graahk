<template>
  <div
    class="flex flex-col gap-4 p-4 origin-center"
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

    <div class="flex">
      <div class="relative" ref="handsize">
        <img src="/images/icons/handsize.jpg" alt="">
        <p
          class="absolute inset-0 text-4xl font-bold items-center justify-center flex"
          v-text="player.hand.length"
        ></p>
      </div>

      <div class="relative" ref="decksize">
        <img src="/images/icons/decksize.jpg" alt="">
        <p
          class="absolute inset-0 text-4xl font-bold items-center justify-center flex"
          v-text="player.deck.length"
        ></p>
      </div>

      <div class="relative" ref="energy">
        <img src="/images/icons/energy.jpg" alt="">
        <p
          class="absolute inset-0 text-4xl font-bold items-center justify-center flex"
          v-text="player.energy"
        ></p>
      </div>
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
