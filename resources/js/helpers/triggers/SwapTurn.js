export class SwapTurn {
  trigger (_vue) {
    _vue.queue([
      () => {
        _vue.gameState.current_player = _vue.gameState.current_player === _vue.player.user.id
            ? _vue.opponent.user.id
            : _vue.player.user.id

        _vue.checkTriggers('end_turn')
      },
      () => _vue.effect('gain_energy', { amount: 3, target: 'player' }),
      () => _vue.effect('draw_cards', { amount: 1, target: 'player' }),
      () => _vue.checkTriggers('start_turn'),
      () => {
        _vue.currentPlayer.board.forEach((card) => {
          card.ready = true
        })
      },
    ])
  }
}
