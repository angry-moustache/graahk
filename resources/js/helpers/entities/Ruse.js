export class Ruse {
  constructor (card) {
    Object.entries(card).forEach(([key, value]) => {
      this[key] = value
    })
  }

  $el () {
    return document.getElementById('ruse-' + this.uuid)
  }

  coords () {
    return {
      x: this.$el().offsetLeft + (this.$el().offsetWidth / 2),
      y: this.$el().offsetTop + (this.$el().offsetHeight / 2),
    }
  }
}
