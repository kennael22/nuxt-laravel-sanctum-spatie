<template>
	<div>
		<v-row justify="center" align="center">
			<v-col cols="12" sm="5" md="5" lg="5" xl="5" xs="5" class="d-flex align-center justify-center">
				<div >
					<h1>Fight 1</h1>
				</div>
			</v-col>
		</v-row>
		<v-row>
			<v-col cols="12" sm="6" md="6" lg="6" xl="6" xs="6">
				<v-toolbar dense dark color="red" class="d-flex justify-space-around">
					Meron
				</v-toolbar>
				<v-card rounded="0" class="d-flex align-center justify-center" min-height="70vh">
					<div justify="center" align="center">
						<h1>8,000</h1>
						<h1>payout 2.20</h1>
					</div>
				</v-card>
			</v-col>
			<!-- <v-card flat class="d-flex align-center justify-center" min-width="10vh">
				<v-card-title>
					close
				</v-card-title>
			</v-card> -->
			<v-col cols="12" sm="6" md="6" lg="6" xl="6" xs="6">
				<v-toolbar dense dark color="green" class="d-flex justify-space-around">
					Wala
				</v-toolbar>
				<v-card rounded="0" class="d-flex align-center justify-center" min-height="70vh">
					<div justify="center" align="center">
						<h1>10,000</h1>
						<h1>payout 1.80</h1>
					</div>
				</v-card>
			</v-col>
		</v-row>
	</div>
</template>

<script>
export default {
	name: 'IndexPage',
	layout: 'guest',
	auth: false,
}
</script>

<style scoped>
.center {
	margin: auto;
	width: 60%;
	border: 3px solid #73AD21;
	padding: 10px;
}
</style>