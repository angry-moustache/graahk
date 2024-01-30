import { SwapTurn } from './events/SwapTurn'
import { PlayDude } from './events/PlayDude'
import { Mulliganed } from './events/Mulliganed'

import { Player } from './entities/Player'
import { Attack } from './events/Attack'
import { HandleAnimation } from './entities/animations/HandleAnimation'

import { reactive } from 'vue'
import { Surrender } from './events/Surrender'
import { ExpGain } from './events/ExpGain'

export class Game {
  constructor (_vue) {
    this._vue = _vue
    this.gameId = _vue.gameId
    this.pusher = window.pusher.subscribe(this.gameId)

    this.completed = _vue.gameState.completed || false
    this.afterGameUpgrades = _vue.gameState.afterGameUpgrades || []

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
        case 'surrender': (new Surrender).resolve(this, data); break
        case 'exp_gain': (new ExpGain).resolve(this, data); break
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

  getTargets (type, owner, target = null, data = []) {
    let player, opponent
    if (owner) {
      player = (owner.owner === this.currentPlayer.id ? this.currentPlayer : this.currentOpponent)
      opponent = (owner.owner === this.currentPlayer.id ? this.currentOpponent : this.currentPlayer)
    } else {
      [player, opponent] = [this.currentPlayer, this.currentOpponent]
    }

    type = type || 'player'

    let targets = []

    switch (type) {
      case 'player': targets = [player]; break
      case 'opponent': targets = [opponent]; break
      case 'dude': targets = [this._vue.getTarget()]; break
      case 'dude_player': targets = [this._vue.getTarget()]; break
      case 'dude_opponent': targets = [this._vue.getTarget()]; break
      case 'all_players': targets = [player, opponent]; break
      case 'all_player_dudes': targets = player.board; break
      case 'all_other_player_dudes': targets = player.board.filter((c) => c.uuid !== owner.uuid); break
      case 'all_opponent_dudes': targets = opponent.board; break
      case 'all_dudes': targets = [...player.board, ...opponent.board]; break
      case 'all_other_dudes': targets = [...player.board, ...opponent.board].filter((c) => c.uuid !== owner.uuid); break
      case 'everything': targets = [...player.board, ...opponent.board, player, opponent]; break
      case 'itself': targets = [owner]; break
      case 'from_uuid': targets = [...player.board, ...opponent.board, player, opponent].filter((c) => c.uuid === target); break
      case 'all_tribe': targets = [...player.board, ...opponent.board].filter((c) => c.tribes.includes(data.target_tribe)); break
      case 'all_tribe_but_self': targets = [...player.board, ...opponent.board].filter((c) => c.tribes.includes(data.target_tribe) && c.uuid !== owner.uuid); break
      case 'all_player_tribe': targets = player.board.filter((c) => c.tribes.includes(data.target_tribe)); break
      case 'all_player_tribe_but_self': targets = player.board.filter((c) => c.tribes.includes(data.target_tribe) && c.uuid !== owner.uuid); break
      case 'all_opponent_tribe': targets = opponent.board.filter((c) => c.tribes.includes(data.target_tribe)); break
      default: window.errorToast(`No target type ${type}`);
    }

    switch (type) {
      case 'artifact': targets = targets.filter((card) => card.type === 'artifact'); break
      default: targets = targets.filter((card) => card.type !== 'artifact')
    }

    return targets
  }

  getAmount (data, source) {
    if (data.amount !== 'X') {
      return parseInt(data.amount)
    }

    let player, opponent
    if (source) {
      player = (source.owner === this.currentPlayer.id ? this.currentPlayer : this.currentOpponent)
      opponent = (source.owner === this.currentPlayer.id ? this.currentOpponent : this.currentPlayer)
    } else {
      [player, opponent] = [this.currentPlayer, this.currentOpponent]
    }

    const multiplier = parseInt(data.amount_multiplier) || 1
    let amount = 0

    const playerCount = player.board.filter((c) => ! c.dead).length
    const opponentCount = opponent.board.filter((c) => ! c.dead).length

    switch (data.amount_special) {
      case 'for_each_dude_player': amount = playerCount * multiplier; break
      case 'for_each_dude_player_except_self': amount = (playerCount - 1) * multiplier; break
      case 'for_each_dude_opponent': amount = opponentCount * multiplier; break
      case 'for_each_dude': amount = (playerCount + opponentCount) * multiplier; break
      case 'for_each_energy_player': amount = player.energy * multiplier; break
      case 'for_each_energy_opponent': amount = opponent.energy * multiplier; break
      default: window.errorToast(`No amount special type ${type}`);
    }

    return amount
  }

  // Check for any triggers after an effect or event has fired
  // This only pushes to the queue, it doesn't actually run the effect yet
  async checkTriggers (trigger, checks = false, targetedTargets = false) {
    window.game._vue.queue(() => {
      let targets
      (checks || this.getTargets('all_dudes')).forEach((dude) => {
        dude.effects.filter((e) => e.trigger === trigger).reverse().forEach(async (effect) => {
          // Only use targeted targets if it uses them
          targets = null
          if (window.requiresTarget.includes(effect.target)) {
            targets = targetedTargets
          }

          this.effect(
            effect.effect,
            effect,
            targets || this.getTargets(effect.target, dude, null, effect),
            dude
          )
        })
      })

      window.nextJob()
    })
  }

  // Do something
  effect (effect, data, targets, source = null) {
    // Make sure to include the .nextJob() in the event itself
    window.game._vue.queue(async () => {
      if (targets.length === 0) {
        return window.nextJob()
      }

      await new HandleAnimation(targets, effect, data, source).resolve(() => {
        targets.forEach(async (target) => {
          if (target[effect] === undefined) {
            // return window.errorToast(`No effect ${effect} on ${target.uuid}`)
            return window.nextJob()
          }

          await target[effect](data, source)
        })
      })
    })
  }

  cleanup () {
    let playerList = this.currentPlayer.causalityList()
    playerList.forEach((death) => {
      if (death.type === 'dude') {
        this.checkTriggers('dude_dies', [...this.currentPlayer.board, ...this.currentOpponent.board])
        this.checkTriggers('player_dude_dies', this.currentPlayer.board)
        this.checkTriggers('opponent_dude_dies', this.currentOpponent.board)
      }

      this.checkTriggers('leave_field', [death])
    })

    let opponentList = this.currentOpponent.causalityList()
    opponentList.forEach((death) => {
      if (death.type === 'dude') {
        this.checkTriggers('dude_dies', [...this.currentPlayer.board, ...this.currentOpponent.board])
        this.checkTriggers('player_dude_dies', this.currentOpponent.board)
        this.checkTriggers('opponent_dude_dies', this.currentPlayer.board)
      }

      this.checkTriggers('leave_field', [death])
    })

    this._vue.$refs.targeting.stopTargeting()
    this.updateGameState()

    if (playerList.length <= 0 && opponentList.length <= 0) {
      this.checkGameOver()

      return
    }

    this._vue.queue(async () => {
      await timeout(200)

      this.player.cleanupDead()
      this.opponent.cleanupDead()

      this.checkGameOver()

      window.nextJob()
    })
  }

  checkGameOver () {
    if (this.player.power > 0 && this.opponent.power > 0) return

    this._vue.queue(() => {
      window.game.completed = true
      window.game._vue.gameCompleted = true
      this.updateGameState()
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

  areCurrentPlayer () {
    return this.currentPlayer.id === this._vue.playerId
  }

  playerById (id) {
    return (id === this.player.id ? this.player : this.opponent)
  }

  // Send data to the server to update the game state
  updateGameState () {
    window.setTimeout(() => {
      let gameState = {}
      gameState['completed'] = this.completed
      gameState['afterGameUpgrades'] = this.afterGameUpgrades
      gameState['current_player'] = this.currentPlayer.id
      gameState[`player_${this.player.id}`] = this.player
      gameState[`player_${this.opponent.id}`] = this.opponent

      window.axios.put(`/api/games/${this._vue.gameId}`, {
        gameState: gameState,
      })
    }, 100)
  }
}
