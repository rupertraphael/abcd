<template>
    <div>
        <el-form label-position="top" ref="fieldData" :rules="fieldData.rules" :model="fieldData" @submit.native.prevent>
            
            <el-form-item prop="title">
                <label>
                    Question/Title
                </label>
                <el-input v-model="fieldData.title"></el-input>
            </el-form-item>
            
            <el-form-item class="tw-mt-8">
                <label>
                    This Field is
                </label><br>
                <el-switch v-model="fieldData.settings.required" active-text="Required" inactive-text="Optional" class="tw--mt-6"></el-switch>
            </el-form-item>
            
            <el-form-item>
                <label>
                    Number of choices
                </label>
                <el-input-number v-model="fieldData.settings.radioNum" controls-position="right" :min="1" :max="30"></el-input-number>
            </el-form-item>
            
            <el-form-item class="tw-relative tw-text-center tw-mt-12">
                <el-button type="success" @click="save('fieldData')" class="tw-w-48">Add it!</el-button>
            </el-form-item>
        </el-form>
    </div>
</template>

<script>
export default {
    name: 'radioField',
    data: () => {
        return {
            fieldData: {
                type: 'RadioField',
                name: 'radio_field',
                title: '',
                settings: {
                    required: false,
                    radioNum: 2,
                    nextChoice: 0
                },
                choices: [],
                rules: {
                    title: [
                        { required: true, message: 'Please input question title', trigger: 'blur' }
                    ]
                }
            },
        }
    },
    props: {
        inputData: Object,
    },
    methods: {
        setChoices() {
            for(let i = 1; i <= this.fieldData.settings. radioNum; i++) {
                this.fieldData.choices.push( 'Choice ' + i )
            }
        },

        save(formName) {
            this.$refs[formName].validate((valid) => {
                if (valid) {
                    this.setChoices();
                    this.$emit('save', this.fieldData);
                } else {
                    this.$message.error('Oops, You forgot to enter a Question/Title for this field.');
                    return false;
                }
            })
        },
    }
}
</script>

<style>

</style>
