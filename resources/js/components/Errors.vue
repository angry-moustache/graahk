<template>
  <div
    class="w-full h-screen fixed inset-0 pointer-events-none"
    style="z-index: 10000000000"
  >
    <TransitionGroup
      class="flex flex-col gap-4 text-center mt-[5rem] text-4xl font-bold text-red-500 text-border"
      name="errors"
      tag="ul"
    >
      <li
        v-for="message in messages"
        v-bind:key="message.key"
        v-text="message.message"
      ></li>
    </TransitionGroup>
  </div>
</template>

<script>
export default {
  name: 'errors',
  data () {
    return {
      messages: [],
    }
  },
  mounted () {
    window.errorToast = this.addMessage
  },
  methods: {
    addMessage(message) {
      this.messages.push({
        message: message,
        key: Date.now() + Math.random(),
      })

      window.setTimeout(() => this.messages.shift(), 3000)
    },
  }
}
</script>

<style scoped>
.errors-enter-active,
.errors-leave-active {
  transition: all 0.5s ease;
}

.errors-enter-from {
  opacity: 0;
  transform: translateX(-30px);
}

.errors-leave-to {
  opacity: 0;
  transform: translateX(30px);
}
</style>
