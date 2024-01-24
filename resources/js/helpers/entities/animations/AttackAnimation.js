import { Animation } from "./Animation"

export class AttackAnimation extends Animation {
  constructor (...args) {
    super('attack', ...args)

    this.duration = 200
    this.grace = 500
  }

  async resolve (callback) {
    super.resolve()

    const attacker = this.data.attacker.$el()
    const defender = this.data.defender.$el()

    attacker.style.zIndex = 100
    attacker.style.translate = `0px 0px`
    attacker.style.transition = `all ${this.duration}ms linear`

    this.addClass(attacker, 'animate-attacking')

    await timeout(100 /* Dude getting up to attack */).then(async () => {
      // Make sure we end up in the middle of the card, even if we attack from above
      const offsetLeftCenter = (defender.offsetWidth - attacker.offsetWidth) / 2

      // Move the attacker on top of the defender
      if (attacker.offsetTop > defender.offsetTop) {
        attacker.style.translate = `
          ${defender.offsetLeft - attacker.offsetLeft + offsetLeftCenter}px
          ${defender.offsetTop - attacker.offsetTop + defender.offsetHeight / 2}px
        `
      } else {
        attacker.style.translate = `
          ${defender.offsetLeft - attacker.offsetLeft + offsetLeftCenter}px
          ${defender.offsetTop - attacker.offsetTop - attacker.offsetHeight / 2}px
        `
      }

      // Return to its position
      await timeout(this.duration).then(() => { /* Dude Returning */
        callback(this) // Deal damage

        if (! attacker) return

        attacker.style.transition = `all ${this.grace}ms linear`
        attacker.style.translate = `0px 0px`
      })
    })
  }
}
