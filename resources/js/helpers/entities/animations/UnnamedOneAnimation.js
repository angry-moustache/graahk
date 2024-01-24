import { Animation } from "./Animation"

export class UnnamedOneAnimation extends Animation {
  constructor (...args) {
    super('unnamed_one', ...args)

    this.grace = 1000
    this.duration = 1000
  }

  async resolve (callback = null, finallyCallback = null) {
    this.addClass(this.data.target.$el(), 'animate-unnamed_one')
    super.resolve()

    await window.timeout(this.duration)

    if (callback) callback()

    await window.timeout(this.grace)

    if (finallyCallback) finallyCallback()
  }
}
