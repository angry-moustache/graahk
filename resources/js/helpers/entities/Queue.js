export class Queue {
  constructor () {
    this.queue = []
    this.isProcessing = false
    this.amount = 0
  }

  push (events, pos) {
    if (! Array.isArray(events)) events = [events]

    if (pos === 'start') {
      this.queue = [...events, ...this.queue]
    } else {
      this.queue = [...this.queue, ...events]
    }
  }

  addChecks (amount) {
    this.amount += amount
  }

  count () {
    return this.queue.length
  }

  checkQueueEmpty () {
    return ! this.isProcessing
  }

  processQueue () {
    if (this.queue.length === 0 && this.amount === 0) {
      this.isProcessing = false

      window.cleanupTimer = window.setTimeout(() => {
        window.game.cleanup()
      }, 100)

      return
    } else {
      this.amount = 0
    }

    this.isProcessing = true

    // Used to call the next job in the queue
    window.nextJob = (() => {
      window.resizeCards()
      this.amount = Math.max(0, this.amount - 1)

      if (window.game.completed) return

      this.processQueue()
    })

    const current = this.queue.shift()
    current() // Call the current callback
  }
}
