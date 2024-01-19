import { Job } from "../entities/Job"

export class Attack {
  resolve (game, event) {
    let attacker = [game.player, game.opponent, ...game.player.board, ...game.opponent.board].find((c) => c.uuid === event.data.attacker)
    let defender = [game.player, game.opponent, ...game.player.board, ...game.opponent.board].find((c) => c.uuid === event.data.defender)

    game._vue.queue([
      new Job(() => game.checkTriggers('attack', [attacker])),
      new Job(() => game.checkTriggers('dealing_damage', [attacker])),
      new Job(() => {
        let a, d
        [d, a] = [defender.power, attacker.power]

        defender.deal_damage({ amount: a })
        if (defender.constructor.name !== 'Player') {
          attacker.deal_damage({ amount: d })
        }

        attacker.ready = false
      }),
      new Job(() => game.checkTriggers('after_damage', [attacker])),
    ])
  }
}
