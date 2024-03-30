<template>

<container>
    <template v-if="getUrlParameter('hide_nav_bar', false) === false">
        <div class="row mb-2 pl-1 pr-1">
            <div class="flex-fill">
                <barcode-input-field @barcodeScanned="searchForProductSku"
                                     url_param_name="filter[product_sku]"
                                     ref="barcode"
                                     placeholder="Search"

                />
            </div>

            <button type="button" v-b-modal="'quick-actions-modal'" class="btn btn-primary ml-2"><font-awesome-icon icon="cog" class="fa-lg"></font-awesome-icon></button>
        </div>
    </template>

    <report-head :report-name="reportName"></report-head>

    <div v-if="records === null || records.length === 0" class="text-secondary small text-center">
        No records found with filters specified
    </div>
    <template v-else>
        <card>
            <!--
            style="transform: rotateX(180deg);"
            this is used twice to move scrollbar to the top of the table
            -->
            <div class="table-responsive py-2" style="transform: rotateX(180deg);">
                <table class="table-hover w-100 text-left small text-nowrap" style="transform: rotateX(180deg);">
                    <thead>
                    <tr>
                        <template v-for="field in visibleFields">
                            <th class="small pr-2">
                                <div class="dropdown">
                                    <button class="btn btn-link dropdown-toggle" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        {{ field.display_name }}
                                        <font-awesome-icon v-if="field.is_current" :icon="field.is_desc ? 'caret-down' : 'caret-up'" class="fa-xs" role="button"></font-awesome-icon>
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenu2">
                                        <button class="dropdown-item" type="button" @click="setUrlParameterAngGo('sort', field.name)">
                                            <icon-sort-asc/>&nbsp; Sort Ascending
                                        </button>
                                        <button class="dropdown-item" type="button" @click="setUrlParameterAngGo('sort', ['-', field.name].join(''))">
                                            <icon-sort-desc/>&nbsp; Sort Descending
                                        </button>
                                        <button class="dropdown-item" type="button" @click="showFilterBox(field)">
                                            <icon-filter/>&nbsp; Filter by value
                                        </button>
                                    </div>
                                </div>
                            </th>
                        </template>
                    </tr>
                    </thead>
                    <tbody>
                    <tr class="table-hover" v-for="record in records">
                        <template v-for="field in visibleFields">
                            <td class="pr-3" v-if="field.type === 'datetime'">{{ formatDateTime(record[field.name], 'YYYY MMM D HH:mm') }}</td>
                            <td class="pr-3" v-else-if="field.type === 'date'">{{ formatDateTime(record[field.name], 'YYYY MMM D') }}</td>
                            <td class="pr-3 text-right" v-else-if="field.type === 'numeric'">{{ record[field.name] }}</td>
                            <td class="pr-3" v-else >{{ record[field.name] }}</td>
                        </template>
                    </tr>
                    </tbody>
                </table>
            </div>
            <hr>
            <div class="row mx-2 small" v-if="pagination">
                <div class="col col-sm-4">
                    <p>
                        show
                        <select v-model="pagination.per_page" @change="changePagination('perPage')">
                            <option v-for="option in perPageOptions" :value="option">{{ option }}</option>
                        </select>
                        records
                    </p>
                </div>
                <div class="col col-sm-4">
                    <div class="d-flex justify-content-end justify-content-sm-center">
                        <div>
                            <button class="no-style-button mr-md-3" @click="changePagination('decrementPage')">
                                <icon-arrow-left/>
                            </button>
                        </div>
                        <div>
                            <p class="pb-0" style="margin-top: 0.1rem">Page</p>
                        </div>
                        <div>
                            <input type="text"
                                   v-on:input="customChangePagination"
                                   v-model.number="pagination.current_page"
                                   style="max-width: 30px; height: 19px; margin-top: 0.1rem;"
                                   class="mx-1 text-center"
                            />
                        </div>
                        <div>
                            <p class="pb-0" style="margin-top: 0.1rem">of {{ pagination.last_page }}</p>
                        </div>
                        <div>
                            <button class="no-style-button ml-md-3" @click="changePagination('incrementPage')">
                                <icon-arrow-right/>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-4">
                    <p class="float-right mr-1 mr-sm-0">{{ pagination.total }} records</p>
                </div>
            </div>
        </card>
    </template>

    <b-modal id="filter-box-modal" size="sm" no-fade hide-header @shown="focusFilterBoxInput">
        <div v-if="filterAdding" class="d-flex flex-column" style="gap: 5px;">
            <select v-model="filterAdding.selectedOperator" @change="focusFilterBoxInput" class="form-control form-control-sm">
                <option v-for="operator in filterAdding.operators" :key="operator" :value="operator">{{ operator === 'btwn' ? 'between' : operator }}</option>
            </select>
            <form @submit.prevent="addFilter" @keyup.enter="addFilter" class="d-flex flex-row" style="grid-gap: 5px;">
                <!-- between filter inputs -->
                <template v-if="filterAdding.selectedOperator === 'btwn'">
                    <input v-model="filterAdding.value" id='inputFilterBetweenValueFrom' :type="filterAdding.selectedField.type === 'numeric' ? 'number' : 'text'" class="form-control form-control-sm">
                    <input v-model="filterAdding.valueBetween" id='inputFilterBetweenValueTo' :type="filterAdding.selectedField.type === 'numeric' ? 'number' : 'text'" class="form-control form-control-sm">
                </template>

                <!-- other filters -->
                <template v-else>
                  <input v-model="filterAdding.value" id='inputFilterValue' :type="filterAdding.selectedField.type === 'numeric' ? 'number' : 'text'" class="form-control form-control-sm">
                </template>
            </form>
        </div>
        <template #modal-footer>
            <b-button variant="secondary" class="float-right" @click="$bvModal.hide('filter-box-modal')">Cancel</b-button>
            <b-button variant="primary" class="float-right" @click="addFilter">Apply</b-button>
        </template>
    </b-modal>

    <ModalDateBetweenSelector
        :starting_date.sync="filterAdding.value"
        :ending_date.sync="filterAdding.valueBetween"
        @close="$bvModal.hide('modal-date-selector-widget')"
        @apply="addFilter"
    />

    <b-modal id="quick-actions-modal" no-fade hide-header @hidden="setFocusElementById('barcode-input')">
        <stocktake-input v-bind:auto-focus-after="100" ></stocktake-input>
        <hr>
        <a class="btn btn-primary btn-block" :href="downloadUrl">{{ downloadButtonText }}</a>
        <template #modal-footer>
            <b-button variant="secondary" class="float-right" @click="$bvModal.hide('quick-actions-modal');">
                Cancel
            </b-button>
            <b-button variant="primary" class="float-right" @click="$bvModal.hide('quick-actions-modal');">
                OK
            </b-button>
        </template>
    </b-modal>

</container>

</template>

<script>
    import loadingOverlay from '../../mixins/loading-overlay';
    import url from "../../mixins/url";
    import api from "../../mixins/api";
    import helpers from "../../mixins/helpers";
    import IconSortAsc from "../UI/Icons/IconSortAsc.vue";
    import IconSortDesc from "../UI/Icons/IconSortDesc.vue";
    import IconFilter from "../UI/Icons/IconFilter.vue";
    import IconArrowRight from "../UI/Icons/IconArrowRight.vue";
    import IconArrowLeft from "../UI/Icons/IconArrowLeft.vue";
    import ModalDateBetweenSelector from "../Widgets/ModalDateBetweenSelector.vue";
    import SearchFilter from "./SearchFilter.vue";
    import ReportHead from "./ReportHead.vue";
    import moment from "moment";

    export default {
        mixins: [loadingOverlay, url, api, helpers],

        components: {IconArrowRight, IconArrowLeft, IconSortAsc, IconSortDesc, IconFilter, ModalDateBetweenSelector, SearchFilter, ReportHead},

        props: {
            recordString: String,
            fieldsString: String,
            reportName: String,
            downloadUrl: String,
            downloadButtonText: String,
            paginationString: String,
        },

        data() {
            return {
                records: JSON.parse(this.recordString),
                fields: JSON.parse(this.fieldsString),
                pagination: JSON.parse(this.paginationString),
                filters: [],
                filterAdding: null,
                showFilters: true,
            }
        },

        beforeMount() {
            this.setFilterAdding()
        },

        mounted() {
            this.buildFiltersFromUrl()
        },

        methods: {
            searchForProductSku(barcode) {
                if (barcode === '') {
                    this.removeUrlParameterAndGo('filter[product_sku]');
                    return;
                }
                this.setUrlParameterAngGo('filter[product_sku]', barcode);
            },

            focusFilterBoxInput() {
                switch (this.filterAdding.selectedOperator) {
                    case 'btwn':
                        this.setFocusElementById('inputFilterBetweenValueFrom', true)
                        break;
                    default:
                        this.setFocusElementById('inputFilterValue', true)
                        break;
                }
            },

            showFilterBox(field){
                if(['date', 'datetime'].includes(field.type)) {
                    this.$bvModal.show('modal-date-selector-widget')
                    this.setFilterAdding(field.name);
                    return;
                }

                this.$bvModal.show('filter-box-modal')
                this.setFilterAdding(field.name);
            },

            setFilterAdding(fieldName = null) {
                let selectedField = fieldName ? this.fields.find(f => f.name === fieldName) : this.fields[0];
                let existingFilter = this.filters.find(f => f.name === selectedField.name);
                let defaultOperator = selectedField.operators[0];
                let defaultFilterValueMin = '';
                let defaultFilterValueMax = '';

                switch (selectedField.type) {
                    case 'numeric':
                        defaultOperator = 'btwn';
                        defaultFilterValueMin = 0;
                        defaultFilterValueMax = 0;
                        break;
                    case 'string':
                        defaultOperator = 'contains';
                        defaultFilterValueMin = '';
                        defaultFilterValueMax = '';
                        break;
                    case 'date':
                        defaultOperator = 'btwn';
                        defaultFilterValueMin = moment().startOf("year").format('YYYY-MM-DD HH:mm');
                        defaultFilterValueMax = moment().endOf("day").format('YYYY-MM-DD HH:mm');
                        break;
                    case 'datetime':
                        defaultOperator = 'btwn';
                        defaultFilterValueMin = moment().startOf("day").format('YYYY-MM-DD HH:mm');
                        defaultFilterValueMax = moment().endOf("day").format('YYYY-MM-DD HH:mm');
                        break;
                    default:
                        defaultOperator = 'contains';
                        defaultFilterValueMin = '';
                        defaultFilterValueMax = '';
                }

                let selectedOperator = existingFilter ? existingFilter.selectedOperator : defaultOperator;
                let filterValueMin = existingFilter ? existingFilter.value : defaultFilterValueMin;
                let filterValueMax = existingFilter ? existingFilter.valueBetween : defaultFilterValueMax;

                this.filterAdding = {
                    fields: this.fields,
                    selectedField: selectedField,
                    operators: selectedField.operators,
                    selectedOperator: selectedOperator,
                    value: filterValueMin,
                    valueBetween: filterValueMax,
                }
            },

            buildFiltersFromUrl() {
                const urlParams = new URLSearchParams(window.location.search);

                this.getUrlFilter()
                for (const [key, value] of urlParams.entries()) {
                    if(key.startsWith('filter')) {
                        let filterName = key.split('[')[1].split(']')[0];

                        let fieldName = filterName;
                        fieldName = fieldName.replaceAll('_equal','');
                        fieldName = fieldName.replaceAll('_contains','');
                        fieldName = fieldName.replaceAll('_between','');
                        fieldName = fieldName.replaceAll('_lower_than','');
                        fieldName = fieldName.replaceAll('_greater_than','');

                        let filterOperator = filterName.replace(fieldName, '');
                        let filterOperatorHumanString = filterOperator;

                        let field = this.findField(fieldName);

                        switch (filterOperator) {
                          case '':
                            filterOperatorHumanString = 'equals'
                            break;
                          case '_equal':
                            filterOperatorHumanString = 'equals'
                            break;
                          case '_contains':
                            filterOperatorHumanString = 'contains'
                            break;
                          case '_between':
                            filterOperatorHumanString = 'between'
                            break;
                          case '_greater_than':
                            filterOperatorHumanString = 'greater than'
                            break;
                          case '_lower_than':
                            filterOperatorHumanString = 'lower than'
                            break;
                          default:
                            filterOperatorHumanString = filterOperator
                        }

                        let filter = {
                            name: fieldName,
                            displayName: field.display_name,
                            selectedOperator: filterOperator === '_between' ? 'btwn' : filterOperatorHumanString,
                            value: value,
                            valueBetween: '',
                        }

                        if(filterOperator === '_between') {
                            let values = Array.isArray(value) ? value : value.split(',');
                            filter.value = values[0];
                            filter.valueBetween = values[1];
                        }

                        this.filters.push(filter);
                    }
                }
            },

            addFilter() {
                const { value, selectedOperator, valueBetween, selectedField } = this.filterAdding;

                let filterName = '';
                let filterValue = '';

                switch (selectedOperator) {
                  case 'equals':
                    filterName = ['filter[', selectedField.name, ']'].join('');
                    filterValue = `${value}`;
                    break;
                  case 'btwn':
                    filterName = ['filter[', selectedField.name, '_between]'].join('');
                    filterValue = `${value},${valueBetween}`;
                    break;
                  case 'greater than':
                    filterName = `filter[${selectedField.name}_greater_than]`;
                    filterValue = value;
                    break;
                  case 'lower than':
                    filterName = `filter[${selectedField.name}_lower_than]`;
                    filterValue = value;
                    break;
                  default:
                    filterName = `filter[${selectedField.name}_${selectedOperator}]`;
                    filterValue = value;
                }

                this.setUrlParameterAngGo(filterName, filterValue)
            },

            buildUrl() {
                let baseUrl = window.location.pathname;

                const paginationParams = `per_page=${this.pagination.per_page}&page=${this.pagination.current_page}`;

                const sortField = this.fields.find(f => f.is_current);
                const sortParam = sortField ? `sort=${sortField.is_desc ? '-' : ''}${sortField.name}` : '';

                const filterParams = this.filters.map(filter => {
                    const operator = filter.selectedOperator === 'btwn' ? 'between' : filter.selectedOperator;
                    const value = filter.selectedOperator === 'btwn' ? `${filter.value},${filter.valueBetween}` : filter.value;
                    return `filter[${filter.name}${operator === 'equals' ? '' : `_${operator}`}]=${value}`;
                }).join('&');

                const params = [paginationParams, sortParam, filterParams].filter(p => p).join('&');
                return `${baseUrl}?${params}`;
            },

            customChangePagination: _.debounce(function() {
                if(this.pagination.current_page < 1) {
                    this.pagination.current_page = 1;
                }else if(this.pagination.current_page > this.pagination.last_page) {
                    this.pagination.current_page = this.pagination.last_page;
                }
                this.changePagination();
            }, 800),

            changePagination(changeType = null) {
                if (changeType === 'perPage') {
                    this.pagination.current_page = 1;
                    location.href = this.buildUrl();
                } else if (changeType === 'incrementPage' && this.pagination.current_page < this.pagination.last_page) {
                    this.pagination.current_page++;
                    location.href = this.buildUrl();
                } else if (changeType === 'decrementPage' && this.pagination.current_page > 1) {
                    this.pagination.current_page--;
                    location.href = this.buildUrl();
                }

                location.href = this.buildUrl();
            },

            findField(fieldName) {
                return this.fields.find(f => f.name === fieldName);
            }
        },

        computed: {
            perPageOptions(){
                return [...new Set([this.pagination.per_page, 10, 25, 50, 100])].sort((a, b) => a - b);
            },

            visibleFields() {
                return Object.keys(this.records[0])
                    .map(this.findField);
            },
        }
    }
</script>

<style scoped>

.dropdown > .btn.dropdown-toggle {
    font-size: 12px;
    padding: 0;
    color: black;
    font-weight: bold;
    &:focus, &:active {
        outline: none;
        box-shadow: none;
        border-color: transparent;
    }
}

.dropdown-toggle::after {
    display: none;
}

.no-style-button {
    background: none;
    border: none;
    padding: 0;
    cursor: pointer;
}

::-webkit-scrollbar{
    height: 4px;
    width: 4px;
}

::-webkit-scrollbar-thumb:horizontal{
    background: lightgray;
    border-radius: 10px;
}

::-webkit-scrollbar:horizontal{
    height: 8px;
    background: none;
}

</style>
