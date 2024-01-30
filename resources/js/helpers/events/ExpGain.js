export class ExpGain {
  resolve (game, data) {
    game.afterGameUpgrades = data.data
    game.updateGameState()
  }
}
