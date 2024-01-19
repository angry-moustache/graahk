import axios from "axios"

export class DrawCard {
  async resolve (_vue, data, target) {
    _vue.queue(() => {
      target = _vue.resolveTarget(target)

      let card = target.deck.pop()
      axios.get(`/api/cards/${card}`).then((card) => {
        for (let index = 0; index < data.amount; index++) {
          target.hand.push(card.data)
        }

        _vue.checkTriggers('draw_cards', _vue.player)
        _vue.checkTriggers('draw_cards', _vue.opponent)
      })
    })
  }
}
