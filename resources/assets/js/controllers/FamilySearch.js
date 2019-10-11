import { create } from 'vue-modal-dialogs'
import FilterFlagsModal from '../modals/FlagsFilterModal';
import ExportModal from '../modals/ExportModal';
import JustificationModal from '../modals/JustificationModal';

const selectFilterFlags = create(FilterFlagsModal, 'selectedFlags');
const exportResults = create(ExportModal, 'filters');
const justifyPendence = create(JustificationModal, 'alert', 'type');

export default {

	props: ['activeFilters', 'alertsData'],

	data: () => { return {
		isLoading: false,
		filters: {q: ''},
		alerts: []
	}},

	mounted: function() {
		if(!this.activeFilters) return;
		this.filters = Object.assign(this.filters, this.activeFilters);
		this.alerts = this.alertsData;
	},

	methods: {

		exportResults: async function() {
			await exportResults(this.filters);
		},

		setFilter: function (filterName, value) {
			this.filters[filterName] = value;
			this.filters = Object.assign({}, this.filters);

			this.$nextTick(() => this.doSearch());
		},

		selectFlagsToFilter: async function() {
			let selectedFlags = await selectFilterFlags(this.filters.flags);

			if(!selectedFlags) return;

			this.setFilter('flags', selectedFlags);
			this.doSearch();
		},

		doSearch: function() {
			this.isLoading = true;
			this.$forceUpdate();

			this.$nextTick(() => this.$refs.filterForm.submit());
        },

        justify: async function(index){
			let alert = this.alerts[index];
            let justification = await justifyPendence({
                id: alert.id,
                justification: alert.justification
			}, 'set');
			
			this.alerts[index].justification = justification;
			this.alerts[index].is_justified = true;
        },

        showJustification: async function(index){
			let alert = this.alerts[index];
            let justification = await justifyPendence({
                id: alert.id,
                justification: alert.justification
            }, 'show');
			this.alerts[index].justification = justification;
			this.alerts[index].is_justified = false;
        }
	}


}
