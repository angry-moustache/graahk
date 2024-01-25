<template>
  <div
      class="
          graahk-card w-full rounded-xl overflow-hidden
          bg-cover bg-center relative
          text-black select-none aspect-[2.5/3.5]
          cursor-pointer
      "
      v-bind:style="{ backgroundImage: `url('${card.image}')` }"
      v-bind:class="{
        'border border-green-500': canPlay,
        'max-w-[10rem]': ! fullSized,
      }"
      v-on:click="canPlay && $emit('play-card', cardKey)"
  >
      <img v-bind:src="`/images/cards/dude-${card.level}.svg`" />

      <h2
        class="absolute top-[4%] left-[4%] text-center w-[14.5%] font-bold"
        v-text="card.cost"
      ></h2>

      <h3
        class="absolute top-[5%] left-[21%] w-full font-bold"
        v-text="card.name"
      ></h3>

      <span
        class="absolute bottom-[36.5%] left-[8%] w-[80%]"
        v-text="card.tribesText"
        v-bind:class="{
          'bottom-[5.5%] left-[36.5%]': card.level > 2
        }"
      ></span>

      <p
        class="absolute top-[65%] bottom-[14%] left-[9%] w-[82%] overflow-y-auto"
        v-html="card.text"
        v-bind:class="{
          'text-white text-border-hard': card.level > 2,
        }"
      ></p>

      <h4
        v-text="updatedPower || power"
        class="absolute bottom-[2.6%] left-[4%] w-[29%] text-center font-bold"
        v-bind:class="{
          'text-green-500 text-border-hard': updatedPower,
        }"
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
    fullSized: {
      type: Boolean,
      default: false,
    },
  },
  data () {
    return {
      updatedPower: false,
    }
  },
  mounted () {
    this.card.level ??= 1
    window.resizeCards()
  },
  computed: {
    power () {
      const effects = this.card.effects.map((e) => e.effect)

      if (!this.updatedPower && effects.includes('unnamed_one')) {
        this.updatedPower = parseInt(window.game.player.graveyard.length * 50)
      }

      return this.card.power
    },
  },
}
</script>
