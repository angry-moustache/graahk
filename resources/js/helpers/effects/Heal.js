export class Heal {
  resolve (_vue, data, target) {
    _vue.queue(() => {
      console.log(target)
      if (target.power > target.originalPower) {
        return
      }

      target.power = Math.min(
        target.power + parseInt(data.amount),
        target.originalPower
      )
    })
  }
}
