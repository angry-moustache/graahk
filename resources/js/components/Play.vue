<template>
  <div class="flex h-screen w-screen overflow-hidden" v-if="! loading">
    <Tooltip ref="tooltip" />

    <div class="absolute z-20 inset-0 pointer-events-none">
      <Animation
        v-for="(animation, key) in animations"
        :key="key"
        :animation="animation"
      />
    </div>

    <div class="flex flex-col gap-4 h-screen w-[15rem] bg-surface border-r border-r-border justify-between">
      <Player
        :player="game.opponent"
        v-on:click="target(game.opponent)"
        :ref="'player-' + game.opponent.uuid"
      />

      <Player
        :player="game.player"
        :reverse="true"
        v-on:click="target(game.opponent)"
        :ref="'player-' + game.player.uuid"
      />
    </div>

    <div class="flex flex-col grow">
      <Board
        :board="game.opponent.board"
        :active="! game.areCurrentPlayer()"
        :ref="'board-' + game.opponent.uuid"
      >
        <Dude
          v-for="(dude, key) in game.opponent.board"
          v-bind:key="key"
          :ref="'dude-' + dude.uuid"
          :dude="dude"
          v-on:click="target(dude)"
          v-on:mouseenter="tooltip(dude)"
          v-on:mouseleave="tooltip(null)"
        />
      </Board>

      <Board
        :board="game.player.board"
        :active="game.areCurrentPlayer()"
        :ref="'board-' + game.player.uuid"
      >
        <Dude
          v-for="(dude, key) in game.player.board"
          v-bind:key="key"
          :ref="'dude-' + dude.uuid"
          :dude="dude"
          v-on:click="target(dude)"
          v-on:mouseenter="tooltip(dude)"
          v-on:mouseleave="tooltip(null)"
        />
      </Board>

      <div class="relative flex justify-evenly h-[20vh] border-t border-t-border bg-surface">
        <div class="absolute z-10 left-0 right-0 -bottom-[4rem] flex justify-center items-center gap-2">
          <Card
            v-for="(card, key) in game.player.hand"
            v-bind:key="key"
            :card="card"
            :card-key="key"
            :can-play="canDoAnything() && card.cost <= game.player.energy"
            v-on:play-card="playCard"
            v-on:mouseenter="tooltip(card)"
            v-on:mouseleave="tooltip(null)"
          />
        </div>
      </div>
    </div>

    <div class="flex flex-col gap-4 h-screen w-[15rem] bg-surface border-l border-l-border justify-center items-center">
      <button
        v-on:click="endTurn()"
        v-bind:class="{
          'bg-green-500 hover:bg-green-600 cursor-pointer': canDoAnything(),
          'bg-gray-500 cursor-not-allowed': ! canDoAnything(),
        }"
        class="block rounded px-4 py-2 font-bold text-surface"
      >
        End Turn
      </button>

      <div>
        Jobs ready:
        <span v-html="jobs.length"></span>
      </div>

      <div>
        Runners ready:
        <span v-html="runner.length"></span>
      </div>
    </div>
  </div>
</template>

<script>
import Card from './Card.vue'
import Board from './Board.vue'
import Dude from './Dude.vue'
import Player from './Player.vue'
import Tooltip from './Tooltip.vue'
import Animation from './animations/Animation.vue'
import { Game } from '../helpers/game'

export default {
  name: 'game',
  components: { Card, Board, Dude, Player, Tooltip, Animation },
  props: {
    startingGameState: String,
    playerId: Number,
    gameId: String,
  },
  data () {
    return {
      loading: true,
      jobs: [],
      currentJob: null,
      runner: [],
      animations: [],
      game: null,
      gameState: JSON.parse(this.startingGameState),
      targeting: false,
    }
  },
  created () {
    this.game = new Game(this)
    window.game = this.game

    window.setInterval(() => {
      if (this.jobs.length === 0) return
      this.startRunner()
      if (this.jobs.length === 0) this.game.updateGameState()
    }, 1)

    this.loading = false

    this.$nextTick(() => {
      window.resizeCards()
    })
  },
  methods: {
    startRunner () {
      if (this.jobs.length === 0 || this.runner.length > 0) return

      // check if the current job has finished
      if (this.currentJob && ! this.currentJob.finished) return

      const job = this.jobs.shift()
      this.runner.push(job)

      this.currentJob = job
      job.resolve()
    },
    // Targeting
    // TODO: move to separate component
    target (target) {
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
      return this.game.areCurrentPlayer()
        && this.jobs.length === 0
        && this.runner.length === 0
    },
    tooltip (card) {
      this.$refs.tooltip.show(card)
    },
    animate (animation) {
      console.log(animation)
    }
  },
}
</script>
