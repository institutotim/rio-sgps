export default {

	data: () => { return {
        selectedGroups: null,
        selectedrps: null,
	}},

	props: ['initiallySelectedGroups'],

	mounted: function() {
		this.selectedGroups = this.initiallySelectedGroups || [];
	},

	methods: {

        hasSelectedGroup: function(rpCode) {
			if(!this.selectedrps) return false;
			return this.selectedrps.indexOf(rpCode) !== -1;
		},

		onGroupsUpdate: function(groupCode, $event) {

			if(!$event.target.checked) {
				this.selectedGroups = this.selectedGroups.filter((code) => code !== groupCode);
			} else {
				this.selectedGroups.push(groupCode);
				this.$forceUpdate();
			}

			console.log(this.selectedGroups);
        },

        onRPsUpdate: function(rpCode, $event) {
			console.log(rpCode);
		}

	}

}
