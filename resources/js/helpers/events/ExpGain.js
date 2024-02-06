export class ExpGain {
  resolve (game, data) {
    game.afterGameUpgrades = data.data

    game._vue.$refs.game_over.dataReceived()

    game.updateGameState()
  }
}
