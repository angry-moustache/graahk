import axios from "axios"

export class DrawCard {
  async resolve (_vue, data) {
    let target = data.target === 'player' ? _vue.currentPlayer : _vue.currentOpponent

    let card = target.deck.pop()
    card = await axios.get(`/api/cards/${card}`)

    target.hand.push(card.data)

    _vue.checkTriggers('draw_cards')
  }
}
