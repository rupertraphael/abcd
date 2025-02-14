<template>
    <div id="textField" class="tw-my-8">
        <el-row>
            <el-col :span="6">
                <label for="text" class="tw-block tw-text-right tw-mr-1">{{ field.title }}</label>
            </el-col>
            <el-col v-if="field.reference_target_type_id == null" :span="8">
                <el-input id="text"
                    v-if="limit"
                    show-word-limit
                    :value="value"
                    @input="$emit('input', $event)"
                    :required="isRequired"
                    :maxlength="max">
                </el-input>
                <el-input id="text"
                    v-else
                    :value="value"
                    @input="$emit('input', $event)"
                    :required="isRequired">
                </el-input>
            </el-col>
            <el-col v-else :span="8">
                <base-select
                    v-if="targetName == 'Records'"
                    :value="value"
                    @input="$emit('input', $event)"
                    filterable
                    remote
                    :remote-method="retrieveTargetItems"
                    name="type"
                    :placeholder="`Input ${targetName}`"
                    @change="targetRequest.errors.clear('team_id')">
                    <el-option
                        v-for="(item, index) in targetItems"
                        :key="index"
                        :label="getPrimaryData(item, item.fields)"
                        :value="item.id">
                        {{ getPrimaryData(item, item.fields) }}
                    </el-option>
                </base-select>

                <base-select
                    v-else-if="targetName == 'Form Fields'"
                    :value="value"
                    @input="$emit('input', $event)"
                    @click.native="retrieveTargetItems"
                    filterable
                    remote
                    :remote-method="retrieveTargetItems"
                    name="type"
                    :placeholder="`Input ${targetName}`"
                    @change="targetRequest.errors.clear('team_id')">
                    <el-option
                        v-for="(item, index) in targetItems"
                        :key="index"
                        :label="item.value"
                        :value="item.value">
                        {{ item.value }}
                    </el-option>
                </base-select>

                <base-select
                    v-else
                    :value="value"
                    @input="$emit('input', $event)"
                    @click.native="retrieveTargetItems"
                    filterable
                    remote
                    :remote-method="retrieveTargetItems"
                    name="type"
                    :placeholder="`Input ${targetName}`"
                    @change="targetRequest.errors.clear('team_id')">
                    <el-option
                        v-for="(item, index) in targetItems"
                        :key="index"
                        :label="item.name"
                        :value="item.id">
                        {{ item.name }}
                    </el-option>
                </base-select>
            </el-col>
        </el-row>
    </div>
</template>

<script>
    import TargetTypeRequest from '@/api/FormFieldTargetTypeRequest';

    import ProgramRequest from '@/api/ProgramRequest';
    import GroupRequest from '@/api/GroupRequest';
    import RecordTypeRequest from '@/api/RecordTypeRequest';
    import RecordRequest from '@/api/RecordRequest';
    import FormFieldEntryRequest from '@/api/FormFieldEntryRequest';

    export default {

        data() {
            return {
                targetTypeRequest: new TargetTypeRequest({}),
                targetType: {
                    name: ''
                },
                targetRequest: {},
                requests: {
                    Programs: ProgramRequest,
                    Groups: GroupRequest,
                    Records: RecordRequest,
                    'Form Fields': FormFieldEntryRequest
                },
                targetParams: {
                    ascending: true,
                    sortBy: 'name',
                    page: 1,
                    perPage: 10,
                    search: ''
                },
                recordParams: {
                    ascending: true,
                    sortBy: 'field_1_value',
                    page: 1,
                    perPage: 10,
                    search: ''
                },
                fieldParams: {
                    ascending: true,
                    sortBy: 'id',
                    page: 1,
                    perPage: 10,
                    search: ''
                },
                targetItems: [],
            }
        },
        props: {
            field: {
                type: Array | Object,
                default: {}
            },
            value: '',
        },
        computed: {
            max: {
                get() { return this.field.settings.max }
            },
            isRequired: {
                get() { return this.field.settings.required }
            },
            limit: {
                get() { return this.field.settings.isLimited }
            },
            targetName() {
                return this.targetType.name;
            }
        },
        methods: {
            getTargetType() {
                if(this.field.reference_target_type_id == null)
                    return;

                this.targetTypeRequest.show(this.field.reference_target_type_id).then((response) => {
                    this.targetType = response.data;
                });
            },

            retrieveTargetItems(keywords) {
                this.targetRequest = new this.requests[this.targetName]({});

                this.targetParams.search = keywords;
                let params = this.targetParams;

                if(this.targetName == 'Records') {
                    this.recordParams.search = keywords;
                    params = this.recordParams;

                    const request = new RecordTypeRequest({});
                    request.show(this.field.reference_target_id).then((response) => {
                        this.targetRequest.setFields({
                            params
                        });

                        this.targetRequest.retrieve(response.data.slug).then((response) => {
                            this.targetItems = response.data;
                        });
                    });

                    return;
                } else if(this.targetName == 'Form Fields') {
                    this.fieldParams.search = keywords;
                    params = this.fieldParams;

                    this.targetRequest.setFields({
                        params
                    });

                    this.targetRequest.retrieve(this.field.reference_target_id).then((response) => {
                        this.targetItems = response.data;
                    });

                    return;
                }

                this.targetRequest.setFields({
                    params
                });

                this.targetRequest.retrieve().then((response) => {
                    this.targetItems = response.data;
                });
            },

            getPrimaryData(record, fields) {
                let remainingFields = [];

                if('first_name' in fields)
                    remainingFields.push('first_name');

                if('last_name' in fields)
                    remainingFields.push('last_name');

                if('business_name' in fields)
                    remainingFields.push('business_name');

                let data = '';

                remainingFields.forEach(field => {
                    data += record[field] + ' ';
                });

                return data.trim();
            },
        },

        created() {
            this.getTargetType();
        }
    }
</script>
