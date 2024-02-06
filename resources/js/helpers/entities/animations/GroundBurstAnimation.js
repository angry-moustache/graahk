import { Animation } from "./Animation"
import { CircleExplosionAnimation } from "./CircleExplosionAnimation"

export class GroundBurstAnimation extends Animation {
  constructor (...args) {
    super('ground-burst', ...args)

    this.grace = 1500
    this.duration = 500
  }

  async resolve (callback = null, finallyCallback = null) {
    this.addClass(this.data.source.$el(), 'animate-ground-burst')
    super.resolve()

    await window.timeout(this.duration)
    if (callback) callback()

    new CircleExplosionAnimation({
      source: this.data.source,
      data: { color: 'gray' },
    }).resolve()

    await window.timeout(this.grace)
    if (finallyCallback) finallyCallback()
  }
}
