/*
 * Scott Lake
 * https://github.com/voceconnect/myslbc
 */

'use strict';

module.exports = function(grunt) {

  // Project configuration.
  grunt.initConfig({
    "jshint": {
      "options": {
        "curly": true,
        "eqeqeq": true,
        "eqnull": true,
        "browser": true,
        "plusplus": true,
        "undef": true,
        "unused": true,
        "trailing": true,
        "globals": {
          "jQuery": true,
          "$": true,
          "ajaxurl": true
        }
      },
      "theme": [
        "wp-content/themes/scott-lake/js/main.js"
      ],
    },
    "uglify": {
      "theme": {
        "options": {
          "preserveComments": "some"
        },
        "files": {
          "wp-content/themes/scott-lake/js/main.min.js": [
            "wp-content/themes/scott-lake/js/main.js",
            "wp-content/themes/scott-lake/js/skip-link-focus-fix.js"
          ],
          "wp-content/themes/scott-lake/js/libs/bootstrap.min.js": [
            "wp-content/themes/scott-lake/js/libs/bootstrap/**/*.js",
            "!wp-content/themes/scott-lake/js/libs/bootstrap/popover.js",
            "wp-content/themes/scott-lake/js/libs/bootstrap/popover.js"
          ]
        }
      }
    },
    "concat": {
      "bootstrap": {
        "src": [
          "wp-content/themes/scott-lake/js/libs/bootstrap/**/*.js",
          "!wp-content/themes/scott-lake/js/libs/bootstrap/popover.js",
          "wp-content/themes/scott-lake/js/libs/bootstrap/popover.js"
        ],
        "dest": "wp-content/themes/scott-lake/js/libs/bootstrap.js"
      },
      "main": {
        "src": [
          "wp-content/themes/scott-lake/js/main.js",
          "wp-content/themes/scott-lake/js/skip-link-focus-fix.js"
        ],
        "dest": "wp-content/themes/scott-lake/js/main.js"
      }
    },
    "imagemin": {
      "theme": {
        "files": [
          {
            "expand": true,
            "cwd": "wp-content/themes/scott-lake/images/",
            "src": "wp-content/themes/scott-lake/**/*.{png,jpg}",
            "dest": "wp-content/themes/scott-lake/images/"
          }
        ]
      }
    },
    "compass": {
      "options": {
        "config": "wp-content/themes/scott-lake/config.rb",
        "basePath": "wp-content/themes/scott-lake/",
        "force": true
      },
      "production": {
        "options": {
          "environment": "production"
        }
      },
      "development": {
        "options": {
          "environment": "development"
        }
      }
    },
    "watch": {
      "scripts": {
        "files": "wp-content/themes/scott-lake/js/**/*.js",
        "tasks": ["jshint", "concat"]
      },
      "images": {
        "files": "wp-content/themes/scott-lake/images/**/*.{png,jpg,gif}",
        "tasks": ["imagemin"]
      },
      "composer": {
        "files": "composer.json",
        "tasks": ["composer:update"]
      },
      "styles": {
        "files": "wp-content/themes/scott-lake/sass/**/*.scss",
        "tasks": ["compass"]
      }
    },
    "build": {
      "production": ["uglify", "composer:install:no-dev:optimize-autoloader", "compass:production"],
      "uat": ["uglify", "composer:install:no-dev:optimize-autoloader", "compass:production"],
      "staging": ["concat", "composer:install", "compass:development"],
      "development": ["concat", "composer:install", "compass:development"]
    }
  });

  //load the tasks
  grunt.loadNpmTasks('grunt-voce-plugins');

  //set the default task as the development build
  grunt.registerTask('default', ['build:development']);

};