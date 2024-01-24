import { Animation } from "./Animation"

export class TurnStartAnimation extends Animation {
  constructor (...args) {
    super('turn_start', ...args)

    this.duration = 700
    this.grace = 700
  }

  async resolve (callback) {
    // Add the new turn div to the body
    const div = document.createElement('div')
    div.classList.add('new-turn-animation')
    div.innerHTML = '<div class="whee"></div> <h1>Your turn to play!</h1>'
    document.body.appendChild(div)

    window.setTimeout(() => div.remove(), this.fullDuration())

    await window.timeout(this.duration)
    callback()
  }
}
