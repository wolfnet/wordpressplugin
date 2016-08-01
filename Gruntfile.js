module.exports = function (grunt) {

	var exec = require('child_process').exec, child;
	var execSync = require('child_process').execSync;
	var clc = require('cli-color');
	var colors = {
		alert      : clc.xterm(214),
		warn       : clc.yellow,
		error      : clc.red.bold,
		info       : clc.blueBright,
		note       : clc.magenta,
		notice     : clc.cyanBright,
		success    : clc.greenBright,
		code       : clc.white.bold,
	};
	var flags = {
		main       : function (text) {
			return colors.success('♥ ') + (typeof text !== 'undefined' ? text + ' ' : '');
		},
		alias      : function (text) {
			return colors.note('alias ') + (typeof text !== 'undefined' ? colors.note('for  : ') + text + ' ' : '      ');
		},
		subtask    : function (text) {
			return colors.info('subtask ') + (typeof text !== 'undefined' ? colors.info('of : ') + text + ' ' : '    ');
		},
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

	grunt.registerTask(
		'build',
		flags.main() +
		'Build the current version.        ' +
		colors.code('build[:test]'),
		function (mode) {
			mode = (typeof mode !== 'undefined' ? mode : 'main');
			grunt.task.run('create-build');
			grunt.task.run('gitinfo');
			if (mode === 'test') {
				grunt.task.run('update-version:test');
				grunt.task.run('compress-build:test');
			} else {
				grunt.task.run('update-version');
				grunt.task.run('compress-build');
			}
		}
	);

	grunt.registerMultiTask(
		'compile',
		flags.main() +
		'Compile LESS and/or JS files.     ' +
		colors.code('compile[:less|:js]'),
		function () {
			grunt.log.writeln('');
			grunt.log.writeln(colors.notice(this.data.desc));
			grunt.task.run(this.data.task);
		}
	);

	grunt.registerTask(
		'info',
		flags.main() +
		'Print current commit info.        ' + colors.code('info'),
		function () {
			grunt.task.run('gitinfo');
			grunt.task.run('output-info');
		}
	);


	/* Aliases ********************************************************************************** */

	grunt.registerTask('default',        flags.alias('build'),          'build');
	grunt.registerTask('build-test',     flags.alias('build:test'),     'build:test');
	grunt.registerTask('dist',           flags.alias('build'),          'build');
	grunt.registerTask('test-dist',      flags.alias('build:test'),     'build:test');
	grunt.registerTask('compile-less',   flags.alias('compile:less'),   'compile:less');
	grunt.registerTask('compile-js',     flags.alias('compile:js'),     'compile:js');


	/* Subtasks ********************************************************************************* */

	grunt.registerTask(
		'compress-build',
		flags.subtask('build') + '   creates a zip file of the build',
		function (mode) {
			mode = (typeof mode !== 'undefined' ? mode : 'main');
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

	grunt.registerTask(
		'create-build',
		flags.subtask('build') + '   cleans and creates a new build',
		function () {
			grunt.task.run('clean');
			grunt.log.writeln('Creating ' + colors.code('build') + ' directory');
			grunt.task.run('copy:main');
		}
	);

	grunt.registerTask(
		'output-info',
		flags.subtask('info') + '    outputs git commit info',
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
		'update-version',
		flags.subtask('build') + '   updates version number on build files',
		function (mode) {
			var version = grunt.config('pkg').version,
				versionParts = version.split('.'),
				majorVersion = versionParts[0] + '.' + versionParts[1],
				minorVersion = (versionParts.length > 2 ? versionParts[2] : 0),
				versionFiles = grunt.file.expand('build/**/*.php', 'build/ReadMe.txt');
			mode = (typeof mode !== 'undefined' ? mode : 'main');
			if (mode === 'test') {
				var shortSHA = grunt.config('gitinfo').local.branch.current.shortSHA;
			}
			versionFiles.forEach(function (filePath) {
				grunt.file.copy(filePath, filePath, {
					process: function (text) {
						text = text.replace('{majorVersion}', majorVersion);
						text = text.replace('{minorVersion}', minorVersion);
						text = text.replace('{X.X.X}', version + (mode === 'test' ? '+' + shortSHA : ''));
						text = text.replace('{X.X.X-stable}', version);
						return text;
					}
				});
			});
		}
	);

};
