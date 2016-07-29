module.exports = function (grunt) {

	var exec = require('child_process').exec, child;
	var execSync = require('child_process').execSync;
	var clc = require('cli-color');
	var colors = {
		alert      : clc.xterm(214),
		warn       : clc.yellow,
		error      : clc.red.bold,
		info       : clc.blueBright,
		notice     : clc.cyanBright,
		success    : clc.greenBright,
		code       : clc.white.bold,
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
		},
		compile: {
			less: {
				task: 'less',
				desc: 'Compiling Less Files'
			},
			js: {
				task: 'uglify',
				desc: 'Minifiying JavaScript Files'
			}
		}
	});

	grunt.loadNpmTasks('grunt-contrib-clean');
	grunt.loadNpmTasks('grunt-contrib-copy');
	grunt.loadNpmTasks('grunt-contrib-compress');
	grunt.loadNpmTasks('grunt-contrib-less');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-gitinfo');


	/* Tasks ************************************************************************************ */

	grunt.registerTask('default', 'compile');

	grunt.registerMultiTask(
		'compile',
		colors.success('use: ') +
		'Compiles LESS (.less) and/or JavaScript (.js) files.' +
		'\n\t' + colors.code('compile[:less|:js]'),
		function () {
			grunt.log.writeln('');
			grunt.log.writeln(colors.notice(this.data.desc));
			grunt.task.run(this.data.task);
		}
	);

	grunt.registerTask(
		'info',
		colors.success('use: ') +
		'Print current commit info.',
		function () {
			grunt.task.run('gitinfo');
			grunt.task.run('output-info');
		}
	);

	grunt.registerTask(
		'build',
		colors.success('use: ') +
		'Create a build of the current version.',
		function () {
			grunt.task.run('create-build');
			grunt.task.run('gitinfo');
			grunt.task.run('compress-build');
		}
	);

	grunt.registerTask(
		'build-test',
		colors.success('use: ') +
		'Create a test build of the current version.',
		function () {
			grunt.task.run('create-build');
			grunt.task.run('gitinfo');
			grunt.task.run('compress-build:test');
		}
	);


	/* Subtasks ********************************************************************************* */

	grunt.registerTask(
		'output-info',
		colors.info('Subtask') + ' of ' + colors.code('info') + '. ' +
		'Output git commit info. Requires ' + colors.code('gitinfo') + '.',
		function () {
			var commitInfo = grunt.config('gitinfo').local.branch.current;
			grunt.log.writeln('Current HEAD SHA:       ' + commitInfo['SHA']);
			grunt.log.writeln('Current HEAD short SHA: ' + commitInfo['shortSHA']);
			grunt.log.writeln('Current branch name:    ' + commitInfo['name']);
			grunt.log.writeln('Current git user:       ' + commitInfo['currentUser']);
			grunt.log.writeln('Last commit time:       ' + commitInfo['lastCommitTime']);
			grunt.log.writeln('Last commit message:    ' + commitInfo['lastCommitMessage']);
			grunt.log.writeln('Last commit author:     ' + commitInfo['lastCommitAuthor']);
			grunt.log.writeln('Last commit number:     ' + commitInfo['lastCommitNumber']);
		}
	);

	grunt.registerTask(
		'create-build',
		colors.info('Subtask') + ' of ' + colors.code('build') + '. ' +
		'Clean and create a new build.',
		function () {
			grunt.task.run('clean');
			grunt.log.writeln('Creating ' + colors.code('build') + ' directory');
			grunt.task.run('copy:main');
		}
	);

	grunt.registerTask(
		'compress-build',
		colors.info('Subtask') + ' of ' + colors.code('build') + '. ' +
		'Create a zip file of the build.' +
		'\n\t' + colors.code('compress-build[:test]') +
		'\n\tIf using `test` mode, requires ' + colors.code('gitinfo') + '.',
		function (mode) {
			mode = (typeof mode === 'undefined' ? 'main' : mode);
			if (mode === 'test') {
				// Global variable shortSHA
				shortSHA = grunt.config('gitinfo').local.branch.current.shortSHA;
			}
			grunt.log.writeln('');
			grunt.log.writeln(
				'Creating file: ' +
				colors.alert(
					grunt.template.process('<%= pkg.name %>_<%= pkg.version %>') +
					(mode === 'test' ? '+' + shortSHA : '') +
					'.zip'
				)
			);
			grunt.task.run('compress:main');
		}
	);

};
