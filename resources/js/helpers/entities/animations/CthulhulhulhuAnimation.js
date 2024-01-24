import { Animation } from "./Animation"
import { GainEnergyAnimation } from "./GainEnergyAnimation"

export class CthulhulhulhuAnimation extends Animation {
  constructor (...args) {
    super('cthulhulhulhu', ...args)

    this.grace = 3000
    this.duration = 1000
  }

  async resolve (callback = null, finallyCallback = null) {
    this.addClass(this.data.target.$el(), 'animate-cthulhulhulhu')
    super.resolve()

    new GainEnergyAnimation({ target: this.data.target.$el(), width: 500 }).resolve()

    await window.timeout(this.duration)

    if (callback) callback()

    await window.timeout(this.grace)

    if (finallyCallback) finallyCallback()
  }
}
