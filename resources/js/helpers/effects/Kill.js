export class Kill {
  resolve (_vue, data) {
    _vue.queue(() => this[data.target](_vue, data))
  }

  itself (_vue, data) {
    let player = _vue.playerFromId(data._meta.player)
    player.graveyard.push(player.board[data.key])
    player.board.splice(data.key, 1)
  }
}
