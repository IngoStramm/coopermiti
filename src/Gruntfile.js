/* jshint node:true */
module.exports = function (grunt) {
    'use strict';

    const sass = require('node-sass');
    require('load-grunt-tasks')(grunt);

    var odinConfig = {

        // gets the package vars
        pkg: grunt.file.readJSON('package.json'),

        // setting folder templates
        dirs: {
            css: '../assets/css',
            js: '../assets/js',
            sass: '../assets/sass',
            images: '../assets/images',
            tmp: 'tmp'
        },

        // javascript linting with jshint
        jshint: {
            options: {
                jshintrc: '<%= dirs.js %>/.jshintrc'
            },
            all: [
                'Gruntfile.js',
                '<%= dirs.js %>/coopermiti.js'
            ]
        },

        // uglify to concat and minify
        uglify: {
            dist: {
                files: {
                    '<%= dirs.js %>/coopermiti.min.js': [
                        '<%= dirs.js %>/coopermiti.js'    // Custom JavaScript
                    ]
                }
            }
        },

        sass: {
            options: {
                implementation: sass,
                sourceMap: true,
                style: 'compressed'
            },
            dist: {
                files: [{
                    expand: true,
                    cwd: '<%= dirs.sass %>',
                    src: ['*.scss'],
                    dest: '<%= dirs.css %>',
                    ext: '.css'
                }],
            }
        },

        // watch for changes and trigger sass, jshint, uglify and livereload browser
        watch: {
            sass: {
                files: [
                    '<%= dirs.sass %>/**'
                ],
                tasks: ['sass']
            },
            js: {
                files: [
                    '<%= jshint.all %>'
                ],
                tasks: ['jshint', 'uglify']
            },
            livereload: {
                options: {
                    livereload: true
                },
                files: [
                    '<%= dirs.css %>/*.css',
                    '<%= dirs.js %>/*.js',
                    '../**/*.php'
                ]
            },
            options: {
                spawn: false
            }
        },

        // image optimization
        imagemin: {
            dist: {
                options: {
                    optimizationLevel: 7,
                    progressive: true
                },
                files: [{
                    expand: true,
                    filter: 'isFile',
                    cwd: '<%= dirs.images %>/',
                    src: '**/*.{png,jpg,gif}',
                    dest: '<%= dirs.images %>/'
                }]
            }
        },

        // zip the theme
        zip: {
            dist: {
                cwd: '../../',
                src: [
                    '../**',
                    '!../src/**',
                    '!../dist/**',
                    '!../**.md',
                    '!../**.txt',
                    '!<%= dirs.sass %>/**',
                    '!../**.zip',
                    '!../info.json',
                    '<%= dirs.js %>/coopermiti.min.js'
                ],
                dest: '../dist/<%= pkg.name %>.zip'
            }
        }


    };

    // Initialize Grunt Config
    // --------------------------
    grunt.initConfig(odinConfig);

    // Register Tasks
    // --------------------------

    // Default Task
    grunt.registerTask('default', [
        'jshint',
        'sass',
        'uglify'
    ]);

    // Optimize Images Task
    grunt.registerTask('optimize', ['imagemin']);

    // Compress
    grunt.registerTask('compress', [
        'default',
        'optimize',
        'zip'
    ]);


    // Short aliases
    grunt.registerTask('w', ['watch']);
    grunt.registerTask('o', ['optimize']);
    grunt.registerTask('c', ['compress']);
};