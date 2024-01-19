import { SwapTurn } from './events/SwapTurn'
import { PlayDude } from './events/PlayDude'

import { Player } from './entities/Player'
import { reactive } from 'vue'
import { Job } from './entities/Job'
import { Attack } from './events/Attack'

export class Game {
  constructor (_vue) {
    this._vue = _vue
    this.gameId = _vue.gameId
    this.pusher = window.pusher.subscribe(this.gameId)

    this.player = reactive(new Player(_vue.gameState.player))
    this.opponent = reactive(new Player(_vue.gameState.opponent))

    this.currentPlayer = (_vue.gameState.current_player === this.player.id ? this.player : this.opponent)
    this.currentOpponent = (_vue.gameState.current_player === this.player.id ? this.opponent : this.player)

    this.pusher.bind('event', (data) => {
      // Triggers
      switch (data.event) {
        case 'end_turn': (new SwapTurn).resolve(this, data); break
        case 'play_dude': (new PlayDude).resolve(this, data); break
        case 'attack': (new Attack).resolve(this, data); break
        default: console.error(`No trigger for ${data.event}`); break
      }
    })
  }

  // Post an event to the server
  event (event, data = {}) {
    window.axios.post(`/api/games/${this.gameId}/event`, { event: event, data: data })
  }

  getTargets (type, owner) {
    switch (type) {
      case 'player': return [this.currentPlayer]; break
      case 'opponent': return [this.currentOpponent]; break
      // case 'dude': return ; break
      // case 'dude_player': return ; break
      // case 'dude_opponent': return ; break
      case 'all_players': return [this.currentPlayer, this.currentOpponent]; break
      case 'all_player_dudes': return this.currentPlayer.board; break
      case 'all_other_player_dudes': return this.currentPlayer.board.filter((c) => c.uuid !== owner.uuid); break
      case 'all_opponent_dudes': return this.currentOpponent.board; break
      case 'all_dudes': return [...this.currentPlayer.board, ...this.currentOpponent.board]; break
      case 'all_other_dudes': return [...this.currentPlayer.board, ...this.currentOpponent.board].filter((c) => c.uuid !== owner.uuid); break
      case 'everything': return [...this.currentPlayer.board, ...this.currentOpponent.board, this.currentPlayer, this.currentOpponent]; break
      case 'itself': return [owner]; break
    }
  }

  // Do something
  effect (effect, data, targets) {
    targets.forEach((target) => {
      if (target[effect] === undefined) return console.error(`No effect ${effect} on ${target.name}`)
      target[effect](data)
    })
  }

  // Cleanup the board
  cleanup () {
    this._vue.queue([
      new Job(() => {
        this.player.cleanup(this)
        this.opponent.cleanup(this)
      })
    ])
  }

  // Check for any triggers after an effect or event has fired
  checkTriggers (trigger, targets = false) {
    (targets || this.getTargets('all_dudes')).forEach((dude) => {
      dude.effects.filter((e) => e.trigger === trigger).forEach((effect) => {
        this.effect(effect.effect, effect, this.getTargets(effect.target, dude))
      })
    })

    this.cleanup()
  }

  // Play a card from your hand
  playCard (key) {
    if (! this.areCurrentPlayer ()) return

    let card = this.player.hand[key]
    if (card.cost > this.player.energy) return

    this.event('play_dude', { card: card })
  }

  // Send data to the server to update the game state
  updateGameState () {
    return // DEV

    if (! this.areCurrentPlayer()) return

    window.setTimeout(() => {
      let gameState = {}
      gameState['current_player'] = this.currentPlayer.id
      gameState[`player_${this.player.id}`] = this.player
      gameState[`player_${this.opponent.id}`] = this.opponent

      window.axios.put(`/api/games/${this._vue.gameId}`, {
        gameState: gameState,
      })
    }, 1000)
  }

  areCurrentPlayer () {
    return this.currentPlayer.id === this._vue.playerId
  }

  playerById (id) {
    return (id === this.player.id ? this.player : this.opponent)
  }
}
