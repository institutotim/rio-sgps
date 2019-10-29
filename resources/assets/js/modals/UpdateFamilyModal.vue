<template>
	<modal id="" ref="modal">
		<strong slot="header">Alterar Responsável Familiar</strong>
		<form slot="body" method="post" @submit.prevent="">
			<loading-feedback :is-loading="isLoading"></loading-feedback>
            <h4>Responsável: {{getMemberInChargeName}}</h4>
            <table class="table table-hover sgps__table" >
                <thead>
                    <tr>
                        <th class="col-md-1">RF</th>
                        <th class="col-md-4">Nome</th>
                        <th class="col-md-2">Nascimento</th>
                        <th class="col-md-5">Parentesco</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(member, index) in members" :key="index">
                        <template v-if="member.archived_by === null">
                            <td class="col-md-1">
                                <b-form-group>
                                    <b-form-radio v-show="canBeResponsible(member.dob)" :value="member.id" v-model="memberInChargeId" @change="setAsMemberInCharge(index)"></b-form-radio>
                                </b-form-group>
                            </td>
                            <td class="col-md-4">{{member.name}}</td>
                            <td class="col-md-2">
                                <template v-if="member.dob">{{member.dob | moment('DD/MM/YYYY')}}</template>
                            </td>
                            <td class="col-md-5">
                                <select name="" class="custom-select custom-select-sm" :disabled="shouldBlockSubmit || member.id === memberInChargeId" v-model="member.kinship" @change="setKinship(index)">
                                    <option value=0></option>
                                    <option value=1>Cônjuge,Companheiro(a)</option>
                                    <option value=2>Filho(a), Enteado(a)</option>
                                    <option value=3>Pai, Mãe, Sogro(a)</option>
                                    <option value=4>Neto(a), Bisneto(a)</option>
                                    <option value=5>Irmã, Irmão</option>
                                    <option value=6>Outro Parente</option>
                                    <option value=7>Agregado(a)</option>
                                    <option value=8>Pensioninsta/Inquilino</option>
                                    <option value=9>NS/NR</option>
                                </select>
                            </td>
                        </template>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
				<button class="btn btn-primary pull-right" type="submit" :disabled="shouldBlockSubmit" @click="save()">Salvar</button>
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
        props: ['family'],

        data: () => { return {
            isLoading: false,
            members: [],
            memberInChargeId: '',
            kinship: {}
        }},

        computed: {
            shouldBlockSubmit: function(){
                return this.memberInChargeId === null;
            },

            getMemberInChargeName: function(){
                if(!this.members) return;
                if(!this.memberInChargeId) return;

                return this.members.find(member => {
                    return member.id === this.memberInChargeId;
                }).name;
            }
        },

        mounted: function(){
            this.members = this.family.members;
            this.memberInChargeId = this.family.person_in_charge_id;
        },

        methods: {
            canBeResponsible: function(dob){
                return this.$moment().diff(dob, 'years') >= 18;
            },

            setAsMemberInCharge: function(index){
                const memberId = this.members[index].id;

                this.memberInChargeId = memberId;
                this.members[index].kinship = 0;
                delete this.kinship[memberId];
            },

            setKinship: function(index){
                const member = this.members[index];

                this.kinship[member.id] = member.kinship;
            },

            save: async function(){
                this.isLoading = true;
                await this.saveMemberInCharge();
                await this.saveKinship();
                this.isLoading = false;
                this.$close(true);
            },

            saveMemberInCharge: function(){
                return axios.put(
					API.url(Endpoints.Family.SetMemberInCharge, {family_id: this.family.id}),
					{
                        member_id: this.memberInChargeId
                    },
					API.headers()
				).catch(async (err) => {

					console.error("UpdateFamilyModal.setAsMemberInCharge: ", err);
					await Dialogs.alert('Ocorreu um erro ao salvar as informações!');
				})
            },

            saveKinship(){
                return axios.put(
					API.url(Endpoints.Family.updateKinship, {family_id: this.family.id}),
					{
                        data: this.kinship
                    },
					API.headers()
				).catch(async (err) => {

                    console.error("UpdateFamilyModal.updateKinship: ", err);
					await Dialogs.alert('Ocorreu um erro ao salvar as informações!');
				})
            }
        }

	}
</script>
