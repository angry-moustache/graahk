export class Job {
  constructor (callback) {
    this.callback = callback
    this.uuid = Math.random().toString(36).substring(2)
    this.finished = true
  }

  async resolve () {
      await this.callback()

      window.game._vue.runner.shift()
      window.game._vue.startRunner()
  }

  await () {
    this.finished = false

    return this
  }

  finish () {
    this.finished = true

    return this
  }
}
