<template>
  <div class="flex h-screen w-screen overflow-hidden">
    <div class="flex flex-col gap-4 h-screen w-[15rem] bg-surface border-r border-r-border justify-between">
      <Player :player="opponent" />
      <Player :player="player" :reverse="true" />
    </div>

    <div class="flex flex-col grow">
      <Board :board="opponent.board" :active="! areCurrentPlayer">
        <Dude :dude="dude" v-for="dude in opponent.board" v-bind:key="dude.id"/>
      </Board>

      <Board :board="player.board" :active="areCurrentPlayer">
        <Dude :dude="dude" v-for="dude in player.board" v-bind:key="dude.id"/>
      </Board>

      <div class="relative flex justify-evenly h-[20vh] border-t border-t-border bg-surface">
        <div class="absolute left-0 right-0 -bottom-[4rem] flex justify-center items-center gap-2">
          <Card :card="card" v-for="card in player.hand" v-bind:key="card.id"/>
        </div>
      </div>
    </div>

    <div class="flex flex-col gap-4 h-screen w-[15rem] bg-surface border-l border-l-border justify-center items-center">
      <button
        v-on:click="endTurn()"
        class="
          block rounded px-4 py-2 font-bold text-surface
          bg-green-500 hover:bg-green-600 cursor-pointer
        "
      >
        End Turn
      </button>
    </div>
  </div>
</template>

<script>
import Card from './Card.vue'
import Board from './Board.vue'
import Dude from './Dude.vue'
import Player from './Player.vue'
import Game from '../helpers/game'

export default {
  name: 'game',
  components: { Card, Board, Dude, Player },
  props: {
    startingGameState: String,
    playerId: Number,
    gameId: String,
  },
  data () {
    return {
      gameState: JSON.parse(this.startingGameState),
    }
  },
  mounted () {
    console.log(this.gameState)
    Game.init(this)
  },
  methods: {
    endTurn: () => Game.trigger('end_turn'),
  },
  computed: {
    areCurrentPlayer () {
      return this.gameState.current_player === this.playerId
    },
    currentPlayer () {
      return this.areCurrentPlayer ? this.player : this.opponent
    },
    player () {
      return this.gameState.player
    },
    opponent () {
      return this.gameState.opponent
    },
  }
}
</script>
