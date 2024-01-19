export class PlayDude {
  resolve (_vue, event) {
    _vue.queue(() => {
      let data = event.data

      let player = _vue.playerFromId(data.player)
      let card = player.hand[data.key]

      card.owner = player.user.id
      card.uniqId = 'id' + Math.random().toString(16).slice(2)
      player.energy -= card.cost

      player.hand.splice(data.key, 1)
      player.board.push(card)

      _vue.queue(() => {
        if (_vue.areCurrentPlayer) {
          _vue.checkTriggers('play_dude', _vue.player)
          _vue.checkTriggers('player_play_dude', _vue.player)
          _vue.checkTriggers('opponent_play_dude', _vue.opponent)
        } else {
          _vue.checkTriggers('play_dude', _vue.opponent)
          _vue.checkTriggers('player_play_dude', _vue.opponent)
          _vue.checkTriggers('opponent_play_dude', _vue.player)
        }
      })

      // Check for enter_field event
      card.effects.filter((effect) => effect.trigger === 'enter_field').forEach((trigger) => {
        _vue.queue(() => {
          // _vue.effect(trigger.effect, {
          //   player: player.user.id,
          //   cardKey: player.board.length - 1
          // })
        })
      })
    })
  }
}
