<template>
  <div
    class="absolute pointer-events-none aspect-[2.5/3.5] w-1/6"
    style="z-index: 1000"
    v-if="card"
    v-bind:class="{
      'top-4 left-4': ! mouseLeftSideScreen,
      'top-4 right-4': mouseLeftSideScreen,
      'top-4 right-4': true,
    }"
  >
    <Card :card="card" :full-sized="true" />
  </div>
</template>

<script>
import Card from './Card.vue'

export default {
  name: 'tooltip',
  components: { Card },
  data () {
    return {
      card: null,
      mouseLeftSideScreen: true,
      memory: {},
      timeout: null,
    }
  },
  mounted () {
    window.addEventListener('mousemove', (e) => {
      this.mouseLeftSideScreen = e.clientX < window.innerWidth / 2
    })
  },
  methods : {
    show (card) {
      if (card === null) {
        this.card = null
        clearTimeout(this.timeout)
        return
      }

      // Only show after half a second
      this.timeout = setTimeout(() => {
        if (this.memory[card.id + '-' + card.level]) {
          this.card = this.memory[card.id + '-' + card.level]
          return
        }

        axios.get(`/api/cards/${card.id}`).then((response) => {
          this.card = response.data
          this.card.level = card.level
          this.memory[card.id + '-' + card.level] = this.card
        })
      }, 500)
    },
  }
}
</script>
