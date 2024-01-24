import { reactive } from 'vue'
import { Dude } from './Dude'
import { GainEnergyAnimation } from './animations/GainEnergyAnimation'
import { ShakeAnimation } from './animations/ShakeAnimation'
import { HealAnimation } from './animations/HealAnimation'
import { ActivatedAnimation } from './animations/ActivatedAnimation'

export class Player {
  constructor (player) {
    this.id = player.id
    this.uuid = player.uuid
    this.owner = player.id
    this.power = player.power
    this.originalPower = player.originalPower
    this.energy = player.energy
    this.hand = reactive(player.hand)
    this.deck = reactive(player.deck)
    this.graveyard = reactive(player.graveyard)
    this.board = reactive(player.board.map((card) => reactive(new Dude(card))))
  }

  $ref () {
    return window.game._vue.$refs['player-' + this.uuid]
  }

  $el () {
    return this.$ref().$el.querySelector('img')
  }

  cleanup (game) {
    let deaths = this.board
      .filter((card) => card.power <= 0)
      .filter((card) => card.dead)

    if (deaths.length === 0) return

    console.log('dead', deaths)
    deaths.forEach((card) => {
      card.dead = true

      // VueJS handles the animation
      this.graveyard.push(card)
      this.board.splice(this.board.map((c) => c.uuid).indexOf(card.uuid), 1)

      if (card.type === 'dude') {
        game.checkTriggers('dude_dies', [...game.player.board, ...game.opponent.board])
        game.checkTriggers('player_dude_dies', game.board)
        game.checkTriggers('opponent_dude_dies', game.opponent.board)
      }
    })

    game.checkTriggers('leave_field', deaths)
  }

  async gain_energy (data) {
    await new GainEnergyAnimation({ target: this.$ref().$refs.energy }).resolve(
      () => this.energy += parseInt(data.amount),
      () => {
        window.game.checkTriggers('gain_energy', this.board)
        window.nextJob()
      }
    )
  }

  async spawn (data, type = 'token') {
    let card
    for (let index = 0; index < data.amount; index++) {
      card = await axios.get(`/api/cards/${data[type]}`)
      card.data.owner = this.id
      card.data.uuid = window.uuid(this.uuid + this.board.length + this.graveyard.length)
      this.board.push(reactive(new Dude(card.data)))
    }
  }

  async spawn_token (data) {
    await this.spawn(data, 'token')
    window.nextJob()
  }

  async spawn_dude (data) {
    await this.spawn(data, 'dude')
    window.nextJob()
  }

  async draw_cards (data) {
    for (let index = 0; index < data.amount; index++) {
      this.hand.push(this.deck.pop())
      await timeout(100)
    }

    game.checkTriggers('draw_card', this.board)

    window.nextJob()
  }

  async draw_specific_tribe (data, source) {
    let uuids = this.deck
      .filter((card) => card.tribes.includes(data.tribe))
      .map((card) => card.uuid)

    await this.drawFromSpecificDeck(data, uuids, source)

    window.nextJob()
  }

  async draw_specific_dude (data, source) {
    let uuids = this.deck
      .filter((card) => card.id == data.dude)
      .map((card) => card.uuid)

    await this.drawFromSpecificDeck(data, uuids, source)

    window.nextJob()
  }

  async draw_specific_cost (data, source) {
    let uuids = this.deck
      .filter((card) => {
        switch (data.operator) {
          case 'greater than equal': return card.cost >= parseInt(data.cost); break
          case 'less than equal': return card.cost <= parseInt(data.cost); break
          case 'equal to': return card.cost === parseInt(data.cost); break
        }
      })
      .map((card) => card.uuid)

    await this.drawFromSpecificDeck(data, uuids, source)

    window.nextJob()
  }

  async deal_damage (data, source) {
    this.power -= data.amount

    if (source) {
      new ActivatedAnimation({ target: source }).resolve()
    }

    await new ShakeAnimation({ target: this }).resolve(() => {
      window.nextJob()
    })
  }

  async heal (data, source) {
    if (this.power < this.originalPower) {
      this.power = Math.min(
        this.power + parseInt(data.amount),
        this.originalPower
      )
    }

    if (source && this.uuid !== source.uuid) {
      new ActivatedAnimation({ target: source }).resolve()
    }

    await new HealAnimation({ target: this }).resolve(null, () => {
      window.nextJob()
    })
  }

  async drawFromSpecificDeck (data, uuids, source) {
    let key
    for (let index = 0; index < data.amount; index++) {
      key = this.deck.indexOf(this.deck.find((card) => card.uuid === uuids[index]))
      if (key === -1) {
        window.errorToast('No more cards to draw')
        new ShakeAnimation({ target: source }).resolve()
        await timeout(500)
      } else {
        this.hand.push(this.deck.splice(key, 1)[0])
        new ActivatedAnimation({ target: source }).resolve()
        await timeout(1000)
      }
    }
  }
}
