<template>
    <section class="card mb-3">
        <div class="card-header text-bg-primary d-flex align-items-center">
            <div class="flex-fill">
                <h2 class="card-title">
                    {{ $gettext('Scheduled Time #%{num}', {num: index + 1}) }}
                </h2>
            </div>
            <div class="flex-shrink-0">
                <button
                    type="button"
                    class="btn btn-sm btn-dark"
                    @click="doRemove()"
                >
                    <icon :icon="IconRemove" />
                    <span>
                        {{ $gettext('Remove') }}
                    </span>
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="p-2 mb-2 bg-secondary text-white text-center">
                ⚠️ This is a modified scheduler to fit CLVBS needs ⚠️
            </div>
            <div class="row g-3">
                <form-group-field
                    :id="'edit_form_start_time_'+index"
                    class="col-md-4"
                    :field="v$.start_time"
                    :label="$gettext('Start Time')"
                >
                    <template #default="{id, model, fieldClass}">
                        <playlist-time
                            :id="id"
                            v-model="model.$model"
                            :class="fieldClass"
                        />
                    </template>
                </form-group-field>

                <form-group-field
                    :id="'edit_form_end_time_'+index"
                    class="col-md-4"
                    :field="v$.end_time"
                    :label="$gettext('End Time')"
                >
                    <template #default="{id, model, fieldClass}">
                        <playlist-time
                            :id="id"
                            v-model="model.$model"
                            :class="fieldClass"
                        />
                    </template>
                </form-group-field>

                <form-markup
                    id="station_time_zone"
                    class="col-md-4"
                    :label="$gettext('Station Time Zone')"
                >
                    <time-zone />
                </form-markup>

                <form-group-field
                    :id="'edit_form_start_date_'+index"
                    class="col-md-4"
                    :field="v$.start_date"
                    input-type="date"
                    :label="$gettext('Start Date')"
                />

                <form-group-field
                    :id="'edit_form_end_date_'+index"
                    class="col-md-4"
                    :field="v$.end_date"
                    input-type="date"
                    :label="$gettext('End Date')"
                />

                <form-group-checkbox
                    :id="'edit_form_loop_once_'+index"
                    class="col-md-4"
                    :field="v$.loop_once"
                    :label="$gettext('Loop Once')"
                    :description="$gettext('Only loop through playlist once.')"
                />

                <form-group-multi-check
                    :id="'edit_form_days_'+index"
                    class="col-md-4"
                    :field="v$.days"
                    :label="$gettext('Scheduled Play Days of Week')"
                    :description="$gettext('Leave blank to play on every day of the week.')"
                    :options="dayOptions"
                    stacked
                />
            </div>
        </div>
    </section>
</template>

<script setup lang="ts">
import PlaylistTime from "~/components/Common/TimeCode.vue";
import Icon from "~/components/Common/Icon.vue";
import FormGroupField from "~/components/Form/FormGroupField.vue";
import {required} from "@vuelidate/validators";
import useVuelidate from "@vuelidate/core";
import {toRef} from "vue";
import {useTranslate} from "~/vendor/gettext";
import FormGroupCheckbox from "~/components/Form/FormGroupCheckbox.vue";
import FormMarkup from "~/components/Form/FormMarkup.vue";
import FormGroupMultiCheck from "~/components/Form/FormGroupMultiCheck.vue";
import TimeZone from "~/components/Stations/Common/TimeZone.vue";
import {IconRemove} from "~/components/Common/icons";

interface PlaylistScheduleRow {
    start_time: number,
    end_time: number,
    start_date: string,
    end_date: string,
    days: number[],
    loop_once: boolean,
}

const props = defineProps<{
    index: number,
    row: PlaylistScheduleRow
}>();

const emit = defineEmits<{
    (e: 'remove'): void
}>();

const v$ = useVuelidate(
    {
        'start_time': {required},
        'end_time': {required},
        'start_date': {},
        'end_date': {},
        'days': {},
        'loop_once': {}
    },
    toRef(props, 'row')
);

const {$gettext} = useTranslate();

const dayOptions = [
    {value: 1, text: $gettext('Monday')},
    {value: 2, text: $gettext('Tuesday')},
    {value: 3, text: $gettext('Wednesday')},
    {value: 4, text: $gettext('Thursday')},
    {value: 5, text: $gettext('Friday')},
    {value: 6, text: $gettext('Saturday')},
    {value: 7, text: $gettext('Sunday')}
];

const doRemove = () => {
    emit('remove');
};
</script>
