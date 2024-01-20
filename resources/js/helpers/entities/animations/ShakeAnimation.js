import { Animation } from "./Animation"

export class ShakeAnimation extends Animation {
  constructor (...args) {
    super('dude_shake', ...args)

    this.duration = 100
    this.grace = 400
  }

  resolve (callback = null) {
    this.addClass(this.data.target.$ref().$el, 'animate-shake')
    super.resolve()

    if (callback) callback()
  }
}
