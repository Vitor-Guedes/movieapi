<template>
    <h2 class="text-sm font-medium text-indigo-500 mt-4 mb-2">
        <slot name="head"></slot>
    </h2>

    <div class="flex flex-row mb-4 gap-2 overflow-x-auto scrollbar pb-2">
        <span class="text-nowrap bg-indigo-100 text-indigo-800 text-xs font-medium rounded-full p-1 px-4" v-for="item in source">
            <slot name="body" :item="item"></slot>
        </span>
    </div>
</template>

<script>
export default {
    name: 'BadgeList',

    props: ['source']
}
</script>