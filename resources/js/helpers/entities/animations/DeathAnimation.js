import { Animation } from "./Animation"

export class DeathAnimation extends Animation {
  constructor (...args) {
    super('death', ...args)

    this.duration = 500
    this.grace = 100
  }

  resolve (callback) {
    super.resolve()

    this.addClass(this.data.target.$ref().$el, 'animate-death')
    window.setTimeout(() => callback(), this.duration)
  }
}
