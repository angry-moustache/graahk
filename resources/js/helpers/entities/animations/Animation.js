export class Animation {
  constructor (animation, data = {}, duration, grace = 1) {
    this.animation = animation
    this.duration = duration
    this.grace = grace
    this.data = data
    this.uuid = Math.floor(Math.random() * 100) + '' + Date.now()
  }

  resolve () {
    game._vue.currentJob.animation = this
    window.game._vue.animations.push(this)
  }

  addClass ($el, className) {
    $el.classList.add(className)
    window.setTimeout(
      () => $el.classList.remove(className),
      this.duration + this.grace + 100
    )
  }

  // Let the queue know we're done
  finish () {
    window.game._vue.currentJob.finish()
  }
}
