import { TurnStartAnimation } from "../entities/animations/TurnStartAnimation"

export class Mulliganed {
  async resolve (game, event) {
    game.playerById(event.data.player).mulliganed = event.data.amount

    if (game.haveMulliganed()) {
      const data = {
        text: (game.areCurrentPlayer() ? 'You go first!' : 'Opponent goes first!')
      }

      await new TurnStartAnimation().resolve(() => {
        game.currentPlayer.gain_energy({ amount: 2 })
      }, data)

      game.updateGameState()
    }
  }
}
