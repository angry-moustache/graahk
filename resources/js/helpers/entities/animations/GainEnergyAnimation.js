import { Animation } from "./Animation"

export class GainEnergyAnimation extends Animation {
  constructor (...args) {
    super('gain_energy', ...args)

    this.grace = 900
    this.duration = 100
  }

  resolve () {
    const div = this.data.target.$ref().$refs.energy
    const width = 200

    this._meta = {
      x: (div.offsetLeft + div.offsetWidth / 2) - (width / 2),
      y: (div.offsetTop + div.offsetHeight / 2) - (width / 2),
      width: width
    }

    super.resolve()
  }
}
