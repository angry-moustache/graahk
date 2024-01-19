import { reactive } from 'vue'
import { Dude } from './Dude'

export class Player {
  constructor (player) {
    this.id = player.id
    this.uuid = player.uuid
    this.owner = player.id
    this.power = player.power
    this.energy = player.energy
    this.hand = reactive(player.hand)
    this.deck = reactive(player.deck)
    this.graveyard = reactive(player.graveyard)
    this.board = reactive(player.board.map((card) => reactive(new Dude(card))))
  }

  cleanup (game) {
    let deaths = this.board
      .filter((card) => card.power <= 0)
      .filter((card) => ! card.dead)

    if (deaths.length === 0) return

    deaths.forEach((card) => {
      card.dead = true

      this.graveyard.push(card)
      this.board.splice(this.board.map((c) => c.uuid).indexOf(card.uuid), 1)
    })

    game.checkTriggers('leave_field', deaths)
  }

  gain_energy (data) {
    this.energy += parseInt(data.amount)
    game.checkTriggers('gain_energy', this.board)
  }

  async spawn_token (data) {
    let token = await axios.get(`/api/cards/${data.token}`)
    token.data.owner = this.id
    this.board.push(new Dude(token.data))
  }

  async draw_cards (data) {
    let card = this.deck.pop()
    card = await axios.get(`/api/cards/${card}`)

    for (let index = 0; index < data.amount; index++) {
      this.hand.push(card.data)
    }

    game.checkTriggers('draw_card', this.board)
  }

  deal_damage (data) {
    this.power -= data.amount
  }

  ready_dudes () {
    this.board.forEach((card) => card.ready = true)
  }
}
