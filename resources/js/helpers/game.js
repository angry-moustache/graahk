import { SwapTurn } from './triggers/SwapTurn'
import { PlayDude } from './triggers/PlayDude'

import { DrawCard } from './effects/DrawCard'
import { GainEnergy } from './effects/GainEnergy'
import { Kill } from './effects/Kill'
import { Bounce } from './effects/Bounce'
import { BuffDude } from './effects/BuffDude'
import { DealDamage } from './effects/DealDamage'

export default {
  _vue: null,
  gameId: null,
  pusher: null,
  init (_vue) {
    this._vue = _vue
    this.gameId = _vue.gameId
    this.pusher = window.pusher.subscribe(this.gameId)

    window.resizeCards()

    // Triggers
    this.pusher.bind('trigger', (data) => {
      switch (data.trigger) {
        case 'end_turn': (new SwapTurn).trigger(this._vue, data); break
        case 'play_dude': (new PlayDude).trigger(this._vue, data); break
      }

      this.updateGameState(this._vue.gameState)
    })
  },
  // Post a trigger event to the server
  trigger (trigger, data = {}) {
    window.axios.post(`/api/games/${this.gameId}/trigger`, {
      trigger: trigger,
      data: data,
    })
  },
  // Post an effect to the server
  effect (effect, data = {}, target = {}) {
    switch (effect) {
      case 'draw_cards': (new DrawCard).resolve(this._vue, data, target); break
      case 'gain_energy': (new GainEnergy).resolve(this._vue, data, target); break
      case 'kill': (new Kill).resolve(this._vue, data, target); break
      case 'bounce': (new Bounce).resolve(this._vue, data, target); break
      case 'buff_dude': (new BuffDude).resolve(this._vue, data, target); break
      case 'deal_damage': (new DealDamage).resolve(this._vue, data, target); break
      default: console.error('Unknown effect: ' + effect); break
    }

    this.updateGameState(this._vue.gameState)
  },
  // Play a card from your hand
  playCard (key) {
    this.trigger('play_dude', { key: key, player: this._vue.player })
  },
  // Loop everything and check if it contains the trigger
  checkTriggers (trigger) {
    this._vue.queue(() => {
      console.log('Checking triggers for ' + trigger)

      for (const [who, player] of Object.entries({ player: this._vue.currentPlayer, opponent: this._vue.currentOpponent})) {
        player.board.forEach((card, key) => {
          let triggers = card.effects.filter((effect) => effect.trigger === trigger)

          if (
            (trigger.startsWith('player_') && who === 'opponent') ||
            (trigger.startsWith('opponent_') && who === 'player') ||
            (trigger === 'start_turn' && who === 'opponent') ||
            (trigger === 'end_turn' && who === 'opponent')
          ) {
            return
          }

          triggers.forEach((trigger) => {
            trigger._meta = { player: player.user.id, cardKey: key }
            this.effect(trigger.effect, trigger)
          })
        })
      }
    })

    this.updateGameState(this._vue.gameState)
  },
  // Check for any dead dudes or players
  deathSweep () {
    console.log('Performing death sweep')
    Object.values([this._vue.currentPlayer, this._vue.currentOpponent]).forEach((player) => {
      player.board.forEach((card, key) => {
        // TODO: don't check for tireless dudes
        if (card.power <= 0) {
          this.effect('kill', { target: 'itself', key: key, _meta: {
            player: player.user.id,
            cardKey: key
          } })
        }
      })
    })
  },
  // Send data to the server to update the game state
  updateGameState (gameState) {
    // if (! this._vue.areCurrentPlayer) return

    // gameState[`player_${this._vue.player.user.id}`] = this._vue.player
    // gameState[`player_${this._vue.opponent.user.id}`] = this._vue.opponent

    // window.axios.put(`/api/games/${this.gameId}`, {
    //   gameState: gameState,
    // })
  },
}
