## workflow for Monolithic repository

![Img](.docs/monolithic.png)

What is Monolithic?
-----------------
For a system consisting of the strongly coupled discrete services, some changes may have global impact, so the source code 
organization should allow to perform them in an efficient way. The monolithic repo is the main source code repository 
for all development activities, contains the source code of all repositories nested inside another as a subdirectory.

Create monolithic from existing repos
------------------------------------

Git subtree add allow include into monolithic repo by importing its content from the given remote repo keeping git history

Example:
```
git subtree add --prefix=packages/database-isolation git@github.com:vtsykun/database-isolation.git master
```

...