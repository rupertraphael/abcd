<template>
  <div>
    <list
      :has-header="hasListHeader"
      :page="page"
      @page-change="handlePageChange"
      :per-page="perPage"
      :total="total"
      @add="$emit('add')"
    >
      <template slot="empty-placeholder">
        <slot name="empty-placeholder">
          <div>
            <div v-if="total == 0" class="tw-p-4 tw-text-base tw-text-black tw-font-semibold">
              <slot name="empty-placeholder-text">
                There is currently no item in this list.
                <base-link class="tw-text-base tw-font-semibold">Create a new one</base-link>to get started.
              </slot>
            </div>
            <!-- <div>
                            <slot v-if="total == 0 && search == '' && loading == false && hasAdd " name="empty-placeholder-add-button">
                                <base-button
                                    v-if="!showInactive"
                                    class="tw-py-2 tw-pl-2 tw-pr-4 tw-bg-blue-500 hover:tw-bg-transparent hover:tw-text-blue-500 tw-text-white tw-border-none"
                                    @click="$emit('add')">
                                    <span class="tw-text-xs tw-align-middle">
                                        <slot name="empty-placeholder-add-button-text">Add</slot>
                                    </span>
                                </base-button>
                            </slot>
            </div>-->
          </div>
        </slot>
      </template>

      <template slot="header-text">
        <slot name="header-text">Title</slot>
      </template>

      <!-- <template slot="options" v-if="hasOptions" v-show="showListHeaderOptions">
                <slot v-if="hasSearch" name="options-search">
                    <search v-model="search" @input="handleSearch" class="tw-w-1/2"/>
                </slot>
                <slot v-if="hasAdd" name="options-add">
                    <div class="tw-w-1/4 tw-text-right">
                        <base-button
                            class="tw-px-4 tw-bg-white tw-border-none tw-text-white tw-bg-blue-500 tw-no-shrink"
                            @click="$emit('add')">
                            <span class="tw-align-middle">
                                <slot name="options-add-text">Add Resource</slot>
                            </span>
                        </base-button>
                    </div>
                </slot>
      </template>-->

      <slot v-if="hasListColumns" name="list-columns">
        <div
          class="tw-flex tw-pt-6 tw-pb-2 tw-px-4 tw-text-xs tw-text-gray-500 tw-uppercase tw-font-semibold"
        >
          <div class="tw-w-1/4" :column="primaryDataColumn">
            <slot name="list-column-primary-data">{{ primaryDataColumn }}</slot>
          </div>
          <div class="tw-flex tw-w-3/4">
            <div class="tw-flex tw-flex-grow">
              <span
                v-for="(column, index) in tertiaryColumns"
                :key="index"
                :column="column"
                class="tw-flex-1"
              >{{ column }}</span>
            </div>
            <div class="tw-w-1/6">
              <slot name="list-column-options"></slot>
            </div>
          </div>
        </div>
      </slot>

      <slot name="list" :items="items">
        <list-item
          v-for="(item, index) in items"
          :key="index"
          class="tw-py-4 tw-px-4 hover:tw-bg-gray-100"
          :to="item.links.to + pathSuffix"
        >
          <template slot="image">
            <slot name="list-item-image" :item="item"></slot>
          </template>

          <slot name="list-item-primary-data" :item="item">{{ item }}</slot>

          <template slot="secondary-data">
            <span class="tw-text-sm">
              <slot name="list-item-secondary-data" :item="item"></slot>
            </span>
          </template>

          <template slot="tertiary-data">
            <slot name="list-item-tertiary-data-container" :item="item">
              <div class="tw-flex tw-flex-grow">
                <slot name="list-item-tertiary-data" :item="item"></slot>
              </div>
            </slot>
          </template>

          <template slot="options">
            <slot name="options-edit-button" :item="item">
              <base-button
                v-if="hasEdit"
                size="small"
                outline
                @click="$emit('edit', item[resourceIdentifier])"
              >
                <base-icon class="tw-text-xs tw-mr-1 tw-align-middle">
                  <slot name="options-edit-icon">edit</slot>
                </base-icon>
                <span class="tw-text-xs tw-align-middle">
                  <slot name="options-edit-text">Edit</slot>
                </span>
              </base-button>
            </slot>
            <slot name="options-disable-button" :item="item">
              <base-button
                v-if="hasDisable && item.active"
                size="small"
                outline
                @click="$emit('disable', item[resourceIdentifier])"
              >
                <base-icon class="tw-text-xs tw-mr-1 tw-align-middle">
                  <slot name="options-disable-icon">visibility_off</slot>
                </base-icon>
                <span class="tw-text-xs tw-align-middle">
                  <slot name="options-disable-text">Disable</slot>
                </span>
              </base-button>
              <base-button
                v-if="hasDisable && !item.active"
                size="small"
                outline
                @click="$emit('enable', item[resourceIdentifier])"
              >
                <base-icon class="tw-text-xs tw-mr-1 tw-align-middle">
                  <slot name="options-disable-icon">visibility</slot>
                </base-icon>
                <span class="tw-text-xs tw-align-middle">
                  <slot name="options-disable-text">Enable</slot>
                </span>
              </base-button>
            </slot>
            <slot name="options-remove-button" :item="item">
              <base-button
                v-if="hasRemove"
                size="small"
                outline
                @click="$emit('remove', item[resourceIdentifier])"
              >
                <base-icon class="tw-text-xs tw-mr-1 tw-align-middle">
                  <slot name="options-remove-icon">close</slot>
                </base-icon>
                <span class="tw-text-xs tw-align-middle">
                  <slot name="options-remove-text">Remove</slot>
                </span>
              </base-button>
            </slot>
            <base-button
              v-if="hasDelete"
              size="small"
              outline
              @click="$emit('delete', item[resourceIdentifier])"
            >
              <base-icon class="tw-text-xs tw-mr-1 tw-align-middle">
                <slot name="options-delete-icon">archive</slot>
              </base-icon>
              <span class="tw-text-xs tw-align-middle">
                <slot name="options-delete-text">Archive</slot>
              </span>
            </base-button>
          </template>
        </list-item>
      </slot>

      <template slot="footer-options">
        <slot name="footer-options">
          <base-button
            v-if="hasManage && total > 0"
            class="tw-py-2 tw-pl-2 tw-pr-4 hover:tw-bg-transparent hover:tw-text-blue-500 tw-text-gray-500 tw-border-none"
            @click="$emit('manage')"
          >
            <base-icon class="tw-text-sm tw-align-middle tw-mr-1">add</base-icon>
            <span class="tw-text-xs tw-align-middle">
              <slot name="footer-options-manage-text">Manage Resources</slot>
            </span>
          </base-button>
          <div class="tw-mr-3 tw-ml-auto">
            <base-checkbox
              @change="$emit('show-inactive', showInactive = $event)"
              class="tw-text-sm tw-text-black tw-font-normal"
            >Only Show Inactive</base-checkbox>
          </div>
        </slot>
      </template>
    </list>
  </div>
</template>
<script>
import List from "@/components/list";
import ListItem from "@/components/listItem";
import Search from "../components/search";

export default {
  components: {
    List,
    ListItem,
    Search
  },

  props: {
    pathSuffix: {
      type: String,
      default: ""
    },

    loading: {
      type: Boolean,
      default: false
    },

    resourceIdentifier: {
      type: String,
      default: "id"
    },

    searchTerms: {
      type: String,
      default: ""
    },

    page: {
      type: Number | String,
      default: 1
    },

    perPage: {
      type: Number | String,
      default: 10
    },

    total: {
      type: Number | String,
      default: 0
    },

    items: Array | Object,

    hasAdd: {
      type: Boolean,
      default: false
    },

    hasEdit: {
      type: Boolean,
      default: true
    },

    hasDelete: {
      type: Boolean,
      default: false
    },

    hasDisable: {
      type: Boolean,
      default: false
    },

    hasListHeader: {
      type: Boolean,
      default: true
    },

    hasListColumns: {
      type: Boolean,
      default: true
    },

    hasRemove: {
      type: Boolean,
      default: false
    },

    hasManage: {
      type: Boolean,
      default: false
    },

    hasOptions: {
      type: Boolean,
      default: true
    },

    hasSearch: {
      type: Boolean,
      default: true
    },

    primaryDataColumn: {
      type: String | Object,
      default: ""
    },

    tertiaryColumns: {
      type: Array | Object,
      default: () => {
        return [];
      }
    }
  },

  computed: {
    showListHeaderOptions() {
      if (!this.hasListHeader) return false;

      if (this.searchTerms.length == 0 && this.total == 0) return false;

      return true;
    }
  },

  data() {
    return {
      optionsWidth: "",
      search: "",
      showInactive: false
    };
  },

  methods: {
    handlePageChange(page) {
      this.$emit("update:page", page);
      this.$emit("page-change", page);
    },

    handleSearch: _.debounce(function() {
      this.$emit("update:search-terms", this.search);
      this.$emit("search", this.search);
      this.handlePageChange(1);
    }, 300)
  }
};
</script>
