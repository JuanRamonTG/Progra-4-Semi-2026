import { ref, onMounted, onUnmounted } from 'vue';

export function useDatetime() {
    const currentDate = ref('');
    const currentTime = ref('');
    let interval = null;

    const updateDatetime = () => {
        const now = new Date();
        const months = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
            'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        currentDate.value = `${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear()}`;

        let hours = now.getHours();
        const minutes = now.getMinutes().toString().padStart(2, '0');
        const ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12 || 12;
        currentTime.value = `${hours.toString().padStart(2, '0')}:${minutes} ${ampm}`;
    };

    onMounted(() => {
        updateDatetime();
        interval = setInterval(updateDatetime, 30000); // Update every 30 seconds
    });

    onUnmounted(() => {
        if (interval) clearInterval(interval);
    });

    return { currentDate, currentTime };
}
