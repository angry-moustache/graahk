<template>
  <div class="flex h-screen w-screen overflow-hidden">
    <div class="flex flex-col gap-4 h-screen w-[15rem] bg-surface border-r border-r-border justify-between">
      <Player :player="opponent" />
      <Player :player="player" :reverse="true" />
    </div>

    <div class="flex flex-col grow">
      <Board :board="opponent.board" :active="! areCurrentPlayer">
        <Dude
          v-for="(dude, key) in opponent.board"
          v-bind:key="key"
          :dude="dude"
          v-on:click="targetOpponentDude(dude)"
        />
      </Board>

      <Board :board="player.board" :active="areCurrentPlayer">
        <Dude
          v-for="(dude, key) in player.board"
          v-bind:key="key"
          :dude="dude"
          v-on:click="targetDude(dude)"
        />
      </Board>

      <div class="relative flex justify-evenly h-[20vh] border-t border-t-border bg-surface">
        <div class="absolute left-0 right-0 -bottom-[4rem] flex justify-center items-center gap-2">
          <Card
            v-for="(card, key) in player.hand"
            v-bind:key="key"
            :card="card"
            :card-key="key"
            :can-play="areCurrentPlayer && card.cost <= player.energy"
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

      <div v-html="jobs.length"></div>
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
      jobs: [],
      targetingWith: false,
      playingCard: false,
      doDeathSweep: false,
    }
  },
  mounted () {
    Game.init(this)

    window.setInterval(() => {
      if (this.jobs.length === 0) {
        if (this.doDeathSweep) {
          this.doDeathSweep = false
          Game.deathSweep()
        }

        return
      }

      this.jobs.shift()() // Do the job

      Game.updateGameState(this.gameState)
    }, 100)
  },
  methods: {
    // Add events to the queue
    queue (event, pos = 'start') {
      if (! Array.isArray(event)) {
        event = [event]
      }

      this.doDeathSweep = true

      if (pos === 'end') {
        this.jobs = [...this.jobs, ...event]
      } else if (pos === 'start') {
        this.jobs = [...event, ...this.jobs]
      }
    },
    // Play a card if you can
    playCard (key) {
      if (! this.areCurrentPlayer) return

      if (this.player.hand[key].cost > this.player.energy) return

      this.queue(() => Game.playCard(key), 'end')
    },
    // Do something when clicking your dudes
    targetDude (dude) {
      if (! this.canAddToQueue) return

      // Starting a new target
      if (! this.targetingWith) {
        if (! dude.ready) return

        this.targetingWith = dude
        dude.highlighted = true

        return
      }

      // Selecting a target
      if (this.targetingWith) {
        if (dude === this.targetingWith) {
          dude.highlighted = false
          this.targetingWith = false
          return
        }

        // this.targetingWith.highlighted = false
        // this.targetingWith = false

        // this.queue(() => Game.attack(this.targetingWith, dude), 'end')
        return
      }
    },
    // Do something when clicking your opponents dudes
    targetOpponentDude (dude) {
      if (! this.canAddToQueue) return

      // Selecting a target
      if (this.targetingWith) {
        // Attacking
        this.queue(() => {
          const attacker = this.targetingWith.power
          const defender = dude.power

          this.targetingWith.power -= defender
          dude.power -= attacker

          this.targetingWith.highlighted = false
          this.targetingWith.ready = false
          this.targetingWith = false
        })

        return
      }
    },
    // Game functions
    checkTriggers (trigger) {
      Game.checkTriggers(trigger)
    },
    endTurn () {
      if (! this.canAddToQueue) return
      Game.trigger('end_turn')
    },
    effect (effect, data) {
      Game.effect(effect, data)
    },
    playerFromId (id) {
      return id === this.playerId ? this.player : this.opponent
    },
  },
  computed: {
    canAddToQueue() {
      return (this.areCurrentPlayer && this.jobs.length === 0)
    },
    areCurrentPlayer () {
      return this.gameState.current_player === this.playerId
    },
    currentPlayer () {
      return this.areCurrentPlayer ? this.player : this.opponent
    },
    currentOpponent () {
      return this.areCurrentPlayer ? this.opponent : this.player
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
