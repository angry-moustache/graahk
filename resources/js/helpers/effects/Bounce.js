export class Bounce {
  resolve (_vue, data, target) {
    _vue.queue(() => {
      let owner = _vue.playerFromId(target.owner)

      owner.board.splice(target.index, 1)
      owner.hand.push(target)
    })
  }
}
