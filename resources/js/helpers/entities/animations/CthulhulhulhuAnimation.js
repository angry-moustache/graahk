import { Animation } from "./Animation"
import { ExplosionAnimation } from "./ExplosionAnimation"

export class CthulhulhulhuAnimation extends Animation {
  constructor (...args) {
    super('cthulhulhulhu', ...args)

    this.grace = 3000
    this.duration = 1000
  }

  async resolve (callback = null, finallyCallback = null) {
    this.addClass(this.data.source.$el(), 'animate-cthulhulhulhu')
    super.resolve()

    new ExplosionAnimation({
      target: this.data.source.$el(),
      width: 500,
      image: 'explosion/red',
    }).resolve()

    await window.timeout(this.duration)

    if (callback) callback()

    await window.timeout(this.grace)

    if (finallyCallback) finallyCallback()
  }
}
