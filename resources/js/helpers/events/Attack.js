import { Job } from "../entities/Job"
import { AsyncJob } from "../entities/AsyncJob"
import { AttackAnimation } from "../entities/animations/AttackAnimation"

export class Attack {
  resolve (game, event) {
    let attacker = [game.player, game.opponent, ...game.player.board, ...game.opponent.board].find((c) => c.uuid === event.data.attacker)
    let defender = [game.player, game.opponent, ...game.player.board, ...game.opponent.board].find((c) => c.uuid === event.data.defender)

    game._vue.queue([
      new Job(() => {
        game.checkTriggers('attack', [attacker])
      }),
      new Job(() => {
        game.checkTriggers('dealing_damage', [attacker])
      }),
      new AsyncJob(() => {
        console.log(3)
        let a, d
        [d, a] = [defender.power, attacker.power]

        new AttackAnimation({ attacker: attacker, defender: defender }).resolve(() => {
          console.log(3.5)
          defender.deal_damage({ amount: a })
          if (defender.constructor.name !== 'Player') {
            attacker.deal_damage({ amount: d })
          }

          attacker.ready = false
        })
      }),
      new Job(() => {
        console.log(4)
        game.checkTriggers('after_damage', [attacker])
      }),
    ])
  }
}
