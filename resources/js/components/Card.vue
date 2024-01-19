<template>
  <div
      class="
          graahk-card w-full max-w-[10rem] rounded-xl overflow-hidden
          bg-cover bg-center relative
          text-black select-none aspect-[2.5/3.5]
      "
      v-bind:style="{ backgroundImage: `url('${card.image}')` }"
      v-bind:class="{
        'border border-green-500': canPlay
      }"
      v-on:click="canPlay && $emit('play-card', cardKey)"
  >
      <img src="/images/cards/dude-1.svg" />

      <h2
        class="absolute top-[4%] left-[4%] text-center w-[14.5%] font-bold"
        v-text="card.cost"
      ></h2>

      <h3
        class="absolute top-[5%] left-[21%] w-full font-bold"
        v-text="card.name"
      ></h3>

      <span
        class="absolute bottom-[36.5%] left-[8%] w-[80%] text-lg"
        v-text="card.tribes"
      ></span>

      <p
        class="absolute top-[65%] bottom-[14%] left-[9%] w-[82%] overflow-y-auto"
        v-html="card.text"
      ></p>

      <h4
        class="absolute bottom-[2.6%] left-[4%] w-[29%] text-center font-bold"
        v-text="card.power"
      ></h4>
  </div>
</template>

<script>
export default {
  name: 'card',
  props: {
    card: Object,
    cardKey: Number,
    canPlay: Boolean,
  },
  mounted () {
    window.resizeCards()
  },
}
</script>
