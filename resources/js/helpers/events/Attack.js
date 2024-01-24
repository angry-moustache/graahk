import { Player } from "../entities/Player"
import { AttackAnimation } from "../entities/animations/AttackAnimation"

export class Attack {
  resolve (game, event) {
    let attacker = [game.player, game.opponent, ...game.player.board, ...game.opponent.board].find((c) => c.uuid === event.data.attacker)
    let defender = [game.player, game.opponent, ...game.player.board, ...game.opponent.board].find((c) => c.uuid === event.data.defender)

    game._vue.queue([
      (() => {
        game.checkTriggers('attack', [attacker])
        window.nextJob()
      }),
      (() => {
        let a, d
        [d, a] = [defender.power, attacker.power]
        attacker.ready = false

        new AttackAnimation({ attacker: attacker, defender: defender }).resolve(async (animation) => {
          defender.deal_damage({ amount: a })
          if (! (defender instanceof Player)) {
            attacker.deal_damage({ amount: d })
          }

          if (defender.dead) {
            game.checkTriggers('killing_blow', [attacker])
          }

          await timeout(animation.grace + 100).then(() => {
            window.nextJob()
          })
        })
      }),
      (() => {
        game.checkTriggers('after_attack', [attacker])
        window.nextJob()
      }),
    ])
  }
}
