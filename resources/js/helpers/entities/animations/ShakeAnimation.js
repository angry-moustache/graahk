import { Animation } from "./Animation"

export class ShakeAnimation extends Animation {
  constructor (...args) {
    super('dude_shake', ...args)

    this.duration = 100
    this.grace = 400
  }

  async resolve (finallyCallback) {
    const intensity = Math.min(this.data.intensity || 100, 1000)

    this.addClass(this.data.target.$el(), `animate-shake-${intensity}`)
    super.resolve()

    await window.timeout(this.duration)
    await window.timeout(this.grace)

    if (finallyCallback) finallyCallback()
  }
}
