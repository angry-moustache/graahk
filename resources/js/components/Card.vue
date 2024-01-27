<template>
  <div
      class="
        graahk-card w-full rounded-xl overflow-hidden
        bg-cover bg-center relative
        text-black select-none aspect-[2.5/3.5]
        isolate cursor-pointer
      "
      v-bind:style="{ backgroundImage: `url('${card.image}')` }"
      v-bind:class="{
        'border border-green-500': canPlay,
        'max-w-[10rem]': ! fullSized,
      }"
      v-on:click="canPlay && $emit('play-card', cardKey)"
  >
      <div class="absolute inset-0 rounded-xl overflow-hidden">
        <div v-if="card.level >= 4" class="z-[-1] rounded-xl overflow-hidden animate-foil"></div>
      </div>

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
        v-text="card.tribesText"
        v-bind:class="{
            'absolute w-[80%] text-lg': true,
            'bottom-[36.5%] left-[8%]': (card.level <= 2),
            'bottom-[5.5%] left-[36.5%]': (card.level > 2),
        }"
      ></span>

      <p
        v-if="card.text !== ''"
        v-html="card.text"
        v-bind:class="{
          'absolute bottom-[14%] overflow-y-auto': true,
          'left-[9%] w-[82%] top-[65%]': (card.level <= 2),
          'left-[4%] w-[92%]': (card.level > 2),
          'text-white text-border-hard': (card.level > 2),
          'bg-black p-2 bg-opacity-25 rounded-lg': (card.level >= 3),
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
