import { SwapTurn } from './triggers/SwapTurn'

export default {
  _vue: null,
  gameId: null,
  pusher: null,
  init (_vue) {
    this._vue = _vue
    this.gameId = _vue.gameId
    this.pusher = window.pusher.subscribe(this.gameId)

    window.resizeCards()

    this.pusher.bind('trigger', (data) => {
      switch (data.trigger) {
        case 'end_turn':
          (new SwapTurn).trigger(this._vue, data)
          break
      }

      this.updateGameState(this._vue.gameState)
    })
  },
  trigger (trigger) {
    window.axios.post(`/api/games/${this.gameId}/trigger`, { trigger: trigger })
  },
  updateGameState (gameState) {
    if (! this._vue.areCurrentPlayer) return

    gameState[`player_${this._vue.player.user.id}`] = this._vue.player
    gameState[`player_${this._vue.opponent.user.id}`] = this._vue.opponent

    window.axios.put(`/api/games/${this.gameId}`, {
      gameState: gameState,
    })
  },
}
