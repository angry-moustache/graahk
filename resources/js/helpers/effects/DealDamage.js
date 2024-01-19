export class DealDamage {
  resolve (_vue, data, target) {
    _vue.queue(() => {
      target.power -= parseInt(data.amount)
    })
  }
}
