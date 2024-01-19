export class PlayDude {
  trigger (_vue, trigger) {
    _vue.queue(() => {
      let data = trigger.data
      let player = data.player.user.id

      player = _vue.gameState.player.user.id === player ? _vue.player : _vue.opponent

      let card = player.hand[data.key]

      player.energy -= card.cost

      player.hand.splice(data.key, 1)
      player.board.push(card)

      _vue.queue(() => {
        _vue.checkTriggers('opponent_play_dude')
        _vue.checkTriggers('play_dude')
      })

      // Check for enter_field trigger
      card.effects.filter((effect) => effect.trigger === 'enter_field').forEach((trigger) => {
        _vue.queue(() => {
          _vue.effect(trigger.effect, {
            player: player.user.id,
            cardKey: player.board.length - 1
          })
        })
      })
    })
  }
}
