export class Dude {
  constructor (card) {
    this.id = card.id
    this.owner = card.owner
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
  }

  async reset () {
    let uuid = this.uuid
    let card = await axios.get(`/api/cards/${this.id}`)
    card.data.uuid = uuid

    Object.assign(this, new Dude(card.data))
  }

  reset_health () {
    this.power = this.originalPower
  }

  deal_damage (data) {
    this.power -= data.amount

    if (this.power > 0) {
      window.game.checkTriggers('after_damage', [this])
    }
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

  buff_dude (data) {
    this.power += parseInt(data.amount)
  }

  bounce () {
    let player = window.game.playerById(this.owner)

    player.board.splice(player.board.map((c) => c.uuid).indexOf(this.uuid), 1)

    this.reset()
    player.hand.push(this)
  }
}
