<template>
  <div
    class="w-full h-screen fixed inset-0 items-center justify-center flex"
    style="z-index: 1000000"
  >
    <div class="flex flex-col gap-12 items-center justify-center w-3/5">
      <h1 class="text-4xl font-bold uppercase">Select cards to mulligan</h1>

      <div class="w-full border-t border-border"></div>

      <div class="
        flex items-center gap-2 w-full
        transition-all duration-200 ease-in-out opacity-100
      ">
        <TransitionGroup name="card">
          <Card
            v-for="(card, key) in player.hand"
            v-bind:key="card.uuid"
            :id="'hand-' + card.uuid"
            :card="card"
            :card-key="key"
            :can-play="! finishedMulligan"
            v-on:play-card="selectCard"
            v-bind:class="{
              'max-w-[20%] min-w-[20%] w-1/5 grow transition-all duration-500 ease-in-out': true,
              'opacity-50 scale-75': selections.includes(key),
            }"
          />
        </TransitionGroup>
      </div>

      <div class="w-full border-t border-border"></div>

      <p v-if="opponent.mulliganed !== -1">
        {{ opponent.name }} has chosen their cards
      </p>

      <button
        v-on:click="finish"
        v-bind:class="{
          'bg-green-500 hover:bg-green-600 cursor-pointer': ! finishedMulligan && canMulligan,
          'bg-gray-500 cursor-not-allowed': finishedMulligan || ! canMulligan,
        }"
        class="block rounded px-4 py-2 font-bold text-surface"
      >
        <span v-if="! finishedMulligan">
          Confirm mulligan
        </span>
        <span v-else>
          Waiting for other player...
        </span>
      </button>
    </div>
  </div>
</template>

<script>
import Card from './Card.vue'

export default {
  name: 'mulligan',
  components: {
    Card,
  },
  props: {
    gameId: String,
    player: Object,
    opponent: Object,
  },
  data () {
    return {
      selections: [],
      finishedMulligan: false,
      canMulligan: false,
    }
  },
  async mounted () {
    this.finishedMulligan = (this.player.mulliganed > -1)
    this.canMulligan = false

    if (this.player.hand.length === 0) {
      for (let i = 0; i < 5; i++) {
        this.player.hand.push(this.player.deck.pop())
        await timeout(500)
      }
    }

    this.canMulligan = true
  },
  methods: {
    selectCard (cardKey) {
      if (this.selections.includes(cardKey)) {
        this.selections = this.selections.filter(key => key !== cardKey)
        return
      }

      this.selections.push(cardKey)
    },
    async finish () {
      if (this.finishedMulligan || ! this.canMulligan) {
        return
      }

      this.finishedMulligan = true

      // Remove selection from the hand and return them to the deck
      this.selections.sort().reverse().forEach((key) => {
        const card = this.player.hand[key]
        this.player.hand = this.player.hand.filter((_, i) => i !== key)
        this.player.deck.unshift(card)
      })

      const amount = this.selections.length
      this.selections = []

      await timeout(500)

      this.player.deck.sort(() => Math.random() - 0.5)

      while (this.player.hand.length < 5) {
        this.player.hand.push(this.player.deck.pop())
        await timeout(500)
      }

      window.game.event('mulliganed', {
        player: this.player.id,
        hand: this.player.hand.map(card => card.uuid),
        deck: this.player.deck.map(card => card.uuid),
        amount: amount,
      })
    },
  },
}
</script>

<style scoped>
/* Cards in hand */
.card-enter-active,
.card-leave-active {
  transition: all 0.25s ease;
}

.card-enter-from {
  opacity: 0;
  transform: translateY(-30px) scale(0.75);
}

.card-leave-to {
  opacity: 0;
  width: 0%;
  transform: translateY(-100px) scale(0.75);
}
</style>
