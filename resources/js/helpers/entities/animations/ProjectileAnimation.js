import { ActivatedAnimation } from "./ActivatedAnimation"
import { Animation } from "./Animation"

export class ProjectileAnimation extends Animation {
  constructor (...args) {
    super('projectile', ...args)

    this.grace = 400
    this.duration = 600
  }

  async resolve (callback = null) {
    const from = this.data.source.coords()
    const width = this.data.data.size || 200

    let to, animation, div
    let projectiles = []

    new ActivatedAnimation({ target: this.data.source }).resolve()

    for (let index = 0; index < this.data.target.length; index++) {
      const target = this.data.target[index]
      to = target.coords()

      animation = JSON.parse(JSON.stringify(this))

      animation.uuid = `${animation.uuid}-${index}`
      animation._meta = {
        projectile: animation.data.data.type,
        rotate: this.data.data.thrown ? 0 : Math.atan2(to.y - from.y, to.x - from.x) * 180 / Math.PI,
        x: from.x - (width / 2),
        y: from.y - (width / 2),
        width: width,
        class: this.data.data.thrown ? 'animate-projectile-thrown' : '',
        to: {
          x: to.x,
          y: to.y,
        }
      }

      projectiles[index] = animation
    }

    for (let index = 0; index < projectiles.length; index++) {
      const projectile = projectiles[index]

      window.game._vue.animations.push(projectile)
      window.setTimeout(() => {
        div = document.getElementById(`projectile-${projectile.uuid}`)

        div.style.top = projectile._meta.to.y - (width / 2) + 'px'
        div.style.left = projectile._meta.to.x - (width / 2) + 'px'
      }, 50)
    }

    await window.timeout(this.duration)
    if (callback) callback()
  }
}
