export class Artifact {
  constructor (card) {
    Object.entries(card).forEach(([key, value]) => {
      this[key] = value
    })
  }

  $el () {
    return document.getElementById('artifact-' + this.uuid)
  }

  coords () {
    return {
      x: this.$el().offsetLeft + (this.$el().offsetWidth / 2),
      y: this.$el().offsetTop + (this.$el().offsetHeight / 2),
    }
  }

  async activate () {

  }

  async deal_damage () {
    this.power = Math.max(0, this.power - 1)
  }

  async gain_charge (data, source) {
    this.power += window.game.getAmount(data, source)

    await timeout(500)

    window.nextJob()
  }
}
