module.exports = function (grunt) {

	var exec = require('child_process').exec, child;
	var execSync = require('child_process').execSync;
	var clc = require('cli-color');
	var colors = {
		alert : clc.xterm(214),
		warn  : clc.yellow,
		error : clc.red.bold,
		notice: clc.blue,
		code  : clc.white.bold
	};


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
		// Remove temporary directories and files that are part of the build process.
		clean: {
			build: [ 'build' ]
		},
		// Compress the build contents into a zip file
		compress: {
			main: {
				options: {
					archive: function () {
						return (
							'dist/' + grunt.config('pkg').name + '_' + grunt.config('pkg').version +
							((typeof shortSHA !== 'undefined') && (shortSHA.length > 0) ? '+' + shortSHA : '') +
							'.zip'
						);
					},
					level: 5,
					pretty: true
				},
				files: [{
					expand: true,
					cwd: 'build',
					src: [ '**' ],
					dest: '<%= pkg.name %>'
				}]
			}
		},
		// Create a build directory
		copy: {
			main: {
				files: [
					{
						expand: true,
						src: [
							'*',
							'**/*',
							// Excluded Directories
							'!.*/**',
							'!**/.*/',
							'!{build,dist,docs,tests,node_modules}/**',
							// Excluded Files
							'!{build.xml,phpunit.xml,LessCompilerOutput.txt,vagrantfile}',
							// Excluded File Patterns
							'!**/.*',
							'!**/*.{sublime*,less,tmp}',
							'!phpdoc*.xml'
						],
						dest: 'build'
					},
					{
						expand: true,
						cwd: 'htdocs/',
						src: ['js/**', 'css/**', 'lib/**', 'img/**', 'sass/**', 'skins/**'],
						dest: 'build/static/'
					}
				]
			}
		}
	});

	grunt.loadNpmTasks('grunt-contrib-clean');
	grunt.loadNpmTasks('grunt-contrib-copy');
	grunt.loadNpmTasks('grunt-contrib-compress');
	grunt.loadNpmTasks('grunt-contrib-less');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-gitinfo');

	var gitProp = function (propName) {
		return grunt.template.process('<%= gitinfo.local.branch.current.' + propName + ' %>');
	};


	/* Tasks ************************************************************************************ */

	grunt.registerTask('default', 'compile');

	grunt.registerTask(
		'compile',
		'Compiles LESS (.less) and JavaScript (.js) files.\n\t' + colors.code('compile[:less|:js]') + '.',
		function (mode) {
			var less = (!mode || (mode === 'less')) ? grunt.task.run(['less'])   : null;
			var js   = (!mode || (mode === 'js'))   ? grunt.task.run(['uglify']) : null;
		}
	);

	grunt.registerTask('outputInfo', function () {
		grunt.log.writeln('Current HEAD SHA:       ' + gitProp('SHA'));
		grunt.log.writeln('Current HEAD short SHA: ' + gitProp('shortSHA'));
		grunt.log.writeln('Current branch name:    ' + gitProp('name'));
		grunt.log.writeln('Current git user:       ' + gitProp('currentUser'));
		grunt.log.writeln('Last commit time:       ' + gitProp('lastCommitTime'));
		grunt.log.writeln('Last commit message:    ' + gitProp('lastCommitMessage'));
		grunt.log.writeln('Last commit author:     ' + gitProp('lastCommitAuthor'));
		grunt.log.writeln('Last commit number:     ' + gitProp('lastCommitNumber'));
	});

	grunt.registerTask('info', function () {
		grunt.task.run('gitinfo');
		grunt.task.run('outputInfo');
	});

	grunt.registerTask('build', function () {
		grunt.task.run('createBuild');
		grunt.task.run('compressBuild');
	});

	grunt.registerTask('build-test', function () {
		grunt.task.run('gitinfo');
		grunt.task.run('compressBuild:test');
	});


	/* Subtasks ********************************************************************************* */

	grunt.registerTask('createBuild', function (mode) {
		if (typeof mode === 'undefined') {
			mode = 'main';
		}
		grunt.task.run('clean');
		grunt.log.writeln('Creating ' + colors.code('build') + ' directory');
		grunt.task.run('copy:main');
	});

	grunt.registerTask('compressBuild', function (mode) {
		mode = (typeof mode === 'undefined' ? 'main' : mode);
		if (mode === 'test') {
			// Global variable shortSHA
			shortSHA = grunt.config('gitinfo').local.branch.current.shortSHA;
		}
		grunt.log.writeln('');
		grunt.log.writeln(
			'Creating file: ' +
			colors.alert(
				grunt.template.process(
					'<%= pkg.name %>_<%= pkg.version %>' +
					(mode === 'test' ? '+<%= gitinfo.local.branch.current.shortSHA %>' : '')
				) + '.zip'
			)
		);
		grunt.task.run('compress:main');
	});

};
