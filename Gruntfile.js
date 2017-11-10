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
				'!vendor/**',
				'!resources/bluespice.extjs/Ext.ux/**',
				'!resources/extjs/**'
			]
		},
		banana: {
			all: [
				'i18n/api/',
				'i18n/core/',
				'i18n/credits/',
				'i18n/deferred/',
				'i18n/diagnostics/',
				'i18n/extjs/',
				'i18n/extjs-portal/',
				'i18n/filerepo/',
				'i18n/installer/',
				'i18n/notifications/',
				'i18n/upload',
				'i18n/validator/'
			]
		},
		jsonlint: {
			all: [
				'*.json',
				'**/*.json',
				'!node_modules/**',
				'!vendor/**'
			]
		}
	} );

	grunt.registerTask( 'test', [ 'jshint', 'jsonlint', 'banana' ] );
	grunt.registerTask( 'default', 'test' );
};
