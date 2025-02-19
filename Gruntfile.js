module.exports = function ( grunt ) {
	grunt.loadNpmTasks( 'grunt-eslint' );
	grunt.loadNpmTasks( 'grunt-banana-checker' );

	var conf = grunt.file.readJSON( 'extension.json' );
	grunt.initConfig( {
		eslint: {
			options: {
				cache: true
			},
			src: [
				'**/*.{js,json}',
				'!node_modules/**',
				'!vendor/**',
				'!resources/bluespice.extjs/**' // extjs to be removed
			]
		},
		// eslint-disable-next-line es-x/no-object-assign
		banana: Object.assign(
			conf.MessagesDirs,
			{
				options: {
					requireLowerCase: 'initial'
				}
			}
		)
	} );

	grunt.registerTask( 'test', [ 'eslint', 'banana' ] );
	grunt.registerTask( 'default', 'test' );
};
