export class SwapTurn {
  resolve (game) {
    game._vue.queue([
      (() => {
        game.checkTriggers('end_turn', game.currentPlayer.board)
        window.nextJob()
      }),
      (() => {
        console.log('Swapping turn');
        [game.currentPlayer, game.currentOpponent] = [game.currentOpponent, game.currentPlayer]
        window.nextJob()
      }),
      (() => {
        game.checkTriggers('start_turn', game.currentPlayer.board)
        window.nextJob()
      }),
      (() => {
        game.effect('gain_energy', { amount: 3 }, [game.currentPlayer])
        window.nextJob()
      }),
      (() => {
        game.effect('draw_cards', { amount: 1 }, [game.currentPlayer])
        window.nextJob()
      }),
      (() => {
        game.effect('ready_dudes', {}, game.currentPlayer.board)
        window.nextJob()
      }),
    ], 'end')
  }
}
