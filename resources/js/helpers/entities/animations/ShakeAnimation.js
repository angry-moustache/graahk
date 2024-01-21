import { Animation } from "./Animation"

export class ShakeAnimation extends Animation {
  constructor (...args) {
    super('dude_shake', ...args)

    this.duration = 100
    this.grace = 400
  }

  async resolve () {
    this.addClass(this.data.target.$el(), 'animate-shake')
    super.resolve()
  }
}
