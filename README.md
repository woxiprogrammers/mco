# mco
Manisha Construction Management Software


Prerequisites:

1. php version >= 5.6
2. composer
3. nodejs version >=7 

Project Installation steps:

1. Clone this repository in a folder using command :

        git clone <repository url/ssh key>
        
2. Switch to branch 'develop' Run command:

        composer install        
          
3. Above command will create 'vendor' folder in your project directory.
    Now give 777 permission to following folders.
    
    1. storage/
    2. bootstrap/cache
    
4. For installing laravel mix Run command
        
        npm install

5. To run laravel mix,(convert 'scss' to 'css' file) 
     
        npm run production 
        
6. You need to setup virtual hosts also.