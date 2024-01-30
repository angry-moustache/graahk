<template>
  <div class="h-0" ref="aimer">
    <!-- <Card v-if="aimer" :card="aimer"  /> -->

    <canvas
      id="canvas-targeting"
      class="w-full h-screen fixed inset-0 pointer-events-none"
      style="z-index: 1000000"
    />
  </div>
</template>

<script>
import { Artifact } from '../helpers/entities/Artifact'
import { Dude } from '../helpers/entities/Dude'
import { Player } from '../helpers/entities/Player'
import Card from './Card.vue'

export default {
  name: 'targeting',
  components: { Card },
  data () {
    return {
      aimer: false,
      victim: false,
      effect: null,
      validTargets: [],
      canvas: null,
    }
  },
  mounted () {
    this.canvas = document.getElementById('canvas-targeting')

    document.addEventListener('mousemove', (e) => {
      if (! this.aimer) return
      this.drawArrow(e)
    })

    document.addEventListener('contextmenu', (e) => {
      e.preventDefault()
      this.stopTargeting()
    }, false)
  },
  methods: {
    target (target) {
      if (! this.$parent.canDoAnything()) return

      if (! this.aimer) {
        this.setAimer(target)
      } else {
        this.setVictim(target)
      }
    },
    setAimer (target) {
      if (target instanceof Player || target.owner !== this.$parent.game.player.id) return

      if (target instanceof Dude && target.owner === this.$parent.game.player.id) {
        // We're starting an attack
        if (! target.ready) return
        if (target.keywords.includes('scenery')) return

        // Selecting a friendly dude
        target.highlighted = true
        this.aimer = target

        this.effect = (() => {
          this.$parent.game.event('attack', {
            attacker: this.aimer.uuid,
            defender: this.victim.uuid,
          })
        })

        let board = this.$parent.game.opponent.board
        const validTargetChecks = board.filter((card) => card.keywords.includes('protect'))

        // Add a special 'tireless' check
        if (validTargetChecks.filter((dude) => dude.power > 0).length > 0) {
          // We have a protector, so we can only attack the protector
          this.validTargets = validTargetChecks
        } else {
          // We don't have a protector, so we can attack anything
          this.validTargets = [
            this.$parent.game.opponent,
            ...this.$parent.game.opponent.board,
          ]
        }
      } else if (target instanceof Artifact && target.owner === this.$parent.game.player.id) {
        // We're activating an artifact ability
      } else {
        // Card in our hand
        this.aimer = target

        let effectTarget = this.aimer.effects
          .find((e) => ['dude', 'dude_player', 'dude_opponent'].includes(e.target))
          .target

        switch (effectTarget) {
          case 'dude':
            this.validTargets = [
              ...this.$parent.game.player.board,
              ...this.$parent.game.opponent.board,
            ]
            break
          case 'dude_player':
            this.validTargets = this.$parent.game.player.board
            break
          case 'dude_opponent':
            this.validTargets = this.$parent.game.opponent.board
            break
        }

        if (['dude', 'dude_player', 'dude_opponent'].includes(effectTarget)) {
          this.validTargets = this.validTargets.filter((c) => c instanceof Dude)
        }

        this.validTargets = this.validTargets.filter((c) => ! c.keywords.includes('ghostly'))

        this.effect = (() => {
          let cardKey = this.$parent.game.player.hand.indexOf(this.aimer)
          this.$parent.game.playCard(cardKey, { target: this.victim.uuid })

          this.resetCanvas()
        })
      }

      if (this.aimer) {
        document.body.style.cursor = 'none'
        const $aimer = ((this.aimer.$el) ? this.aimer.$el() : document.getElementById(`hand-${this.aimer.uuid}`))
          .getBoundingClientRect()

        this.drawArrow({
          clientX: $aimer.x + ($aimer.width / 2),
          clientY: $aimer.y + ($aimer.height / 2),
        })
      }
    },
    setVictim (target) {
      // Deselect if we click the same dude
      if (target.uuid === this.aimer.uuid) {
        return this.stopTargeting()
      }

      // Check the validness of the target
      if (! this.validTargets.map((t) => t.uuid).includes(target.uuid)) {
        return window.errorToast('Invalid target')
      }

      // Do the thing
      this.victim = target
      this.effect()

      document.body.style.cursor = 'auto'
    },
    areTargeting () {
      return this.aimer !== false
    },
    stopTargeting () {
      if (this.areTargeting()) {
        this.aimer.highlighted = false
        this.aimer = false
      }

      this.victim = false
      this.effect = null
      this.validTargets = []

      this.resetCanvas()
    },
    drawArrow (e) {
      try {
        let targetX, targetY
        if (this.victim) {
          const $victim = this.victim.$el()

          [targetX, targetY] = [
            $victim.offsetLeft + ($victim.offsetWidth / 2),
            $victim.offsetTop + ($victim.offsetHeight / 2),
          ]
        } else {
          [targetX, targetY] = [e.clientX, e.clientY]
        }

        const $aimer = ((this.aimer.$el) ? this.aimer.$el() : document.getElementById(`hand-${this.aimer.uuid}`))
          .getBoundingClientRect()

        let [originX, originY] = [
          $aimer.x + ($aimer.width / 2),
          $aimer.y + ($aimer.height / 2),
        ]

        // Draw an arrow
        this.canvas.width = window.innerWidth
        this.canvas.height = window.innerHeight
        let ctx = this.canvas.getContext('2d')

        // Rounded line
        ctx.lineCap = 'round'
        ctx.strokeStyle = 'red'
        ctx.lineWidth = 10

        // Arrow head
        let headLength = 50
        let angle = Math.atan2(targetY - originY, targetX - originX)
        ctx.beginPath()
        ctx.moveTo(targetX - headLength * Math.cos(angle - Math.PI / 6), targetY - headLength * Math.sin(angle - Math.PI / 6))
        ctx.lineTo(targetX, targetY)
        ctx.stroke()

        ctx.beginPath()
        ctx.moveTo(targetX - headLength * Math.cos(angle + Math.PI / 6), targetY - headLength * Math.sin(angle + Math.PI / 6))
        ctx.lineTo(targetX, targetY)
        ctx.stroke()

        // Line
        ctx.setLineDash([10, 30])

        ctx.beginPath()
        ctx.moveTo(originX, originY)
        ctx.lineTo(targetX, targetY)
        ctx.stroke()

        // Circle around starting point
        ctx.beginPath()
        ctx.arc(originX, originY, 40, 0, 2 * Math.PI)
        ctx.stroke()
      } catch (e) {
        return // Canvas not ready yet
      }
    },
    resetCanvas () {
      let ctx = this.canvas.getContext('2d')
      ctx.clearRect(0, 0, this.canvas.width, this.canvas.height)
      document.body.style.cursor = 'auto'
    }
  },
}
</script>
