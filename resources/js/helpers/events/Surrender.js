export class Surrender {
  resolve (game, data) {
    let loser = (data.data.loser === game.player.uuid ? game.player : game.opponent)
    loser.power = 0

    game.checkGameOver()
  }
}
