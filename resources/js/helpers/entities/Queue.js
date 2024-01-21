export class Queue {
  constructor () {
    this.queue = []
    this.isProcessing = false
  }

  push (events, pos) {
    if (! Array.isArray(events)) events = [events]

    // console.log(`Pushing ${events.length} events to the queue`)

    if (pos === 'start') {
      this.queue = [...events, ...this.queue]
    } else {
      this.queue = [...this.queue, ...events]
    }
  }

  count () {
    return this.queue.length
  }

  processQueue () {
    if (this.queue.length === 0) {
      this.isProcessing = false
      window.game.cleanup()

      return
    }

    console.log(`Jobs left: ${this.queue.length}`)

    this.isProcessing = true
    window.nextJob = (() => {
      console.log('--- NEXT JOB ---')
      this.processQueue()
    }) // Used to call the next job in the queue

    const current = this.queue.shift()
    // console.log(current)
    current() // Call the current callback
  }
}
