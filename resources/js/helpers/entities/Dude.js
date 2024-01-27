import { ActivatedAnimation } from "./animations/ActivatedAnimation"
import { HealAnimation } from "./animations/HealAnimation"
import { ShakeAnimation } from "./animations/ShakeAnimation"
import { reactive } from "vue"
import { Debuff } from "./Debuff"
import { ReadyAnimation } from "./animations/ReadyAnimation"

export class Dude {
  constructor (card) {
    Object.entries(card).forEach(([key, value]) => {
      this[key] = value
    })

    this.dead = this.dead || false
    this.debuffs = this.debuffs || []
  }

  $el () {
    return document.getElementById('dude-' + this.uuid)
  }

  coords () {
    return {
      x: this.$el().offsetLeft + (this.$el().offsetWidth / 2),
      y: this.$el().offsetTop + (this.$el().offsetHeight / 2),
    }
  }

  async reset () {
    this.power = this.originalPower
    this.ready = false
    this.dead = false
    this.debuffs = []
  }

  async reset_health () {
    this.power = this.originalPower

    await new ActivatedAnimation({ target: this }).resolve(null, () => {
      window.nextJob()
    })
  }

  async silence () {
    this.effects = []
    this.keywords = []

    this.debuffs.push(new Debuff({
      type: 'silenced',
      visual: 'silenced',
    }))

    await new ActivatedAnimation({ target: this }).resolve(null, () => {
      window.nextJob()
    })
  }

  async deal_damage (data, source) {
    const amount = window.game.getAmount(data, source)
    this.power -= amount

    if (source && data.animation !== 'projectile') {
      new ActivatedAnimation({ target: source }).resolve()
    }

    if (this.power <= 0) {
      this.dead = ! this.keywords.includes('tireless')
      this.power = 0
    }

    if (! this.dead) {
      game.checkTriggers('survive_damage', [this])
    }

    game.checkTriggers('took_damage', [this])

    await new ShakeAnimation({ target: this, intensity: amount }).resolve(() => {
      window.nextJob()
    })
  }

  async heal (data, source) {
    if (this.power < this.originalPower && ! this.dead) {
      this.power = Math.min(
        this.power + window.game.getAmount(data, source),
        this.originalPower
      )
    }

    await new HealAnimation({ target: this }).resolve(null, () => {
      window.nextJob()
    })
  }

  async kill () {
    this.dead = true
    this.power = 0

    await new ShakeAnimation({ target: this }).resolve(() => {
      window.nextJob()
    })
  }

  async duplicate () {
    let player = window.game.playerById(this.owner)

    let clone = reactive(new Dude(this))
    clone.uuid = window.uuid(player.uuid + player.board.length + player.graveyard.length)
    clone.highlighted = false
    player.board.push(clone)

    // TODO: animation
    await new ShakeAnimation({ target: this }).resolve(() => {
      window.nextJob()
    })
  }

  async ready_dudes () {
    this.ready = ! this.debuffs.some((debuff) => debuff.type === 'stun')
    this.willClearStuns = this.debuffs.some((debuff) => debuff.type === 'stun')

    if (this.ready) {
      await new ReadyAnimation({ target: this }).resolve(null, () => {
        window.nextJob()
      })
    } else {
      window.nextJob()
    }
  }

  async stun (data) {
    this.debuffs.push(new Debuff({
      type: 'stun',
      visual: data.stun_type || 'web',
    }))

    this.ready = false

    await new ActivatedAnimation({ target: this }).resolve(null, () => {
      window.nextJob()
    })
  }

  async buff_dude (data, source) {
    if (! this.dead) {
      this.power += window.game.getAmount(data, source)

      await new ActivatedAnimation({ target: this }).resolve(() => {}, () => {
        window.nextJob()
      })
    } else {
      await timeout(1000)
      window.nextJob()
    }
  }

  bounce () {
    if (this.dead) return

    let player = window.game.playerById(this.owner)

    player.board.splice(player.board.map((c) => c.uuid).indexOf(this.uuid), 1)

    this.reset()

    // Turn it into a normal object
    this.highlighted = false
    player.hand.push(JSON.parse(JSON.stringify(this)))

    window.nextJob()
  }

  async shuffle_into_deck () {
    let player = window.game.playerById(this.owner)

    await new ShakeAnimation({ target: this }).resolve(() => {
      this.reset()
      player.deck.push(player.board.splice(player.board.map((c) => c.uuid).indexOf(this.uuid), 1)[0])
      player.shuffle()
      window.nextJob()
    })
  }

  async shuffle_into_opponents_deck () {
    let player = window.game.playerById(this.owner)
    let opponent = window.game.playerById(this.opponent)

    await new ShakeAnimation({ target: this }).resolve(() => {
      this.reset()
      opponent.deck.push(player.board.splice(player.board.map((c) => c.uuid).indexOf(this.uuid), 1)[0])
      opponent.shuffle()
      window.nextJob()
    })
  }

  async unnamed_one () {
    let player = window.game.playerById(this.owner)
    this.power = parseInt(player.graveyard.length * 50)

    window.nextJob()
  }
}
