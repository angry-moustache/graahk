import './fit-text';

window.resizeCards()
window.onresize = (() => window.resizeCards())

Livewire.hook('morph.updated', () => {
  window.resizeCards()
})
