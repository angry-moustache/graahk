<template>
  <div class="flex h-screen w-screen overflow-hidden">
    <div class="flex flex-col gap-4 h-screen w-[15rem] bg-surface border-r border-r-border justify-between">
      <Player
        :player="game.opponent"
        v-on:click="target(game.opponent)"
      />

      <Player
        :player="game.player"
        :reverse="true"
        v-on:click="target(game.opponent)"
      />
    </div>

    <div class="flex flex-col grow">
      <Board :board="game.opponent.board" :active="! game.areCurrentPlayer()">
        <Dude
          v-for="(dude, key) in game.opponent.board"
          v-bind:key="key"
          :dude="dude"
          v-on:click="target(dude)"
        />
      </Board>

      <Board :board="game.player.board" :active="game.areCurrentPlayer()">
        <Dude
          v-for="(dude, key) in game.player.board"
          v-bind:key="key"
          :dude="dude"
          v-on:click="target(dude)"
        />
      </Board>

      <div class="relative flex justify-evenly h-[20vh] border-t border-t-border bg-surface">
        <div class="absolute left-0 right-0 -bottom-[4rem] flex justify-center items-center gap-2">
          <Card
            v-for="(card, key) in game.player.hand"
            v-bind:key="key"
            :card="card"
            :card-key="key"
            :can-play="game.areCurrentPlayer() && card.cost <= game.player.energy"
            v-on:play-card="playCard"
          />
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

      <div>
        Jobs running:
        <span v-html="jobs.length"></span>
      </div>
    </div>
  </div>
</template>

<script>
import Card from './Card.vue'
import Board from './Board.vue'
import Dude from './Dude.vue'
import Player from './Player.vue'
import { Game } from '../helpers/game'

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
      jobs: [],
      game: null,
      gameState: JSON.parse(this.startingGameState),
      loading: false,
      targeting: false,
    }
  },
  created () {
    this.game = new Game(this)
    window.game = this.game

    window.setInterval(() => {
      if (this.jobs.length === 0) return
      this.jobs.shift().resolve()
      if (this.jobs.length === 0) this.game.updateGameState()
    }, 10)

    window.resizeCards()
  },
  methods: {
    // Targeting
    target (target) {
      console.log(target)
      if (! this.canDoAnything()) return

      if (this.targeting) {
        // Unselect current target
        if (target.uuid === this.targeting.uuid) {
          this.targeting.highlighted = false
          this.targeting = false

          return
        }

        // Select opponents target
        if (target.owner === this.game.opponent.id) {
          this.game.event('attack', {
            attacker: this.targeting.uuid,
            defender: target.uuid,
          })

          this.targeting.highlighted = false
          this.targeting = false

          return
        }
      } else {
        // New targeting starting
        if (! target.ready) return

        // Selecting a friendly dude
        if (target.owner == this.game.player.id) {
          this.targeting = target
          this.targeting.highlighted = true

          return
        }
      }
    },
    // Add events to the queue
    queue (events, pos = 'start') {
      if (pos === 'end') {
        this.jobs = [...this.jobs, ...events]
      } else if (pos === 'start') {
        this.jobs = [...events, ...this.jobs]
      }
    },
    playCard (cardKey) {
      if (! this.canDoAnything()) return
      this.game.playCard(cardKey)
    },
    endTurn () {
      if (! this.canDoAnything()) return
      this.game.event('end_turn')
    },
    effect (effect, data, target = null) {
      this.game.effect(effect, data, target)
    },
    canDoAnything () {
      return this.game.areCurrentPlayer() && this.jobs.length === 0 && ! this.isTargeting
    },
  },
}
</script>
