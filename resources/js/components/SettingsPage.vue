<template>
    <div>
        <maintenance-section></maintenance-section>

        <auto-pilot-tuning-section></auto-pilot-tuning-section>
    </div>
</template>

<script>
import MaintenanceSection from "./Settings/MaintenanceSection";
import AutoPilotTuningSection from "./Settings/AutoPilotTuningSection";

export default {
    name: "SettingsPage",

    components: {
        'maintenance-section': MaintenanceSection,
        'auto-pilot-tuning-section': AutoPilotTuningSection
    },
}
</script>

<style scoped>

</style>
