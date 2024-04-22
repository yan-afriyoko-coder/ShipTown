<template>
    <div class="dropdown">
        <button class="btn btn-sm dropdown-toggle text-primary font-weight-bold p-0" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            {{ itemSelected }}
        </button>
        <div class="dropdown-menu" :class="alignMenuRight ? 'dropdown-menu-right' : ''">
            <a class="dropdown-item" v-for="item in items" :key="item" @click.prevent="$emit('update:item-selected', item)">
                {{ item }}
            </a>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        items: {
            type: Array,
            required: true
        },
        itemSelected: {
            type: String,
            required: true
        },
        alignMenuRight: {
            type: Boolean,
            default: false
        }
    }
}
</script>

<style scoped>
    a.dropdown-item {
        cursor: pointer;
    }
</style>
