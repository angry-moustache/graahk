export class SwapTurn {
  trigger (_vue) {
    _vue.gameState.current_player = _vue.gameState.current_player === _vue.player.user.id
        ? _vue.opponent.user.id
        : _vue.player.user.id

    // Check endTurn triggers

    _vue.currentPlayer.energy += 1

    // Draw card

    // Check startTurn triggers
  }
}
