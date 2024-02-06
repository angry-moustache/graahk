import { Ruse } from "../entities/Ruse"
import { reactive } from "vue"

export class PlayRuse {
  resolve (game, event) {
    let card = reactive(new Ruse(event.data.card))

    game._vue.queue([
      (async () => {
        card.owner = game.currentPlayer.id
        card.opponent = game.currentOpponent.id

        card.glowing = false

        game._vue.$refs.display.setCard(card)
        game.currentPlayer.hand = game.currentPlayer.hand.filter((c, key) => key !== event.data.key)
        // game.currentPlayer.hand = game.currentPlayer.hand.filter((c) => c.uuid !== card.uuid)
        game.currentPlayer.energy -= card.cost

        await timeout(card.enterSpeed || 500)

        window.nextJob()
      }),
      (() => {
        game.checkTriggers('cast_ruse', [card], game.getTargets('from_uuid', null, event.data.target))
        window.nextJob()
      }),
      (async () => {
        await timeout(1000)
        game._vue.$refs.display.setCard(null)
        game.currentPlayer.graveyard.push(card)
        window.nextJob()
      }),
    ])
  }
}
