/*jshint node:true */
module.exports = function ( grunt ) {
	grunt.loadNpmTasks( 'grunt-contrib-jshint' );
	grunt.loadNpmTasks( 'grunt-jsonlint' );
	grunt.loadNpmTasks( 'grunt-banana-checker' );

	grunt.initConfig( {
		jshint: {
			options: {
				jshintrc: true
			},
			all: [
				'**/*.js',
				'!node_modules/**',
				'!resources/bluespice.extjs/Ext.ux/**',
				'!resources/extjs/**'
			]
		},
		banana: {
			all: [
				'i18n/core/',
				'i18n/credits/',
				'i18n/diferred/',
				'i18n/diagnostics/',
				'i18n/extjs/',
				'i18n/extjs-portal/',
				'i18n/installer/',
				'i18n/notifications/',
				'i18n/validator/'
			]
		},
		jsonlint: {
			all: [
				'*.json',
				'**/*.json',
				'!node_modules/**'
			]
		}
	} );

	grunt.registerTask( 'test', [ 'jshint', 'jsonlint', 'banana' ] );
	grunt.registerTask( 'default', 'test' );
};
