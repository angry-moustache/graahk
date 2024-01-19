export class GainEnergy {
  resolve (_vue, data, target) {
    _vue.queue(() => {
      target = _vue.resolveTarget(target)
      target.energy += parseInt(data.amount)

      _vue.checkTriggers('gain_energy', _vue.player)
      _vue.checkTriggers('gain_energy', _vue.opponent)
    })
  }
}
