import { Animation } from "./Animation"

export class AttackAnimation extends Animation {
  constructor (...args) {
    super('attack', ...args)

    this.duration = 200
    this.grace = 500
  }

  resolve (callback) {
    super.resolve()

    const attacker = this.data.attacker.$ref().$el
    const defender = this.data.defender.$ref().$el

    attacker.style.zIndex = 100
    attacker.style.translate = `0px 0px`
    attacker.style.transition = `all ${this.duration}ms linear`

    this.addClass(attacker, 'animate-attacking')

    window.setTimeout(() => {
      // Move the attacker on top of the defender
      // Make sure we end up in the middle of the card, even if we attack from above
      const offsetLeftCenter = (defender.offsetWidth - attacker.offsetWidth) / 2
      attacker.style.translate = `
        ${defender.offsetLeft - attacker.offsetLeft + offsetLeftCenter}px
        ${defender.offsetTop - attacker.offsetTop + defender.offsetHeight / 2}px
      `

      // Deal damage after it resolves
      window.setTimeout(() => callback(), this.duration)

      // Return to its position
      window.setTimeout(() => {
        if (! attacker) return

        attacker.style.transition = `all ${this.grace}ms linear`
        attacker.style.translate = `0px 0px`

        window.setTimeout(() => this.finish(), this.grace /* Attack has completed */)
      }, this.duration /* Dude attacking */)
    }, 100 /* Dude getting up to attack */)
  }
}
