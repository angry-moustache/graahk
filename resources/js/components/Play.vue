<template>
  <div class="flex h-screen w-screen overflow-hidden relative">
    <Mulligan
      v-if="! game.haveMulliganed()"
      v-bind:player="game.player"
      v-bind:opponent="game.opponent"
      v-bind:game-id="gameId"
      ref="mulligan"
    />

    <GameFinished
      v-if="gameCompleted"
      v-bind:game="game"
      ref="game_over"
    />

    <div
      class="flex h-screen w-screen overflow-hidden relative"
      v-if="game.haveMulliganed()"
    >
      <Errors ref="errors" />
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
          v-on:click="target(game.player)"
          :ref="'player-' + game.player.uuid"
        />
      </div>

      <div class="flex flex-col grow">
        <Board
          :board="game.opponent.board"
          :active="! game.areCurrentPlayer()"
          :ref="'board-' + game.opponent.uuid"
        >
          <TransitionGroup
            class="flex flex-wrap h-[30vh] w-full gap-4 items-center justify-evenly"
            name="dude"
            tag="div"
          >
            <Dude
              v-for="dude in game.opponent.board"
              v-bind:key="dude.uuid"
              :dude="dude"
              v-on:click="target(dude)"
              v-on:mouseenter="tooltip(dude)"
              v-on:mouseleave="tooltip(null)"
            />
          </TransitionGroup>
        </Board>

        <Board
          :board="game.player.board"
          :active="game.areCurrentPlayer()"
          :ref="'board-' + game.player.uuid"
        >
          <TransitionGroup
            class="flex flex-wrap h-[30vh] w-full gap-4 items-center justify-evenly"
            name="dude"
            tag="div"
          >
            <Dude
              v-for="dude in game.player.board"
              v-bind:key="dude.uuid"
              :dude="dude"
              v-on:click="target(dude)"
              v-on:mouseenter="tooltip(dude)"
              v-on:mouseleave="tooltip(null)"
            />
          </TransitionGroup>
        </Board>

        <div class="relative flex justify-evenly h-[20vh] border-t border-t-border bg-surface">
          <div
            class="
              absolute z-10 left-0 right-0 bottom-0 -mb-[50px] flex justify-center items-center gap-2
              transition-all duration-500 ease-in-out opacity-100
            "
            v-bind:class="{
              'opacity-50': ! canDoAnything(),
            }"
          >
            <TransitionGroup name="card">
              <Card
                v-for="(card, key) in game.player.hand"
                v-bind:key="card.uuid"
                :id="'hand-' + card.uuid"
                :card="card"
                :card-key="key"
                :can-play="canDoAnything() && card.cost <= game.player.energy"
                v-on:play-card="playCard"
                v-on:mouseenter="tooltip(card)"
                v-on:mouseleave="tooltip(null)"
              />
            </TransitionGroup>
          </div>
        </div>
      </div>

      <div class="flex flex-col gap-4 h-screen w-[15rem] bg-surface border-l border-l-border justify-center items-center">
        <Targeting ref="targeting" />

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

        <!-- <button
          v-on:click="game.updateGameState()"
          v-bind:class="{
            'bg-green-500 hover:bg-green-600 cursor-pointer': canDoAnything(),
            'bg-gray-500 cursor-not-allowed': ! canDoAnything(),
          }"
          class="block rounded px-4 py-2 font-bold text-surface"
        >
          Save the game
        </button>

        <button
          v-on:click="game.effect('draw_cards', { amount: 1 }, [game.currentPlayer])"
          v-bind:class="{
            'bg-green-500 hover:bg-green-600 cursor-pointer': canDoAnything(),
            'bg-gray-500 cursor-not-allowed': ! canDoAnything(),
          }"
          class="block rounded px-4 py-2 font-bold text-surface"
        >
          Draw a card
        </button>

        <button
          v-on:click="game.effect('gain_energy', { amount: 3 }, [game.currentPlayer])"
          v-bind:class="{
            'bg-green-500 hover:bg-green-600 cursor-pointer': canDoAnything(),
            'bg-gray-500 cursor-not-allowed': ! canDoAnything(),
          }"
          class="block rounded px-4 py-2 font-bold text-surface"
        >
          Gain 3 energy
        </button>

        <button
          v-on:click="game.cleanup()"
          v-bind:class="{
            'bg-green-500 hover:bg-green-600 cursor-pointer': canDoAnything(),
            'bg-gray-500 cursor-not-allowed': ! canDoAnything(),
          }"
          class="block rounded px-4 py-2 font-bold text-surface"
        >
          Cleanup
        </button>

        <div>
          Jobs running:
          <span v-html="jobs.isProcessing"></span>
        </div>

        <div>
          Jobs total:
          <span v-html="jobs.count()"></span>
        </div> -->
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
import Targeting from './Targeting.vue'
import Errors from './Errors.vue'
import Mulligan from './Mulligan.vue'
import GameFinished from './GameFinished.vue'
import { Game } from '../helpers/game'
import { Queue } from '../helpers/entities/Queue'

import { reactive } from 'vue'

export default {
  name: 'game',
  components: {
    Card,
    Board,
    Dude,
    Player,
    Tooltip,
    Animation,
    Targeting,
    Errors,
    Mulligan,
    GameFinished,
  },
  props: {
    startingGameState: String,
    playerId: Number,
    gameId: String,
  },
  data () {
    return {
      jobs: [],
      animations: [],
      game: null,
      gameState: JSON.parse(this.startingGameState),
      gameCompleted: false,
    }
  },
  created () {
    this.game = window.game = new Game(this)
    this.jobs = window.jobs = reactive(new Queue())

    this.gameCompleted = this.game.completed

    this.$nextTick(() => window.resizeCards())
  },
  methods: {
    // Add events to the queue
    async queue (events, pos = 'start') {
      window.jobs.push(events, pos)

      if (window.jobs.isProcessing) return
      window.jobs.processQueue()
    },
    target (target) {
      this.$refs.targeting.target(target)
    },
    playCard (cardKey, data = {}) {
      if (! this.canDoAnything()) return

      let card = this.game.player.hand[cardKey]
      if (this.$refs.targeting.areTargeting()) {
        this.$refs.targeting.target(card)
      } else {
        const requiresTarget = card.effects.map((c) => c.target).some((t) => window.requiresTarget.includes(t))

        if (! requiresTarget) {
          return this.game.playCard(cardKey, data)
        }

        this.$refs.targeting.setAimer(card)
      }
    },
    getTarget () {
      return this.$refs.targeting.victim
    },
    endTurn () {
      if (! this.canDoAnything() || this.$refs.targeting.areTargeting()) return
      this.game.event('end_turn')
    },
    effect (effect, data, target = null) {
      this.game.effect(effect, data, target)
    },
    canDoAnything () {
      return this.game.areCurrentPlayer()
          && this.jobs.checkQueueEmpty()
          && ! this.gameCompleted
    },
    tooltip (card) {
      this.$refs.tooltip.show(card)
    },
  },
}
</script>

<style scoped>
/* Dudes on the field */
.dude-enter-active,
.dude-leave-active {
  transition: all 0.5s ease;
}

.dude-enter-from {
  opacity: 0;
  transform: translateX(30px);
}

.dude-leave-to {
  opacity: 0;
  transform: translateY(100px) rotate(30deg) scale(0.5);
}

/* Cards in hand */
.card-enter-active,
.card-leave-active {
  transition: all 0.5s ease;
}

.card-enter-from {
  opacity: 0;
  transform: translateX(-30px);
}

.card-leave-to {
  opacity: 0;
  width: 0%;
  transform: translateY(-100px);
}
</style>
