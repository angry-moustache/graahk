export class Job {
  constructor (callback) {
    this.callback = callback
  }

  resolve () {
    this.callback()
  }
}
