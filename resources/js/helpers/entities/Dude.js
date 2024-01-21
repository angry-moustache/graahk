import { ActivatedAnimation } from "./animations/ActivatedAnimation"
import { ShakeAnimation } from "./animations/ShakeAnimation"

export class Dude {
  constructor (card) {
    this.id = card.id
    this.owner = card.owner
    this.opponent = card.opponent
    this.cost = card.cost
    this.effects = card.effects
    this.type = card.type
    this.image = card.image
    this.name = card.name
    this.originalCost = card.originalCost
    this.originalPower = card.originalPower
    this.power = card.power
    this.ready = card.ready
    this.text = card.text
    this.keywords = card.keywords
    this.tribes = card.tribes
    this.uuid = card.uuid
    this.dead = card.dead || false
    this.visualEffects = card.visualEffects || []
  }

  $el () {
    return document.getElementById('dude-' + this.uuid)
  }

  async reset () {
    let card = await axios.get(`/api/cards/${this.id}`)
    Object.assign(this, new Dude(card.data))
  }

  reset_health () {
    this.power = this.originalPower
  }

  async silence () {
    this.effects = []
    this.keywords = []

    this.visualEffects.push('silenced')

    await new ActivatedAnimation({ target: this }).resolve()
  }

  deal_damage (data) {
    this.power -= data.amount
    new ShakeAnimation({ target: this }).resolve()
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

  kill () {
    this.power = 0
  }

  ready_dudes () {
    this.ready = true
  }

  async buff_dude (data) {
    this.power += parseInt(data.amount)

    await new ActivatedAnimation({ target: this }).resolve()
  }

  bounce () {
    if (this.power <= 0) return

    let player = window.game.playerById(this.owner)

    player.board.splice(player.board.map((c) => c.uuid).indexOf(this.uuid), 1)

    this.reset()
    player.hand.push(this)
  }
}
