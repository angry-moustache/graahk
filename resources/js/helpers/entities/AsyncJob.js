import { Job } from "./Job"

export class AsyncJob extends Job {
  constructor (...args) {
    super(...args)

    this.finished = false
  }
}
