module.exports = function (grunt) {

	/* Project Configuration ******************************************************************** */
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),
		// Compile and minify LESS content
		less: {
			development: {
				options: {
					//paths: [ 'public/css' ],
					//compress: true,
					//sourceMap: true
				},
				files: [{
					expand: true,
					cwd: 'public/css/',
					src: [ '*.less', '!*.inc.less' ],
					dest: 'public/css/',
					ext: '.min.css',
					extDot: 'last'
				}]
			}
		},
	});

	grunt.loadNpmTasks('grunt-contrib-less');

};
