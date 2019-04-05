var timer = require('grunt-timer');

module.exports = function (grunt) {

	timer.init(grunt);

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
			return colors.success('main task : ') + (typeof text !== 'undefined' ? text + ' ' : '');
		},
		alias      : function (text) {
			return colors.note('alias ') + (typeof text !== 'undefined' ? colors.note('for : ') + text + ' ' : ':      ');
		},
		subtask    : function () {
			return colors.info('subtask');
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
							'!{build.xml,phpunit.xml,LessCompilerOutput.txt,docker-compose.yml}',
							// Excluded File Patterns
							'!**/.*',
							'!**/*.{sublime*,less,tmp}',
							'!phpdoc*.xml',
							'!readme.md',
							'!ReadMe.txt'
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
	grunt.loadNpmTasks('grunt-timer');


	/* Tasks ************************************************************************************ */

	grunt.registerTask(
		'build',
		flags.main() + 'Build the current version.' +
		'\n\tUsage:\t' + colors.code('build[:test]') + '\n',
		function (mode) {
			mode = (typeof mode !== 'undefined' ? mode : 'main');
			grunt.task.run('compile');
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
		flags.main() + 'Compile LESS and/or JS files.' +
		'\n\tUsage:\t' + colors.code('compile[:less|:js]') +
		'\n\t' + colors.info('This is a "multi task"'),
		function () {
			grunt.log.writeln('');
			grunt.log.writeln(colors.notice(this.data.desc));
			grunt.task.run(this.data.task);
		}
	);

	grunt.registerTask(
		'git-info',
		flags.main() + 'Print current commit info.' +
		'\n\tUsage:\t' + colors.code('git-info') + '\n',
		function (item) {
			grunt.task.run('gitinfo');
			grunt.task.run('output-info' + (typeof item !== 'undefined' ? ':' + item : ''));
		}
	);


	/* Aliases ********************************************************************************** */

	grunt.registerTask('default',             flags.alias('build'),               'build');
	grunt.registerTask('build-test',          flags.alias('build:test'),          'build:test');
	grunt.registerTask('dist',                flags.alias('build'),               'build');
	grunt.registerTask('test-dist',           flags.alias('build:test'),          'build:test');
	grunt.registerTask('compile-less',        flags.alias('compile:less'),        'compile:less');
	grunt.registerTask('compile-js',          flags.alias('compile:js'),          'compile:js');
	grunt.registerTask('minify-javascript',   flags.alias('compile:js'),          'compile:js');
	grunt.registerTask('git-revision',        flags.alias('git-info:shortSHA'),   'git-info:shortSHA');


	/* Subtasks ********************************************************************************* */

	// Creates a zip file of the build
	grunt.registerTask(
		'compress-build',
		flags.subtask(),
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

	// Cleans and creates a new build
	grunt.registerTask(
		'create-build',
		flags.subtask(),
		function () {
			grunt.task.run('clean');
			grunt.log.writeln('Creating ' + colors.code('build') + ' directory');
			grunt.task.run('copy:main');
			grunt.task.run('generate-readme');
		}
	);

	// Outputs git commit info
	grunt.registerTask(
		'output-info',
		flags.subtask(),
		function (item) {
			var commitInfo = grunt.config('gitinfo').local.branch.current;
			var infoItems = [
				{ name: 'SHA',                 label: 'Current HEAD SHA:       ' },
				{ name: 'shortSHA',            label: 'Current HEAD short SHA: ' },
				{ name: 'name',                label: 'Current branch name:    ' },
				{ name: 'currentUser',         label: 'Current git user:       ' },
				{ name: 'lastCommitTime',      label: 'Last commit time:       ' },
				{ name: 'lastCommitMessage',   label: 'Last commit message:    ' },
				{ name: 'lastCommitAuthor',    label: 'Last commit author:     ' },
				{ name: 'lastCommitNumber',    label: 'Last commit number:     ' }
			];
			item = (typeof item !== 'undefined' ? item : '');
			for (var i=0, l=infoItems.length; i<l; i++) {
				if ((item === '') || (item == infoItems[i].name)) {
					grunt.log.writeln(infoItems[i].label + commitInfo[infoItems[i].name]);
				}
			}
		}
	);

	// Updates version number on build files
	grunt.registerTask(
		'update-version',
		flags.subtask(),
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

	// Generates WordPress ReadMe.txt file
	grunt.registerTask('generate-readme', flags.subtask(), function () {
		var readme = grunt.file.read('readme.md');

		grunt.log.writeln('Generating ' + colors.code('ReadMe.txt') + ' for WordPress');

		// Format headings
		readme = readme.replace(/### ([^\n]*)/g, '= $1 =')
			.replace(/## ([^\n]*)/g, '== $1 ==')
			.replace(/# ([^\n]*)/g, '=== $1 ===');

		// Parse document
		sections = readme.match(/===?[^=]*===?(\n(?!(==))[^\n]*)*/g);

		readme = '';

		for (var i=0, l=sections.length; i<l; i++) {

			var section = sections[i],
				heading = section.match(/^[^\n]*\n/g),
				body    = section.substring(section.search(/\n/) + 1, section.length);

			// Format code snippets
			body = body.replace(/^```[^\n]*(\n[^`]*\n)```/gm, '`$1`');

			// Format YouTube items
			body = body.replace(/^\[\!?\[?[^\]]*\]?[^\]]*\][^\(\n]*\(([^\/\n]*\/\/[^\/]*youtube\.com[^\)]*)\)/gm, '[youtube $1]');

			// Top section
			if (i === 0) {

				// Replace properties table
				body = body.replace(/\n*[^\|]*\|[^|]*\n\s*---+\s*\|\s*---+[^\n]*\n/g, '')
					.replace(/^([^\|]*)\|([^\n]*)\n/gm, '$1$2\n');

				// Remove quoted description format
				body = body.replace(/^\>\s*/gm, '');

			} else {
				readme += '\n\n';
			}

			readme += heading + body;

		}

		grunt.file.write('build/ReadMe.txt', readme);

	});

};
