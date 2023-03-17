module.exports = function (grunt) {
	grunt.initConfig({
		compress: {
			main: {
			  options: {
				archive: 'insta-admin.zip'
			  },
			  files: [
				{src: ['insta-admin-landing-page.php'], dest: '/', filter: 'isFile'}, // includes files in path
				{src: ['assets/**'], dest: '/'}, // includes files in path and its subdirs
				{src: ['build/**'], dest: '/'}, // includes files in path and its subdirs
				{src: ['dist/**'], dest: '/'}, // includes files in path and its subdirs
				{src: ['lib/**'], dest: '/'}, // includes files in path and its subdirs
				{src: ['php/**'], dest: '/'}, // includes files in path and its subdirs
			  ]
			}
		  }
	  });
	  grunt.registerTask('default', ["compress"]);

 
 
	grunt.loadNpmTasks( 'grunt-contrib-compress' );
   
 };
