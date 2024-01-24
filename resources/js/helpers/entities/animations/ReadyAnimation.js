import { Animation } from "./Animation"

export class ReadyAnimation extends Animation {
  constructor (...args) {
    super('healed', ...args)

    this.grace = 200
    this.duration = 200
  }

  async resolve (callback = null, finallyCallback = null) {
    this.addClass(this.data.target.$el(), 'animate-ready')

    super.resolve()

    await window.timeout(this.duration)
    if (callback) callback()

    await window.timeout(this.grace)
    if (finallyCallback) finallyCallback()
  }
}
