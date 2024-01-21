import { Dude } from "../entities/Dude"

export class PlayDude {
  resolve (game, event) {
    let card = new Dude(event.data.card)

    game._vue.queue([
      (() => {
        card.owner = game.currentPlayer.id
        card.opponent = game.currentOpponent.id

        game.currentPlayer.board.push(card)
        game.currentPlayer.hand = game.currentPlayer.hand.filter((c) => c.uuid !== card.uuid)
        game.currentPlayer.energy -= card.cost

        window.nextJob()
      }),
      (() => {
        game.checkTriggers('enter_field', [card])
        window.nextJob()
      }),
      (() => {
        game.checkTriggers('play_dude', [...game.currentPlayer.board, ...game.opponent.board].filter((c) => c.uuid !== card.uuid))
        window.nextJob()
      }),
      (() => {
        game.checkTriggers('player_play_dude', game.currentPlayer.board.filter((c) => c.uuid !== card.uuid))
        window.nextJob()
      }),
      (() => {
        game.checkTriggers('opponent_play_dude', game.currentOpponent.board.filter((c) => c.uuid !== card.uuid))
        window.nextJob()
      }),
    ])
  }
}
