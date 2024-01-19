import { Job } from "../entities/Job"

export class SwapTurn {
  resolve (game) {
    game._vue.queue([
      new Job(() => game.checkTriggers('end_turn', game.currentPlayer.board)),
      new Job(() => [game.currentPlayer, game.currentOpponent] = [game.currentOpponent, game.currentPlayer]),
      new Job(() => game.checkTriggers('start_turn', game.currentPlayer.board)),
      new Job(() => game.effect('gain_energy', { amount: 3 }, [game.currentPlayer])),
      new Job(() => game.effect('draw_cards', { amount: 1 }, [game.currentPlayer])),
      new Job(() => game.effect('ready_dudes', {}, game.currentPlayer.board)),
    ], 'end')
  }
}
