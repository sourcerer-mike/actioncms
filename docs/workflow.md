# From WordPress to ActionCMS

> Because actions are louder than words.

## Abstract overview

There are some major steps and separations that need to be done. Those are learned while creating the clean ActionCMS:

- Seperate WordPress from ActionCMS
- Apply scripts on WordPress AST
- Use WordPress and ActionCMS

  It is very important to distinguish WordPress and the (almost) generated ActionCMS. The major repository of ActionCMS should only contain the changes from one version to another.
Making it the same source as WordPress is really hard, because you run into merge conflicts more often due to removed or changed code from both sides.
In addition the parser can be faster.
If the WordPress Code is separated from ActionCMS,
the PHP AST can be stored temporary based on the WordPress commit number.
This will bypass refetching the whole AST with every commit made in ActionCMS. 

All scripts that change the WordPress Core are made on the WordPress AST. The output should be the same over and over again.

WordPress and ActionCMS are fetched as external repository or own packages.
First of all WordPress is fetched as external package.
Some of its content will be moved to a in-between-folder
and some code will be manipulated before it's stored.
After that process all changes will be pushed to the ActionCMS repository.
TravisCI will investigate if the changes keep all tests green.