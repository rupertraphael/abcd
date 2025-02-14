<template>
    <base-dialog :visible="active" @close="close" @open="open">
        <div slot="title">
            <base-icon class="tw-align-middle">person_add</base-icon> Update program status of {{ record.display_value }}
        </div>
        <form>
            <div v-if="'status' in requestData" class="tw-mb-2">
                <div class="tw-flex tw-items-center tw-w-full">
                    <label class="tw-w-1/5">
                        Status
                    </label>
                    <div class="tw-w-2/3">
                        <base-select
                            v-model="requestData.status.id"
                            :value="requestData.status.id"
                            name="status"
                            placeholder="Select Program Status"
                            @change="request.errors.clear('status');requestData.notes = ''">
                            <el-option
                                v-for="status in statuses"
                                :key="status.id"
                                :label="status.name"
                                :value="status.id">
                                {{ status.name }}
                            </el-option>
                        </base-select>
                    </div>
                </div>
                <div v-if="request.errors.has('status')" class="tw-flex tw-justify-end">
                    <div class="tw-w-4/5 tw-py-2">
                        <span v-text="request.errors.get('status')[0]" class="tw-text-xs tw-text-red"></span>
                    </div>
                </div>
            </div>
            <div class="tw-mb-2">
                <div  class="tw-flex tw-items-center tw-w-full">
                    <label class="tw-w-1/5 tw-capitalize">
                        Enrollment Date
                    </label>
                    <div class="tw-w-2/3">
                        <base-input
                            v-model="requestData.enrolled_at"
                            name="enrolled_at"
                            type="date"
                            @keydown.native="request.errors.clear($event.target.name)"/>
                    </div>
                </div>
                <div v-if="request.errors.has('enrolled_at')" class="tw-flex tw-justify-end">
                    <div class="tw-w-4/5 tw-py-2">
                        <span v-text="request.errors.get('enrolled_at')[0]" class="tw-text-xs tw-text-red"></span>
                    </div>
                </div>
            </div>
            <div v-if="'notes' in requestData">
                <div class="tw-flex tw-items-center tw-w-full">
                    <label class="tw-w-1/5 tw-capitalize">
                        Notes
                    </label>
                    <div class="tw-w-2/3">
                        <base-input
                            v-model="requestData.notes"
                            name="notes"
                            type="textarea"
                            @keydown="request.errors.clear($event.target.name)"/>
                    </div>
                </div>
                <div v-if="request.errors.has('status')" class="tw-flex tw-justify-end">
                    <div class="tw-w-4/5 tw-py-2">
                        <span v-text="request.errors.get('status')[0]" class="tw-text-xs tw-text-red"></span>
                    </div>
                </div>
            </div>
        </form>
        <div slot="footer" class="tw-border-t tw-px-4 tw-py-4 tw-bg-gray-100 tw-rounded-b">
            <base-button class="tw-py-2 tw-pl-4 tw-bg-transparent tw-pr-4 tw-text-gray-700 tw-font-bold tw-border-none hover:tw-bg-transparent hover:tw-text-blue" @click="close()">
                <span class="tw-text-xs tw-align-middle">Nevermind</span>
            </base-button>
            <base-button class="tw-py-2 tw-pl-4 tw-pr-4 tw-bg-blue-500 tw-text-white tw-font-bold tw-border-none" @click="update">
                <span class="tw-text-xs tw-align-middle">OK</span>
            </base-button>
        </div>
    </base-dialog>
</template>
<script>
    import Request from '@/api/ProgramRecordsRequest';
    import RecordRequest from '@/api/RecordRequest';
    import StatusRequest from '@/api/ClientStatusRequest';

    import PrimaryData from '@/App/components/record/primaryData';

    // This is a modal for editing program record pivot data.
    // If a program client is edited in this modal, program client status field
    // and status notes are shown in this modal and submitted to the backend.
    // Otherwise, only the enrolled_at field is shown and submitted.

    export default {
        props: {
            active: Boolean,
            recordId: Number|String,
            programId: Number|String,
            recordType: String
        },

        components: {
            PrimaryData
        },

        data() {
            return {
                request: new Request(),
                requestData: {
                    status: {
                        id: '',
                        name: ''
                    },
                    notes: '',
                    enrolled_at: ''
                },
                record: {
                    fields: []
                },
                statuses: []
            }
        },

        methods: {
            close() {
                this.$emit('update:active', false);

                this.request.errors.clear();

                this.requestData = {
                    status: {
                        name: '',
                    },
                    notes: '',
                    enrolled_at: ''
                };
            },

            open() {
                this.retrieveRecord();
                this.retrieveStatuses();
                this.retrieveProgramRecord();
            },

            retrieveProgramRecord() {
                let request = new Request({});

                request.edit(this.programId, this.recordType, this.recordId).then((response) => {
                    if(response.data.latest_status) {
                      this.requestData.status = response.data.latest_status.status;
                      this.requestData.notes = response.data.latest_status.notes;
                    } else {
                      delete this.requestData.status;
                      delete this.requestData.notes;
                    }
                    this.requestData.enrolled_at = response.data.enrolled_at;
                });
            },

            retrieveRecord() {
                let request = new RecordRequest({});

                request.show(this.recordType, this.recordId).then((response) => {
                    this.record = response.data;
                });
            },

            retrieveStatuses() {
                let request = new StatusRequest({
                    params: {
                        disabled: false
                    }
                });

                request.retrieve().then((response) => {
                    this.statuses = response.data;
                });
            },

            update() {
                this.request = new Request(this.requestData);

                this.request.update(this.programId, this.recordType, this.recordId)
                    .then((response) => {
                        this.$emit('update');
                        this.$message({
                            type: 'success',
                            message: 'Record updated successfully!'
                        });
                        this.close()
                    })
                    .catch((error) => {
                        this.$message({
                            type: 'error',
                            message: error.message
                        });
                    });
            }
        },

        created() {

        }
    }
</script>
