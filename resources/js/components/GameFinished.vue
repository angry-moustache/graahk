<template>
  <div
    class="w-full h-screen fixed inset-0 items-center justify-center flex gap-4 bg-black bg-opacity-75 game-finished"
    style="z-index: 1000000"
  >
    <TransitionGroup
      class="relative flex gap-4 justify-center align-stretch game-finished-content w-full h-[60vh]"
      name="steps"
      tag="div"
    >
      <div
        class="bg-background p-8 w-1/3 rounded-lg flex flex-col justify-between items-center h-[60vh] absolute top-0"
        v-if="step === 1"
        v-bind:key="1"
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

        <div class="flex flex-col gap-4 items-center">
          <h1 v-text="won ? 'Victory!' : 'Defeat...'" class="text-4xl font-bold uppercase mt-8" />
          <p v-text="won ? 'Truly a glorious battle!' : 'There\'s always next time!'"/>
        </div>

        <button
          v-on:click="receivedData ? next() : null"
          v-text="won ? 'Huzzah!' : 'Oh bugger..'"
          class="block rounded px-6 py-3 font-bold text-xl text-surface mt-8"
          v-bind:class="{
            'bg-green-500 hover:bg-green-600 cursor-pointer': receivedData,
            'bg-gray-500 cursor-wait': ! receivedData,
          }"
        ></button>
      </div>

      <div
        class="bg-background p-8 w-1/3 rounded-lg flex flex-col justify-between items-center h-[60vh] absolute top-0"
        v-if="step === 2"
        v-bind:key="2"
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
  data () {
    return {
      won: null,
      step: 1,
      game: window.game,
      receivedData: false,
    }
  },
  async mounted () {
    this.won = (this.game.player.power > 0)
    this.receivedData = this.game.afterGameUpgrades[this.$parent.playerId] !== undefined
  },
  methods: {
    next () {
      if (this.step === 2) {
        window.location.href = '/server'
      }

      this.step++
    },
    dataReceived () {
      this.receivedData = true
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
