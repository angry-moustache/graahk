<template>
  <div
    class="w-full h-screen fixed inset-0 items-center justify-center flex bg-black bg-opacity-75 game-finished"
    style="z-index: 10000000000"
  >
    <div class="bg-background p-8 w-1/3 rounded-lg flex flex-col gap-2 items-center game-finished-content">
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

      <a
        class="bg-green-500 hover:bg-green-600 cursor-pointer block rounded px-4 py-2 font-bold text-surface mt-8"
        v-text="won ? 'Huzzah!' : 'Demand a rematch!'"
        href="/server"
      />
    </div>
  </div>
</template>

<script>
export default {
  name: 'game_over',
  props: {
    game: Object,
  },
  data () {
    return {
      won: null,
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
}
</script>
