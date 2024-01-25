import { Dude } from "../entities/Dude"
import { reactive } from "vue"
import { CthulhulhulhuAnimation } from "../entities/animations/CthulhulhulhuAnimation"
import { GainEnergyAnimation } from "../entities/animations/GainEnergyAnimation"

export class PlayDude {
  resolve (game, event) {
    let card = reactive(new Dude(event.data.card))

    game._vue.queue([
      (async () => {
        card.owner = game.currentPlayer.id
        card.opponent = game.currentOpponent.id

        card.ready = card.keywords.includes('rush')

        game.currentPlayer.board.push(card)
        game.currentPlayer.hand = game.currentPlayer.hand.filter((c) => c.uuid !== card.uuid)
        game.currentPlayer.energy -= card.cost

        await timeout(card.enterSpeed || 0)

        if (card.name === 'Cthulhulhulhu') {
          // Cool, but should move it to a separate thingy TODO
          await new CthulhulhulhuAnimation({ target: card }).resolve(async () => {
            window.nextJob()
          })
        } else {
          window.nextJob()
        }
      }),
      (() => {
        game.checkTriggers('enter_field', [card], game.getTargets('from_uuid', null, event.data.target))
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
