export class GainEnergy {
  resolve (_vue, data) {
    let target = data.target === 'player' ? _vue.currentPlayer : _vue.currentOpponent
    target.energy += parseInt(data.amount)

    _vue.checkTriggers('gain_energy')
  }
}
