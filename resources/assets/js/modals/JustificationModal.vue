<template>
	<modal id="" ref="modal">
		<strong slot="header">Justificar</strong>
		<form slot="body" method="post" @submit.prevent="">
			<loading-feedback :is-loading="isLoading"></loading-feedback>

			<div class="form-group">
				<label for="justification" class="col-form-label"> Justificativa:</label>
				<textarea class="form-control" id="justification" rows="3" v-model="justification"></textarea>
			</div>

			<div class="form-group">
				<button v-if="type==='set'" class="btn btn-primary pull-right" :disabled="shouldBlockSubmit" :class="{disabled: shouldBlockSubmit}" type="submit" @click="justify()">Salvar</button>
                <button v-else class="btn btn-primary pull-right" :disabled="shouldBlockSubmit" :class="{disabled: shouldBlockSubmit}" type="submit" @click="removeJustification()">Remover</button>
			</div>
		</form>
	</modal>
</template>

<script>
	import axios from "axios";

	import API from "../services/API";
	import Endpoints from "../config/Endpoints";
	import Dialogs from "../services/Dialogs";

	export default {
		props: ['alert', 'type'],

		mounted: function(){
			this.justification = this.alert.justification;
		},

		data: () => { return {
			isLoading: false,
			justification: ''
		}},

		computed: {

			shouldBlockSubmit: function() {
				return (!this.justification);
			},

		},


		methods: {
			justify: function(){
				this.isLoading = true;

				return axios.post(
					API.url(Endpoints.Family.Alert.Justify, {family_id: this.alert.id}),
					{justification: this.justification},
					API.headers()
				).then(async (res) => {
					this.isLoading = false;
					this.$close(this.justification);
				}).catch(async (err) => {
					this.isLoading = false;

					console.error("JustificationModal.justify: ", err);
					await Dialogs.alert('Ocorreu um erro ao salvar as informações!');
				})
			},

			removeJustification: function(){
				this.isLoading = true;

				return axios.post(
					API.url(Endpoints.Family.Alert.Justify, {family_id: this.alert.id}),
					{justification: null},
					API.headers()
				).then(async (res) => {
					this.isLoading = false;
					this.$close();
				}).catch(async (err) => {
					this.isLoading = false;

					console.error("JustificationModal.removeJustification: ", err);
					await Dialogs.alert('Ocorreu um erro ao salvar as informações!');
				})
			}

		}
	}
</script>
