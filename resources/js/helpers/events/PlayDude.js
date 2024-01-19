import { Dude } from "../entities/Dude"
import { Job } from "../entities/Job"

export class PlayDude {
  resolve (game, event) {
    let card = new Dude(event.data.card)

    game._vue.queue([
      new Job(() => {
        card.owner = game.currentPlayer.id

        game.currentPlayer.board.push(card)
        game.currentPlayer.hand = game.currentPlayer.hand.filter((c) => c.uuid !== card.uuid)
        game.currentPlayer.energy -= card.cost
      }),
      new Job(() => {
        // Check for any enter_field triggers
        card.effects.filter((e) => e.trigger === 'enter_field').forEach((effect) => {
          let targets = game.getTargets(effect.target, card)
          game.effect(effect.effect, effect, targets)
        })
      }),
      new Job(() => {
        // Check other dudes for x_play_dude triggers
        game.checkTriggers('play_dude', [...game.currentPlayer.board, ...game.opponent.board].filter((c) => c.uuid !== card.uuid))
        game.checkTriggers('player_play_dude', game.currentPlayer.board.filter((c) => c.uuid !== card.uuid))
        game.checkTriggers('opponent_play_dude', game.currentOpponent.board.filter((c) => c.uuid !== card.uuid))
      }),
    ])
  }
}
