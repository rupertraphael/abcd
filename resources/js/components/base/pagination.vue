<template>
    <el-pagination
        v-bind="$attrs"
        :current-page="currentPage"
        :page-size="pageSize"
        :total="total"
        layout="prev, pager, next"
        :pager-count="5"
        @current-change="handleCurrentChange">
    </el-pagination>
</template>
<script>
    export default {
        inheritAttrs: false,
        props: {
            currentPage: Number,
            pageSize: Number,
            total: Number
        },
        computed: {
            firstItemNo() {
                return (this.pageSize*(this.currentPage-1)) + 1 ;
            },

            lastItemNo() {
                const itemNo = this.currentPage*this.pageSize;

                if(itemNo > this.total)
                    return this.total;

                return itemNo;
            },

            paginationInfo() {
                if(this.total == 0)
                    return `Nothing to show`;

                return `Showing ${ this.firstItemNo }-${ this.lastItemNo } of ${ this.total }`;
            }
        },
        methods: {
            handleCurrentChange(page) {
                this.$emit('update:currentPage', page);
                this.$emit('current-change', page);
            }
        }
    }
</script>
