export class BuffDude {
  resolve (_vue, data, target) {
    _vue.queue(() => {
      target.power += parseInt(data.amount)
    })
  }
}
