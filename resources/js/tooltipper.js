import { Tooltipper } from './helpers/tooltipper'

(() => {
  let cache = {}
  window.Tooltipper = new Tooltipper()

  document.addEventListener('mousemove', (e) => {
    if (
      ! e.target.parentNode.classList.contains('has-tooltip')
      && ! e.target.parentNode.parentNode?.classList.contains('has-tooltip')
    ) {
      window.Tooltipper.reset()
      return
    }

    const card = e.target.parentNode.dataset.cardId
      || e.target.parentNode.parentNode.dataset.cardId

    if (! card) return

    if (cache[card]) {
      window.Tooltipper.set(card, cache[card], e)
    } else {
      cache[card] = 'pending'

      window.axios.get(`/api/cards/tooltip/${card}`).then((response) => {
        cache[card] = response.data
        window.Tooltipper.set(card, response.data, e)
      })
    }
  })
})()
