import { Animation } from "./Animation"

export class CircleExplosionAnimation extends Animation {
  constructor (...args) {
    super('circle-explosion', ...args)

    this.grace = 900
    this.duration = 100
  }

  async resolve (callback = null, finallyCallback = null) {
    const div = this.data.source.$el()

    this._meta = {
      class: 'animate-circle-explosion ' + (this.data.data.color || 'purple'),
      x: div.offsetLeft + (div.offsetWidth / 2),
      y: div.offsetTop + (div.offsetHeight / 2),
    }

    super.resolve()

    await window.timeout(this.duration)
    if (callback) callback()

    await window.timeout(this.grace)
    if (finallyCallback) finallyCallback()
  }
}
