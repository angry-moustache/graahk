import './fit-text';

window.resizeCards()
window.onresize = (() => window.resizeCards())

Livewire.hook('request', ({ succeed }) => {
    succeed(({ snapshot, effect }) => {
      window.resizeCards()
    })
})
