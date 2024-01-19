import { SwapTurn } from './triggers/SwapTurn'
import { PlayDude } from './triggers/PlayDude'

import { DrawCard } from './effects/DrawCard'
import { GainEnergy } from './effects/GainEnergy'
import { Kill } from './effects/Kill'
import { Bounce } from './effects/Bounce'
import { BuffDude } from './effects/BuffDude'
import { DealDamage } from './effects/DealDamage'
import { Heal } from './effects/Heal'

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
    this.pusher.bind('event', (data) => {
      switch (data.event) {
        case 'end_turn': (new SwapTurn).resolve(this._vue, data); break
        case 'play_dude': (new PlayDude).resolve(this._vue, data); break
      }

      this.updateGameState(this._vue.gameState)
    })
  },
  // Post a trigger event to the server
  event (event, data = {}) {
    window.axios.post(`/api/games/${this.gameId}/event`, { event: event, data: data })
  },
  // Post an effect to the server
  effect (effect, data = {}, target = {}) {
    switch (effect) {
      case 'draw_cards': (new DrawCard).resolve(this._vue, data, target); break
      case 'gain_energy': (new GainEnergy).resolve(this._vue, data, target); break
      // case 'kill': (new Kill).resolve(this._vue, data, target); break
      case 'bounce': (new Bounce).resolve(this._vue, data, target); break
      case 'buff_dude': (new BuffDude).resolve(this._vue, data, target); break
      case 'deal_damage': (new DealDamage).resolve(this._vue, data, target); break
      case 'heal': (new Heal).resolve(this._vue, data, target); break
      default: console.error('Unknown effect: ' + effect); break
    }

    this.updateGameState(this._vue.gameState)
  },
  // Play a card from your hand
  playCard (key) {
    this.event('play_dude', {
      player: this._vue.player.user.id,
      key: key,
    })
  },
  // Loop everything and check if it contains the trigger
  checkTriggers (trigger, target) {
    this._vue.queue(() => {
      console.log('Checking triggers for ' + trigger)

      target.board.forEach((card, index) => {
        let triggers = card.effects.filter((effect) => effect.trigger === trigger)

        triggers.reverse().forEach((trigger) => {
          let targets

          switch (trigger.target) {
            case 'player': targets = this._vue.player.board; break
            case 'opponent': targets = this._vue.opponent.board; break
            // case 'dude': break
            // case 'dude_player': break
            // case 'dude_opponent': break
            case 'all_players': targets = [this._vue.player, this._vue.opponent]; break
            case 'all_player_dudes': targets = this._vue.player.board; break
            case 'all_player_dudes_not_self': targets = this._vue.player.board.filter((c, i) => i !== index); break
            case 'all_opponent_dudes': targets = this._vue.opponent.board; break
            case 'all_dudes': targets = [...this._vue.player.board, ...this._vue.opponent.board]; break
            case 'all_other_dudes': targets = [...this._vue.player.board.filter((c, i) => i !== index), ...this._vue.opponent.board]; break
            case 'everything': targets = [...this._vue.player.board, ...this._vue.opponent.board, this._vue.player, this._vue.opponent]; break
            case 'itself': targets = [card]; break
          }

          if (! targets) return

          Object.values(targets).forEach((target) => {
            this.effect(trigger.effect, trigger, target)
            this._vue.queue(() => this.deathSweep())
          })
        })
      })
    })

    this.updateGameState(this._vue.gameState)
  },
  // Check for any dead dudes or players
  deathSweep () {
    console.log('Performing death sweep')
    // Object.values([this._vue.currentPlayer, this._vue.currentOpponent]).forEach((player) => {
    //   player.board.forEach((card, key) => {
    //     // TODO: don't check for tireless dudes
    //     if (card.power <= 0) {
    //       this.effect('kill', { target: 'itself', key: key, _meta: {
    //         player: player.user.id,
    //         cardKey: key
    //       } })
    //     }
    //   })
    // })
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
