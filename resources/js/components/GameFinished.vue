<template>
  <div
    class="w-full h-screen fixed inset-0 items-center justify-center flex gap-4 bg-black bg-opacity-75 game-finished"
    style="z-index: 1000000"
  >
    <TransitionGroup name="steps" class="flex gap-4 justify-center align-stretch game-finished-content w-full">
      <div
        class="bg-background p-8 w-1/3 rounded-lg flex flex-col gap-2 items-center"
        v-if="step === 1"
      >
        <div v-if="won" class="w-64 h-64 relative">
          <div
              v-bind:style="`background-image: url('${game.player.avatar}')`"
              class="w-64 h-64 pt-[100%] rounded-lg bg-cover bg-center"
          ></div>
        </div>
        <div v-if="! won" class="w-64 h-64 relative">
          <div class="broken-avatar-left w-32 h-64 absolute top-0 left-0 overflow-hidden">
            <div
                v-bind:style="`background-image: url('${game.player.avatar}')`"
                class="w-64 h-64 pt-[100%] rounded-lg bg-cover bg-center"
            ></div>
          </div>

          <div class="broken-avatar-right w-32 h-64 absolute top-0 right-0 overflow-hidden">
            <div
                v-bind:style="`background-image: url('${game.player.avatar}')`"
                class="w-64 h-64 pt-[100%] rounded-lg bg-cover bg-center ml-[-8rem]"
            ></div>
          </div>
        </div>

        <h1 v-text="won ? 'Victory!' : 'Defeat...'" class="text-4xl font-bold uppercase mt-8" />
        <p v-text="won ? 'Truly a glorious battle!' : 'There\'s always next time!'"/>

        <button
          v-on:click="next()"
          v-text="won ? 'Huzzah!' : 'Next time...!'"
          class="
            block rounded px-6 py-3 font-bold text-xl text-surface mt-8
            bg-green-500 hover:bg-green-600 cursor-pointer
          "
        ></button>
      </div>

      <div
        class="bg-background p-8 w-1/3 rounded-lg flex flex-col gap-12 items-center"
        v-if="step === 2"
      >
        <template v-if="game.afterGameUpgrades[$parent.playerId]">
          <div class="flex flex-col items-center">
            <p class="text-sm opacity-50">
              You gained experience for a card!
            </p>

            <p
              class="font-bold text-xl"
              v-text="`${game.afterGameUpgrades[$parent.playerId].name} gained experience!`"
            />
          </div>

          <div class="w-1/3 aspect-[2.5/3.5]">
            <Card
              :card="game.afterGameUpgrades[$parent.playerId]"
              full-sized
              glowing
            />
          </div>

          <button
            v-on:click="next()"
            class="
              block rounded px-6 py-3 font-bold text-xl text-surface
              bg-green-500 hover:bg-green-600 cursor-pointer
            "
          >
            Hell yeah
          </button>
        </template>
      </div>
    </TransitionGroup>
  </div>
</template>

<script>
import Card from './Card.vue'

export default {
  name: 'game_over',
  components: {
    Card,
  },
  props: {
    game: Object,
  },
  data () {
    return {
      won: null,
      step: 1,
    }
  },
  async mounted () {
    this.won = (this.game.player.power > 0)

    window.axios.put(`/api/games/${this.game.gameId}/finish`, {
      players: [
        { id: this.game.player.id, power: this.game.player.power },
        { id: this.game.opponent.id, power: this.game.opponent.power },
      ],
    })
  },
  methods: {
    next () {
      if (this.step === 2) {
        window.location.href = '/server'
      }

      this.step++
    },
  },
}
</script>

<style scoped>
.steps-enter-active,
.steps-leave-active {
  transition: all 0.5s ease;
}

.steps-enter-from {
  position: absolute;
  opacity: 0;
  transform: translateY(-100px);
}

.steps-enter-to,
.steps-leave-from {
  position: absolute;
  opacity: 1;
  transform: translateY(0);
}

.steps-leave-to {
  position: absolute;
  opacity: 0;
  transform: translateY(100px);
}
</style>
