export class Bounce {
  resolve (_vue, data) {
    _vue.queue(() => this[data.target](_vue, data))
  }

  itself (_vue, data) {
    let target = _vue.playerFromId(data._meta.player)
    let dude = target.board[data._meta.cardKey]

    target.board.splice(data._meta.cardKey, 1)
    target.hand.push(dude)
  }
}
