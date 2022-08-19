**ABOUT**

Developer instructions and system documentation written by Nicolas Kristoffersen, nk@aperify.com, +45 2022 0044.

The setup requires some knowledge about Docker. Read more about Docker at https://www.docker.com/

Code is documented in the code files.

**SETUP**

Set up your local environment this way:
1. Install docker and docker-compose on your developer machine.
2. Clone the repository with "git clone {url of the repository}".
3. Change directory into the respository: "cd {name of repository}".
4. Copy variables_template.env to the parent folder outside the git repository and rename it to variables.env running this command: "cp variables_template.env ../variables.env"
5. Modify environmental variables. Ask the lead developer for database access.
6. Run "docker-compose --env-file ../variables.env up --build --force-recreate --detach".
7. Open "http://localhost" in a browser.
8. To stop run "docker-compose --env-file ../variables.env down --remove-orphans".

Sometimes (on Windows at least) we experience issues with linking the shared drives for the file server instance. To disable this part (such that the docker application instance will run without constantly rebooting), edit your local file groupcaliberportal/application/Dockerfile and outcomment/remove the lines for sshfs like this:
#sshfs -o StrictHostKeyChecking=no,nonempty,allow_other,port=$FILES_PORT,IdentityFile=$APPLICATION_SSH_IDENTITY_FILE user@$FILES_URL:/home/user/logos /var/www/html/images/logos && \
#sshfs -o StrictHostKeyChecking=no,nonempty,allow_other,port=$FILES_PORT,IdentityFile=$APPLICATION_SSH_IDENTITY_FILE user@$FILES_URL:/home/user/slides /var/www/html/images/slides && \

**ACCESS**

Access the application docker instance this way:
1. Run "docker container list" (take the instance id from here).
2. Run "docker container attach {id from step 1}".

**DEVELOP**

You can change files locally and build them in the Docker instance this way:
1. Setup your IDE like IntelliJ or similar to upload changed code files with ssh to user@localhost port 3322 (or other port defined in variables).

**BRANCHES**

The main branch is "development". When a feature request, change request or issue report is processed, it is done in a separate branch with a corresponding name.  Thus, when a deliverable is assigned to a developer, the developer creates a branch locally from the development branch. The developer then makes changes and tests locally. When successfully tested, the developed creates a pull requests. The lead developer merges code into the "development" branch. When code has been tested, it is merged into the "staging" branch and deployed to the staging servers. When tested there, changes are merged into the production branch, built, deployed to production and released.

**ARCHITECTURE**

The system uses a standard architecture with a load balancer, three (or more) applications servers, a shared database and a shared cache. The architecture was implemented on Amazon Web Services (AWS). The setup uses AWS EC2 instances and load balancer. See readme-architecture.jpeg for a graphical representation.
The load balancers are:

Production:

    ARN: arn:aws:elasticloadbalancing:eu-north-1:006851472419:loadbalancer/app/groupcaliberportalapplicationpro/91a96c049072c9b6 
    DNS: groupcaliberportalapplicationpro-1358434654.eu-north-1.elb.amazonaws.com

Staging:

    ARN: arn:aws:elasticloadbalancing:eu-north-1:006851472419:loadbalancer/app/groupcaliberportalapplicationsta/e553ed07e3ffb055 
    DNS: groupcaliberportalapplicationsta-277845103.eu-north-1.elb.amazonaws.com

**DNS**

For each subdomain, the following records should exits (xyz used as subdomain):

    CNAME xyz.groupcaliber.com -> groupcaliberportalapplicationpro-1358434654.eu-north-1.elb.amazonaws.com
    CNAME xyzstaging.groupcaliber.com -> groupcaliberportalapplicationsta-277845103.eu-north-1.elb.amazonaws.com
    A     xyzdevelopment.groupcaliber.com -> 127.0.0.1


**DEPLOYMENT**

The lead developer clones the "staging" or "production" branches, builds the application Docker file and deploy it to the applications instances.  

**SYSTEM HISTORY**

The portal application was developed as a standalone PHP application by a team of software developers in Ukraine until 2021. The original code was then encapsulated in a Docker structure for easier deployment to a load balanced environment and for support for future increased modularity and gradual improvements in structure and performance upgrades for the code base.

DEVELOPMENT PROCEDURES AND RULES

Prior to be included in a sprint each task must be specified on a Jira card and meet the following requirements:

1. It must be clear to the programmer how the user interface looks and functions.
2. It must be clear to the programmer how the logic works.
3. It must be clear to the programmer which changes to make to the data structure.
4. It must be clear to the tester how the task can be tested.

The software programmer must comply with the following rules:

1. Each task must be implemented in its own branch named the same as the Jira card.
2. A task must be fully completed and tested before a pull request is created.
3. A task must be tested by the programmer herself/himself in a development environment similar to the production and staging environment before a pull request is created.
4. The programmer must complete her/his current task and create a pull request before starting on a new task. In some cases the programmers can work on 2 or 3 tasks at a time, but never more than that.
5. The programmer must document all functions and all blocks of code with code comments. If this is not the case, the code is not considered complete.
6. The programmer must complete all her/his tasks in their sprints well in time for testers to be able to test during working hours and still make it in time for the closure of the sprint. This includes time for modifications and retest. This usually means 3 working days before the sprint ends.
7. The programmer must complete features as fast as possible and then help testing at the end of the sprint.

**CODING BEST PRACTICES**

We use tabs not spaces.

Function descriptions:
You must add a function description to any function you create on the following form:

```
/* 
* This is a function description. Foo does nothing special.
* $bar is an array containing expected data strings on the form ["baz"].
* It returns bool: true on success else false.
*/
function foo($bar) {
...
```

Single line comments:
"//" is used for single line comments in the code. If the comment gets too long, please use multi line comments.

Multi line comments:
"/* */" is used when a comment takes up multiple lines.

All files should end with a new line (i.e. a blank line at the end/bottom of the file).
