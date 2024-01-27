export class Artifact {
  constructor (card) {
    Object.entries(card).forEach(([key, value]) => {
      this[key] = value
    })
  }

  $el () {
    return document.getElementById('artifact-' + this.uuid)
  }

  async activate () {

  }
}
