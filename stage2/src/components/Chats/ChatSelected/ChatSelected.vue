<template>
    <div class="ChatSelected">
        <pre v-text="$attrs"/>
        <div>
            {{ $route.params.chatId }}
        </div>
    </div>
</template>

<script>
    export default {
        props: {
        },
    };
</script>
