<template>
  <div
    :id="'dude-' + dude.uuid"
    class="relative flex justify-center origin-center"
  >
    <div
      v-bind:style="{ backgroundImage: `url('${dude.image}')` }"
      class="
        w-[10rem] aspect-[2.5/3.5]
        border-[2px] bg-cover bg-center rounded-xl overflow-hidden
      "
      v-bind:class="{
        'border-white': dude.ready,
        'border-black': ! dude.ready,
        '!border-green-500': dude.highlighted,
      }"
    ></div>
      <span class="absolute opacity-50" v-text="dude.uuid" />

      <div
        class="
            absolute -bottom-[2rem] pb-1 pt-2 px-6 text-5xl font-bold bg-surface
            border-[2px] border-black rounded-2xl
        "
        v-bind:class="{
          'border-white': dude.ready,
          'border-black': ! dude.ready,
          '!border-green-500': dude.highlighted,
        }"
      >
        <span
          v-text="dude.power"
          v-bind:class="{
            'text-green-500': dude.originalPower < dude.power,
            'text-red-500': dude.originalPower > dude.power,
          }"
        />
      </div>
  </div>
</template>

<script>
export default {
  name: 'dude',
  props: {
    dude: Object,
  },
};
</script>
