import { Animation } from "./Animation"

export class GainEnergyAnimation extends Animation {
  constructor (...args) {
    super('gain_energy', ...args)

    this.grace = 900
    this.duration = 100
  }

  async resolve (callback, finallyCallback) {
    const div = this.data.target
    const width = this.data.width || 200

    this._meta = {
      x: (div.offsetLeft + div.offsetWidth / 2) - (width / 2),
      y: (div.offsetTop + div.offsetHeight / 2) - (width / 2),
      width: width
    }

    super.resolve()

    await window.timeout(this.duration)
    if (callback) callback()

    await window.timeout(this.grace)
    if (finallyCallback) finallyCallback()
  }
}
