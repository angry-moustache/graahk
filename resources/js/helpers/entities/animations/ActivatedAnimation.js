import { Animation } from "./Animation"

export class ActivatedAnimation extends Animation {
  constructor (...args) {
    super('dude_activated', ...args)

    this.grace = 900
    this.duration = 100
  }

  async resolve (callback = null, finallyCallback = null) {
    this.addClass(this.data.target.$el(), 'animate-activated')
    super.resolve()

    await window.timeout(this.duration)

    if (callback) callback()

    await window.timeout(this.grace)

    if (finallyCallback) finallyCallback()
  }
}
