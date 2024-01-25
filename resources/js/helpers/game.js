import { SwapTurn } from './events/SwapTurn'
import { PlayDude } from './events/PlayDude'
import { Mulliganed } from './events/Mulliganed'

import { Player } from './entities/Player'
import { reactive } from 'vue'
import { Attack } from './events/Attack'

export class Game {
  constructor (_vue) {
    this._vue = _vue
    this.gameId = _vue.gameId
    this.pusher = window.pusher.subscribe(this.gameId)
    this.completed = _vue.gameState.completed || false

    this.player = reactive(new Player(_vue.gameState.player))
    this.opponent = reactive(new Player(_vue.gameState.opponent))

    this.currentPlayer = (_vue.gameState.current_player === this.player.id ? this.player : this.opponent)
    this.currentOpponent = (_vue.gameState.current_player === this.player.id ? this.opponent : this.player)

    // Triggers
    this.pusher.bind('event', (data) => {
      switch (data.event) {
        case 'mulliganed': (new Mulliganed).resolve(this, data); break
        case 'end_turn': (new SwapTurn).resolve(this, data); break
        case 'play_dude': (new PlayDude).resolve(this, data); break
        case 'attack': (new Attack).resolve(this, data); break
        default: window.errorToast(`No trigger for ${data.event}`); break
      }
    })
  }

  haveMulliganed () {
    return (this.player.mulliganed !== -1 && this.opponent.mulliganed !== -1)
  }

  // Post an event to the server
  event (event, data = {}) {
    window.axios.post(`/api/games/${this.gameId}/event`, { event: event, data: data })
  }

  getTargets (type, owner, target = null) {
    let player, opponent
    if (owner) {
      player = (owner.owner === this.currentPlayer.id ? this.currentPlayer : this.currentOpponent)
      opponent = (owner.owner === this.currentPlayer.id ? this.currentOpponent : this.currentPlayer)
    } else {
      [player, opponent] = [this.currentPlayer, this.currentOpponent]
    }

    type = type || 'player'

    switch (type) {
      case 'player': return [player]; break
      case 'opponent': return [opponent]; break
      case 'dude': return [this._vue.getTarget()]; break
      case 'dude_player': return [this._vue.getTarget()]; break
      case 'dude_opponent': return [this._vue.getTarget()]; break
      case 'all_players': return [player, opponent]; break
      case 'all_player_dudes': return player.board; break
      case 'all_other_player_dudes': return player.board.filter((c) => c.uuid !== owner.uuid); break
      case 'all_opponent_dudes': return opponent.board; break
      case 'all_dudes': return [...player.board, ...opponent.board]; break
      case 'all_other_dudes': return [...player.board, ...opponent.board].filter((c) => c.uuid !== owner.uuid); break
      case 'everything': return [...player.board, ...opponent.board, player, opponent]; break
      case 'itself': return [owner]; break
      case 'from_uuid': return [...player.board, ...opponent.board, player, opponent].filter((c) => c.uuid === target); break
      default: window.errorToast(`No target type ${type}`); break
    }
  }

  // Check for any triggers after an effect or event has fired
  async checkTriggers (trigger, checks = false, targetedTargets = false) {
    console.log(`TRIGGER: ${trigger}`);

    window.game._vue.queue(() => {
      console.log(`STARTED TRIGGERING: ${trigger}`);

      let targets
      (checks || this.getTargets('all_dudes')).forEach((dude) => {
        dude.effects.filter((e) => e.trigger === trigger).reverse().forEach((effect) => {
          // Only use targeted targets if it uses them
          targets = null
          if (window.requiresTarget.includes(effect.target)) {
            targets = targetedTargets
          }

          // This only pushes to the queue, it doesn't actually run the effect yet
          this.effect(effect.effect, effect, targets || this.getTargets(effect.target, dude), dude)
        })
      })

      console.log(`ENDED TRIGGERING: ${trigger}`);

      window.nextJob()
    })
  }

  // Do something
  effect (effect, data, targets, source = null) {
    // Make sure to include the .nextJob() in the event itself
    window.game._vue.queue(() => {
      console.log(`STARTED EFFECT: ${effect}`);
      targets.forEach(async (target) => {
        if (target[effect] === undefined) {
          return window.errorToast(`No effect ${effect} on ${target.uuid}`)
        }

        console.log(`EFFECT: ${effect}`)
        await target[effect](data, source)
      })

      if (targets.length === 0) {
        window.nextJob()
      }

      console.log(`ENDED EFFECT: ${effect}`);
    })
  }

  cleanup () {
    this.player.causalityList().forEach((death) => {
      if (death.type === 'dude') {
        this.checkTriggers('dude_dies', [...this.player.board, ...this.opponent.board])
        this.checkTriggers('player_dude_dies', this.player.board)
        this.checkTriggers('opponent_dude_dies', this.opponent.board)
      }

      this.checkTriggers('leave_field', [death])
    })

    this.opponent.causalityList().forEach((death) => {
      if (death.type === 'dude') {
        this.checkTriggers('dude_dies', [...this.player.board, ...this.opponent.board])
        this.checkTriggers('player_dude_dies', this.opponent.board)
        this.checkTriggers('opponent_dude_dies', this.player.board)
      }

      this.checkTriggers('leave_field', [death])
    })

    this._vue.$refs.targeting.stopTargeting()

    this.checkGameOver()
    this.updateGameState()
  }

  checkGameOver () {
    if (this.player.power > 0 && this.opponent.power > 0) return

    this._vue.queue(() => {
      window.game.completed = true
      window.game._vue.gameCompleted = true
      console.log('GAME OVER')
    }, 'end')
  }

  // Play a card from your hand
  playCard (key, data = {}) {
    if (! this.areCurrentPlayer ()) return

    let card = this.player.hand[key]
    if (card.cost > this.player.energy) return

    data.card = card
    this.event('play_dude', data)
  }

  // Send data to the server to update the game state
  updateGameState () {
    if (! this.areCurrentPlayer()) return

    window.setTimeout(() => {
      let gameState = {}
      gameState['completed'] = this.completed
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
