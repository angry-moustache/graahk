import { reactive } from 'vue'
import { Dude } from './Dude'
import { GainEnergyAnimation } from './animations/GainEnergyAnimation'
import { ShakeAnimation } from './animations/ShakeAnimation'

export class Player {
  constructor (player) {
    this.id = player.id
    this.uuid = player.uuid
    this.owner = player.id
    this.power = player.power
    this.originalPower = player.power
    this.energy = player.energy
    this.hand = reactive(player.hand)
    this.deck = reactive(player.deck)
    this.graveyard = reactive(player.graveyard)
    this.board = reactive(player.board.map((card) => reactive(new Dude(card))))
  }

  $ref () {
    return window.game._vue.$refs['player-' + this.uuid]
  }

  $el () {
    return this.$ref().$el
  }

  cleanup (game) {
    let deaths = this.board
      .filter((card) => card.power <= 0)
      .filter((card) => ! card.dead)

    if (deaths.length === 0) return

    deaths.forEach((card) => {
      card.dead = true

      // VueJS handles the animation
      this.graveyard.push(card)
      this.board.splice(this.board.map((c) => c.uuid).indexOf(card.uuid), 1)
    })

    game.checkTriggers('leave_field', deaths)
  }

  gain_energy (data) {
    window.game._vue.queue(() => {
      new GainEnergyAnimation({ target: this }).resolve(
        () => this.energy += parseInt(data.amount),
        () => {
          console.log('GAINED ENERGY')
          window.nextJob()
        }
      )
    })
  }

  async spawn_token (data) {
    let token
    for (let index = 0; index < data.amount; index++) {
      token = await axios.get(`/api/cards/${data.token}`)
      token.data.owner = this.id
      this.board.push(new Dude(token.data))
    }
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

    new ShakeAnimation({ target: this }).resolve()
  }

  ready_dudes () {
    this.board.forEach((card) => card.ready = true)
  }

  heal (data) {
    if (this.power > this.originalPower) {
      return
    }

    this.power = Math.min(
      this.power + parseInt(data.amount),
      this.originalPower
    )
  }
}
