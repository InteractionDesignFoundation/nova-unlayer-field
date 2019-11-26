<template>
    <panel-item :field="field">
        <template slot="value">
            <div class="overflow-hidden">
                <code>{{ field.value }}</code>
            </div>
        </template>
    </panel-item>
</template>

<script>
    export default {
        props: ['resource', 'resourceName', 'resourceId', 'field'],
    }
</script>
