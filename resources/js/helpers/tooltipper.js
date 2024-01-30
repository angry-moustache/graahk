export class Tooltipper {
  constructor () {
    this.timer = null
    this.card = null
    this.position = { x: 0, y: 0 }
    this.$el = document.getElementById('tooltipper')

    document.addEventListener('mousemove', (e) => {
      this.setPosition(e)
    })

    window.addEventListener('DOMMouseScroll', () => {
      this.reset()
      console.log('ugh')
    }, false)
  }

  set (card, data, e) {
    if (card !== this.card) {
      this.reset()
      this.card = card
    }

    if (data === 'pending') return

    window.clearTimeout(this.timer)
    this.timer = window.setTimeout(() => {
      this.$el.innerHTML = data
      this.setPosition(e)
    }, 500)
  }

  reset () {
    this.$el.innerHTML = null
    window.clearTimeout(this.timer)
  }

  setPosition (e) {
    this.position.x = e.clientX
    this.position.y = e.clientY

    // Change position depending on mouse position
    if (this.position.x > window.innerWidth / 2) {
      this.$el.style.left = `${this.position.x - 20 - this.$el.offsetWidth}px`
    } else {
      this.$el.style.left = `${this.position.x + 20}px`
    }

    // Make sure we don't fall off the screen
    if (this.position.y + this.$el.offsetHeight > window.innerHeight) {
      this.$el.style.top = `${window.innerHeight - this.$el.offsetHeight}px`
    } else {
      this.$el.style.top = `${this.position.y + 20}px`
    }
  }
}
