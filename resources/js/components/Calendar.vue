<template>
  <div>
    <FullCalendar :options="calendarOptions" />
  </div>
</template>

<script setup>
import { ref } from 'vue';
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'
import listPlugin from '@fullcalendar/list'
import multiMonthPlugin from '@fullcalendar/multimonth'
import interactionPlugin from '@fullcalendar/interaction'
import rrulePlugin from '@fullcalendar/rrule'
import csLocale from '@fullcalendar/core/locales/cs'

const calendarOptions = ref({
  plugins: [dayGridPlugin, timeGridPlugin, listPlugin, multiMonthPlugin, interactionPlugin, rrulePlugin],
  locales: [csLocale],
  locale: 'cs',
  initialView: 'dayGridMonth',
  weekends: true,
  events: '/api/calendar-events',
  headerToolbar: {
    left: 'prev,next today',
    center: 'title',
    right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek,multiMonthYear'
  },
  buttonText: {
    today: 'Dnes',
    month: 'Měsíc',
    week: 'Týden',
    day: 'Den',
    list: 'Seznam',
    multiMonthYear: 'Rok'
  },
  allDayText: 'Celý den',
  noEventsText: 'Žádné události k zobrazení',
  moreLinkText: 'více',
  weekText: 'Týd.',
  weekTextLong: 'Týden',
});
</script>
