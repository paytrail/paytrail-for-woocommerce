import { defineConfig } from 'allure';

export default defineConfig({
	name: 'Paytrail for WooCommerce tests',
	plugins: {
		awesome: {
			options: {
				reportName: 'Paytrail for WooCommerce tests',
			},
		},
	},
});
