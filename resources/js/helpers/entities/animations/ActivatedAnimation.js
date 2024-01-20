import { Animation } from "./Animation"

export class ActivatedAnimation extends Animation {
  constructor (...args) {
    super('dude_activated', ...args)

    this.grace = 900
    this.duration = 100
  }

  resolve () {
    this.addClass(this.data.target.$ref().$el, 'animate-activated')
    super.resolve()
  }
}
