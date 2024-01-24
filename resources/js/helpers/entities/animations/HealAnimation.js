import { Animation } from "./Animation"

export class HealAnimation extends Animation {
  constructor (...args) {
    super('healed', ...args)

    this.grace = 500
    this.duration = 500
  }

  async resolve (callback = null, finallyCallback = null) {
    this.addClass(this.data.target.$el(), 'animate-healed')

    super.resolve()

    await window.timeout(this.duration)
    if (callback) callback()

    await window.timeout(this.grace)
    if (finallyCallback) finallyCallback()
  }
}
