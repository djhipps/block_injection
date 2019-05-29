# block_jsinjection
This is a block that is a boilerplate plugin designed to appear on target pages and inject javascripts into the page.
This will allow important overrides to core plugins moodle using javascript and ajax calls as necessary.

Quick setup:
- rename all namespaced files to your target plugin name
- do a search and replace for jsinjection for all you files
- update the amd scripts to provide the injected functionality 
- edit the advanecd settings (this release only) to allow fine grained 
GUI control over the the blocks appearance and settings
- update ajax and webservices to provide a better interface  
