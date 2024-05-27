<template>
    <div>
        <h1>Liste des Sentiers</h1>
        <ul>
            <li v-for="sentier in sentiers" :key="sentier.id">
                {{ sentier.nom }} - {{ sentier.description }}
            </li>
        </ul>
    </div>
</template>

<script>
export default {
    data() {
        return {
            sentiers: [],
        };
    },
    mounted() {
        this.fetchSentiers();
    },
    methods: {
        fetchSentiers() {
            fetch('/sentiers')
                .then(response => response.json())
                .then(data => {
                    this.sentiers = data;
                });
        },
    },
};
</script>