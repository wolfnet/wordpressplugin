module.exports = function (grunt) {

	/* Project Configuration ******************************************************************** */
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),
		gitinfo: {},
		// Compile and minify LESS content
		less: {
			development: {
				options: {
					compress: true,
					sourceMap: true
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
		// Minify JavaScript files and create source maps
		uglify: {
			main: {
				options: {
					sourceMap: true
				},
				files: [{
					expand: true,
					cwd: 'public/js',
					src: [ '*.src.js', '!**/*.min.js' ],
					dest: 'public/js/',
					ext: '.min.js',
					extDot: 'last',
					rename: function (dest, src) {
						return dest + src.replace('.src', '');
					}
				}]
			}
		},
		// Compress the build contents into a zip file
		compress: {
			main: {
				options: {
					archive: 'dist/<%= pkg.name %>_<%= pkg.version %>.zip'
				},
				files: [{
					expand: true,
					cwd: 'build',
					src: [ '**' ],
					dest: '.'
				}]
			},
			test: {
				options: {
					archive: 'dist/<%= pkg.name %>_<%= pkg.version %>+<%= gitinfo.local.branch.current.shortSHA %>.zip',
					level: 5
				},
				files: [{
					expand: true,
					cwd: 'build',
					src: [ '**' ],
					dest: '<%= pkg.name %>'
				}]
			}
		}
	});

	grunt.loadNpmTasks('grunt-contrib-compress');
	grunt.loadNpmTasks('grunt-contrib-less');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-gitinfo');

	grunt.registerTask('build', function () {
		grunt.task.run('gitinfo');
		grunt.task.run('compress');
	});

	grunt.registerTask('build-test', function () {
		grunt.task.run('gitinfo');
		grunt.task.run('compress:test');
	});

};
